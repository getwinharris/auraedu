<div style="display:flex; gap:var(--space-md); align-items:stretch; margin-bottom:var(--space-lg); flex-wrap:wrap;">
    <div class="admin-card" style="flex:2; min-width:280px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-sm);">
            <span style="font-size:0.8rem; font-weight:600; display:flex; align-items:center; gap:4px;">🔄 Cycle <?= date('W') ?> — <?= date('M j') ?></span>
            <span style="font-size:0.7rem; color:var(--color-text-muted);">Week <?= date('W') ?></span>
        </div>
        <?php $pct = $stats['completion_pct'] ?? 0; ?>
        <div style="height:6px; background:var(--color-border); border-radius:999px; overflow:hidden; margin-bottom:var(--space-sm);">
            <div style="height:100%; width:<?= $pct ?>%; background:var(--color-gold); border-radius:999px; transition:width 0.3s;"></div>
        </div>
        <div style="display:flex; gap:var(--space-sm); font-size:0.75rem;">
            <span style="display:flex; align-items:center; gap:3px;"><span style="width:8px; height:8px; border-radius:50%; background:#00D4A4;"></span> <?= $stats['done'] ?? 0 ?> done</span>
            <span style="display:flex; align-items:center; gap:3px;"><span style="width:8px; height:8px; border-radius:50%; background:#f59e0b;"></span> <?= $stats['in_progress'] ?? 0 ?> active</span>
            <span style="display:flex; align-items:center; gap:3px;"><span style="width:8px; height:8px; border-radius:50%; background:rgba(255,255,255,0.2);"></span> <?= $stats['pending'] ?? 0 ?> pending</span>
        </div>
    </div>
    <div class="admin-card" style="flex:1; min-width:160px; display:flex; align-items:center; justify-content:center; flex-direction:column; gap:4px;">
        <div style="font-size:2rem; font-weight:700; color:var(--color-gold);"><?= $pct ?>%</div>
        <div style="font-size:0.7rem; color:var(--color-text-muted); text-transform:uppercase; letter-spacing:0.05em;">Cycle Completion</div>
    </div>
</div>

<div class="board">
    <?php $statuses = ['backlog' => 'Backlog', 'todo' => 'Todo', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'];
    foreach ($statuses as $key => $label): ?>
    <div class="board-col">
        <div class="board-col-header">
            <h3><?= $label ?></h3>
            <span class="board-col-count"><?= count($columns[$key] ?? []) ?></span>
        </div>
        <?php foreach (($columns[$key] ?? []) as $card): ?>
        <div class="issue-card">
            <div class="issue-card-top">
                <span class="issue-id"><?= e($card['id'] ?? '') ?></span>
                <span class="issue-agent <?= ($card['type'] ?? 'human') === 'bot' ? 'bot' : 'human' ?>">
                    <?= ($card['type'] ?? 'human') === 'bot' ? '🤖' : '👤' ?>
                </span>
            </div>
            <div class="issue-title"><?= e($card['title']) ?></div>
            <?php if (!empty($card['labels'])): ?>
            <div class="issue-labels">
                <?php foreach ($card['labels'] as $lb): ?>
                <span class="issue-label <?= e($lb['class'] ?? '') ?>"><?= e($lb['text'] ?? '') ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:var(--space-lg); margin-top:var(--space-lg);">
    <div class="admin-card">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:var(--space-sm);">
            <h3 style="font-size:0.9rem; margin:0; display:flex; align-items:center; gap:6px;">🤖 Active Agents</h3>
            <span style="font-size:0.65rem; color:var(--color-text-muted);">MCP connected</span>
        </div>
        <?php if (empty($agentEvents)): ?>
        <div style="color:var(--color-text-muted); font-size:0.8rem; padding:var(--space-md); text-align:center;">No agents active</div>
        <?php else: ?>
        <?php foreach (array_slice($agentEvents, 0, 5) as $a): ?>
        <div style="display:flex; align-items:center; gap:var(--space-sm); padding:6px 0; border-bottom:1px solid var(--color-border); font-size:0.8rem;">
            <span style="color:#00D4A4;">🤖</span>
            <span style="font-weight:500; min-width:80px;"><?= e($a['actor'] ?? 'Agent') ?></span>
            <span style="color:var(--color-text-muted); flex:1;"><?= e($a['text'] ?? '') ?></span>
            <span style="font-size:0.7rem; color:var(--color-text-muted);"><?= e($a['time'] ?? '') ?></span>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="admin-card">
        <h3 style="font-size:0.9rem; margin:0 0 var(--space-sm); display:flex; align-items:center; gap:6px;">📋 Open Objectives</h3>
        <?php if (empty($openObjectives)): ?>
        <div style="color:var(--color-text-muted); font-size:0.8rem; padding:var(--space-md); text-align:center;">All objectives completed</div>
        <?php else: ?>
        <?php foreach ($openObjectives as $o): ?>
        <div style="display:flex; align-items:center; gap:var(--space-sm); padding:6px 0; border-bottom:1px solid var(--color-border); font-size:0.8rem;">
            <span style="font-family:monospace; font-size:0.7rem; color:var(--color-text-muted); min-width:70px;"><?= e($o['id']) ?></span>
            <span style="flex:1;"><?= e($o['title']) ?></span>
            <span style="font-size:0.7rem; padding:1px 6px; border-radius:999px; background:#fef3c7; color:#92400e;">active</span>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="admin-card">
        <h3 style="font-size:0.9rem; margin:0 0 var(--space-sm); display:flex; align-items:center; gap:6px;">🔌 MCP Tools</h3>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-xs);">
            <div style="font-size:0.7rem; padding:4px 8px; background:var(--color-surface); border-radius:var(--radius-sm); color:var(--color-text-muted);">bapXaura_map</div>
            <div style="font-size:0.7rem; padding:4px 8px; background:var(--color-surface); border-radius:var(--radius-sm); color:var(--color-text-muted);">schema_list</div>
            <div style="font-size:0.7rem; padding:4px 8px; background:var(--color-surface); border-radius:var(--radius-sm); color:var(--color-text-muted);">ci</div>
            <div style="font-size:0.7rem; padding:4px 8px; background:var(--color-surface); border-radius:var(--radius-sm); color:var(--color-text-muted);">test</div>
            <div style="font-size:0.7rem; padding:4px 8px; background:var(--color-surface); border-radius:var(--radius-sm); color:var(--color-text-muted);">db_query</div>
            <div style="font-size:0.7rem; padding:4px 8px; background:var(--color-surface); border-radius:var(--radius-sm); color:var(--color-text-muted);">search_code</div>
        </div>
        <div style="margin-top:var(--space-sm); font-size:0.75rem; color:var(--color-text-muted); border-top:1px solid var(--color-border); padding-top:var(--space-sm);">
            <a href="/api/chat/tools" style="color:var(--color-gold);">View all 12 tools →</a>
        </div>
    </div>
</div>
