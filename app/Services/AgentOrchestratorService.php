<?php
namespace App\Services;

final class AgentOrchestratorService {
    private SecretService $secrets;
    private AiService $ai;
    private DatabaseService $db;
    private ?AiChatController $aiChat;
    private array $roleModels;

    private const ROLES_DIR = '.agents/roles';

    public function __construct() {
        $this->secrets = new SecretService();
        $this->ai = new AiService($this->secrets);
        $this->db = new DatabaseService();
        $this->roleModels = $this->loadRoleModels();
    }

    public function handle(string $message, string $role, string $channel, bool $showThinking = true): array {
        $role = isset($this->roleModels[$role]) ? $role : 'cto';
        $roleDef = $this->roleModels[$role];
        $mc = $this->secrets->getModelConfig();
        $actualModel = $mc['model'] ?? 'gemma-4-31b-it';
        $modelName = $roleDef['name'] ?? ucfirst($role);
        $thinking = [];

        $thinking[] = ['step' => 1, 'action' => "Loaded role: {$modelName}", 'detail' => "Channel: {$channel}"];

        $roleFile = $this->loadRoleFile($role);
        $thinking[] = ['step' => 2, 'action' => 'Loaded role definition', 'detail' => basename($roleFile)];

        $toolHooks = $this->loadToolHooks();
        if ($toolHooks) {
            $thinking[] = ['step' => 3, 'action' => 'Loaded tool hooks', 'detail' => count($toolHooks['before_tool_call'] ?? []) . ' before, ' . count($toolHooks['after_tool_call'] ?? []) . ' after'];
        }

        $schedules = $this->loadSchedules();
        if ($schedules) {
            $thinking[] = ['step' => 4, 'action' => 'Loaded schedule hooks', 'detail' => count($schedules) . ' scheduled tasks'];
        }

        $tools = $this->loadToolDefs();
        $thinking[] = ['step' => 5, 'action' => 'Loaded tools', 'detail' => $tools['count'] . ' tools available'];

        $thinking[] = ['step' => 6, 'action' => 'Calling model', 'detail' => "Model: {$modelName} ({$actualModel})"];

        $prompt = $this->buildSystemPrompt($message, $role, $roleDef);
        $result = $this->ai->call(
            [['role' => 'system', 'content' => $prompt]],
            ['max_tokens' => 4096, 'timeout' => 120]
        );

        $thinking[] = ['step' => 7, 'action' => 'Response ready', 'detail' => ''];

        return [
            'reply' => $result,
            'thinking' => $thinking,
            'role' => $role,
            'model' => $modelName,
            'actual_model' => $actualModel,
            'channel' => $channel,
        ];
    }

    private function loadRoleModels(): array {
        $dir = app_path(self::ROLES_DIR);
        if (!is_dir($dir)) return ['cto' => ['name' => 'CTO']];

        $roles = [];
        $files = glob($dir . '/*.md');
        if (!$files) return ['cto' => ['name' => 'CTO']];

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $roleName = basename($file, '.md');
            $meta = ['name' => ucfirst($roleName)];

            if (preg_match('/^---\s*\n(.*?)\n---/s', $content, $m)) {
                foreach (explode("\n", $m[1]) as $line) {
                    if (preg_match('/^(\w+):\s*(.+)$/', $line, $kv)) {
                        $meta[$kv[1]] = trim($kv[2], " \"'");
                    }
                }
            }

            $roles[$roleName] = $meta;
        }

        return $roles;
    }

    private function loadRoleFile(string $role): string {
        $path = app_path(self::ROLES_DIR . '/' . $role . '.md');
        if (!is_file($path)) {
            $path = app_path(self::ROLES_DIR . '/_default.md');
        }
        return is_file($path) ? $path : '';
    }

    private function buildSystemPrompt(string $message, string $role, array $roleDef): string {
        $file = $this->loadRoleFile($role);
        if (!$file) {
            return "# Role: {$roleDef['name']}\nRespond concisely.\n\n{$message}";
        }

        $content = file_get_contents($file);
        $body = $content;

        if (preg_match('/^---.*?---\s*(.*)$/s', $content, $m)) {
            $body = trim($m[1]);
        }

        $tools = $this->loadToolDefs();
        $toolHooks = $this->loadToolHooks();
        $schedules = $this->loadSchedules();
        $siteCtx = $this->buildSiteContext();

        $parts = [
            $body,
            $siteCtx ? "## Site Context\n{$siteCtx}" : '',
            $tools['text'] ? "## Available Tools\n{$tools['text']}" : '',
            $toolHooks ? "## Tool Hooks\n" . json_encode($toolHooks, JSON_PRETTY_PRINT) : '',
            $schedules ? "## Scheduled Tasks\n" . json_encode($schedules, JSON_PRETTY_PRINT) : '',
            "## User Request\n{$message}",
        ];

        return implode("\n\n", array_filter($parts));
    }

    private function loadToolDefs(): array {
        try {
            $chat = new AiChatController();
            $defs = $chat->getToolDefinitions();
            if (empty($defs)) return ['count' => 0, 'text' => ''];

            $text = '';
            foreach ($defs as $def) {
                $fn = $def['function'] ?? [];
                $name = $fn['name'] ?? '';
                $desc = $fn['description'] ?? '';
                $text .= "- `{$name}`: {$desc}\n";
            }

            return ['count' => count($defs), 'text' => trim($text)];
        } catch (\Throwable $e) {
            return ['count' => 0, 'text' => ''];
        }
    }

    private function loadToolHooks(): array {
        $path = app_path('.agents/hooks/tool-hooks.yaml');
        if (!is_file($path)) return [];

        return $this->parseYamlLike(file_get_contents($path));
    }

    private function loadSchedules(): array {
        $path = app_path('.agents/hooks/schedule.yaml');
        if (!is_file($path)) return [];

        $content = file_get_contents($path);
        $result = [];
        $current = null;

        foreach (explode("\n", $content) as $line) {
            if (preg_match('/^  (\w+):/', $line, $m)) {
                if ($current !== null) $result[] = $current;
                $current = ['name' => $m[1]];
            } elseif ($current && preg_match('/^\s{4}(\w+):\s*(.+)$/', $line, $m)) {
                $current[$m[1]] = trim($m[2], " \"'");
            }
        }
        if ($current !== null) $result[] = $current;

        return $result;
    }

    private function buildSiteContext(): string {
        try {
            $userCount = count($this->db->read('users'));
            $orderCount = count($this->db->read('orders'));
            $productCount = count($this->db->read('products'));
            return "- Users: {$userCount}\n- Orders: {$orderCount}\n- Products: {$productCount}";
        } catch (\Throwable $e) {
            return '';
        }
    }

    private function parseYamlLike(string $content): array {
        $result = [];
        $currentSection = null;
        $currentItem = null;
        foreach (explode("\n", $content) as $line) {
            if (preg_match('/^(\w[\w-]*):$/', $line, $m)) {
                $currentSection = $m[1];
                $currentItem = null;
            } elseif (preg_match('/^  - (\w[\w-]*):$/', $line, $m)) {
                $currentItem = &$result[$currentSection][];
                $currentItem = ['name' => $m[1]];
            } elseif ($currentItem !== null && preg_match('/^    (\w[\w-]*):\s*(.*)$/', $line, $m)) {
                $currentItem[$m[1]] = trim($m[2], " \"'");
            } elseif ($currentSection && preg_match('/^  (\w[\w-]*):\s*(.*)$/', $line, $m)) {
                $val = trim($m[2], " \"'");
                if (!isset($result[$currentSection])) $result[$currentSection] = [];
                if ($val !== '') {
                    $decoded = json_decode($val, true);
                    $result[$currentSection][$m[1]][] = $decoded ?? $val;
                }
            }
        }
        return $result;
    }
}
