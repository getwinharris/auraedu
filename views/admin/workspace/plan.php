<div style="display:grid; grid-template-columns:2fr 1fr; gap:var(--space-lg);">
    <div>
        <div class="admin-card">
            <h3 style="font-size:0.9rem; margin:0 0 var(--space-md); display:flex; align-items:center; gap:6px;">🗺️ Roadmap</h3>
            <div style="display:flex; gap:var(--space-sm); margin-bottom:var(--space-md); flex-wrap:wrap;">
                <?php $statusColors = ['on_track'=>'#00D4A4', 'at_risk'=>'#f59e0b', 'blocked'=>'#ef4444', 'completed'=>'#6ee7b7']; ?>
                <?php foreach ($initiatives as $init): ?>
                <div style="flex:1; min-width:200px; padding:var(--space-md); border:1px solid var(--color-border); border-radius:var(--radius-lg); border-left:3px solid <?= $statusColors[$init['status']] ?? '#ccc' ?>;">
                    <div style="font-size:0.7rem; color:var(--color-text-muted); text-transform:uppercase; letter-spacing:0.05em;"><?= e($init['category']) ?></div>
                    <div style="font-size:0.85rem; font-weight:600; margin:2px 0;"><?= e($init['title']) ?></div>
                    <div style="font-size:0.75rem; color:var(--color-text-muted);">
                        <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:<?= $statusColors[$init['status']] ?? '#ccc' ?>; margin-right:4px;"></span>
                        <?= ucfirst(str_replace('_', ' ', $init['status'])) ?>
                    </div>
                    <div style="font-size:0.7rem; color:var(--color-text-muted); margin-top:4px;"><?= $init['count'] ?? 0 ?> items</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="admin-card">
            <h3 style="font-size:0.9rem; margin:0 0 var(--space-md); display:flex; align-items:center; gap:6px;">🎯 Objectives</h3>
            <?php if (empty($objectives)): ?>
            <div style="color:var(--color-text-muted); font-size:0.85rem; text-align:center; padding:var(--space-lg); border:1px dashed var(--color-border); border-radius:var(--radius-md);">No objectives defined.</div>
            <?php else: ?>
            <?php foreach ($objectives as $obj): ?>
            <div style="display:flex; align-items:center; gap:var(--space-sm); padding:var(--space-xs) 0; border-bottom:1px solid var(--color-border);">
                <span style="font-size:0.7rem; color:var(--color-text-muted); font-family:monospace; min-width:60px;"><?= e($obj['id']) ?></span>
                <span style="flex:1; font-size:0.85rem;"><?= e($obj['title']) ?></span>
                <span style="font-size:0.7rem; padding:1px 8px; border-radius:999px; background:<?= $obj['status'] === 'done' ? '#d1fae5' : ($obj['status'] === 'in_progress' ? '#fef3c7' : '#f3f4f6') ?>; color:<?= $obj['status'] === 'done' ? '#065f46' : ($obj['status'] === 'in_progress' ? '#92400e' : '#666') ?>;">
                    <?= str_replace('_', ' ', $obj['status']) ?>
                </span>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div>
        <div class="admin-card">
            <h3 style="font-size:0.9rem; margin:0 0 var(--space-md); display:flex; align-items:center; gap:6px;">📈 Insights</h3>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-sm);">
                <div style="background:var(--color-surface); padding:var(--space-md); border-radius:var(--radius-md); text-align:center;">
                    <div style="font-size:1.5rem; font-weight:700;"><?= $stats['total_issues'] ?? 0 ?></div>
                    <div style="font-size:0.7rem; color:var(--color-text-muted);">Total Issues</div>
                </div>
                <div style="background:var(--color-surface); padding:var(--space-md); border-radius:var(--radius-md); text-align:center;">
                    <div style="font-size:1.5rem; font-weight:700; color:#00D4A4;"><?= $stats['done'] ?? 0 ?></div>
                    <div style="font-size:0.7rem; color:var(--color-text-muted);">Completed</div>
                </div>
                <div style="background:var(--color-surface); padding:var(--space-md); border-radius:var(--radius-md); text-align:center;">
                    <div style="font-size:1.5rem; font-weight:700; color:#f59e0b;"><?= $stats['in_progress'] ?? 0 ?></div>
                    <div style="font-size:0.7rem; color:var(--color-text-muted);">In Progress</div>
                </div>
                <div style="background:var(--color-surface); padding:var(--space-md); border-radius:var(--radius-md); text-align:center;">
                    <div style="font-size:1.5rem; font-weight:700; color:#00D4A4;"><?= $stats['completion_pct'] ?? 0 ?>%</div>
                    <div style="font-size:0.7rem; color:var(--color-text-muted);">Completion</div>
                </div>
            </div>
        </div>
        <div class="admin-card" style="margin-top:var(--space-md);">
            <h3 style="font-size:0.9rem; margin:0 0 var(--space-sm);">📋 Recent Handoffs</h3>
            <?php if (empty($recentHandoffs)): ?>
            <div style="color:var(--color-text-muted); font-size:0.8rem;">No recent handoffs</div>
            <?php else: ?>
            <?php foreach ($recentHandoffs as $h): ?>
            <div style="display:flex; gap:var(--space-sm); padding:4px 0; border-bottom:1px solid var(--color-border); font-size:0.75rem;">
                <span style="color:var(--color-text-muted);">#<?= $h['issue'] ?></span>
                <span style="color:#00D4A4;"><?= e($h['from']) ?>→<?= e($h['to']) ?></span>
                <span style="color:var(--color-text-muted);"><?= e($h['status']) ?></span>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
