<?php
namespace App\Services;

final class AgentRuntimeService {
    private SecretService $secrets;
    private array $routing;

    private AiService $ai;

    public function __construct() {
        $this->secrets = new SecretService();
        $this->routing = $this->loadRouting();
        $this->ai = new AiService($this->secrets);
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
        $response = $this->ai->call([
            ['role' => 'system', 'content' => 'You are a precise, action-oriented agent in an automated orchestration system.'],
            ['role' => 'user', 'content' => $aiPrompt],
        ], ['max_tokens' => 2048, 'timeout' => 60]);
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
        foreach ($this->routing as $role => $config) {
            if (in_array($eventKey, $config['handle'] ?? [], true)) {
                return $role;
            }
        }
        return null;
    }

    public function buildRolePrompt(string $role, int $issueNum, string $title, string $body, string $context, array $payload): string {
        $roleFile = app_path('.agents/roles/' . $role . '.md');
        $roleContent = '';
        if (is_file($roleFile)) {
            $content = file_get_contents($roleFile);
            if (preg_match('/^---.*?---\s*(.*)$/s', $content, $m)) {
                $roleContent = trim($m[1]);
            } else {
                $roleContent = trim($content);
            }
        }
        $desc = $roleContent ?: "You are a {$role} agent in the bapXaura orchestration system.";

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

        $prompt = "Handoff from {$handoff['from_role']} to {$toRole} for objective {$objectiveId} on issue #{$issue}.\n\nStatus: {$handoff['status']}\n\n" .
                  ($handoff['blocking_findings'] ? "Blocking findings: " . implode(', ', $handoff['blocking_findings']) : '') .
                  "\n\nSite data:\n{$context}\n\nExecute your role.";

        $response = $this->ai->call([
            ['role' => 'system', 'content' => 'You are a precise, action-oriented agent in an automated orchestration system.'],
            ['role' => 'user', 'content' => $prompt],
        ], ['max_tokens' => 2048, 'timeout' => 60]);

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
