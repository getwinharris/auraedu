<style>
.workspace-tabs { display:flex; gap:0; margin-bottom:var(--space-lg); border-bottom:2px solid var(--color-border); }
.workspace-tab { padding:var(--space-sm) var(--space-lg); font-size:0.85rem; font-weight:500; color:var(--color-text-muted); text-decoration:none; border-bottom:2px solid transparent; margin-bottom:-2px; transition:all 0.15s; }
.workspace-tab:hover { color:var(--color-text); }
.workspace-tab.active { color:var(--color-primary); border-bottom-color:var(--color-primary); }
.workspace-tab-count { display:inline-block; background:var(--color-border); border-radius:999px; padding:0 6px; font-size:0.65rem; margin-left:4px; font-weight:600; color:var(--color-text-muted); }
.workspace-tab.active .workspace-tab-count { background:var(--color-primary); color:var(--color-canvas); }
</style>
<nav class="workspace-tabs">
    <a href="/admin/workspace?tab=intake" class="workspace-tab <?= ($tab === 'intake' ? 'active' : '') ?>">
        📥 Intake <span class="workspace-tab-count"><?= $counts['intake'] ?? 0 ?></span>
    </a>
    <a href="/admin/workspace?tab=plan" class="workspace-tab <?= ($tab === 'plan' ? 'active' : '') ?>">
        🗺️ Plan
    </a>
    <a href="/admin/workspace?tab=build" class="workspace-tab <?= ($tab === 'build' ? 'active' : '') ?>">
        🔨 Build <span class="workspace-tab-count"><?= $counts['build'] ?? 0 ?></span>
    </a>
    <a href="/admin/workspace?tab=monitor" class="workspace-tab <?= ($tab === 'monitor' ? 'active' : '') ?>">
        📊 Monitor
    </a>
</nav>
