<?php
namespace App\Controllers;

final class McpController extends BaseController {
    private array $toolRegistry = [];
    private array $sessionStore = [];

    public function __construct() {
        $ai = new AiChatController();
        $this->toolRegistry = $ai->getToolDefinitions();
    }

    public function handle(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['jsonrpc']) || $input['jsonrpc'] !== '2.0') {
            $this->jsonRpcError(null, -32600, 'Invalid JSON-RPC request');
            return;
        }

        $id = $input['id'] ?? null;
        $method = $input['method'] ?? '';
        $params = $input['params'] ?? [];

        match ($method) {
            'initialize' => $this->handleInitialize($id),
            'ping' => $this->jsonRpcResult($id, []),
            'tools/list' => $this->handleListTools($id, $params),
            'tools/call' => $this->handleCallTool($id, $params),
            'resources/list' => $this->handleListResources($id),
            'resources/templates/list' => $this->handleListResourceTemplates($id),
            'resources/read' => $this->handleReadResource($id, $params),
            'prompts/list' => $this->handleListPrompts($id),
            'prompts/get' => $this->handleGetPrompt($id, $params),
            default => $this->jsonRpcError($id, -32601, "Method not found: {$method}"),
        };
    }

    private function handleInitialize(mixed $id): void {
        $result = [
            'protocolVersion' => '2025-11-25',
            'capabilities' => [
                'tools' => ['listChanged' => false],
                'resources' => ['listChanged' => false],
                'prompts' => ['listChanged' => false],
            ],
            'serverInfo' => [
                'name' => 'auraedu-mcp',
                'version' => '1.0.0',
                'description' => 'AuraEdu MCP server — project tools, resources, and agent prompts',
            ],
        ];
        $this->jsonRpcResult($id, $result);
    }

    private function handleListTools(mixed $id, array $params): void {
        $tools = [];
        foreach ($this->toolRegistry as $def) {
            $fn = $def['function'] ?? [];
            $tools[] = [
                'name' => $fn['name'] ?? '',
                'description' => $fn['description'] ?? '',
                'inputSchema' => $fn['parameters'] ?? ['type' => 'object', 'additionalProperties' => false],
            ];
        }
        $this->jsonRpcResult($id, ['tools' => $tools]);
    }

    private function handleCallTool(mixed $id, array $params): void {
        $name = $params['name'] ?? '';
        $arguments = $params['arguments'] ?? [];

        try {
            $result = (new AiChatController())->executeToolCall($name, $arguments);
            $this->jsonRpcResult($id, [
                'content' => [['type' => 'text', 'text' => $result]],
                'isError' => false,
            ]);
        } catch (\Throwable $e) {
            $this->jsonRpcResult($id, [
                'content' => [['type' => 'text', 'text' => $e->getMessage()]],
                'isError' => true,
            ]);
        }
    }

    private function handleListResources(mixed $id): void {
        $this->jsonRpcResult($id, [
            'resources' => [
                [
                    'uri' => 'schema://collections',
                    'name' => 'Schema Collections',
                    'description' => 'List of all MySQL schema collections',
                    'mimeType' => 'application/json',
                ],
                [
                    'uri' => 'schema://handoffs',
                    'name' => 'Active Handoffs',
                    'description' => 'Current agent handoff state',
                    'mimeType' => 'application/json',
                ],
                [
                    'uri' => 'file://AGENTS.md',
                    'name' => 'Agent Operating Guide',
                    'description' => 'Binding agent contract',
                    'mimeType' => 'text/markdown',
                ],
                [
                    'uri' => 'file://docs/systematic-map.mmd',
                    'name' => 'Systematic Map',
                    'description' => 'Route/controller/service wiring diagram',
                    'mimeType' => 'text/vnd.mermaid',
                ],
            ],
        ]);
    }

    private function handleListResourceTemplates(mixed $id): void {
        $this->jsonRpcResult($id, [
            'resourceTemplates' => [
                [
                    'uriTemplate' => 'schema://{collection}',
                    'name' => 'Schema Detail',
                    'description' => 'Full schema for a specific collection',
                    'mimeType' => 'application/json',
                ],
                [
                    'uriTemplate' => 'file://{path}',
                    'name' => 'Project File',
                    'description' => 'Read a file from the project',
                    'mimeType' => 'application/octet-stream',
                ],
            ],
        ]);
    }

    private function handleReadResource(mixed $id, array $params): void {
        $uri = $params['uri'] ?? '';

        try {
            if ($uri === 'schema://collections') {
                $schemaFile = storage_path('schema/collections.php');
                $collections = is_file($schemaFile) ? require $schemaFile : [];
                $names = array_keys($collections);
                $this->jsonRpcResult($id, [
                    'contents' => [[
                        'uri' => $uri,
                        'mimeType' => 'application/json',
                        'text' => json_encode(['collections' => $names], JSON_PRETTY_PRINT),
                    ]],
                ]);
            } elseif ($uri === 'schema://handoffs') {
                $active = storage_path('../.agents/handoffs/active/current.json');
                $text = is_file($active) ? file_get_contents($active) : '{"handoff": "none"}';
                $this->jsonRpcResult($id, [
                    'contents' => [[
                        'uri' => $uri,
                        'mimeType' => 'application/json',
                        'text' => $text,
                    ]],
                ]);
            } elseif (str_starts_with($uri, 'schema://')) {
                $collection = substr($uri, 9);
                $schemaFile = storage_path('schema/collections.php');
                $collections = is_file($schemaFile) ? require $schemaFile : [];
                $detail = $collections[$collection] ?? ['error' => "Collection not found: {$collection}"];
                $this->jsonRpcResult($id, [
                    'contents' => [[
                        'uri' => $uri,
                        'mimeType' => 'application/json',
                        'text' => json_encode($detail, JSON_PRETTY_PRINT),
                    ]],
                ]);
            } elseif (str_starts_with($uri, 'file://')) {
                $relPath = substr($uri, 7);
                $fullPath = realpath(app_path($relPath));
                if (!$fullPath || !is_file($fullPath) || !str_starts_with($fullPath, realpath(app_path()))) {
                    $this->jsonRpcError($id, -32002, "Resource not found: {$uri}");
                    return;
                }
                $content = file_get_contents($fullPath);
                $ext = pathinfo($relPath, PATHINFO_EXTENSION);
                $mime = match ($ext) {
                    'php' => 'text/x-php',
                    'md' => 'text/markdown',
                    'json' => 'application/json',
                    'yml', 'yaml' => 'text/yaml',
                    'css' => 'text/css',
                    'js' => 'application/javascript',
                    'mmd' => 'text/vnd.mermaid',
                    default => 'text/plain',
                };
                $this->jsonRpcResult($id, [
                    'contents' => [[
                        'uri' => $uri,
                        'mimeType' => $mime,
                        'text' => $content,
                    ]],
                ]);
            } else {
                $this->jsonRpcError($id, -32002, "Resource not found: {$uri}");
            }
        } catch (\Throwable $e) {
            $this->jsonRpcError($id, -32603, $e->getMessage());
        }
    }

    private function handleListPrompts(mixed $id): void {
        $this->jsonRpcResult($id, [
            'prompts' => [
                [
                    'name' => 'analyze_issue',
                    'description' => 'Analyze a GitHub issue and produce a bounded objective for the CTO',
                    'arguments' => [
                        ['name' => 'issue_number', 'description' => 'GitHub issue number', 'required' => true],
                        ['name' => 'issue_body', 'description' => 'Issue description text', 'required' => true],
                    ],
                ],
                [
                    'name' => 'create_objective',
                    'description' => 'Create a bounded, actionable objective from an analyzed issue',
                    'arguments' => [
                        ['name' => 'analysis', 'description' => 'Issue analysis output', 'required' => true],
                    ],
                ],
                [
                    'name' => 'review_changes',
                    'description' => 'Review code changes against an objective and schema',
                    'arguments' => [
                        ['name' => 'diff', 'description' => 'Git diff text', 'required' => true],
                        ['name' => 'objective_id', 'description' => 'Objective identifier', 'required' => true],
                    ],
                ],
                [
                    'name' => 'diagnose_bug',
                    'description' => 'Trace a bug report to affected files and root cause',
                    'arguments' => [
                        ['name' => 'description', 'description' => 'Bug description', 'required' => true],
                        ['name' => 'steps', 'description' => 'Steps to reproduce', 'required' => false],
                    ],
                ],
            ],
        ]);
    }

    private function handleGetPrompt(mixed $id, array $params): void {
        $name = $params['name'] ?? '';
        $args = $params['arguments'] ?? [];

        $prompts = [
            'analyze_issue' => [
                'description' => 'Analyze a GitHub issue',
                'messages' => [[
                    'role' => 'user',
                    'content' => [
                        'type' => 'text',
                        'text' => sprintf(
                            "You are the CTO agent. Analyze this issue and produce a bounded objective.\n\nIssue #%s:\n%s\n\nOutput JSON: {objective_id, title, scope, files_likely_affected, acceptance_criteria, risks}",
                            $args['issue_number'] ?? '?',
                            $args['issue_body'] ?? ''
                        ),
                    ],
                ]],
            ],
            'create_objective' => [
                'description' => 'Create actionable objective',
                'messages' => [[
                    'role' => 'user',
                    'content' => [
                        'type' => 'text',
                        'text' => sprintf(
                            "Refine this analysis into a bounded objective:\n%s\n\nOutput JSON: {objective_id, title, bounded_scope, files, acceptance, estimated_effort}",
                            $args['analysis'] ?? ''
                        ),
                    ],
                ]],
            ],
            'review_changes' => [
                'description' => 'Review code changes',
                'messages' => [[
                    'role' => 'user',
                    'content' => [
                        'type' => 'text',
                        'text' => sprintf(
                            "Review these changes against objective %s.\n\nDiff:\n```diff\n%s\n```\n\nCheck: scope adherence, schema changes, test coverage, side effects.",
                            $args['objective_id'] ?? '?',
                            $args['diff'] ?? ''
                        ),
                    ],
                ]],
            ],
            'diagnose_bug' => [
                'description' => 'Diagnose a bug',
                'messages' => [[
                    'role' => 'user',
                    'content' => [
                        'type' => 'text',
                        'text' => sprintf(
                            "Trace this bug to affected files and root cause.\n\nDescription: %s\nSteps: %s\n\nOutput: {affected_files: [], root_cause, fix_recommendation}",
                            $args['description'] ?? '',
                            $args['steps'] ?? 'not provided'
                        ),
                    ],
                ]],
            ],
        ];

        if (!isset($prompts[$name])) {
            $this->jsonRpcError($id, -32602, "Unknown prompt: {$name}");
            return;
        }

        $this->jsonRpcResult($id, $prompts[$name]);
    }

    private function jsonRpcResult(mixed $id, array $result): void {
        $this->jsonResponse(['jsonrpc' => '2.0', 'id' => $id, 'result' => $result]);
    }

    private function jsonRpcError(mixed $id, int $code, string $message): void {
        $status = match ($code) {
            -32002 => 404,
            -32601, -32602 => 400,
            default => 500,
        };
        $this->jsonResponse(['jsonrpc' => '2.0', 'id' => $id, 'error' => ['code' => $code, 'message' => $message]], $status);
    }

    public function models(): void {
        $data = [
            'object' => 'list',
            'data' => [
                ['id' => 'gemma-4-31b-it', 'object' => 'model', 'created' => time(), 'owned_by' => 'google'],
                ['id' => 'gemini-3.5-flash', 'object' => 'model', 'created' => time(), 'owned_by' => 'google'],
                ['id' => 'gemini-3.1-pro', 'object' => 'model', 'created' => time(), 'owned_by' => 'google'],
                ['id' => 'gemini-3-deepthink', 'object' => 'model', 'created' => time(), 'owned_by' => 'google'],
                ['id' => 'gemini-3-flash', 'object' => 'model', 'created' => time(), 'owned_by' => 'google'],
                ['id' => 'gemini-3-pro', 'object' => 'model', 'created' => time(), 'owned_by' => 'google'],
                ['id' => 'gemini-2.5-flash', 'object' => 'model', 'created' => time(), 'owned_by' => 'google'],
                ['id' => 'gemini-2.5-pro', 'object' => 'model', 'created' => time(), 'owned_by' => 'google'],
                ['id' => 'gemini-2.0-flash', 'object' => 'model', 'created' => time(), 'owned_by' => 'google'],
                ['id' => 'gpt-4o', 'object' => 'model', 'created' => time(), 'owned_by' => 'openai'],
                ['id' => 'gpt-4o-mini', 'object' => 'model', 'created' => time(), 'owned_by' => 'openai'],
                ['id' => 'claude-sonnet-4-5', 'object' => 'model', 'created' => time(), 'owned_by' => 'anthropic'],
                ['id' => 'claude-opus-4-5', 'object' => 'model', 'created' => time(), 'owned_by' => 'anthropic'],
            ],
        ];
        $this->jsonResponse($data);
    }

    public function listTools(): void {
        $this->handleListTools(1, []);
    }

    public function callTool(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $params = $input['params'] ?? $input;
        $name = $params['name'] ?? '';
        $arguments = $params['arguments'] ?? [];
        $this->handleCallTool(1, ['name' => $name, 'arguments' => $arguments]);
    }
}
