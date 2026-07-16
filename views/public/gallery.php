<section class="section section--warm">
    <div class="container">
        <div class="section-header">
            <span class="eyebrow serif-accent">Campus &amp; Hospital</span>
            <h1 class="section-title" style="margin-bottom:var(--space-sm);">Gallery</h1>
            <p class="lede">A glimpse of the Aura Medical campus, hospital, therapy practice, and student life in Coimbatore.</p>
        </div>

        <div class="category-grid">
            <?php
            $gallery = [
                ['src' => '/assets/images/institute/students-campus.jpg', 'alt' => 'Indian medical students on campus', 'cap' => 'Campus & Hospital'],
                ['src' => '/assets/images/institute/medical-students.jpg', 'alt' => 'Indian medical students in clinical training', 'cap' => 'Student Training'],
                ['src' => '/assets/images/institute/lab-students.jpg', 'alt' => 'Students in a science laboratory', 'cap' => 'Laboratory Practice'],
                ['src' => '/assets/images/institute/free-medical-camp.jpg', 'alt' => 'Medical students at a free medical camp', 'cap' => 'Community Care'],
                ['src' => '/assets/images/institute/esic-students.jpg', 'alt' => 'Medical students with faculty at a hospital', 'cap' => 'Hospital Training'],
                ['src' => '/assets/images/institute/student-group.jpg', 'alt' => 'Group of medical students', 'cap' => 'Campus Life'],
            ];
            foreach ($gallery as $g):
            ?>
            <figure class="category-card" style="margin:0;">
                <div class="category-img-wrap">
                    <img src="<?= e($g['src']) ?>" alt="<?= e($g['alt']) ?>" decoding="async" loading="lazy">
                </div>
                <figcaption style="padding:var(--space-sm) var(--space-md); font-weight:500;"><?= e($g['cap']) ?></figcaption>
            </figure>
            <?php endforeach; ?>
        </div>

        <div class="page-cta-card reveal" style="margin-top:var(--space-3xl);">
            <div>
                <span class="page-cta-card__eyebrow">Visit us</span>
                <h3>See the Campus in Person</h3>
                <p>Book a campus tour at 10/6A, VKV Kumaraguru Nagar, Saravanampatti, Coimbatore.</p>
            </div>
            <a class="btn btn-primary page-cta-card__button" href="/contact#contact-form">Book a Tour →</a>
        </div>
    </div>
</section>
