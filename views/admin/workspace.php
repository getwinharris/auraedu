<div class="workspace-header">
    <h1>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
        Workspace
    </h1>
    <div class="workspace-stats">
        <span>🎯 <strong><?= $stats['objectives'] ?? 0 ?></strong> objectives</span>
        <span>📋 <strong><?= $stats['todos'] ?? 0 ?></strong> todos</span>
        <span>🤖 <strong><?= $stats['agent_events'] ?? 0 ?></strong> agent events</span>
        <span>✅ <strong><?= $stats['done'] ?? 0 ?></strong> done</span>
    </div>
</div>

<?php require __DIR__ . '/workspace/_nav.php'; ?>

<?php
$section = $_GET['tab'] ?? 'build';
$sectionFile = __DIR__ . '/workspace/' . $section . '.php';
if (is_file($sectionFile)) {
    require $sectionFile;
} else {
    echo '<div style="color:var(--color-text-muted); padding:var(--space-lg); text-align:center;">Section not found.</div>';
}
?>
