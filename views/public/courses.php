<section class="hero-band">
    <div class="container">
        <div class="section-header">
            <span class="eyebrow serif-accent">Aura Medical Institute of Electropathy &amp; Hospital</span>
            <h1 class="section-title" style="margin-bottom:var(--space-sm);">Courses Offered</h1>
            <p class="lede">Electropathy, acupuncture, and allied-health programmes with hospital training and practice-led learning. Admissions are open — no NEET required, no upper age bar.</p>
        </div>

        <div class="about-feature-grid">
            <?php foreach ($courses as $c): ?>
            <article class="about-feature-card reveal">
                <span class="about-feature-card__icon"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></span>
                <h3><?= e($c['title']) ?></h3>
                <p class="lede" style="font-size:0.95rem;"><?= e($c['short']) ?></p>
                <p><?= e($c['lede']) ?></p>
                <p style="margin-top:var(--space-sm);"><strong>Duration:</strong> <?= e($c['duration']) ?></p>
                <a class="btn btn-sm btn-primary" href="/courses/<?= e($c['slug']) ?>" style="margin-top:var(--space-sm);">View Course →</a>
            </article>
            <?php endforeach; ?>
        </div>

        <div class="page-cta-card reveal" style="margin-top:var(--space-3xl);">
            <div>
                <span class="page-cta-card__eyebrow">Admissions open</span>
                <h3>Not sure which programme fits you?</h3>
                <p>Speak with our admissions desk about eligibility, duration, and the right pathway for your goals.</p>
            </div>
            <a class="btn btn-primary page-cta-card__button" href="/contact#contact-form">Enquire Now →</a>
        </div>
    </div>
</section>
