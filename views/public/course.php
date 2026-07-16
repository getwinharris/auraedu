<section class="section section--warm">
    <div class="container">
        <div class="section-header">
            <span class="eyebrow serif-accent">Aura Medical Institute of Electropathy &amp; Hospital</span>
            <h1 class="section-title" style="margin-bottom:var(--space-sm);"><?= e($course['title']) ?> — <?= e($course['short']) ?></h1>
            <p class="lede"><?= e($course['lede']) ?></p>
        </div>

        <div class="about-story-grid">
            <div class="about-story-card reveal">
                <h3>Duration</h3>
                <p><?= e($course['duration']) ?></p>
            </div>
            <div class="about-story-card reveal">
                <h3>Eligibility</h3>
                <p><?= e($course['eligibility']) ?></p>
            </div>
        </div>

        <div class="section-header" style="margin-top:var(--space-3xl);">
            <span class="eyebrow serif-accent">Programme</span>
            <h2 class="section-title">What You Will Learn</h2>
            <p class="lede">Confirm the current module list and clinical structure with the admissions desk.</p>
        </div>
        <div class="value-strip">
            <?php foreach ($course['highlights'] as $h): ?>
            <article class="value-card reveal">
                <div class="value-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
                <h3><?= e($h) ?></h3>
            </article>
            <?php endforeach; ?>
        </div>

        <div class="section-header" style="margin-top:var(--space-3xl);">
            <span class="eyebrow serif-accent">Other Programmes</span>
            <h2 class="section-title">Explore More Courses</h2>
        </div>
        <div class="feature-strip home-order-steps">
            <?php foreach ($courses as $c): ?>
            <?php if ($c['slug'] === $course['slug']) continue; ?>
            <article><h3><a href="/courses/<?= e($c['slug']) ?>"><?= e($c['title']) ?></a></h3><p><?= e($c['short']) ?></p></article>
            <?php endforeach; ?>
        </div>

        <div class="page-cta-card reveal" style="margin-top:var(--space-2xl);">
            <div>
                <span class="page-cta-card__eyebrow">Ready to begin?</span>
                <h3>Start Your <?= e($course['title']) ?> Admission Enquiry</h3>
                <p>Use the contact form for admissions, documents, or a campus tour.</p>
            </div>
            <a class="btn btn-primary page-cta-card__button" href="/contact#contact-form">Enquire Now →</a>
        </div>
    </div>
</section>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Course",
    "name": "<?= e($course['title']) ?> — <?= e($course['short']) ?>",
    "description": "<?= e($course['lede']) ?>",
    "provider": {
        "@type": "CollegeOrUniversity",
        "name": "Aura Medical Institute of Electropathy and Hospital",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "10/6A, VKV Kumaraguru Nagar, Saravanampatti",
            "addressLocality": "Coimbatore",
            "addressRegion": "Tamil Nadu",
            "postalCode": "641035",
            "addressCountry": "IN"
        },
        "telephone": "+91 97902 21065"
    }
}
</script>
