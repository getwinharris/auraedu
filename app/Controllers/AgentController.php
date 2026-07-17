<?php
namespace App\Controllers;

use App\Services\{AiService, SecretService, DatabaseService, AgentOrchestratorService};

class AgentController extends BaseController {
    private function loadAgentConfig(): array {
        $config = ['name' => 'Agent', 'description' => 'AI assistant for the site'];
        $path = app_path('config/agent.yml');
        if (is_file($path)) {
            $yaml = @file_get_contents($path);
            if ($yaml !== false && $yaml !== '') {
                foreach (explode("\n", $yaml) as $line) {
                    if (preg_match('/^\s*(\w+):\s*(.+)$/', $line, $m)) {
                        $config[$m[1]] = trim($m[2], " \"'");
                    }
                }
            }
        }
        return $config;
    }

    public function chat(): void {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $message = trim((string)($input['message'] ?? ''));
        $role = trim((string)($input['role'] ?? 'cto'));
        $channel = trim((string)($input['channel'] ?? 'api'));
        $showThinking = !empty($input['show_thinking'] ?? true);

        if ($message === '') {
            $this->jsonResponse(['error' => 'Message is required'], 400);
            return;
        }

        try {
            $orch = new AgentOrchestratorService();
            $result = $orch->handle($message, $role, $channel, $showThinking);
            $this->jsonResponse($result);
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => 'Agent error: ' . $e->getMessage()], 500);
        }
    }

    public function ask(): void {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $message = trim((string)($input['message'] ?? ''));
        $source = trim((string)($input['source'] ?? 'cli'));
        if ($message === '') {$this->jsonResponse(['error' => 'Message is required'], 400); return;}
        $agentConfig = $this->loadAgentConfig();
        try {
            $secrets = new SecretService();
            $db = new DatabaseService();
            $mc = $secrets->getModelConfig();
            $all = $secrets->all();
            $agentName = trim((string)($all['agent_name'] ?? $agentConfig['name']));
            $siteName = trim((string)($all['seo_site_name'] ?? 'the site'));
            $userCount = count($db->read('users'));
            $orderCount = count($db->read('orders'));
            $productCount = count($db->read('products'));
            $appointmentCount = count($db->read('appointments'));
            $ticketCount = count($db->read('support_tickets'));
            $revenue = array_sum(array_column($db->read('orders'), 'total'));
            $context = "{$siteName} site data:\n- Users: {$userCount}\n- Orders: {$orderCount}\n- Products: {$productCount}\n- Appointments: {$appointmentCount}\n- Support tickets: {$ticketCount}\n- Revenue: ₹" . number_format($revenue, 2);
            $answer = (new AiService())->call([
                ['role' => 'system', 'content' => "You are {$agentName}, the AI assistant for {$siteName}. Answer concisely.\n\n{$context}"],
                ['role' => 'user', 'content' => $message],
            ], ['max_tokens' => 1024, 'timeout' => 60]);
            $this->jsonResponse(['answer' => $answer, 'model' => $mc['model'] ?? 'unknown', 'source' => $source, 'agent_name' => $agentName]);
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => $agentName . ' error: ' . $e->getMessage()], 500);
        }
    }
}
