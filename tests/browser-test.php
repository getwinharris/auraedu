<?php
/**
 * Browser Smoke Test
 * Uses browser-agent.php (Playwright-equivalent) to request pages and verify rendering.
 */
require __DIR__ . '/../app/bootstrap.php';

$port = 6300 + random_int(0, 500);
$base = "http://127.0.0.1:{$port}";
$descriptor = [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w'],
];
$environment = array_merge(getenv() ?: [], ['BAPX_TEST_MODE' => '1']);
$process = proc_open('php -S 127.0.0.1:' . $port . ' index.php', $descriptor, $pipes, app_path(), $environment);
if (!is_resource($process)) {
    fwrite(STDERR, "Failed to start local PHP server for browser testing.\n");
    exit(1);
}

try {
    // Wait for server to be responsive
    $deadline = microtime(true) + 5;
    $connected = false;
    do {
        $fp = @fsockopen('127.0.0.1', $port, $errno, $errstr, 0.5);
        if ($fp) {
            fclose($fp);
            $connected = true;
            break;
        }
        usleep(100000);
    } while (microtime(true) < $deadline);

    if (!$connected) {
        throw new RuntimeException("Local server on port {$port} failed to start.");
    }

    echo "Running browser automation checks on {$base}...\n";

    // 1. Check home page
    $cmd = 'php ' . escapeshellarg(app_path('cli/browser-agent.php')) . ' open ' . escapeshellarg($base . '/');
    $output = shell_exec($cmd);
    
    if (str_contains($output, '- a ') || str_contains($output, '- div ') || str_contains($output, 'class: ')) {
        echo "PASS Home page DOM parsed successfully\n";
    } else {
        throw new RuntimeException("Home page load returned empty or invalid DOM parsing:\n" . $output);
    }

    // 2. Check shop catalog
    $cmd = 'php ' . escapeshellarg(app_path('cli/browser-agent.php')) . ' open ' . escapeshellarg($base . '/shop');
    $output = shell_exec($cmd);
    
    if (str_contains($output, '- a ') || str_contains($output, '- div ')) {
        echo "PASS Shop catalog DOM parsed successfully\n";
    } else {
        throw new RuntimeException("Shop catalog page load returned empty or invalid DOM parsing:\n" . $output);
    }

    // 3. Reset/Close session
    $cmd = 'php ' . escapeshellarg(app_path('cli/browser-agent.php')) . ' close';
    shell_exec($cmd);
    echo "PASS Browser session closed\n";

} catch (Throwable $e) {
    echo "FAIL Browser testing: " . $e->getMessage() . "\n";
    exit(1);
} finally {
    foreach ($pipes as $pipe) {
        if (is_resource($pipe)) fclose($pipe);
    }
    proc_terminate($process);
    proc_close($process);
}

echo "\nAll browser automation checks passed.\n";
exit(0);
