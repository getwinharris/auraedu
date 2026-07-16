<?php if (!$temple): ?>
    <div class="section" style="text-align:center; padding:var(--space-4xl) var(--space-md);">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1.5" style="margin:0 auto var(--space-md);"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <h1 style="margin:0 0 var(--space-sm);">Facility Not Found</h1>
        <p style="color:var(--color-text-muted); margin-bottom:var(--space-lg);">The facility you're looking for doesn't exist.</p>
        <a href="/hospitals" class="btn btn-primary">View All Facilities</a>
    </div>
<?php else: ?>
    <section class="section" style="padding-top:var(--space-xl);">
        <div class="container container--narrow">
            <div style="text-align:center; margin-bottom:var(--space-2xl);">
                <div style="background:var(--color-surface); border-radius:var(--radius-lg); margin-bottom:var(--space-lg); height:250px; display:flex; align-items:center; justify-content:center;">
                    <?php if (!empty($temple['image_url'])): ?>
                        <img src="<?= e($temple['image_url']) ?>" alt="<?= e($temple['name']) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:var(--radius-lg);">
                    <?php else: ?>
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <?php endif; ?>
                </div>
                <span class="eyebrow">Our Facility</span>
                <h1 style="margin:var(--space-sm) 0;"><?= e($temple['name']) ?></h1>
                <p class="lede" style="margin:0 auto var(--space-lg);"><?= e($temple['description'] ?? '') ?></p>
            </div>
            <div class="panel" style="margin-bottom:var(--space-xl);">
                <div style="display:grid; gap:var(--space-md);">
                    <?php if (!empty($temple['address'])): ?>
                        <div style="display:flex; align-items:flex-start; gap:var(--space-sm);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <div>
                                <span><?= e($temple['address']) ?></span>
                                <?php if (!empty($temple['map_link'])): ?>
                                    <a href="<?= e($temple['map_link']) ?>" target="_blank" rel="noopener" style="display:block; font-size:0.8rem; margin-top:2px;">View on Map</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($temple['timings'])): ?>
                        <div style="display:flex; align-items:center; gap:var(--space-sm);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span><?= e($temple['timings']) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($temple['phone'])): ?>
                        <div style="display:flex; align-items:center; gap:var(--space-sm);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                            <a href="tel:<?= e(str_replace(' ', '', $temple['phone'])) ?>" style="color:var(--color-ink); text-decoration:none;"><?= e($temple['phone']) ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!empty($temple['map_embed_url'])): ?>
            <div style="border-radius:var(--radius-lg); overflow:hidden; border:1px solid var(--color-border); margin-bottom:var(--space-xl);">
                <iframe src="<?= e($temple['map_embed_url']) ?>" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <?php endif; ?>
            <div style="text-align:center;">
                <a href="/contact" class="btn btn-primary">Enquire About This Facility</a>
                <a href="/hospitals" class="btn btn-ghost" style="margin-left:var(--space-sm);">View All Facilities</a>
            </div>
        </div>
    </section>
<?php endif; ?>
