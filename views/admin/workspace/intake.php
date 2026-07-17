<div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-lg);">
    <div>
        <div class="admin-card">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:var(--space-md);">
                <h3 style="font-size:0.9rem; margin:0; display:flex; align-items:center; gap:6px;">📥 Triage Inbox</h3>
                <span style="font-size:0.65rem; color:var(--color-text-muted);">AI-powered</span>
            </div>
            <?php if (empty($triageItems)): ?>
            <div style="color:var(--color-text-muted); font-size:0.85rem; padding:var(--space-lg); text-align:center; border:1px dashed var(--color-border); border-radius:var(--radius-md);">No unprocessed items. All clear.</div>
            <?php else: ?>
            <?php foreach ($triageItems as $item): ?>
            <div style="padding:var(--space-sm) var(--space-md); border:1px solid var(--color-border); border-radius:var(--radius-md); margin-bottom:var(--space-sm);">
                <div style="display:flex; justify-content:space-between; font-size:0.75rem; color:var(--color-text-muted); margin-bottom:2px;">
                    <span><?= e($item['actor'] ?? 'system') ?></span>
                    <span><?= e($item['time'] ?? '') ?></span>
                </div>
                <div style="font-size:0.85rem; font-weight:500;"><?= e($item['title'] ?? '') ?></div>
                <div style="display:flex; gap:4px; margin-top:4px; flex-wrap:wrap;">
                    <?php if (!empty($item['labels'])): foreach ($item['labels'] as $lb): ?>
                    <span style="font-size:0.65rem; padding:1px 6px; border-radius:999px; background:var(--color-surface); border:1px solid var(--color-border);"><?= e(is_array($lb) ? ($lb['text'] ?? '') : $lb) ?></span>
                    <?php endforeach; endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div>
        <div class="admin-card">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:var(--space-md);">
                <h3 style="font-size:0.9rem; margin:0; display:flex; align-items:center; gap:6px;">🤖 Agent Activity</h3>
                <span style="font-size:0.65rem; color:var(--color-text-muted);">Live feed</span>
            </div>
            <?php if (empty($agentActivity)): ?>
            <div style="color:var(--color-text-muted); font-size:0.85rem; padding:var(--space-lg); text-align:center; border:1px dashed var(--color-border); border-radius:var(--radius-md);">No agent activity yet.</div>
            <?php else: ?>
            <?php foreach ($agentActivity as $a): ?>
            <div style="display:flex; gap:var(--space-sm); padding:var(--space-xs) 0; border-bottom:1px solid var(--color-border); font-size:0.8rem;">
                <span style="color:var(--color-text-muted); white-space:nowrap; font-size:0.7rem; min-width:45px;"><?= e($a['time'] ?? '') ?></span>
                <span style="color:#00D4A4; font-weight:500; min-width:60px;">🤖 <?= e($a['actor'] ?? '') ?></span>
                <span><?= e($a['text'] ?? '') ?></span>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="admin-card" style="margin-top:var(--space-md);">
            <h3 style="font-size:0.9rem; margin:0 0 var(--space-sm);">⚡ Quick Create</h3>
            <form method="post" action="/admin/workspace/create" style="display:flex; gap:var(--space-sm);">
                <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                <input type="text" name="title" placeholder="Issue title..." required style="flex:1; padding:var(--space-sm) var(--space-md); border:1px solid var(--color-border); border-radius:var(--radius-md); font-size:0.85rem;">
                <button type="submit" class="btn btn-primary" style="font-size:0.85rem;">Create</button>
            </form>
        </div>
    </div>
</div>
