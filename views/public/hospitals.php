<section class="section">
    <div class="container" style="margin-bottom:var(--space-2xl);">
        <div style="text-align:center;">
            <span class="eyebrow">Our Facilities</span>
            <h1 class="section-title" style="margin-bottom:var(--space-sm);">Campus &amp; Hospital Locations</h1>
            <p class="lede">Aura Medical Institute of Electropathy and Hospital has multiple facilities across Coimbatore for education, clinical training, and patient care.</p>
        </div>
    </div>
    <?php if (empty($items)): ?>
        <div class="container">
            <div class="about-story-card reveal" style="text-align:center; padding:var(--space-3xl);">
                <div style="font-size:3rem; margin-bottom:var(--space-md);">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto;"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <h2 style="font-family:var(--font-family); margin:0 0 var(--space-sm);">No Facilities Listed</h2>
                <p style="color:var(--color-text-muted);">Facility information will appear here soon. Visit our <a href="/contact">Contact page</a> for directions and enquiries.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="container">
            <div class="hospital-list">
                <?php foreach ($items as $item): ?>
                    <article class="showcase-card hospital-card reveal">
                        <a class="hospital-card__media" href="/hospitals/<?= e($item['slug']) ?>" aria-label="View <?= e($item['name']) ?>">
                            <?php if (!empty($item['image_url'])): ?>
                                <img src="<?= e($item['image_url']) ?>" alt="<?= e($item['name']) ?>" decoding="async">
                            <?php else: ?>
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            <?php endif; ?>
                        </a>
                        <div class="hospital-card__body">
                            <h2><?= e($item['name']) ?></h2>
                            <p><?= e($item['description'] ?? '') ?></p>
                            <?php if (!empty($item['address'])): ?>
                                <p class="hospital-card__meta">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    <?= e($item['address']) ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($item['timings'])): ?>
                                <p class="hospital-card__meta">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <?= e($item['timings']) ?>
                                </p>
                            <?php endif; ?>
                            <div class="hospital-card__actions">
                                <a href="/hospitals/<?= e($item['slug']) ?>" class="btn btn-sm btn-primary">View Details</a>
                                <?php if (!empty($item['map_link'])): ?>
                                    <a href="<?= e($item['map_link']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        Get Directions
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="container" style="margin-top:var(--space-2xl);">
        <div class="page-cta-card reveal">
            <div>
                <span class="page-cta-card__eyebrow">Visit Us</span>
                <h3>Plan Your Visit</h3>
                <p>Use the contact form for admissions, hospital appointments, or campus tours.</p>
            </div>
            <a class="btn btn-primary page-cta-card__button" href="/contact#contact-form">Let’s Get Connected →</a>
        </div>
    </div>
</section>
