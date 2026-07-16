<?php
namespace App\Services;

final class AgentRuntimeService {
    private SecretService $secrets;
    private array $routing;

    public function __construct() {
        $this->secrets = new SecretService();
        $this->routing = $this->loadRouting();
    }

    public function processWebhook(array $payload, string $event): array {
        $action = $payload['action'] ?? '';
        $eventKey = "{$event}.{$action}";

        if (isset($payload['issue'])) {
            $issue = $payload['issue'];
            $issueNum = $issue['number'] ?? 0;
            $issueBody = $issue['body'] ?? '';
            $issueTitle = $issue['title'] ?? '';
        } elseif (isset($payload['pull_request'])) {
            $pr = $payload['pull_request'];
            $issueNum = $pr['number'] ?? 0;
            $issueBody = $pr['body'] ?? '';
            $issueTitle = $pr['title'] ?? '';
        } else {
            return ['handled' => false, 'reason' => 'No issue or PR in payload'];
        }

        if ($eventKey === 'issue_comment.created' && isset($payload['comment'])) {
            $commentBody = $payload['comment']['body'] ?? '';
            $handoff = $this->parseHandoffJson($commentBody);
            if ($handoff !== null) {
                return $this->executeHandoff($handoff, $payload);
            }
        }

        $role = $this->routeEvent($event, $action);
        if ($role === null) {
            return ['handled' => false, 'reason' => "No handler for event: {$eventKey}"];
        }

        $agentConfig = $this->loadAgentConfig();
        $db = new DatabaseService();
        $siteContext = $this->buildSiteContext($db);

        $aiPrompt = $this->buildRolePrompt($role, $issueNum, $issueTitle, $issueBody, $siteContext, $payload);
        $mc = $this->secrets->getModelConfig();

        if (empty($mc['apiKey'])) {
            return ['handled' => false, 'reason' => 'AI not configured'];
        }

        $response = $this->callAi($mc, $aiPrompt);
        $parsed = $this->parseAiResponse($response, $role);

        $result = [
            'handled' => true,
            'role' => $role,
            'issue' => $issueNum,
            'event' => $eventKey,
            'response' => $parsed,
        ];

        if ($role === 'cto' && !empty($parsed['objectives'])) {
            $result['objectives'] = $parsed['objectives'];
        }

        return $result;
    }

    private function routeEvent(string $event, string $action): ?string {
        $eventKey = "{$event}.{$action}";
        $map = [
            'issues.opened' => 'cto',
            'issues.edited' => 'cto',
            'pull_request.opened' => 'reviewer',
            'pull_request.synchronize' => 'reviewer',
            'push.main' => 'deployment',
        ];
        return $map[$eventKey] ?? null;
    }

    public function buildRolePrompt(string $role, int $issueNum, string $title, string $body, string $context, array $payload): string {
        $roleDescriptions = [
            'cto' => 'You are the CTO. Your job is to analyze issues and create a plan with specific, actionable objectives. Format your response with clear numbered objectives (OBJ-{issue}-{n}) and acceptance criteria.',
            'worker' => 'You are the Worker agent. Your job is to implement one specific objective. Write clean, tested code following existing patterns.',
            'reviewer' => 'You are the Reviewer agent. Your job is to review the implementation diff for correctness, test coverage, and schema consistency.',
            'fixer' => 'You are the Fixer agent. Your job is to fix issues identified during review.',
            'documenter' => 'You are the Documenter agent. Your job is to update affected durable documentation after changes are merged.',
        ];

        $desc = $roleDescriptions[$role] ?? "You are a {$role} agent in the bapXaura orchestration system.";

        return <<<PROMPT
{$desc}

## Site Context
{$context}

## Issue #{$issueNum}: {$title}

{$body}

## Response Format
Respond with a clear analysis and action plan. For CTO role, include numbered objectives.
Use JSON where appropriate for structured data.
PROMPT;
    }

    private function callAi(array $mc, string $prompt): string {
        $endpoint = rtrim($mc['endpoint'] ?? 'https://api.openai.com/v1', '/');
        $model = $mc['model'] ?? 'gemma-4-31b-it';
        $key = $mc['apiKey'] ?? '';
        $provider = $mc['provider'] ?? 'openai';

        if ($provider === 'google') {
            $url = $endpoint . '/' . rawurlencode($model) . ':generateContent';
            $payload = json_encode([
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 2048],
            ]);
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-goog-api-key: ' . $key],
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_CONNECTTIMEOUT => 10,
            ]);
            $body = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($status !== 200 || $body === false) return "API error (HTTP {$status})";
            $result = json_decode($body, true);
            return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'No response.';
        }

        $url = $endpoint . '/chat/completions';
        $payload = json_encode([
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a precise, action-oriented agent in an automated orchestration system.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 2048,
            'temperature' => 0.3,
        ]);

        $ch = curl_init($url);
        $headers = ['Content-Type: application/json'];
        if ($provider === 'anthropic') {
            $headers[] = 'x-api-key: ' . $key;
            $headers[] = 'anthropic-version: 2023-06-01';
        } else {
            $headers[] = 'Authorization: Bearer ' . $key;
        }
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);
        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($status !== 200 || $body === false) return "API error (HTTP {$status})";
        $result = json_decode($body, true);
        return $result['choices'][0]['message']['content'] ?? 'No response.';
    }

    private function parseAiResponse(string $response, string $role): array {
        $objectives = [];
        if ($role === 'cto') {
            preg_match_all('/OBJ-\d+-\d+[^)]*/', $response, $matches);
            foreach ($matches[0] ?? [] as $m) {
                $objectives[] = trim($m);
            }
        }
        return [
            'text' => $response,
            'objectives' => $objectives,
            'summary' => mb_substr($response, 0, 500),
        ];
    }

    public function parseHandoffJson(string $body): ?array {
        if (preg_match('/<!--\s*bapx-handoff\s*\n?(.*?)\n?\s*-->/s', $body, $m)) {
            $decoded = json_decode(trim($m[1]), true);
            if ($decoded && isset($decoded['from_role'], $decoded['to_role'])) {
                return $decoded;
            }
        }
        return null;
    }

    public function executeHandoff(array $handoff, array $payload): array {
        $toRole = $handoff['to_role'] ?? '';
        $objectiveId = $handoff['objective_id'] ?? '';
        $issue = $handoff['issue'] ?? 0;

        $db = new DatabaseService();
        $context = $this->buildSiteContext($db);
        $mc = $this->secrets->getModelConfig();

        $prompt = "Handoff from {$handoff['from_role']} to {$toRole} for objective {$objectiveId} on issue #{$issue}.\n\nStatus: {$handoff['status']}\n\n" .
                  ($handoff['blocking_findings'] ? "Blocking findings: " . implode(', ', $handoff['blocking_findings']) : '') .
                  "\n\nSite data:\n{$context}\n\nExecute your role.";

        $response = !empty($mc['apiKey']) ? $this->callAi($mc, $prompt) : 'AI not configured.';

        return [
            'handled' => true,
            'handoff' => $handoff,
            'response_text' => $response,
            'objective' => $objectiveId,
        ];
    }

    private function buildSiteContext(DatabaseService $db): string {
        $userCount = count($db->read('users'));
        $orderCount = count($db->read('orders'));
        $productCount = count($db->read('products'));

        return "- Users: {$userCount}\n- Orders: {$orderCount}\n- Products: {$productCount}\n- Revenue: ₹" . number_format(array_sum(array_column($db->read('orders'), 'total')), 2);
    }

    private function loadRouting(): array {
        $path = app_path('.agents/workflows/routing.yaml');
        if (!is_file($path)) return [];
        $content = file_get_contents($path);
        $routing = [];
        $currentRole = null;
        foreach (explode("\n", $content) as $line) {
            if (preg_match('/^\s{2}(\w+):/', $line, $m)) {
                $currentRole = $m[1];
                $routing[$currentRole] = ['handle' => [], 'next' => []];
            } elseif (preg_match('/^\s{6}- (.+)$/', $line, $m) && $currentRole) {
                $routing[$currentRole]['handle'][] = $m[1];
            }
        }
        return $routing;
    }

    private function loadAgentConfig(): array {
        $config = ['name' => 'Agent'];
        $path = app_path('config/agent.yml');
        if (is_file($path)) {
            foreach (explode("\n", file_get_contents($path)) as $line) {
                if (preg_match('/^\s*(\w+):\s*(.+)$/', $line, $m)) {
                    $config[$m[1]] = trim($m[2], " \"'");
                }
            }
        }
        return $config;
    }
}
