<?php
namespace App\Controllers;

use App\Services\SecretService;
use App\Services\DatabaseService;

final class AiChatController extends BaseController {
    private array $toolRegistry = [];

    public function __construct() {
        $this->toolRegistry = $this->buildTools();
    }

    public function chat(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['messages']) || !is_array($input['messages'])) {
            $this->jsonResponse(['error' => 'Invalid request. Required: messages array'], 400);
            return;
        }

        $model = $input['model'] ?? 'default';
        $maxTokens = min((int)($input['max_tokens'] ?? 2048), 4096);
        $temperature = (float)($input['temperature'] ?? 0.7);
        $stream = !empty($input['stream']);
        $tools = $input['tools'] ?? $this->toolRegistry['_definitions'] ?? [];

        try {
            $secrets = new SecretService();
            $mc = $secrets->getModelConfig();

            if (empty($mc['apiKey'])) {
                $this->jsonResponse([
                    'error' => 'AI model not configured. Admin → Integrations: set api_endpoint, agent_api_key, agent_model.',
                    'error_code' => 'ai_not_configured',
                ], 503);
                return;
            }

            $result = $this->chatWithTools($mc, $model, $input['messages'], $tools, $maxTokens, $temperature, 0);

            if ($stream) {
                $this->streamResponse($result);
            } else {
                $this->jsonResponse($result, 200);
            }
        } catch (\Throwable $e) {
            $this->jsonResponse(['error' => 'AI chat error: ' . $e->getMessage()], 500);
        }
    }

    private function chatWithTools(array $mc, string $model, array $messages, array $tools, int $maxTokens, float $temperature, int $depth): array {
        if ($depth > 5) {
            return $this->openAiResponse($model, 'Maximum tool call depth reached. Please simplify your request.', $messages);
        }

        $upstreamModel = $mc['model'] ?? 'gemma-4-31b-it';
        if ($model !== 'default' && $model !== '') {
            $upstreamModel = $model;
        }

        $response = $this->callUpstream($mc, $upstreamModel, $messages, $tools, $maxTokens, $temperature);
        $choices = $response['choices'] ?? [];

        if (empty($choices)) {
            return $this->openAiResponse($upstreamModel, 'No response from upstream AI.', $messages);
        }

        $choice = $choices[0];
        $message = $choice['message'] ?? [];

        if (empty($message['tool_calls'])) {
            return $this->formatResponse($upstreamModel, $message, $choice['finish_reason'] ?? 'stop', $response['usage'] ?? []);
        }

        $messages[] = $message;

        foreach ($message['tool_calls'] as $toolCall) {
            $result = $this->executeTool($toolCall);
            $messages[] = [
                'role' => 'tool',
                'tool_call_id' => $toolCall['id'],
                'content' => $result,
            ];
        }

        return $this->chatWithTools($mc, $model, $messages, $tools, $maxTokens, $temperature, $depth + 1);
    }

    private function callUpstream(array $mc, string $model, array $messages, array $tools, int $maxTokens, float $temperature): array {
        $endpoint = rtrim($mc['endpoint'] ?? 'https://api.openai.com/v1', '/');
        $key = $mc['apiKey'] ?? '';

        $url = $endpoint . '/chat/completions';
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => $maxTokens,
            'temperature' => $temperature,
        ];

        if (!empty($tools)) {
            $payload['tools'] = $tools;
            $payload['tool_choice'] = 'auto';
        }

        $ch = curl_init($url);
        $headers = ['Content-Type: application/json', 'Authorization: Bearer ' . $key];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_SLASHES),
            CURLOPT_TIMEOUT => 120,
            CURLOPT_CONNECTTIMEOUT => 15,
        ]);

        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($body === false || $status !== 200) {
            $errorDetail = $body ?: 'No response body';
            return [
                'choices' => [[
                    'index' => 0,
                    'message' => [
                        'role' => 'assistant',
                        'content' => "Upstream API error (HTTP {$status}): {$errorDetail}",
                    ],
                    'finish_reason' => 'stop',
                ]],
                'usage' => ['prompt_tokens' => 0, 'completion_tokens' => 0, 'total_tokens' => 0],
            ];
        }

        return json_decode($body, true) ?: [
            'choices' => [[
                'index' => 0,
                'message' => ['role' => 'assistant', 'content' => 'Failed to parse upstream response.'],
                'finish_reason' => 'stop',
            ]],
            'usage' => ['prompt_tokens' => 0, 'completion_tokens' => 0, 'total_tokens' => 0],
        ];
    }

    private function executeTool(array $toolCall): string {
        $id = $toolCall['id'] ?? 'call_unknown';
        $function = $toolCall['function'] ?? [];
        $name = $function['name'] ?? '';
        $args = json_decode($function['arguments'] ?? '{}', true) ?: [];

        try {
            if (!isset($this->toolRegistry[$name])) {
                return json_encode(['error' => "Unknown tool: {$name}"]);
            }

            $tool = $this->toolRegistry[$name];
            $handler = $tool['handler'] ?? null;

            if ($handler === null) {
                return json_encode(['error' => "No handler for tool: {$name}"]);
            }

            $result = $handler($args);
            $encoded = json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            if ($encoded === false || strlen($encoded) > 50000) {
                return json_encode(['warning' => 'Result too large, truncated', 'preview' => mb_substr((string)($result['output'] ?? json_encode($result)), 0, 50000)]);
            }

            return $encoded;
        } catch (\Throwable $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    private function buildTools(): array {
        $tools = [];

        $tools['bapXaura_map'] = [
            'handler' => fn(array $args): array => $this->execBash('php cli/generate-project-map.php 2>&1', ['format' => 'json']),
        ];
        $tools['bapXaura_schema_list'] = [
            'handler' => fn(array $args): array => $this->execBash('php cli/bapXaura schema list 2>&1'),
        ];
        $tools['bapXaura_ci'] = [
            'handler' => fn(array $args): array => $this->execBash('php cli/bapXaura ci 2>&1', ['timeout' => 120]),
        ];
        $tools['bapXaura_test'] = [
            'handler' => fn(array $args): array => $this->execBash('php tests/run.php 2>&1', ['timeout' => 120]),
        ];
        $tools['bapXaura_handoff_next'] = [
            'handler' => fn(array $args): array => $this->execBash('php cli/bapXaura handoff next ' . ((int)($args['issue'] ?? 0)) . ' 2>&1'),
        ];
        $tools['bapXaura_route_list'] = [
            'handler' => fn(array $args): array => $this->execBash('php cli/bapXaura route:list 2>&1'),
        ];
        $tools['bapXaura_schema_show'] = [
            'handler' => fn(array $args): array => $this->execBash('php cli/bapXaura schema show ' . escapeshellarg($args['collection'] ?? '') . ' 2>&1'),
        ];
        $tools['bapXaura_status'] = [
            'handler' => fn(array $args): array => $this->execBash('php cli/bapXaura status 2>&1'),
        ];
        $tools['bapXaura_db_query'] = [
            'handler' => fn(array $args): array => $this->execBash(
                'php cli/bapXaura db query ' . escapeshellarg($args['collection'] ?? '') .
                (isset($args['limit']) ? ' --limit ' . (int)$args['limit'] : '') .
                ' 2>&1',
                ['timeout' => 30]
            ),
        ];
        $tools['search_code'] = [
            'handler' => function (array $args): array {
                $pattern = $args['pattern'] ?? '';
                $include = $args['include'] ?? '';
                if ($pattern === '') return ['error' => 'Pattern required'];
                $cmd = 'rg -n ' . escapeshellarg($pattern);
                if ($include !== '') $cmd .= ' -g ' . escapeshellarg($include);
                $cmd .= ' 2>&1 | head -200';
                return $this->execBash($cmd);
            },
        ];
        $tools['read_file'] = [
            'handler' => function (array $args): array {
                $path = $args['path'] ?? '';
                if ($path === '') return ['error' => 'Path required'];
                $limit = isset($args['limit']) ? (int)$args['limit'] : 0;
                $fullPath = realpath(app_path($path)) ?: realpath($path) ?: null;
                if (!$fullPath || !is_file($fullPath)) {
                    $fullPath = realpath(app_path($path)) ?: null;
                }
                if (!$fullPath || !is_file($fullPath)) return ['error' => "File not found: {$path}"];
                $content = file_get_contents($fullPath);
                if ($content === false) return ['error' => "Cannot read: {$path}"];
                $lines = explode("\n", $content);
                if ($limit > 0 && count($lines) > $limit) {
                    $lines = array_slice($lines, 0, $limit);
                    $lines[] = '-- truncated (' . count($lines) . '/' . count(explode("\n", $content)) . ' lines) --';
                }
                return ['path' => $path, 'lines' => count($lines), 'content' => implode("\n", $lines)];
            },
        ];
        $tools['list_dir'] = [
            'handler' => function (array $args): array {
                $path = $args['path'] ?? '';
                $fullPath = realpath(app_path($path)) ?: null;
                if (!$fullPath || !is_dir($fullPath)) return ['error' => "Directory not found: {$path}"];
                $items = array_diff(scandir($fullPath), ['.', '..']);
                $result = [];
                foreach ($items as $item) {
                    $itemPath = $fullPath . '/' . $item;
                    $result[] = [
                        'name' => $item,
                        'type' => is_dir($itemPath) ? 'dir' : (is_file($itemPath) ? 'file' : 'other'),
                        'size' => is_file($itemPath) ? filesize($itemPath) : 0,
                    ];
                }
                return ['path' => $path, 'items' => $result];
            },
        ];

        $toolDefs = [];
        foreach ($tools as $name => $config) {
            $toolDefs[] = [
                'type' => 'function',
                'function' => [
                    'name' => $name,
                    'description' => $config['description'] ?? $this->toolDescription($name),
                    'parameters' => $config['parameters'] ?? $this->toolParameters($name),
                ],
            ];
        }

        return $tools + ['_definitions' => $toolDefs];
    }

    public function tools(): void {
        $registry = $this->buildTools();
        $this->jsonResponse($registry['_definitions'] ?? []);
    }

    public function getToolDefinitions(): array {
        return $this->buildTools()['_definitions'] ?? [];
    }

    public function executeToolCall(string $name, array $arguments): string {
        $toolCall = [
            'id' => 'call-' . bin2hex(random_bytes(4)),
            'function' => ['name' => $name, 'arguments' => json_encode($arguments)],
        ];
        return $this->executeTool($toolCall);
    }

    private function toolDescription(string $name): string {
        $descriptions = [
            'bapXaura_map' => 'Generate the project map (routes, controllers, services, views, schema)',
            'bapXaura_schema_list' => 'List all MySQL schema collections with field counts',
            'bapXaura_schema_show' => 'Show full schema details for a specific collection',
            'bapXaura_ci' => 'Run non-mutating CI: lint, test, generate maps, smoke test',
            'bapXaura_test' => 'Run the project test suite',
            'bapXaura_handoff_next' => 'Get the next handoff event for a GitHub issue',
            'bapXaura_route_list' => 'List all registered web routes',
            'bapXaura_status' => 'Show repository status and recent commits',
            'bapXaura_db_query' => 'Query records from a MySQL collection',
            'search_code' => 'Search the codebase using regular expressions',
            'read_file' => 'Read contents of a file in the project',
            'list_dir' => 'List files and directories at a path',
        ];
        return $descriptions[$name] ?? $name;
    }

    private function toolParameters(string $name): array {
        $params = [
            'bapXaura_map' => ['type' => 'object', 'properties' => new \stdClass, 'required' => []],
            'bapXaura_schema_list' => ['type' => 'object', 'properties' => new \stdClass, 'required' => []],
            'bapXaura_schema_show' => [
                'type' => 'object',
                'properties' => ['collection' => ['type' => 'string', 'description' => 'Collection name from collections.php']],
                'required' => ['collection'],
            ],
            'bapXaura_ci' => ['type' => 'object', 'properties' => new \stdClass, 'required' => []],
            'bapXaura_test' => ['type' => 'object', 'properties' => new \stdClass, 'required' => []],
            'bapXaura_handoff_next' => [
                'type' => 'object',
                'properties' => ['issue' => ['type' => 'integer', 'description' => 'GitHub issue number']],
                'required' => ['issue'],
            ],
            'bapXaura_route_list' => ['type' => 'object', 'properties' => new \stdClass, 'required' => []],
            'bapXaura_status' => ['type' => 'object', 'properties' => new \stdClass, 'required' => []],
            'bapXaura_db_query' => [
                'type' => 'object',
                'properties' => [
                    'collection' => ['type' => 'string', 'description' => 'Collection name'],
                    'limit' => ['type' => 'integer', 'description' => 'Max records to return'],
                ],
                'required' => ['collection'],
            ],
            'search_code' => [
                'type' => 'object',
                'properties' => [
                    'pattern' => ['type' => 'string', 'description' => 'Regex pattern to search'],
                    'include' => ['type' => 'string', 'description' => 'File pattern filter e.g. *.php'],
                ],
                'required' => ['pattern'],
            ],
            'read_file' => [
                'type' => 'object',
                'properties' => [
                    'path' => ['type' => 'string', 'description' => 'File path relative to project root'],
                    'limit' => ['type' => 'integer', 'description' => 'Max lines to read'],
                ],
                'required' => ['path'],
            ],
            'list_dir' => [
                'type' => 'object',
                'properties' => ['path' => ['type' => 'string', 'description' => 'Directory path']],
                'required' => ['path'],
            ],
        ];
        return $params[$name] ?? ['type' => 'object', 'properties' => new \stdClass, 'required' => []];
    }

    private function execBash(string $cmd, array $opts = []): array {
        $timeout = $opts['timeout'] ?? 30;
        $descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $process = proc_open($cmd, $descriptors, $pipes, app_path());

        if (!is_resource($process)) {
            return ['error' => 'Failed to start process', 'command' => $cmd];
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $startTime = time();
        while (true) {
            $status = proc_get_status($process);
            if (!$status['running']) break;
            if (time() - $startTime > $timeout) {
                proc_terminate($process, 9);
                return ['error' => 'Command timed out after ' . $timeout . 's', 'output' => $stdout ?? ''];
            }
            usleep(100000);
        }
        $exitCode = $status['exitcode'] ?? -1;
        proc_close($process);

        return [
            'exit_code' => $exitCode,
            'output' => $stdout,
            'stderr' => $stderr,
            'command' => $cmd,
        ];
    }

    private function openAiResponse(string $model, string $content, array $messages): array {
        return $this->formatResponse($model, ['role' => 'assistant', 'content' => $content], 'stop', []);
    }

    private function formatResponse(string $model, array $message, string $finishReason, array $usage): array {
        $id = 'chatcmpl-' . bin2hex(random_bytes(12));
        $promptTokens = $usage['prompt_tokens'] ?? 0;
        $completionTokens = $usage['completion_tokens'] ?? 0;

        return [
            'id' => $id,
            'object' => 'chat.completion',
            'created' => time(),
            'model' => $model,
            'choices' => [[
                'index' => 0,
                'message' => $message,
                'finish_reason' => $finishReason,
            ]],
            'usage' => [
                'prompt_tokens' => $promptTokens,
                'completion_tokens' => $completionTokens,
                'total_tokens' => $promptTokens + $completionTokens,
            ],
        ];
    }

    private function streamResponse(array $result): void {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        $id = $result['id'] ?? 'chatcmpl-' . bin2hex(random_bytes(12));
        $model = $result['model'] ?? 'default';
        $created = $result['created'] ?? time();

        $firstChunk = [
            'id' => $id,
            'object' => 'chat.completion.chunk',
            'created' => $created,
            'model' => $model,
            'choices' => [[
                'index' => 0,
                'delta' => ['role' => 'assistant', 'content' => ''],
                'finish_reason' => null,
            ]],
        ];
        echo 'data: ' . json_encode($firstChunk) . "\n\n";
        ob_flush();
        flush();

        $content = $result['choices'][0]['message']['content'] ?? '';
        $words = mb_str_split($content, 1);
        foreach ($words as $char) {
            $chunk = [
                'id' => $id,
                'object' => 'chat.completion.chunk',
                'created' => $created,
                'model' => $model,
                'choices' => [[
                    'index' => 0,
                    'delta' => ['content' => $char],
                    'finish_reason' => null,
                ]],
            ];
            echo 'data: ' . json_encode($chunk) . "\n\n";
            ob_flush();
            flush();
            usleep(1000);
        }

        $finalChunk = [
            'id' => $id,
            'object' => 'chat.completion.chunk',
            'created' => $created,
            'model' => $model,
            'choices' => [[
                'index' => 0,
                'delta' => [],
                'finish_reason' => 'stop',
            ]],
        ];
        echo 'data: ' . json_encode($finalChunk) . "\n\n";
        echo "data: [DONE]\n\n";
        ob_flush();
        flush();
    }
}
