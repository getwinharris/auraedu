<?php
namespace App\Controllers;

use App\Integrations\GitHub\GitHubChannel;
use App\Services\{AgentRuntimeService, AiService, SecretService};

final class CloudAgentController extends BaseController {
    public function webhook(): void {
        $payload = json_decode(file_get_contents('php://input'), true);
        $event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
        $signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

        if (empty($payload) || $event === '') {
            $this->jsonResponse(['error' => 'Missing payload or event header'], 400);
            return;
        }

        $secrets = (new SecretService())->all();
        $channel = new GitHubChannel(
            $secrets['github_webhook_secret'] ?? '',
            $secrets['github_token'] ?? '',
            $secrets['github_owner'] ?: 'bapXai',
            $secrets['github_repo'] ?? ''
        );

        if ($signature !== '' && !$channel->verifyWebhook($payload, $signature)) {
            $this->jsonResponse(['error' => 'Invalid signature'], 403);
            return;
        }

        try {
            $result = $channel->dispatch($payload, $event);
            $this->jsonResponse($result);
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => 'Agent runtime error: ' . $e->getMessage()], 500);
        }
    }

    public function status(): void {
        $owner = getenv('OWNER_GITHUB') ?: 'bapXai';
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
            $siteContext = "- Users: " . count($db->read('users')) . "\n- Orders: " . count($db->read('orders')) . "\n- Products: " . count($db->read('products'));

            $promptText = $runtime->buildRolePrompt($role, $issueNum, $issueTitle, $issueBody, $siteContext, []);
            $response = (new AiService())->call([
                ['role' => 'system', 'content' => 'You are a bapXaura agent. Respond concisely.'],
                ['role' => 'user', 'content' => $promptText],
            ], ['max_tokens' => 2048, 'timeout' => 60]);

            $this->jsonResponse([
                'role' => $role,
                'issue' => $issueNum,
                'response' => $response,
            ]);
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
