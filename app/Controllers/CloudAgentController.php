<?php
namespace App\Controllers;

use App\Services\AgentRuntimeService;
use App\Services\SecretService;

final class CloudAgentController extends BaseController {
    public function webhook(): void {
        $payload = json_decode(file_get_contents('php://input'), true);
        $event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
        $signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

        if (empty($payload) || $event === '') {
            $this->jsonResponse(['error' => 'Missing payload or event header'], 400);
            return;
        }

        if ($signature !== '' && !$this->verifySignature($payload, $signature)) {
            $this->jsonResponse(['error' => 'Invalid signature'], 403);
            return;
        }

        if ($event === 'ping') {
            $this->jsonResponse(['message' => 'pong', 'event' => $event]);
            return;
        }

        try {
            $runtime = new AgentRuntimeService();
            $result = $runtime->processWebhook($payload, $event);
            $this->jsonResponse($result);
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => 'Agent runtime error: ' . $e->getMessage()], 500);
        }
    }

    public function status(): void {
        $owner = getenv('OWNER_GITHUB') ?: 'getwinharris';
        $runtime = new AgentRuntimeService();
        $handoffEvents = glob(app_path('.agents/handoffs/events/*.json')) ?: [];

        $this->jsonResponse([
            'status' => 'active',
            'owner' => $owner,
            'handoff_events' => count($handoffEvents),
            'runtime' => AgentRuntimeService::class,
            'version' => '1.0.0',
        ]);
    }

    public function handoffs(): void {
        $eventsDir = app_path('.agents/handoffs/events');
        $files = glob($eventsDir . '/*.json') ?: [];
        $events = [];
        foreach ($files as $f) {
            $data = json_decode((string)file_get_contents($f), true);
            if ($data) {
                $wf = $data['workflow'] ?? [];
                $events[] = [
                    'file' => basename($f),
                    'issue' => $data['issue'] ?? '?',
                    'from_role' => $wf['current_role'] ?? $data['from_role'] ?? '?',
                    'to_role' => $wf['next_role'] ?? $data['to_role'] ?? '?',
                    'status' => $data['status'] ?? $wf['status'] ?? '?',
                ];
            }
        }
        $this->jsonResponse(['events' => $events]);
    }

    public function prompt(): void {
        set_time_limit(120);
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || empty($input['message'])) {
            $this->jsonResponse(['error' => 'Message is required'], 400);
            return;
        }

        $role = $input['role'] ?? 'cto';
        $issueNum = (int)($input['issue'] ?? 0);
        $issueTitle = $input['title'] ?? 'Agent Prompt';
        $issueBody = $input['message'];

        try {
            $runtime = new AgentRuntimeService();
            $db = new \App\Services\DatabaseService();
            $secrets = new SecretService();
            $mc = $secrets->getModelConfig();
            $siteContext = "- Users: " . count($db->read('users')) . "\n- Orders: " . count($db->read('orders')) . "\n- Products: " . count($db->read('products'));

            $promptText = $runtime->buildRolePrompt($role, $issueNum, $issueTitle, $issueBody, $siteContext, []);
            $response = !empty($mc['apiKey']) ? $this->callAi($mc, $promptText) : 'AI not configured. Admin → Integrations.';

            $this->jsonResponse([
                'role' => $role,
                'issue' => $issueNum,
                'response' => $response,
            ]);
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    private function callAi(array $mc, string $prompt): string {
        $endpoint = rtrim($mc['endpoint'] ?? 'https://api.openai.com/v1', '/');
        $model = $mc['model'] ?? 'gemma-4-31b-it';
        $key = $mc['apiKey'] ?? '';
        $provider = $mc['provider'] ?? 'openai';

        if ($provider === 'google') {
            $url = $endpoint . '/' . rawurlencode($model) . ':generateContent';
            $payload = json_encode(['contents' => [['parts' => [['text' => $prompt]]]], 'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 2048]]);
            $ch = curl_init($url);
            curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-goog-api-key: ' . $key], CURLOPT_POSTFIELDS => $payload, CURLOPT_TIMEOUT => 60, CURLOPT_CONNECTTIMEOUT => 10]);
            $body = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($status !== 200 || $body === false) return "API error (HTTP {$status})";
            $result = json_decode($body, true);
            return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'No response.';
        }

        $url = $endpoint . '/chat/completions';
        $payload = json_encode(['model' => $model, 'messages' => [['role' => 'system', 'content' => 'You are a bapXaura agent. Respond concisely.'], ['role' => 'user', 'content' => $prompt]], 'max_tokens' => 2048]);
        $ch = curl_init($url);
        $headers = ['Content-Type: application/json'];
        if ($provider === 'anthropic') { $headers[] = 'x-api-key: ' . $key; $headers[] = 'anthropic-version: 2023-06-01'; }
        else { $headers[] = 'Authorization: Bearer ' . $key; }
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_HTTPHEADER => $headers, CURLOPT_POSTFIELDS => $payload, CURLOPT_TIMEOUT => 60, CURLOPT_CONNECTTIMEOUT => 10]);
        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($status !== 200 || $body === false) return "API error (HTTP {$status})";
        $result = json_decode($body, true);
        return $result['choices'][0]['message']['content'] ?? 'No response.';
    }

    private function verifySignature(array $payload, string $signature): bool {
        $secret = getenv('GITHUB_WEBHOOK_SECRET') ?: '';
        if ($secret === '') return true;
        $expected = 'sha256=' . hash_hmac('sha256', json_encode($payload), $secret);
        return hash_equals($expected, $signature);
    }
}
