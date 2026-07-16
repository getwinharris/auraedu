<section class="home-hero">
    <div class="container home-hero-inner">
        <div class="hero-copy">
            <span class="eyebrow">Aura Medical Institute of Electropathy &amp; Hospital · Coimbatore</span>
            <h1>B.E.M.S. — Electropathy Medical Education &amp; Care</h1>
            <p class="lede">Aura Medical Institute of Electropathy and Hospital offers the Bachelor of Electro-Medical Sciences (B.E.M.S.) programme, hospital training, and integrated alternative-medicine care — no NEET, no age bar.</p>
            <div class="hero-actions">
                <a href="/education" class="btn btn-accent btn-lg">Explore B.E.M.S. Admissions</a>
                <a href="/contact" class="btn btn-outline">Book a Hospital Visit</a>
            </div>
            <div class="hero-stats">
                <div>
                    <div class="hero-stat-value">B.E.M.S.</div>
                    <div class="hero-stat-label">Degree Programme</div>
                </div>
                <div>
                    <div class="hero-stat-value">No NEET</div>
                    <div class="hero-stat-label">No Age Bar</div>
                </div>
                <div>
                    <div class="hero-stat-value">24×7</div>
                    <div class="hero-stat-label">Hospital Care</div>
                </div>
            </div>
        </div>
        <div class="hero-deity" data-aura-hero>
            <div class="deity-frame">
                <img class="varahi-slide is-active" src="/assets/images/institute/students-campus.jpg" alt="Aura Medical Institute of Electropathy and Hospital, Coimbatore" width="480" height="640" fetchpriority="high">
                <img class="varahi-slide" src="/assets/images/institute/medical-students.jpg" alt="Aura Medical campus and hospital facilities" width="480" height="640" loading="lazy">
                <img class="varahi-slide" src="/assets/images/institute/esic-students.jpg" alt="Acupuncture and electropathy therapy at Aura Medical" width="480" height="640" loading="lazy">
            </div>
            <div class="varahi-dots" role="tablist" aria-label="Aura Medical slides">
                <button class="varahi-dot is-active" type="button" role="tab" aria-label="Slide 1" aria-current="true" data-slide="0"></button>
                <button class="varahi-dot" type="button" role="tab" aria-label="Slide 2" aria-current="false" data-slide="1"></button>
                <button class="varahi-dot" type="button" role="tab" aria-label="Slide 3" aria-current="false" data-slide="2"></button>
            </div>
        </div>
    </div>
</section>
<script>
(() => {
    const root = document.querySelector('[data-aura-hero]');
    if (!root) return;
    const slides = [...root.querySelectorAll('.varahi-slide')];
    const dots = [...root.querySelectorAll('.varahi-dot')];
    let index = 0, timer;
    const show = n => {
        slides[index].classList.remove('is-active');
        dots[index].classList.remove('is-active');
        dots[index].setAttribute('aria-current', 'false');
        index = (n + slides.length) % slides.length;
        slides[index].classList.add('is-active');
        dots[index].classList.add('is-active');
        dots[index].setAttribute('aria-current', 'true');
    };
    const play = () => {
        if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        clearInterval(timer);
        timer = setInterval(() => show(index + 1), 5000);
    };
    dots.forEach(d => {
        d.addEventListener('click', () => { show(parseInt(d.dataset.slide)); play(); });
    });
    root.addEventListener('mouseenter', () => clearInterval(timer));
    root.addEventListener('mouseleave', play);
    play();
})();
</script>

<div class="trust-bar">
    <div class="trust-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
        Recognised Electropathy Programme
    </div>
    <div class="trust-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Hospital &amp; Clinical Training
    </div>
    <div class="trust-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
        Acupuncture &amp; Allied Therapies
    </div>
    <div class="trust-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
        Hostel &amp; Placement Support
    </div>
</div>

<section class="category-section section">
    <div class="section-header">
        <h2 class="section-title">Therapy &amp; Wellness Products</h2>
        <p class="lede">Clinically oriented acupuncture and electropathy therapy products, chosen to support student training and patient care.</p>
    </div>
    <div class="category-grid">
        <?php foreach($categories as $cat): ?>
            <a class="category-card" href="/shop?category=<?= e($cat['slug']) ?>">
                <div class="category-img-wrap">
                    <img src="<?= e($cat['image_url'] ?? placeholder_img($cat['name'])) ?>" alt="<?= e($cat['name']) ?> at Aura Medical, Coimbatore" decoding="async">
                </div>
                <h3><?= e($cat['name']) ?></h3>
                <p><?= e($cat['description']) ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php
            $cartQuantities = [];
            foreach ($_SESSION['cart'] ?? [] as $cartItem) {
                $cartQuantities[(string)($cartItem['slug'] ?? '')] = (int)($cartItem['qty'] ?? 0);
            }
            $csrf = $_SESSION['csrf_token'] ??= bin2hex(random_bytes(16));
        ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-xl); flex-wrap:wrap; gap:var(--space-sm);">
            <h2 class="section-title" style="margin:0;">Acupuncture &amp; Therapy Products</h2>
            <a href="/shop?category=acupuncture" class="btn btn-sm btn-ghost">View Shop</a>
        </div>
        <div class="product-grid">
        <?php foreach(array_slice($products, 0, min(4, count($products))) as $item): ?>
            <?php $hasOffer = !empty($item['offer_price']) && $item['offer_price'] < $item['price']; ?>
            <article class="product-card reveal">
                <div class="product-card__image">
                    <img src="<?= e(webp_src($item['image_url'] ?? placeholder_img($item['name']))) ?>" alt="<?= e($item['name']) ?> — Aura Medical, Coimbatore" decoding="async">
                    <?php if($hasOffer): ?>
                        <span class="product-card__badge product-card__badge--sale">Sale</span>
                    <?php endif; ?>
                </div>
                <div class="product-card__body">
                    <h3><?= e($item['name']) ?></h3>
                    <p class="product-card__desc"><?= e($item['description']) ?></p>
                    <div class="product-card__price-row">
                        <span class="price">₹<?= e((string)($item['offer_price'] ?: $item['price'] ?: 0)) ?></span>
                        <?php if($hasOffer): ?>
                            <span class="old-price">₹<?= e($item['price']) ?></span>
                            <?php $pct = round((1 - $item['offer_price'] / ($item['price'] ?: 1)) * 100); ?>
                            <span class="discount-pct">-<?= $pct ?>%</span>
                        <?php endif; ?>
                    </div>
                    <?php $itemQty = $cartQuantities[(string)($item['slug'] ?? '')] ?? 0; ?>
                    <div class="product-card__actions">
                        <a href="/product/<?= e($item['slug']) ?>" class="btn btn-sm btn-ghost">View →</a>
                        <div class="product-card__form product-card__stepper" aria-label="<?= e($item['name']) ?> cart quantity">
                            <form method="post" action="/cart/update">
                                <input type="hidden" name="slug" value="<?= e($item['slug']) ?>">
                                <input type="hidden" name="action" value="dec">
                                <input type="hidden" name="redirect" value="/">
                                <input type="hidden" name="_csrf" value="<?= $csrf ?>">
                                <button type="submit" aria-label="Remove one <?= e($item['name']) ?>" <?= $itemQty <= 0 ? 'disabled' : '' ?>>−</button>
                            </form>
                            <span class="qty-input__value"><?= e((string)$itemQty) ?></span>
                            <form method="post" action="/cart/add">
                                <input type="hidden" name="slug" value="<?= e($item['slug']) ?>">
                                <input type="hidden" name="qty" value="1">
                                <input type="hidden" name="redirect" value="/">
                                <input type="hidden" name="_csrf" value="<?= $csrf ?>">
                                <button type="submit" aria-label="Add one <?= e($item['name']) ?>">+</button>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    </div>
</section>

<section class="section section--full">
    <div class="section-header">
        <span class="eyebrow serif-accent">Electropathy · Acupuncture · Allied Health</span>
        <h2 class="section-title">Why Choose Aura Medical</h2>
        <p class="lede">A practice-oriented institute and hospital where electropathy, acupuncture, and allied therapies are taught alongside real clinical exposure.</p>
    </div>
    <div class="astro-carousel" aria-label="Aura Medical focus areas">
        <div class="astro-carousel-track">
        <?php foreach([
            ['title'=>'Electropathy','body'=>'Electro-medical sciences rooted in alternative-medicine practice and hospital training.'],
            ['title'=>'Acupuncture','body'=>'Traditional needle therapy integrated with modern electropathy assessment methods.'],
            ['title'=>'Allied Health','body'=>'Supportive therapies, nutrition guidance, and rehabilitative practice for whole-person care.'],
            ['title'=>'Clinical Training','body'=>'Hands-on hospital rotation so students learn care where it is delivered.'],
        ] as $focus): ?>
            <article class="astro-market-card reveal">
                <div class="astro-market-info">
                    <a href="/education" class="astro-market-name"><?= e($focus['title']) ?></a>
                    <p class="astro-market-speciality"><?= e($focus['body']) ?></p>
                </div>
                <div class="astro-market-actions">
                    <div class="astro-action-row">
                        <a href="/education" class="astro-action">Learn more</a>
                        <a href="/contact" class="astro-action astro-action--primary">Enquire now</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
        </div>
    </div>
    <div style="text-align:center;">
        <a href="/education" class="btn btn-primary">View B.E.M.S. Programme</a>
    </div>
</section>

<section class="section section--alt">
    <div class="container">
        <div class="section-header">
            <span class="eyebrow serif-accent">Admissions · Campus · Hospital</span>
            <h2 class="section-title">How Admission Works</h2>
            <p class="lede">No NEET, no age bar. enquire, submit documents, and begin your electropathy medical education with hospital training.</p>
        </div>
        <div class="feature-strip home-order-steps">
            <article><span class="home-order-step">1</span><h3>Enquire Online</h3><p>Use the contact form or visit the campus to request the B.E.M.S. prospectus.</p></article>
            <article><span class="home-order-step">2</span><h3>Submit Documents</h3><p>Share eligibility documents; our admissions desk confirms your seat.</p></article>
            <article><span class="home-order-step">3</span><h3>Begin Training</h3><p>Join classes and hospital rotation with hostel and placement support.</p></article>
        </div>
        <div class="home-order-actions">
            <a class="btn btn-primary" href="/education">B.E.M.S. Details</a>
            <a class="btn btn-ghost" href="/contact">Apply / Enquire</a>
        </div>
    </div>
</section>

<section class="section section--warm">
    <div class="container">
        <div class="section-header">
            <span class="eyebrow serif-accent">Why Aura Medical</span>
            <h2 class="section-title">Practice · Care · Community</h2>
            <p class="lede">Aura Medical Institute of Electropathy and Hospital blends education with patient care, so learning and service grow together.</p>
        </div>
        <div class="value-strip">
            <article class="value-card reveal">
                <div class="value-card__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                </div>
                <h3>Practice-Led</h3>
                <p>Every programme pairs classroom theory with hospital and therapy practice.</p>
            </article>
            <article class="value-card reveal">
                <div class="value-card__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <h3>Open Access</h3>
                <p>Admissions without NEET and without age bar keep medical education reachable.</p>
            </article>
            <article class="value-card reveal">
                <div class="value-card__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Whole-Person Care</h3>
                <p>Electropathy, acupuncture, and allied health come together for patient wellbeing.</p>
            </article>
            <article class="value-card reveal">
                <div class="value-card__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                </div>
                <h3>Community</h3>
                <p>Students, faculty, and patients form a learning community rooted in Coimbatore.</p>
            </article>
        </div>
        <div class="page-cta-card reveal">
            <div>
                <span class="page-cta-card__eyebrow">Ready to begin?</span>
                <h3>Start Your Admission Enquiry</h3>
                <p>Use the contact form for B.E.M.S. admissions, hospital visits, therapy questions, or campus tours.</p>
            </div>
            <a class="btn btn-primary page-cta-card__button" href="/contact#contact-form">Let’s Get Connected →</a>
        </div>
    </div>
</section>

<!-- FAQ Schema for SEO -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "What is the B.E.M.S. programme at Aura Medical?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "B.E.M.S. (Bachelor of Electro-Medical Sciences) is the electropathy medical education programme offered by Aura Medical Institute of Electropathy and Hospital, Coimbatore, with hospital training and allied-health practice."
            }
        },
        {
            "@type": "Question",
            "name": "Is NEET required for admission?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "No. Aura Medical admissions do not require NEET and there is no upper age bar. Enquire through the contact form or by visiting the campus."
            }
        },
        {
            "@type": "Question",
            "name": "Does Aura Medical offer acupuncture and allied therapies?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Yes. Acupuncture and allied-health therapies are part of the electropathy practice and training at Aura Medical Institute of Electropathy and Hospital."
            }
        },
        {
            "@type": "Question",
            "name": "Where is Aura Medical located?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Aura Medical Institute of Electropathy and Hospital is at 10/6A, VKV Kumaraguru Nagar, Saravanampatti, Coimbatore, Tamil Nadu 641035. Phone: +91 97902 21065."
            }
        }
    ]
}
</script>
