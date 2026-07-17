<div style="display:grid; grid-template-columns:2fr 1fr; gap:var(--space-lg);">
    <div>
        <div class="admin-card">
            <h3 style="font-size:0.9rem; margin:0 0 var(--space-md); display:flex; align-items:center; gap:6px;">⚡ Pulse</h3>
            <?php if (empty($pulseItems)): ?>
            <div style="color:var(--color-text-muted); font-size:0.85rem; padding:var(--space-lg); text-align:center; border:1px dashed var(--color-border); border-radius:var(--radius-md);">No recent activity to show.</div>
            <?php else: ?>
            <?php foreach ($pulseItems as $p): ?>
            <div style="padding:var(--space-sm) var(--space-md); border:1px solid var(--color-border); border-radius:var(--radius-md); margin-bottom:var(--space-sm);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2px;">
                    <span style="font-size:0.75rem; font-weight:600; display:flex; align-items:center; gap:4px;">
                        <?= ($p['actor_type'] ?? 'human') === 'bot' ? '🤖' : '👤' ?> <?= e($p['actor']) ?>
                    </span>
                    <span style="font-size:0.7rem; color:var(--color-text-muted);"><?= e($p['time']) ?></span>
                </div>
                <div style="font-size:0.82rem;"><?= e($p['text']) ?></div>
                <?php if (!empty($p['detail'])): ?>
                <div style="font-size:0.75rem; color:var(--color-text-muted); margin-top:2px;"><?= e($p['detail']) ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div>
        <div class="admin-card">
            <h3 style="font-size:0.9rem; margin:0 0 var(--space-md);">📊 Insights</h3>
            <div style="display:grid; gap:var(--space-sm);">
                <div style="display:flex; justify-content:space-between; padding:var(--space-sm); background:var(--color-surface); border-radius:var(--radius-md);">
                    <span style="font-size:0.8rem;">Total Objectives</span>
                    <span style="font-weight:700;"><?= $insights['total_objectives'] ?? 0 ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:var(--space-sm); background:var(--color-surface); border-radius:var(--radius-md);">
                    <span style="font-size:0.8rem;">Agent Events</span>
                    <span style="font-weight:700; color:#00D4A4;"><?= $insights['agent_events'] ?? 0 ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:var(--space-sm); background:var(--color-surface); border-radius:var(--radius-md);">
                    <span style="font-size:0.8rem;">Handoffs Processed</span>
                    <span style="font-weight:700;"><?= $insights['handoffs'] ?? 0 ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:var(--space-sm); background:var(--color-surface); border-radius:var(--radius-md);">
                    <span style="font-size:0.8rem;">Completion Rate</span>
                    <span style="font-weight:700; color:<?= ($insights['completion_rate'] ?? 0) > 50 ? '#00D4A4' : '#f59e0b' ?>;"><?= $insights['completion_rate'] ?? 0 ?>%</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:var(--space-sm); background:var(--color-surface); border-radius:var(--radius-md);">
                    <span style="font-size:0.8rem;">Open Todos</span>
                    <span style="font-weight:700; color:#f59e0b;"><?= $insights['open_todos'] ?? 0 ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:var(--space-sm); background:var(--color-surface); border-radius:var(--radius-md);">
                    <span style="font-size:0.8rem;">Owner</span>
                    <span style="font-weight:700; font-size:0.8rem;">@<?= e($owner ?? 'bapXai') ?></span>
                </div>
            </div>
        </div>
        <div class="admin-card" style="margin-top:var(--space-md);">
            <h3 style="font-size:0.9rem; margin:0 0 var(--space-sm);">🏁 Status</h3>
            <div style="height:8px; background:var(--color-border); border-radius:999px; overflow:hidden;">
                <div style="height:100%; width:<?= min(100, $insights['completion_rate'] ?? 0) ?>%; background:linear-gradient(90deg, #00D4A4, #087E82); border-radius:999px; transition:width 0.3s;"></div>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:0.7rem; color:var(--color-text-muted); margin-top:4px;">
                <span>Progress</span>
                <span><?= $insights['completion_rate'] ?? 0 ?>%</span>
            </div>
        </div>
    </div>
</div>
