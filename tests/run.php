<?php
require __DIR__ . '/../app/bootstrap.php';

use App\Services\PaymentService;
use App\Services\ProjectMapService;
use App\Services\SchemaService;

function assertTrue(bool $condition, string $message): void {
    if (!$condition) throw new RuntimeException($message);
}

function assertSame(mixed $expected, mixed $actual, string $message): void {
    if ($expected !== $actual) throw new RuntimeException($message . "\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true));
}

$tests = [];

$tests['php source files have valid syntax'] = function (): void {
    $root = app_path();
    foreach (['app', 'cli', 'tests', 'index.php'] as $relative) {
        $path = app_path($relative);
        $files = is_file($path)
            ? [new SplFileInfo($path)]
            : iterator_to_array(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)));
        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') continue;
            $output = []; $status = 0;
            exec('php -l ' . escapeshellarg($file->getPathname()) . ' 2>&1', $output, $status);
            assertSame(0, $status, 'PHP syntax valid for ' . str_replace($root . '/', '', $file->getPathname()) . ': ' . implode("\n", $output));
        }
    }
};

$tests['routes point to callable controller actions'] = function (): void {
    foreach (require app_path('app/routes.php') as $route) {
        [$class, $action] = explode('@', $route['controller']);
        $fqcn = 'App\\Controllers\\' . $class;
        assertTrue(class_exists($fqcn), "Controller {$fqcn} should exist for {$route['path']}");
        assertTrue(method_exists($fqcn, $action), "Action {$route['controller']} should exist for {$route['path']}");
    }
};

$tests['project map registry has no missing route mappings'] = function (): void {
    $map = ProjectMapService::scan();
    $validation = ProjectMapService::validate($map);
    assertSame([], $validation['missing_route_mappings'], 'Routes should map to controllers');
    assertSame([], $validation['missing_services'], 'Routes should reference declared services');
    assertSame([], $validation['missing_collections'], 'Collections should be declared');
};

$tests['project map lists schema collections'] = function (): void {
    $scan = ProjectMapService::scan();
    assertTrue(in_array('secrets', $scan['schema_collections'], true), 'secrets should be a registered schema collection');
    assertTrue(in_array('products', $scan['schema_collections'], true), 'products should be a registered schema collection');
    assertTrue(in_array('orders', $scan['schema_collections'], true), 'orders should be a registered schema collection');
    assertTrue(str_contains(ProjectMapService::renderSystematicMermaid(), 'secrets'), 'Mermaid should include secrets schema');
};

$tests['project map validates shared navigation in registered routes'] = function (): void {
    $scan = ProjectMapService::scan();
    $mustBeEmpty = ['missing_route_mappings','missing_controller_files','missing_service_files','missing_view_files','navigation_without_get_route','unwired_controllers','unwired_views'];
    foreach ($mustBeEmpty as $kind) {
        if (!array_key_exists($kind, $scan['gaps'])) continue;
        assertSame([], $scan['gaps'][$kind], "Map should not report unresolved {$kind} gaps");
    }
    assertSame([], $scan['gaps']['navigation_without_get_route'], 'Shared navigation paths should resolve to GET routes');
};

$tests['admin requires authentication guard'] = function (): void {
    $admin = file_get_contents(app_path('app/Controllers/AdminController.php'));
    assertTrue(str_contains($admin, 'requireAdmin'), 'Admin controller should require admin auth');
    $auth = file_get_contents(app_path('app/Services/AuthService.php'));
    assertTrue(str_contains($auth, 'function requireAdmin'), 'AuthService should expose requireAdmin');
    assertTrue(str_contains($auth, 'no-store'), 'Admin pages should send no-store headers');
};

$tests['payment signature verification matches razorpay format'] = function (): void {
    $service = new PaymentService('secret');
    $sig = hash_hmac('sha256', 'order_1|pay_1', 'secret');
    assertTrue($service->verifySignature('order_1', 'pay_1', $sig), 'Valid signature should pass');
    assertTrue(!$service->verifySignature('order_1', 'pay_1', 'bad'), 'Invalid signature should fail');
};

$tests['schema collections are defined'] = function (): void {
    $schema = require app_path('storage/schema/collections.php');
    $required = ['products','categories','coupons','orders','appointments','support_tickets','media_files','audit_events','mail_queue','reviews','settings','contact_submissions'];
    foreach ($required as $c) {
        assertTrue(isset($schema['collections'][$c]), "Schema should define {$c}");
    }
    assertTrue(in_array('image_urls', $schema['collections']['products']['media_fields'] ?? [], true), 'Products should define image_urls media field');
    assertTrue((new SchemaService())->adminFields('products') !== [], 'SchemaService should expose admin fields');
};

$tests['admin sidebar split into two sections'] = function (): void {
    $layout = file_get_contents(app_path('views/layouts/admin.php'));
    assertTrue(str_contains($layout, 'PRODUCT &amp; BUILD'), 'Sidebar should have PRODUCT & BUILD section');
    assertTrue(str_contains($layout, 'AGENTS &amp; WORKSPACE'), 'Sidebar should have AGENTS & WORKSPACE section');
    assertTrue(str_contains($layout, 'admin-nav__divider'), 'Sidebar should have divider between sections');
    assertTrue(str_contains($layout, 'admin-nav__section'), 'Sidebar should use section labels');
};

$tests['admin sidebar links all admin routes'] = function (): void {
    $layout = file_get_contents(app_path('views/layouts/admin.php'));
    foreach ([
        '/admin', '/admin/products', '/admin/categories', '/admin/coupons',
        '/admin/appointments', '/admin/temples', '/admin/orders',
        '/admin/contact-submissions', '/admin/support-tickets', '/admin/media',
        '/admin/environment', '/admin/settings', '/admin/integrations',
        '/admin/shipping', '/admin/backups', '/admin/audit-log',
        '/admin/developer/project-map', '/admin/workspace', '/admin/agent',
        '/admin/terminal',
    ] as $path) {
        assertTrue(str_contains($layout, 'href="' . $path . '"'), "Sidebar should link {$path}");
    }
};

$tests['admin sidebar product section has top items'] = function (): void {
    $layout = file_get_contents(app_path('views/layouts/admin.php'));
    $productSection = substr($layout, 0, strpos($layout, 'AGENTS &amp; WORKSPACE'));
    $topRoutes = ['/admin', '/admin/products', '/admin/orders', '/admin/temples', '/admin/media', '/admin/settings'];
    foreach ($topRoutes as $path) {
        assertTrue(str_contains($productSection, 'href="' . $path . '"'), "Product section should include {$path}");
    }
};

$tests['admin sidebar workspace section has agents items'] = function (): void {
    $layout = file_get_contents(app_path('views/layouts/admin.php'));
    $agentSection = substr($layout, strpos($layout, 'AGENTS &amp; WORKSPACE'));
    foreach (['/admin/workspace', '/admin/agent', '/admin/terminal', '/admin/developer/project-map', '/admin/developer/workflow'] as $path) {
        assertTrue(str_contains($agentSection, 'href="' . $path . '"'), "Agents section should include {$path}");
    }
    assertTrue(str_contains($agentSection, 'View Site'), 'Agents section should include View Site');
    assertTrue(str_contains($agentSection, 'Logout'), 'Agents section should include Logout');
};

$tests['workspace tab views exist'] = function (): void {
    $dir = app_path('views/admin/workspace/');
    assertTrue(is_file($dir . '_nav.php'), 'Workspace tab nav should exist');
    assertTrue(is_file($dir . 'intake.php'), 'Intake tab should exist');
    assertTrue(is_file($dir . 'plan.php'), 'Plan tab should exist');
    assertTrue(is_file($dir . 'build.php'), 'Build tab should exist');
    assertTrue(is_file($dir . 'monitor.php'), 'Monitor tab should exist');
};

$tests['workspace view renders tab navigation'] = function (): void {
    $nav = file_get_contents(app_path('views/admin/workspace/_nav.php'));
    foreach (['?tab=intake', '?tab=plan', '?tab=build', '?tab=monitor'] as $tab) {
        assertTrue(str_contains($nav, $tab), "Tab nav should include {$tab}");
    }
    assertTrue(str_contains($nav, 'workspace-tab-count'), 'Tab nav should have count badges');
};

$tests['workspace build view has cycle progress and MCP tools'] = function (): void {
    $view = file_get_contents(app_path('views/admin/workspace/build.php'));
    assertTrue(str_contains($view, 'Cycle Completion'), 'Build should show cycle completion');
    assertTrue(str_contains($view, 'MCP'), 'Build should reference MCP');
    assertTrue(str_contains($view, 'bapXaura_map'), 'Build should list MCP tools');
    assertTrue(str_contains($view, 'schema_list'), 'Build should list schema_list tool');
    assertTrue(str_contains($view, 'Active Agents'), 'Build should have active agents section');
    assertTrue(str_contains($view, 'Open Objectives'), 'Build should have open objectives section');
};

$tests['workspace intake view has triage and quick create'] = function (): void {
    $view = file_get_contents(app_path('views/admin/workspace/intake.php'));
    assertTrue(str_contains($view, 'Triage Inbox'), 'Intake should have triage inbox');
    assertTrue(str_contains($view, 'Agent Activity'), 'Intake should have agent activity');
    assertTrue(str_contains($view, 'Quick Create'), 'Intake should have quick create form');
    assertTrue(str_contains($view, '/admin/workspace/create'), 'Quick create should post to create endpoint');
};

$tests['workspace plan view has roadmap and objectives'] = function (): void {
    $view = file_get_contents(app_path('views/admin/workspace/plan.php'));
    assertTrue(str_contains($view, 'Roadmap'), 'Plan should have roadmap');
    assertTrue(str_contains($view, 'Objectives'), 'Plan should have objectives');
    assertTrue(str_contains($view, 'Insights'), 'Plan should have insights');
    assertTrue(str_contains($view, 'Recent Handoffs'), 'Plan should have recent handoffs');
};

$tests['workspace monitor view has pulse and insights'] = function (): void {
    $view = file_get_contents(app_path('views/admin/workspace/monitor.php'));
    assertTrue(str_contains($view, 'Pulse'), 'Monitor should have pulse feed');
    assertTrue(str_contains($view, 'Insights'), 'Monitor should have insights');
    assertTrue(str_contains($view, 'Progress'), 'Monitor should show progress');
};

$tests['admin controller has workspace and terminal methods'] = function (): void {
    $controller = file_get_contents(app_path('app/Controllers/AdminController.php'));
    assertTrue(str_contains($controller, 'function workspace():'), 'Admin should have workspace method');
    assertTrue(str_contains($controller, 'function workspaceCreate():'), 'Admin should have workspaceCreate method');
    assertTrue(str_contains($controller, 'function terminal():'), 'Admin should have terminal method');
    assertTrue(str_contains($controller, 'function terminalRun():'), 'Admin should have terminalRun method');
};

$tests['admin workspace controller aggregates objectives and todos'] = function (): void {
    $controller = file_get_contents(app_path('app/Controllers/AdminController.php'));
    assertTrue(str_contains($controller, 'OBJECTIVE.md'), 'Workspace should read objectives');
    assertTrue(str_contains($controller, '.tmp/todos.json'), 'Workspace should read todos');
    assertTrue(str_contains($controller, '.agents/handoffs/events'), 'Workspace should read handoff events');
    foreach (['triageItems', 'agentActivity', 'openObjectives', 'initiatives', 'recentHandoffs', 'pulseItems', 'insights'] as $key) {
        assertTrue(str_contains($controller, $key), "Workspace should aggregate {$key}");
    }
};

$tests['admin admin mutations are wired for audit'] = function (): void {
    $controller = file_get_contents(app_path('app/Controllers/AdminController.php'));
    assertTrue(str_contains($controller, 'AuditLogService'), 'Admin mutations should use AuditLogService');
    assertTrue(str_contains($controller, 'validateCsrf'), 'Admin POST should validate CSRF');
};

$tests['admin product forms expose owner fields and media'] = function (): void {
    $form = file_get_contents(app_path('views/admin/product-form.php'));
    foreach (['slug', 'price', 'offer_price', 'stock_status', 'image_url', 'image_urls', 'enctype="multipart/form-data"'] as $field) {
        assertTrue(str_contains($form, $field), "Product form should include {$field}");
    }
    assertTrue(str_contains($form, 'admin-media-picker'), 'Product form should have media picker');
};

$tests['admin project map view renders'] = function (): void {
    $view = app_path('views/admin/project-map.php');
    assertTrue(is_file($view), 'Project map admin view should exist');
    $contents = file_get_contents($view);
    assertTrue(str_contains($contents, 'Validation'), 'Project map should show validation');
    assertTrue(str_contains($contents, 'Routes'), 'Project map should show routes');
};

$tests['CLI has REPL mode and AI chat'] = function (): void {
    $cli = file_get_contents(app_path('cli/bapXaura'));
    assertTrue(str_contains($cli, 'cmd_repl()'), 'CLI should have cmd_repl');
    assertTrue(str_contains($cli, 'ai:'), 'REPL should support ai: prefix');
    assertTrue(str_contains($cli, 'read -r -e -p'), 'REPL should read user input with bash readline');
};

$tests['CLI has map and schema commands'] = function (): void {
    $cli = file_get_contents(app_path('cli/bapXaura'));
    foreach (['cmd_map', 'cmd_schema', 'cmd_ci', 'cmd_update', 'cmd_hooks'] as $cmd) {
        assertTrue(str_contains($cli, $cmd . '()'), "CLI should expose {$cmd}");
    }
};

$tests['MCP tool endpoint exists'] = function (): void {
    $controller = file_get_contents(app_path('app/Controllers/AiChatController.php'));
    assertTrue(str_contains($controller, 'function tools():'), 'AiChat should have tools() method');
    assertTrue(str_contains($controller, 'function chat():'), 'AiChat should have chat() method');
    assertTrue(str_contains($controller, 'bapXaura_map'), 'MCP tools should include bapXaura_map');
    assertTrue(str_contains($controller, 'schema_list'), 'MCP tools should include schema_list');
};

$tests['cloud agent runtime exists'] = function (): void {
    $controller = file_get_contents(app_path('app/Controllers/CloudAgentController.php'));
    assertTrue(str_contains($controller, 'function webhook():'), 'Cloud agent should have webhook');
    assertTrue(str_contains($controller, 'function prompt():'), 'Cloud agent should have prompt');
    assertTrue(str_contains($controller, 'function status():'), 'Cloud agent should have status');
    assertTrue(str_contains($controller, 'function handoffs():'), 'Cloud agent should have handoffs');
    $service = file_get_contents(app_path('app/Services/AgentRuntimeService.php'));
    assertTrue(str_contains($service, 'class AgentRuntimeService'), 'AgentRuntimeService should exist');
};

$tests['project map shared views includes workspace sub-views'] = function (): void {
    $shared = ProjectMapService::SHARED_VIEWS;
    foreach (['admin/workspace/_nav', 'admin/workspace/intake', 'admin/workspace/plan', 'admin/workspace/build', 'admin/workspace/monitor'] as $view) {
        assertTrue(in_array($view, $shared, true), "Shared views should include {$view}");
    }
};

$tests['WORKSPACE route is registered'] = function (): void {
    $routes = ProjectMapService::registry()['routes'];
    $paths = array_column($routes, 'path');
    assertTrue(in_array('/admin/workspace', $paths, true), 'Workspace GET route should be registered');
    assertTrue(in_array('/admin/workspace/create', $paths, true), 'Workspace create POST route should be registered');
};

$tests['AGENTS.md is canonical'] = function (): void {
    assertTrue(is_file(app_path('AGENTS.md')), 'AGENTS.md should exist');
    $agents = file_get_contents(app_path('AGENTS.md'));
    assertTrue(str_contains($agents, 'Diagnose, Then Issue'), 'AGENTS.md should include diagnose rule');
    assertTrue(str_contains($agents, 'bapXaura update'), 'AGENTS.md should reference CLI update');
    assertTrue(str_contains($agents, 'bapXaura ci'), 'AGENTS.md should reference CLI CI');
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(app_path(), FilesystemIterator::SKIP_DOTS));
    $agentFiles = [];
    foreach ($iterator as $file) {
        if ($file->getBasename() === 'AGENTS.md') $agentFiles[] = str_replace(app_path() . '/', '', $file->getPathname());
    }
    sort($agentFiles);
    assertTrue($agentFiles === ['AGENTS.md'], 'Only root AGENTS.md should exist');
    foreach (['CLAUDE.md', '.codex'] as $path) assertTrue(!file_exists(app_path($path)), "{$path} should not exist");
};

$tests['systematic map artifacts exist'] = function (): void {
    assertTrue(is_file(app_path('docs/systematic-map.mmd')), 'Systematic map should exist');
    assertTrue(is_file(app_path('docs/map.mmd')), 'docs/map.mmd should exist');
    assertTrue(is_file(app_path('map.mmd')), 'root map.mmd should exist');
    $sys = file_get_contents(app_path('docs/systematic-map.mmd'));
    foreach (['ADMIN Routes', 'Controllers', 'Services', 'Schema Collections', 'Gaps & Missing Links'] as $needle) {
        assertTrue(str_contains($sys, $needle), "Systematic map should include {$needle}");
    }
};

$tests['env file defines required keys'] = function (): void {
    $envPath = app_path('.env');
    assertTrue(is_file($envPath), '.env should exist');
    $env = parse_ini_file($envPath);
    foreach (['APP_NAME', 'APP_URL', 'BAPX_MYSQL_HOST', 'BAPX_MYSQL_DB', 'BAPX_MYSQL_USER', 'BAPX_MYSQL_PASS'] as $key) {
        assertTrue(($env[$key] ?? '') !== '', ".env should define {$key}");
    }
};

$tests['ai chat and mcp tool definitions are correctly registered'] = function (): void {
    $ai = new App\Controllers\AiChatController();
    $defs = $ai->getToolDefinitions();
    assertTrue(is_array($defs) && !empty($defs), 'Tool definitions must be a non-empty array');
    
    $toolNames = array_column(array_column($defs, 'function'), 'name');
    assertTrue(in_array('bapXaura_map', $toolNames, true), 'bapXaura_map tool should be registered');
    assertTrue(in_array('search_code', $toolNames, true), 'search_code tool should be registered');
    assertTrue(in_array('read_file', $toolNames, true), 'read_file tool should be registered');
    assertTrue(in_array('list_dir', $toolNames, true), 'list_dir tool should be registered');
};

$tests['admin CSS classes used in views are defined in admin.css'] = function (): void {
    $css = file_get_contents(app_path('assets/css/admin.css'));
    $views = glob(app_path('views/admin/**/*.php'));
    $views = array_merge($views, glob(app_path('views/admin/*.php')));
    $usedClasses = [];
    foreach ($views as $vf) {
        $content = file_get_contents($vf);
        preg_match_all('/class="([^"]+)"/', $content, $m);
        foreach ($m[1] as $cls) {
            foreach (explode(' ', $cls) as $c) {
                $c = trim($c);
                if ($c === '' || str_contains($c, '<?') || str_contains($c, '$') || str_contains($c, "'") || $c === '===' || $c === '?' || $c === ':' || $c === '?>' || $c === '??' || $c === '!==') continue;
                if (!str_starts_with($c, 'btn-') && !str_starts_with($c, 'flash-') && !in_array($c, ['admin-nav__link--active', 'admin-submenu', 'open'])) {
                    $usedClasses[$c] = true;
                }
            }
        }
    }
    $adminCssClasses = [];
    preg_match_all('/\.([\w-]+(?:::[\w-]+)?)(?=[,\s]*[\.{#])/', $css, $matches);
    foreach ($matches[1] as $cls) {
        $adminCssClasses[$cls] = true;
    }
    $missing = [];
    foreach (array_keys($usedClasses) as $cls) {
        if (!isset($adminCssClasses[$cls])) {
            $missing[] = $cls;
        }
    }
    assertSame([], $missing, 'All admin CSS classes should be defined in admin.css');
};

$tests['admin dashboard uses Linear design tokens (no var(--color-gold))'] = function (): void {
    $views = glob(app_path('views/admin/**/*.php'));
    $views = array_merge($views, glob(app_path('views/admin/*.php')));
    $found = [];
    foreach ($views as $vf) {
        $content = file_get_contents($vf);
        if (str_contains($content, 'var(--color-gold)')) {
            $found[] = basename($vf);
        }
    }
    assertSame([], $found, 'No admin view should use var(--color-gold) — replace with var(--color-primary)');
};

$tests['admin sidebar uses SVG icons instead of Unicode text replacements'] = function (): void {
    $layout = file_get_contents(app_path('views/layouts/admin.php'));
    $unicodeIcons = ['⊞', '▤', '◎', '◈', '▦', '⚙', '◉', '❯', '∷', '↗'];
    $found = [];
    foreach ($unicodeIcons as $icon) {
        if (str_contains($layout, $icon)) {
            $found[] = $icon;
        }
    }
    assertSame([], $found, 'Sidebar should not contain Unicode icons — use inline SVGs');
};


foreach ($tests as $name => $test) {
    try {
        $test();
        echo "PASS {$name}\n";
    } catch (Throwable $e) {
        echo "FAIL {$name}: {$e->getMessage()}\n";
        $failures[] = $e;
    }
}

if (!empty($failures)) {
    echo "\n" . count($failures) . " test(s) failed.\n";
    exit(1);
}
echo "\nAll tests passed.\n";
