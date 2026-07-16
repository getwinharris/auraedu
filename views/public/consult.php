<section class="section">
    <?php $csrf = $_SESSION['csrf_token'] ??= bin2hex(random_bytes(16)); ?>
    <div class="container" style="text-align:center;">
        <span class="eyebrow">Therapy consultations · Scheduled online or in-clinic</span>
        <h1 class="section-title" style="margin-bottom:var(--space-sm);">Therapies &amp; Consultations</h1>
        <p class="lede">Connect with experienced therapists and wellness consultants at Aura Medical Institute. Browse profiles, compare expertise, and book a session.</p>
    </div>
    <?php if(empty($items)): ?>
        <div class="container" style="text-align:center; padding:var(--space-4xl) 0;">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto var(--space-md);"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 000 20 14.5 14.5 0 000-20"/><path d="M2 12h20"/></svg>
            <h2 style="margin:0 0 var(--space-sm);">Consultant Profiles Coming Soon</h2>
            <p style="color:var(--color-text-muted);">Therapy consultant profiles are being created. Contact us to schedule a consultation.</p>
            <div style="margin-top:var(--space-lg);">
                <a href="/contact" class="btn btn-primary">Request a Consultation</a>
            </div>
        </div>
    <?php else: ?>
        <?php
            $filterLanguages = [];
            foreach ($items as $filterItem) {
                foreach (($filterItem['languages'] ?? []) as $filterLanguage) {
                    $filterLanguage = trim((string)$filterLanguage);
                    if ($filterLanguage !== '') $filterLanguages[$filterLanguage] = $filterLanguage;
                }
            }
            ksort($filterLanguages);
        ?>
        <div class="container">
            <div class="search-toolbar reveal">
                <label class="consult-search">
                    <span>Search Consultant</span>
                    <input type="search" id="consult-search-input" placeholder="Search by name, language, speciality">
                </label>
                <?php if($filterLanguages): ?>
                <label class="consult-filter">
                    <span>Language</span>
                    <select id="consult-language-filter">
                        <option value="">All</option>
                        <?php foreach($filterLanguages as $filterLanguage): ?>
                            <option value="<?= e(strtolower($filterLanguage)) ?>"><?= e($filterLanguage) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <?php endif; ?>
            </div>
            <div class="consult-grid">
                <?php foreach($items as $item): ?>
                    <?php
                        $summary = isset($reviews) ? $reviews->summary('product', $item['slug'] ?? '') : ['average' => 0, 'count' => 0];
                        $languageText = implode(', ', array_slice(array_values(array_filter($item['languages'] ?? [])), 0, 2));
                        $experience = trim((string)($item['experience_years'] ?? ''));
                        $speciality = $item['speciality'] ?? 'Therapy & Wellness';
                    ?>
                    <article class="consult-card reveal" data-consult-card data-language="<?= e(strtolower(implode(' ', $item['languages'] ?? []))) ?>" data-search="<?= e(strtolower(($item['name'] ?? '') . ' ' . $languageText . ' ' . $speciality)) ?>">
                        <a class="consult-photo" href="/consult/<?= e($item['slug'] ?? '') ?>" aria-label="View <?= e($item['name'] ?? 'Consultant') ?>">
                            <span class="consult-photo-frame"><img class="consult-photo-img consult-photo-img--<?= e($item['slug'] ?? 'default') ?>" src="<?= e($item['photo_url'] ?? placeholder_img($item['name'] ?? 'Consultant')) ?>" alt="<?= e($item['name'] ?? 'Consultant') ?>" loading="lazy"></span>
                            <?php if(($summary['count'] ?? 0) > 0): ?><span class="rating-pill"><?= e(number_format((float)$summary['average'], 1)) ?> · <?= e((string)$summary['count']) ?></span><?php endif; ?>
                        </a>
                        <div class="consult-info">
                            <a href="/consult/<?= e($item['slug'] ?? '') ?>" class="consult-name"><?= e($item['name'] ?? 'Consultant') ?></a>
                            <p class="consult-speciality"><?= e($speciality) ?></p>
                            <?php if($languageText !== '' || $experience !== ''): ?><div class="consult-meta"><?php if($languageText !== ''): ?><span><?= e($languageText) ?></span><?php endif; ?><?php if($experience !== ''): ?><span><?= e($experience) ?> years</span><?php endif; ?></div><?php endif; ?>
                        </div>
                        <div class="consult-actions">
                            <?php if(!empty($item['slug'])): ?>
                                <div class="consult-action-row">
                                    <a class="consult-action" href="/consult/<?= e($item['slug']) ?>">View profile</a>
                                    <a class="consult-action consult-action--primary" href="/consult/<?= e($item['slug']) ?>#booking-form">Book session</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="container" style="margin-top:var(--space-2xl);">
        <div class="page-cta-card reveal">
            <div>
                <span class="page-cta-card__eyebrow">Need a Therapy Consultation?</span>
                <h3>Start Your Wellness Journey</h3>
                <p>Contact Aura Medical for therapy sessions, electropathy consultations, or wellness guidance.</p>
            </div>
            <a class="btn btn-primary page-cta-card__button" href="/contact#contact-form">Let’s Get Connected →</a>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('consult-search-input');
    var language = document.getElementById('consult-language-filter');
    if (!input) return;
    var cards = Array.prototype.slice.call(document.querySelectorAll('[data-consult-card]'));
    function filterCards() {
        var term = input.value.trim().toLowerCase();
        var languageTerm = language ? language.value : '';
        cards.forEach(function (card) {
            var searchMatch = term === '' || String(card.dataset.search || '').includes(term);
            var languageMatch = languageTerm === '' || String(card.dataset.language || '').includes(languageTerm);
            card.hidden = !(searchMatch && languageMatch);
        });
    }
    input.addEventListener('input', filterCards);
    if (language) language.addEventListener('change', filterCards);
});
</script>
