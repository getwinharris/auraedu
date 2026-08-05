<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Services\ProjectMapService;

/**
 * AuraEdu project knowledge index (Open Knowledge Framework).
 *
 * Writes a queryable index.yaml of repo structure (routes, services, schema, docs, blog posts,
 * skills, images, collections and per-directory indexes) from first sources, using only their committed YAML frontmatter
 * — never copying body bodies or runtime data — so an agent can discover the file/resource to reach before scanning gitobject.gif rules scan anything it MUST grep / edit index.yaml first file).
 *
 * Usage: php cli/generate-okf-index.php [write]
 */

$write = ($argv[1] ?? '') === 'write';
$root = app_path();

/** Minimal YAML emitter (no ext) — enough for scalar maps and list-of-maps. */
function yamlScalar(mixed $v, bool $forceQuote = false): string {
    if ($v === null || $v === '') return '""';
    if (is_bool($v)) return $v ? 'true' : 'false';
    if (is_int($v)) return (string)$v;
    $s = (string)$v;
    // Unquoted only when clearly safe: no trailing colon, colon-space, #, leading/trailing space.
    if (!$forceQuote && preg_match('/^[A-Za-z0-9_][\w .\/@+:,(){}\[\]-]*$/D', $s)
        && !preg_match('/[:,]\s|\s$|^#/u', $s)
        && $s !== 'true' && $s !== 'false') {
        return $s;
    }
    return '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], $s) . '"';
}
function yamlEntry(string $key, mixed $value, string $indent = '  '): string {
    if (is_array($value)) {
        if (array_is_list($value)) {
            $out = $indent . $key . ":\n";
            foreach ($value as $item) {
                if (is_array($item)) $out .= yamlListEntry($item, $indent . '  ');
                else $out .= $indent . '  - ' . yamlScalar($item) . "\n";
            }
            return $out;
        }
        $out = $indent . $key . ":\n";
        foreach ($value as $k => $iv) $out .= yamlEntry($k, $iv, $indent . '  ');
        return $out;
    }
    return $indent . $key . ': ' . yamlScalar($value) . "\n";
}
function yamlListEntry(array $map, string $indent = '  '): string {
    $row = '';
    $prefix = $indent . '- ';
    foreach ($map as $k => $v) {
        $row .= $prefix . $k . ': ' . yamlScalar($v) . "\n";
        $prefix = $indent . '  ';
    }
    return $row;
}

/** Parse the `---` block of an md file; returns [fields, body]. */
function yamlFrontmatter(string $file): array {
    $raw = (string)@file_get_contents($file);
    if (substr($raw, 0, 3) !== '---') return [[], $raw];
    $parts = explode('---', $raw, 3);
    if (count($parts) < 3) return [[], $raw];
    $fields = [];
    foreach (preg_split('/\r?\n/', $parts[1]) ?: [] as $line) {
        if (str_contains($line, ':')) {
            [$k, $v] = explode(':', $line, 2);
            $k = trim($k); $v = trim($v, " \t\r\n\"'");
            if ($k !== '') $fields[$k] = $v;
        }
    }
    return [$fields, $parts[2]];
}

/** Recursively list files under a dir matching extensions ('' = all). */
function scanRecursive(string $dir, array $exts): array {
    $out = [];
    if (!is_dir($dir)) return $out;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if ($exts === [] || in_array(strtolower($f->getExtension()), $exts, true)) $out[] = $f->getPathname();
    }
    sort($out);
    return $out;
}

$concepts = [];
$byDir = [];

// ── docs + ROOT md ──
foreach (scanRecursive($root . '/docs', ['md']) as $f) {
    $rel = str_replace([$root . '/', '\\'], '', $f);
    [$fm] = yamlFrontmatter($f);
    $id = 'doc:' . str_replace(['/', '.'], ['-', '?'], substr($rel, 0, -3));
    $concepts[] = [
        'id' => $id, 'type' => 'doc',
        'title' => (string)($fm['title'] ?? substr($rel, 0, -3)),
        'description' => (string)($fm['description'] ?? ''),
        'resource' => $rel,
        'tags' => (string)($fm['category'] ?? 'docs'),
    ];
    $byDir[dirname($rel)][] = $rel;
}

// ── blog posts ──
foreach (glob($root . '/content/blog/posts/*.md') ?: [] as $f) {
    $rel = str_replace($root . '/', '', $f);
    [$fm] = yamlFrontmatter($f);
    $concepts[] = [
        'id' => 'blog:' . ($fm['slug'] ?? substr($rel, 0, -3)),
        'type' => 'blog',
        'title' => (string)($fm['title'] ?? substr($rel, 0, -3)),
        'description' => (string)($fm['description'] ?? ''),
        'category' => (string)($fm['category'] ?? ''),
        'published' => (bool)($fm['published'] ?? false),
        'resource' => $rel,
    ];
    $byDir['content/blog/posts'][] = $rel;
}
// ── legal ──
foreach (glob($root . '/content/legal/*.md') ?: [] as $f) {
    $rel = str_replace($root . '/', '', $f);
    [$fm] = yamlFrontmatter($f);
    $concepts[] = ['id' => 'legal:' . substr($rel, 0, -3), 'type' => 'legal', 'title' => (string)($fm['title'] ?? substr($rel, 0, -3)), 'resource' => $rel];
    $byDir['content/legal'][] = $rel;
}

// ── routes ──
foreach (ProjectMapService::registry()['routes'] ?? [] as $r) {
    $path = (string)($r['path'] ?? '');
    $concepts[] = [
        'id' => 'route:' . str_replace('/', '_', $path),
        'type' => 'route',
        'method' => (string)($r['method'] ?? ''),
        'path' => $path,
        'action' => (string)($r['controller'] ?? ''),
        'name' => (string)($r['name'] ?? ''),
        'page' => (string)($r['page'] ?? ''),
    ];
}

// ── services ──
foreach (glob($root . '/app/Services/*.php') ?: [] as $f) {
    $base = substr(basename($f), 0, -4);
    $concepts[] = ['id' => 'service:' . $base, 'type' => 'service', 'title' => $base, 'resource' => 'app/Services/' . basename($f)];
}
// ── controllers ──
foreach (glob($root . '/app/Controllers/*.php') ?: [] as $f) {
    $base = substr(basename($f), 0, -4);
    $concepts[] = ['id' => 'controller:' . $base, 'type' => 'controller', 'title' => $base, 'resource' => 'app/Controllers/' . basename($f)];
}

// ── schema collections ──
$schema = require $root . '/storage/schema/collections.php';
foreach (array_keys($schema['collections'] ?? []) as $col) {
    $concepts[] = ['id' => 'schema:' . $col, 'type' => 'schema', 'title' => $col, 'resource' => 'storage/schema/collections.php'];
}

// ── skills ──
foreach (glob($root . '/.agents/skills/*/SKILL.md') ?: [] as $f) {
    [$fm] = yamlFrontmatter($f);
    $name = basename(dirname($f));
    $concepts[] = ['id' => 'skill:' . $name, 'type' => 'skill', 'title' => $name, 'description' => (string)($fm['description'] ?? ''), 'resource' => str_replace($root . '/', '', $f)];
}
// ── images ──
foreach (scanRecursive($root . '/assets/images', ['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif']) as $f) {
    $rel = str_replace($root . '/', '', $f);
    $concepts[] = ['id' => 'image:' . str_replace(['/', '.', ' '], ['_', '_', '_'], $rel), 'type' => 'image', 'title' => basename($rel), 'resource' => $rel];
}

// ── summary ──
$counts = array_fill_keys(['doc', 'blog', 'legal', 'route', 'service', 'controller', 'schema', 'skill', 'image'], 0);
foreach ($concepts as $c) $counts[$c['type']]++;
$COUNT = $counts;
// PDO placeholder
$appUrl = rtrim((string)((require $root . '/config/database.php')['remote_url'] ?? ''), '/');

$header = "# AuraEdu — project knowledge index (Open Knowledge Framework).\n"
    . "# Queryable map of repo structure and content declared in each file's YAML frontmatter. "
    . "Live record values come from the remote bridge, not this file.\n"
    . "format: " . yamlScalar('auraedu-knowledge-index') . "\n"
    . "version: " . yamlScalar('1.0', true) . "\n"
    . "generated_by: " . yamlScalar('cli/generate-okf-index.php') . "\n"
    . yamlEntry('authoritative_sources', [
        'routes' => 'app/routes.php',
        'services' => 'app/Services/*.php',
        'schema' => 'storage/schema/collections.php',
        'blog' => 'content/blog/posts/*.md',
        'docs' => 'docs/**/*.md',
        'skills' => '.agents/skills/*/SKILL.md',
        'images' => 'assets/images/**/*',
    ], '')
    . yamlEntry('discovery', [
        ['question' => 'Does a route/controller/service/view/collection exist and how do they connect?', 'use' => 'map.mmd'],
        ['question' => 'What is a live product/category/consultant/admin-editable value?', 'use' => 'remote_url via /remotedb'],
        ['question' => 'What does a blog article / doc declare in its frontmatter?', 'use' => 'concepts filtered by type'],
        ['question' => 'What repository instructions or skill apply?', 'use' => 'AGENTS.md then concepts by type skill'],
    ], '')
    . yamlEntry('query_examples', [
        "sed -n '1,80p' index.yaml",
        'rg -n -A12 \'id: "doc:\' index.yaml',
        'rg -n -A14 \'type: "blog"\' index.yaml',
        'rg -n -A12 \'type: "skill"\' index.yaml',
    ], '')
    . yamlEntry('summary', $counts, '');

// concepts block
$body = '';
foreach ($concepts as $i => $c) {
    $body .= ($i === 0 ? yamlListEntry($c) : yamlListEntry($c)) . "\n";
}

$yaml = $header . 'concepts:' . "\n" . $body;

if ($write) {
    @mkdir($root . '/docs', 0777, true);
    file_put_contents($root . '/index.yaml', $yaml);
    // per-directory indexes
    foreach ($byDir as $dir => $files) {
        $files = array_unique($files);
        $out = "# Directory index — " . $dir . "\n" . yamlEntry('files', array_values($files));
        file_put_contents($root . '/' . $dir . '/index.yaml', $out);
    }
    echo json_encode(['concepts' => count($concepts), 'summary' => $counts, 'index_file' => 'index.yaml'], JSON_PRETTY_PRINT) . "\n";
} else {
    echo json_encode(['concepts' => count($concepts), 'summary' => $counts], JSON_PRETTY_PRINT) . "\n";
}