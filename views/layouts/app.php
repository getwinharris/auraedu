<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="format-detection" content="telephone=no">
<title><?= e($pageTitle ?? 'AuraEdu') ?></title>
<meta name="description" content="<?= e($metaDescription ?? 'Aura Medical Institute of Electropathy and Hospital — B.E.M.S. admissions, hospital care, acupuncture and allied-health therapies in Coimbatore.') ?>">
<meta name="robots" content="<?= e($metaRobots ?? 'index, follow') ?>">
<?php $__seoKeywords = $seo['keywords'] ?? ''; if ($__seoKeywords !== ''): ?><meta name="keywords" content="<?= e($__seoKeywords) ?>"><?php endif; ?>
<?php
$__settings = (new \App\Services\SettingsService())->public();
$__logo = $__settings['logo_url'] ?? '/assets/images/brand/logo.png';
$__favicon = $__settings['favicon_url'] ?? '/assets/images/auraedu-favicon.svg';
$__faviconMime = str_contains($__favicon,'.svg') ? 'image/svg+xml' : 'image/png';
?>
<link rel="icon" type="<?= e($__faviconMime) ?>" href="<?= e($__favicon) ?>">
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#f5f5f5">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="AuraEdu">
<link rel="apple-touch-icon" href="/assets/images/auraedu-favicon.svg">
<link rel="canonical" href="https://<?= e($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>">
<meta property="og:type" content="<?= e($seo['og_type'] ?? 'website') ?>">
<meta property="og:site_name" content="<?= e($seo['og_site_name'] ?? 'AuraEdu') ?>">
<meta property="og:title" content="<?= e($seo['og_title'] ?? $pageTitle) ?>">
<meta property="og:description" content="<?= e($seo['og_description'] ?? $metaDescription) ?>">
<meta property="og:url" content="<?= e($seo['og_url'] ?? 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>">
<meta property="og:image" content="<?= e($seo['og_image'] ?? 'https://' . $_SERVER['HTTP_HOST'] . '/assets/images/og-image.jpg') ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="en_IN">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($seo['twitter_title'] ?? $seo['og_title'] ?? $pageTitle) ?>">
<meta name="twitter:description" content="<?= e($seo['twitter_description'] ?? $seo['og_description'] ?? $metaDescription) ?>">
<meta name="twitter:image" content="<?= e($seo['twitter_image'] ?? $seo['og_image'] ?? 'https://' . $_SERVER['HTTP_HOST'] . '/assets/images/og-image.jpg') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"></noscript>
<style>
<?php
// Inline critical CSS for instant first paint — bapXaura design language
$critical = '
:root{--color-ink:#0c0a09;--color-primary:#292524;--color-accent:#292524;--color-accent-hover:#0c0a09;--color-canvas:#f5f5f5;--color-surface:#ffffff;--color-surface-soft:#fafafa;--color-border:#e7e5e4;--color-border-light:#f0efed;--color-text-muted:#777169;--color-text-light:#a8a29e;--color-text-soft:#a8a29e;--color-text-disabled:#c0bcb8;--color-white:#ffffff;--color-success:#16a34a;--color-error:#dc2626;--color-info:#2563eb;--gradient-mint:#a7e5d3;--gradient-peach:#f4c5a8;--gradient-lavender:#c8b8e0;--font-main:"Inter",-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;--font-display:"Times New Roman",Georgia,"Noto Serif",serif;--shadow-sm:0 1px 2px rgba(0,0,0,0.04);--shadow-md:0 4px 12px rgba(0,0,0,0.08);--shadow-lg:0 24px 48px -8px rgba(0,0,0,0.12);--radius-sm:6px;--radius-md:8px;--radius-lg:12px;--radius-xl:16px;--radius-full:9999px;--space-xs:0.5rem;--space-sm:0.75rem;--space-md:1rem;--space-lg:1.25rem;--space-xl:1.5rem;--space-xxl:2rem;--space-section:4rem;--container-max:1280px}
*,*::before,*::after{box-sizing:border-box;-webkit-font-smoothing:antialiased}
html{scroll-behavior:smooth}
body{margin:0;font-family:var(--font-main);background:var(--color-canvas);color:var(--color-ink);line-height:1.7;overflow-x:hidden}
a{color:var(--color-ink);text-decoration:none}
a:hover{color:var(--color-text-muted)}
img{max-width:100%;height:auto;display:block}
.site-header{position:sticky;top:0;z-index:100;display:grid;grid-template-columns:auto 1fr auto;align-items:center;gap:var(--space-xl);padding:0 5vw;background:rgba(245,245,245,0.97);border-bottom:1px solid var(--color-border-light);min-height:64px}
.brand{display:flex;align-items:center;gap:var(--space-sm);color:var(--color-ink);font-weight:500;font-size:1rem;font-family:var(--font-main);text-decoration:none}
.brand img{width:36px;height:36px;object-fit:contain}
nav{display:flex;gap:var(--space-lg);justify-content:center}
nav a{font-weight:500;color:var(--color-text-muted);padding:0;font-size:0.875rem;text-decoration:none;height:64px;display:inline-flex;align-items:center;position:relative}
nav a[aria-current="page"]{color:var(--color-ink)}
nav a[aria-current="page"]::after{content:"";position:absolute;bottom:0;left:0;width:100%;height:1.5px;background:var(--color-ink);border-radius:2px}
.nav-dropdown{position:relative;display:inline-flex}
.nav-dropdown__trigger{padding-right:4px!important;cursor:default}
.nav-dropdown__arrow{font-size:0.6rem;margin-left:2px;opacity:0.5}
.nav-dropdown__menu{position:absolute;top:100%;left:50%;transform:translateX(-50%);min-width:160px;background:var(--color-white);border:1px solid var(--color-border);border-radius:var(--radius-md);box-shadow:var(--shadow-md);opacity:0;visibility:hidden;transition:opacity 0.15s ease,visibility 0.15s ease;z-index:200;padding:4px 0;margin-top:4px}
.nav-dropdown__menu a{display:block;padding:6px 16px;font-size:0.85rem;color:var(--color-ink);white-space:nowrap;text-decoration:none}
.nav-dropdown__menu a:hover{background:var(--color-surface-soft);color:var(--color-ink)}
.nav-dropdown:hover .nav-dropdown__menu,.nav-dropdown:focus-within .nav-dropdown__menu{opacity:1;visibility:visible}
@media(max-width:768px){.nav-dropdown__menu{position:static;transform:none;box-shadow:none;border:0;opacity:1;visibility:visible;margin:0;padding:0 0 0 16px;background:transparent}
.nav-dropdown__menu a{padding:6px 0;font-size:0.85rem}}
.header-actions{display:flex;align-items:center;gap:var(--space-md)}
.cart-btn{background:transparent;border:0;font-size:1.2rem;cursor:pointer;position:relative;color:var(--color-ink);padding:var(--space-xs);border-radius:var(--radius-sm)}
.cart-count{position:absolute;top:-4px;right:-6px;background:var(--color-primary);color:var(--color-white);font-size:0.55rem;width:16px;height:16px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600}
.menu-toggle{display:none;border:1px solid var(--color-border);background:var(--color-white);padding:var(--space-xs) var(--space-sm);border-radius:var(--radius-sm);cursor:pointer;font-size:1.2rem;color:var(--color-ink)}
main{padding-bottom:0}
.container{max-width:var(--container-max);margin:0 auto;padding:0 var(--space-xl)}
.section{padding:var(--space-section) 0}
.home-hero{position:relative;min-height:90vh;padding:72px 0;background:var(--color-canvas);color:var(--color-ink);overflow:hidden}
.home-hero::before{content:"";position:absolute;inset:0;background:radial-gradient(50% 50% at 15% 20%,var(--gradient-mint) 0%,transparent 60%),radial-gradient(45% 45% at 82% 15%,var(--gradient-peach) 0%,transparent 55%),radial-gradient(40% 40% at 75% 85%,var(--gradient-lavender) 0%,transparent 55%);pointer-events:none;z-index:1}
.eyebrow{font-size:0.75rem;text-transform:uppercase;letter-spacing:0.12em;color:var(--color-text-muted);margin-bottom:var(--space-md);display:block;font-weight:600}
.home-hero h1{font-family:var(--font-display);font-weight:300;font-size:clamp(2.8rem,5vw,4rem);line-height:1.05;color:var(--color-ink);letter-spacing:-1.92px;margin:0 0 var(--space-lg)}
.lede{font-size:1.1rem;line-height:1.6;color:var(--color-text-muted);margin-bottom:var(--space-xl);max-width:560px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:var(--space-xs);padding:0.5rem 1.25rem;border-radius:var(--radius-full);font-weight:500;font-size:0.875rem;line-height:1;cursor:pointer;border:0;text-decoration:none;white-space:nowrap;background:var(--color-surface);color:var(--color-ink);border:1px solid var(--color-border);min-height:36px}
.btn-primary{background:var(--color-primary);color:var(--color-white);border:none}
.btn-primary:hover{background:var(--color-primary-hover)}
.btn-accent{background:var(--color-primary);color:var(--color-white);border:none}
.btn-accent:hover{background:var(--color-primary-hover)}
.btn-outline{background:transparent;color:var(--color-ink);border:1px solid var(--color-border-dark,var(--color-border))}
.hero-actions{display:flex;gap:var(--space-md);flex-wrap:wrap}
.section-header{text-align:center;margin-bottom:var(--space-xxl)}
.section-title{font-family:var(--font-display);font-weight:300;font-size:clamp(1.8rem,3vw,2.25rem);color:var(--color-ink);line-height:1.17;margin:0}
.section-header .lede{color:var(--color-text-muted);max-width:560px;margin:var(--space-sm) auto 0;font-size:0.95rem}
.product-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:var(--space-xl)}
.product-card{background:var(--color-white);border:1px solid var(--color-border);border-radius:var(--radius-lg);overflow:hidden;transition:box-shadow 0.2s ease,border-color 0.2s ease;box-shadow:none}
.product-card:hover{box-shadow:var(--shadow-md);border-color:var(--color-border-dark)}
.product-card__image{position:relative;overflow:hidden;aspect-ratio:1;background:var(--color-surface)}
.product-card__image img{width:100%;height:100%;object-fit:cover;transition:transform 0.3s ease}
.product-card:hover .product-card__image img{transform:scale(1.05)}
.product-card__badge{position:absolute;top:var(--space-sm);left:var(--space-sm);padding:0.2rem 0.6rem;border-radius:var(--radius-full);font-size:0.65rem;font-weight:600;text-transform:uppercase}
.product-card__badge--sale{background:var(--color-error);color:var(--color-white)}
.product-card__body{padding:var(--space-md)}
.product-card h3{font-family:var(--font-display);font-weight:300;font-size:1.05rem;margin:0 0 var(--space-xs);color:var(--color-ink)}
.product-card__desc{font-size:0.8rem;color:var(--color-text-muted);margin-bottom:var(--space-sm);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.product-card__price-row{display:flex;align-items:center;gap:var(--space-xs);margin-bottom:var(--space-sm)}
.price{font-weight:700;color:var(--color-ink);font-size:1.1rem}
.old-price{text-decoration:line-through;color:var(--color-text-muted);font-size:0.85rem}
.product-card__actions{display:flex;gap:var(--space-xs);align-items:center}
.product-card__actions .btn{flex:1}
.product-card__stepper{height:28px;min-height:28px;border:1px solid var(--color-border);background:var(--color-surface-soft);border-radius:var(--radius-full);overflow:hidden}
.product-card__stepper form{display:flex;height:100%;margin:0}
.product-card__stepper button{width:28px;height:100%;border:0;background:transparent;color:var(--color-ink);cursor:pointer;font-size:0.85rem;line-height:1;padding:0}
.product-card__stepper button:disabled{color:var(--color-text-disabled);cursor:default;opacity:0.5}
.qty-input__value{min-width:20px;color:var(--color-ink);font-size:0.8rem;font-weight:600;text-align:center}
.category-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,220px));justify-content:center;gap:var(--space-xl);max-width:760px;margin:0 auto}
.category-card{cursor:pointer;transition:all 0.2s ease;text-align:center;text-decoration:none;color:var(--color-ink)}
.category-img-wrap{width:clamp(124px,14vw,178px);height:clamp(124px,14vw,178px);border-radius:50%;overflow:hidden;margin:0 auto var(--space-xs);border:3px solid var(--color-surface);box-shadow:var(--shadow-sm);background:radial-gradient(circle at 50% 50%,var(--gradient-mint),var(--gradient-lavender))}
.category-img-wrap img{width:100%;height:100%;object-fit:cover;border-radius:50%}
.category-card h3{font-family:var(--font-display);font-weight:300;font-size:1rem;margin:0;color:var(--color-ink)}
.category-card p{font-size:0.8rem;color:var(--color-text-muted);margin:var(--space-xs) 0 0}
.bottom-nav{display:none;position:fixed;bottom:0;left:0;right:0;background:rgba(245,245,245,0.95);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-top:1px solid var(--color-border);padding:var(--space-xs) 0;z-index:1000}
.nav-grid{display:grid;grid-template-columns:repeat(5,1fr);max-width:480px;margin:0 auto}
.nav-item{display:flex;flex-direction:column;align-items:center;padding:var(--space-xs) 0;color:var(--color-text-muted);font-size:0.6rem;text-decoration:none;min-height:48px;justify-content:center}
.nav-item .icon svg{width:20px;height:20px;margin-bottom:2px}
.support-fab{background:var(--color-primary);color:var(--color-white);box-shadow:var(--shadow-md)}
.site-footer{background:var(--color-surface);color:var(--color-text-muted);padding:var(--space-section) 5vw var(--space-xxl);margin-top:0;border-top:1px solid var(--color-border);font-size:0.85rem}
.footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:var(--space-xxl);margin-bottom:var(--space-xxl);max-width:var(--container-max);margin-left:auto;margin-right:auto}
.footer-brand{font-family:var(--font-display);font-weight:300;font-size:1.1rem;color:var(--color-ink);display:block;margin-bottom:var(--space-sm)}
.footer-desc{font-size:0.85rem;line-height:1.6;color:var(--color-text-muted)}
.footer-heading{font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--color-ink);font-weight:600;margin:0 0 var(--space-sm)}
.footer-links{list-style:none;padding:0;margin:0}
.footer-links li{margin-bottom:var(--space-xs)}
.footer-links a{color:var(--color-text-muted);text-decoration:none;font-size:0.85rem}
.footer-links a:hover{color:var(--color-ink)}
.footer-bottom{text-align:center;padding-top:var(--space-lg);border-top:1px solid var(--color-border-light);font-size:0.8rem;color:var(--color-text-soft);max-width:var(--container-max);margin:0 auto}
.flash{padding:var(--space-md);border-radius:var(--radius-md);margin-bottom:var(--space-md);font-size:0.85rem;font-weight:500}
.flash--success{background:var(--color-success-soft, #e6f9ee);color:var(--color-success)}
.flash--error{background:var(--color-error-soft, #fde8e9);color:var(--color-error)}
.flash--info{background:var(--color-info-soft, #ebf0fa);color:var(--color-info)}
@media(max-width:860px){
nav{display:none;position:absolute;top:100%;left:0;right:0;background:rgba(245,245,245,0.98);flex-direction:column;padding:var(--space-lg);border-bottom:1px solid var(--color-border);box-shadow:var(--shadow-md)}
nav a{font-size:0.95rem;padding:var(--space-sm) var(--space-md);border-radius:var(--radius-md);height:auto}
nav.open{display:flex}
.menu-toggle{display:block}
.home-hero{grid-template-columns:1fr;text-align:center;width:100vw;margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);padding:var(--space-xxl) var(--space-md) var(--space-lg)}
.hero-actions{justify-content:center}
.category-grid{grid-template-columns:repeat(2,minmax(0,128px));justify-content:center;gap:var(--space-md)}
.footer-grid{grid-template-columns:1fr 1fr}
.bottom-nav{display:block}
.main-content{padding-bottom:calc(60px + var(--space-md))}
.site-header{grid-template-columns:48px 1fr 48px;gap:0}
.site-header .brand{justify-self:start}
.site-header .menu-toggle{justify-self:center;width:46px;height:46px;display:inline-flex;align-items:center;justify-content:center}
.site-header .header-actions{justify-self:end}
.cart-btn{width:44px;height:44px;display:inline-flex;align-items:center;justify-content:center}
.brand span{display:none}
}
@media(max-width:480px){
.product-grid{grid-template-columns:1fr}
.footer-grid{grid-template-columns:1fr}
.hero-actions{flex-direction:column;align-items:center}
}
@media(max-width:743px){.site-header{min-height:64px;padding:0 12px}.section{padding:48px 0}.home-hero{min-height:auto;padding:48px 16px}.support-fab{position:absolute;top:10px;right:68px;bottom:auto;width:44px;height:44px;z-index:101}}
';
echo $critical;
?>
</style>
<link rel="stylesheet" href="/assets/css/band.css?v=<?= filemtime(__DIR__ . '/../../assets/css/band.css') ?>">
<style>
<?php
$__palette_semantic = [
    '--color-primary' => ['set' => ($__settings['palette_primary'] ?? '#292524'), 'alias' => ''],
    '--color-accent' => ['set' => ($__settings['palette_secondary'] ?? '#292524'), 'alias' => ''],
    '--color-canvas' => ['set' => ($__settings['palette_canvas'] ?? '#f5f5f5'), 'alias' => '--color-bg'],
    '--color-text-primary' => ['set' => ($__settings['palette_text'] ?? '#0c0a09'), 'alias' => '--color-ink'],
    '--color-link' => ['set' => ($__settings['palette_link'] ?? '#0c0a09'), 'alias' => ''],
];
$__palette_css = ':root{';
foreach ($__palette_semantic as $__n => $__c) {
    $__v = $__c['set'];
    $__palette_css .= $__n . ':' . e($__v) . ';';
    if ($__c['alias'] !== '') $__palette_css .= $__c['alias'] . ':' . e($__v) . ';';
}
$__palette_css .= '}';
echo $__palette_css;
?>
</style>
<style>
.document-page__header{max-width:760px;margin:0 auto var(--space-2xl);text-align:center}.document-page__content{max-width:760px;margin:0 auto;padding:clamp(24px,4vw,48px);border:1px solid var(--color-border);border-radius:var(--radius-lg);background:var(--color-white);box-shadow:var(--shadow-sm)}.document-page__content h2{margin:2rem 0 .65rem;color:var(--color-maroon);font-size:1.35rem}.document-page__content h2:first-child{margin-top:0}.document-page__content p,.document-page__content li{color:var(--color-text-muted);line-height:1.7}.document-page__content ul{padding-left:1.25rem}.document-index{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:var(--space-md)}.document-index__item{display:grid;gap:var(--space-xs);padding:var(--space-lg);border:1px solid var(--color-border);border-radius:var(--radius-md);background:var(--color-white);color:inherit;text-decoration:none;box-shadow:var(--shadow-sm);transition:transform var(--transition-base),box-shadow var(--transition-base),border-color var(--transition-base)}.document-index__item:hover{transform:translateY(-3px);border-color:var(--color-gold);box-shadow:var(--shadow-md)}.document-index__item h2{margin:0;color:var(--color-maroon);font-size:1.15rem}.document-index__item p{margin:0;color:var(--color-text-muted);line-height:1.55}.document-index__link{color:var(--color-maroon);font-weight:700;font-size:.82rem;margin-top:var(--space-sm)}
.blog-page{padding:var(--space-3xl) 0}.blog-page .container{max-width:1180px}.page-title{margin:0 0 var(--space-lg);font-size:clamp(1.8rem,4vw,2.6rem);color:var(--color-ink)}.blog-categories{display:flex;flex-wrap:wrap;gap:var(--space-sm);margin-bottom:var(--space-2xl)}.blog-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:var(--space-xl)}.blog-card{overflow:hidden;border:1px solid var(--color-border);border-radius:var(--radius-md);background:var(--color-white);box-shadow:var(--shadow-sm)}.blog-card__body{display:grid;gap:var(--space-sm);padding:var(--space-lg)}.blog-card__title{margin:0;font-size:1.15rem;line-height:1.3}.blog-card__title a{color:var(--color-maroon)}.blog-card__excerpt{margin:0;color:var(--color-text-muted);line-height:1.55}.blog-card__date{font-size:.8rem;color:var(--color-text-soft)}.blog-card__category{font-size:.72rem;color:var(--color-maroon);font-weight:700;text-transform:uppercase}
@media(max-width:900px){.blog-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:600px){.document-index,.blog-grid{grid-template-columns:1fr}.blog-page{padding:var(--space-2xl) 0}}
</style>
<noscript><link rel="stylesheet" href="/assets/css/band.css?v=<?= filemtime(__DIR__ . '/../../assets/css/band.css') ?>"></noscript>
<?php $__secrets_org = (new \App\Services\SecretService())->all(); $__phone = $__secrets_org['phone'] ?? ''; $__telephone = $__phone !== '' ? '["' . e($__phone) . '"]' : '["+919790221065"]'; ?>
<script type="application/ld+json">
{"@context":"https://schema.org","@type":["Organization","CollegeOrUniversity"],"name":"Aura Medical Institute of Electropathy and Hospital","description":"Aura Medical Institute of Electropathy and Hospital — electropathy, acupuncture, and allied-health healthcare-skilling in Coimbatore.","url":"https://<?= e($_SERVER['HTTP_HOST']) ?>","telephone":<?= $__telephone ?>,"email":"auramieh2017@gmail.com"}
</script>
<?php if (!empty($seo['json_ld'])): ?><?= $seo['json_ld'] ?><?php endif; ?>
<?php
$__secrets = (new \App\Services\SecretService())->all();
// Meta Pixel
$__metaPixelId = $__secrets['meta_pixel_id'] ?? '';
if ($__metaPixelId !== ''):
?>
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '<?= e($__metaPixelId) ?>');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=<?= e($__metaPixelId) ?>&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
<?php endif;
// Google Site Kit
$__gaId = $__secrets['google_analytics_id'] ?? '';
$__adsId = $__secrets['google_ads_id'] ?? '';
$__gsv = $__secrets['google_site_verification'] ?? '';
$__gtagSrc = $__gaId ?: $__adsId;
if ($__gsv !== ''): ?>
<meta name="google-site-verification" content="<?= e($__gsv) ?>" />
<?php endif;
if ($__gtagSrc !== ''): ?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($__gtagSrc) ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
<?php if ($__gaId !== ''): ?>gtag('config', '<?= e($__gaId) ?>');<?php endif; ?>
<?php if ($__adsId !== ''): ?>gtag('config', '<?= e($__adsId) ?>');<?php endif; ?>
</script>
<?php endif; ?>
</head>
<body>
<?php $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'; ?>
<?php $_SESSION['csrf_token'] ??= bin2hex(random_bytes(16)); ?>
<header class="site-header" id="site-header">
    <a href="/" class="brand"><img src="<?= e($__logo) ?>" width="52" height="52" alt="AuraEdu logo"><span>AuraEdu</span></a>
    <button class="menu-toggle" type="button" aria-expanded="false" aria-label="Menu">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
<?php try { $__blogCats = (new \App\Services\BlogService())->categories(); } catch (\Throwable $e) { $__blogCats = []; } ?>
    <nav id="primary-nav">
        <a href="/education"<?= $currentPath === '/education' ? ' aria-current="page"' : '' ?>>B.E.M.S.</a>
        <div class="nav-dropdown">
            <a href="/courses" class="nav-dropdown__trigger"<?= str_starts_with($currentPath, '/courses') ? ' aria-current="page"' : '' ?>>Courses <span class="nav-dropdown__arrow">▾</span></a>
            <div class="nav-dropdown__menu">
                <a href="/courses/bems">B.E.M.S. — Electropathy</a>
                <a href="/courses/mdeh">M.D.E.H.</a>
                <a href="/courses/dacu">D.Acu — Acupuncture</a>
                <a href="/courses/macu">M.Acu — Acupuncture</a>
                <a href="/courses/dhm">D.H.M. &amp; C.T. — Hotel Mgmt</a>
            </div>
        </div>
        <div class="nav-dropdown">
            <a href="#" class="nav-dropdown__trigger"<?= str_starts_with($currentPath, '/eligibility') || str_starts_with($currentPath, '/scope') ? ' aria-current="page"' : '' ?>>Admissions <span class="nav-dropdown__arrow">▾</span></a>
            <div class="nav-dropdown__menu">
                <a href="/eligibility">Eligibility &amp; Requirements</a>
                <a href="/scope">Career Scope</a>
            </div>
        </div>
        <a href="/gallery"<?= str_starts_with($currentPath, '/gallery') ? ' aria-current="page"' : '' ?>>Gallery</a>
        <a href="/shop"<?= str_starts_with($currentPath, '/shop') ? ' aria-current="page"' : '' ?>>Shop</a>
        <a href="/consult"<?= str_starts_with($currentPath, '/consult') ? ' aria-current="page"' : '' ?>>Therapies</a>
        <a href="/hospitals"<?= str_starts_with($currentPath, '/hospitals') ? ' aria-current="page"' : '' ?>>Hospital</a>
        <div class="nav-dropdown">
            <a href="/blog" class="nav-dropdown__trigger"<?= str_starts_with($currentPath, '/blog') ? ' aria-current="page"' : '' ?>>Blog <span class="nav-dropdown__arrow">▾</span></a>
            <div class="nav-dropdown__menu">
                <a href="/blog">All Posts</a>
                <?php foreach ($__blogCats as $__cat): ?>
                <a href="/blog/category/<?= e($__cat['slug'] ?? '') ?>"><?= e($__cat['name'] ?? '') ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="nav-dropdown">
            <a href="/about" class="nav-dropdown__trigger"<?= str_starts_with($currentPath, '/about') || str_starts_with($currentPath, '/faculty') ? ' aria-current="page"' : '' ?>>About <span class="nav-dropdown__arrow">▾</span></a>
            <div class="nav-dropdown__menu">
                <a href="/about">About Institute</a>
                <a href="/faculty">Faculty &amp; Administration</a>
            </div>
        </div>
        <a href="/contact"<?= str_starts_with($currentPath, '/contact') ? ' aria-current="page"' : '' ?>>Contact</a>
        <?php if(!empty($_SESSION['user'])): ?>
            <a href="/account/dashboard"<?= str_starts_with($currentPath, '/account/dashboard') ? ' aria-current="page"' : '' ?>>Dashboard</a>
            <a href="/logout">Logout</a>
        <?php else: ?>
            <a href="/login">Login</a>
        <?php endif; ?>
    </nav>
    <div class="header-actions">
        <a href="/cart" class="cart-btn" aria-label="Cart">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
            <?php $cartCount = 0; if(!empty($_SESSION['cart'])){foreach($_SESSION['cart'] as $c){$cartCount += $c['qty'] ?? 1;}} ?><span class="cart-count"><?= $cartCount ?></span>
        </a>
    </div>
</header>
<main class="<?= $currentPath === '/' ? 'home-main' : '' ?>">
<?php
$__flash = $_SESSION['flash'] ?? null;
if ($__flash):
    $__msg = is_array($__flash) ? $__flash['message'] : $__flash;
    $__type = is_array($__flash) ? ($__flash['type'] ?? 'info') : 'info';
    unset($_SESSION['flash']);
?>
<script>document.addEventListener('DOMContentLoaded',function(){showToast(<?= json_encode($__msg) ?>,<?= json_encode($__type) ?>);});</script>
<?php endif; ?>
<?php require $viewFile; ?>
</main>

<?php if (!in_array($currentPath, ['/login', '/register'])): ?>
<nav class="bottom-nav" id="bottom-nav">
    <div class="nav-grid">
        <a href="/courses" class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], '/courses') === 0 ? 'active' : '') ?>">
            <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            <span>Courses</span>
        </a>
        <a href="/shop" class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], '/shop') === 0 ? 'active' : '') ?>">
            <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            <span>Shop</span>
        </a>
        <a href="/consult" class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], '/consult') === 0 ? 'active' : '') ?>">
            <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 000 20 14.5 14.5 0 000-20"/><path d="M2 12h20"/></svg>
            <span>Therapies</span>
        </a>
        <a href="/hospitals" class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], '/hospitals') === 0 ? 'active' : '') ?>">
            <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4 8 4v14"/><path d="M9 21v-4a2 2 0 012-2h2a2 2 0 012 2v4"/></svg>
            <span>Hospital</span>
        </a>
        <a href="/blog" class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], '/blog') === 0 ? 'active' : '') ?>">
            <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            <span>Blog</span>
        </a>
        <a href="/cart" class="nav-item <?= (strpos($_SERVER['REQUEST_URI'], '/cart') === 0 ? 'active' : '') ?>">
            <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
            <span>Cart</span>
        </a>
    </div>
</nav>
<?php endif; ?>

<?php if ($currentPath !== '/cart'): ?>
<a href="/cart" class="mobile-cart-tray" id="mobile-cart-tray" <?= $cartCount > 0 ? '' : 'hidden' ?>>
    <span><strong id="mobile-cart-count"><?= (int)$cartCount ?></strong> <span id="mobile-cart-label"><?= $cartCount === 1 ? 'item' : 'items' ?></span> in cart</span>
    <span>View cart <span aria-hidden="true">→</span></span>
</a>
<?php endif; ?>

<?php if (!in_array($currentPath, ['/login', '/register'])): ?>
<button class="support-fab" type="button" aria-controls="support-panel" aria-expanded="false" title="Support">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/></svg>
    <span class="sr-only">Support</span>
</button>
<section class="support-panel" id="support-panel" data-support-key="auraedu-support-chat-<?= !empty($_SESSION['user']['email']) ? e(strtolower((string)$_SESSION['user']['email'])) : 'guest' ?>" hidden>
    <div class="support-panel__head">
        <strong>Support</strong>
        <button type="button" class="support-panel__close" aria-label="Close support">×</button>
    </div>
    <div class="support-panel__body" id="support-log" aria-live="polite">
        <p>Ask about products, orders, delivery addresses, or consultant bookings.</p>
        <?php if(empty($_SESSION['user'])): ?><p>Sign in to ask about your personal order or session data.</p><?php endif; ?>
    </div>
    <form class="support-panel__form" id="support-form">
        <textarea name="message" rows="3" required placeholder="Ask about a product, order, address, or booking"></textarea>
        <button class="btn btn-primary btn-sm">Send</button>
    </form>
</section>
<?php endif; ?>

<?php if (!in_array($currentPath, ['/login', '/register'])): ?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <span class="footer-brand">AuraEdu</span>
                <p class="footer-desc">Aura Medical Institute of Electropathy and Hospital — B.E.M.S. medical education, hospital care, acupuncture, and allied-health therapies in Coimbatore.</p>
            </div>
            <div>
                <h4 class="footer-heading">Institute</h4>
                <ul class="footer-links">
                    <li><a href="/courses">All Courses</a></li>
                    <li><a href="/courses/bems">B.E.M.S. Admissions</a></li>
                    <li><a href="/eligibility">Eligibility</a></li>
                    <li><a href="/scope">Career Scope</a></li>
                    <li><a href="/gallery">Gallery</a></li>
                    <li><a href="/faculty">Faculty &amp; Administration</a></li>
                    <li><a href="/about">About</a></li>
                    <li><a href="/contact">Contact</a></li>
                    <li><a href="/terms">Terms</a></li>
                    <li><a href="/privacy">Privacy</a></li>
                </ul>
            </div>
            <div>
                <h4 class="footer-heading">Care &amp; Training</h4>
            <ul class="footer-links">
                <li><a href="/consult">Therapies</a></li>
                <li><a href="/hospitals">Hospital</a></li>
                <li><a href="/education">Admissions</a></li>
                <li><a href="/blog">Blog</a></li>
                <?php foreach ($__blogCats as $__cat): ?>
                <li><a href="/blog/category/<?= e($__cat['slug'] ?? '') ?>"><?= e($__cat['name'] ?? '') ?></a></li>
                <?php endforeach; ?>
                <li><a href="/contact">Contact</a></li>
            </ul>
            </div>
            <div>
                <h4 class="footer-heading">Customer Support</h4>
                <ul class="footer-links">
                    <li><a href="tel:+919790221065">+91 97902 21065</a></li>
                    <li><a href="tel:+919789444038">+91 97894 44038</a></li>
                    <li><a href="mailto:auramieh2017@gmail.com">auramieh2017@gmail.com</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">&copy; <?= date('Y') ?> AuraEdu &middot; <a href="/terms">Terms</a> &middot; <a href="/privacy">Privacy</a> &middot; developed with &#10084;&#65039; by <a href="https://www.instagram.com/bapxmediahub/" target="_blank" rel="noopener noreferrer">@bapxmediahub</a></div>
    </div>
</footer>
<?php endif; ?>
<script>
if ('serviceWorker' in navigator) {
    var swPath = '/sw.js';
    var swScope = '/';
    navigator.serviceWorker.register(swPath, { scope: swScope }).catch(function(){});
}
document.getElementById('site-header').querySelector('.menu-toggle').addEventListener('click',function(){
    var n=document.getElementById('primary-nav');n.classList.toggle('open');
    this.setAttribute('aria-expanded',n.classList.contains('open')?'true':'false');
});
document.addEventListener('click',function(e){
    var n=document.getElementById('primary-nav'),t=document.querySelector('.menu-toggle');
    if(!n.contains(e.target)&&!t.contains(e.target)){n.classList.remove('open');t.setAttribute('aria-expanded','false');}
});
var h=document.getElementById('site-header');
var s=document.createElement('div');s.style.cssText='height:1px;position:absolute;top:0';
document.body.prepend(s);
new IntersectionObserver(function(e){h.classList.toggle('scrolled',!e[0].isIntersecting);},{threshold:0}).observe(s);
var io=new IntersectionObserver(function(entries){entries.forEach(function(e){if(e.isIntersecting){e.target.classList.add('revealed');io.unobserve(e.target);}});},{threshold:0.1,rootMargin:'0px 0px -50px 0px'});
document.querySelectorAll('.reveal,.panel,.product-card,.astrologer-card').forEach(function(el){io.observe(el);});
var supportFab=document.querySelector('.support-fab'),supportPanel=document.getElementById('support-panel'),supportClose=document.querySelector('.support-panel__close'),supportForm=document.getElementById('support-form'),supportLog=document.getElementById('support-log');
function supportEscape(value){return String(value).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c];});}
function supportReplyHtml(value){var safe=supportEscape(value);var allowed=/\/(?:shop|cart|checkout|consult|temples|contact|blog(?:\/[a-z0-9-]+|\/category\/[a-z0-9-]+)?|product\/[a-z0-9-]+|account\/dashboard(?:\/orders|\/sessions|\/install)?)(?=$|[\s.,)])/g;return safe.replace(allowed,function(path){return '<a class="support-action" href="'+path+'">Open '+supportEscape(path.replace(/^\//,'').replace(/[-/]/g,' '))+'</a>';});}
function supportActionsHtml(actions){if(!actions||!actions.length)return'';var h='<div class="support-actions">';for(var i=0;i<actions.length;i++){var a=actions[i];if(a.type==='navigate'&&a.path){h+='<a class="btn btn-sm btn-outline support-action-btn" href="'+supportEscape(a.path)+'">'+supportEscape(a.label)+'</a>';}}return h+'</div>';}
function supportToggle(open){if(!supportPanel||!supportFab)return;supportPanel.hidden=!open;supportFab.setAttribute('aria-expanded',open?'true':'false');}
function supportSaveLog(){try{if(!supportPanel||!supportLog)return;sessionStorage.setItem(supportPanel.dataset.supportKey,supportLog.innerHTML);}catch(e){}}
function supportLoadLog(){try{if(!supportPanel||!supportLog)return;var saved=sessionStorage.getItem(supportPanel.dataset.supportKey);if(saved){supportLog.innerHTML=saved;}}catch(e){}}
if(supportFab&&supportPanel){supportLoadLog();
supportFab.addEventListener('click',function(){supportToggle(supportPanel.hidden);});
supportClose.addEventListener('click',function(){supportToggle(false);});}
if(supportForm){supportForm.addEventListener('submit',async function(e){e.preventDefault();var data=new FormData(supportForm),msg=data.get('message');if(supportLog){supportLog.insertAdjacentHTML('beforeend','<p><strong>You:</strong> '+supportEscape(msg)+'</p>');}supportSaveLog();supportForm.reset();try{var r=await fetch('/support/ask',{method:'POST',body:data});var j=await r.json();if(supportLog){supportLog.insertAdjacentHTML('beforeend','<p><strong>Support:</strong> '+supportReplyHtml(j.reply||j.error||'Unable to answer right now.')+'</p>'+(j.actions?supportActionsHtml(j.actions):''));}}catch(err){if(supportLog){supportLog.insertAdjacentHTML('beforeend','<p><strong>Support:</strong> Unable to answer right now.</p>');}}supportSaveLog();if(supportLog){supportLog.scrollTop=supportLog.scrollHeight;}});}
function showToast(msg,type){type=type||'info';var c=document.getElementById('toast-container');if(!c){c=document.createElement('div');c.id='toast-container';document.body.appendChild(c);}var t=document.createElement('div');t.className='toast toast--'+type;var icons={success:'✓',error:'✕',warning:'⚠',info:'ℹ'};t.innerHTML='<span class="toast__icon">'+(icons[type]||'ℹ')+'</span><span class="toast__text">'+msg+'</span><button class="toast__close" aria-label="Dismiss">&times;</button>';t.querySelector('.toast__close').addEventListener('click',function(e){e.stopPropagation();dismiss(t);});t.addEventListener('click',function(){dismiss(t);});c.appendChild(t);var timer=setTimeout(function(){dismiss(t);},4000);function dismiss(el){if(el.classList.contains('toast--out'))return;el.classList.add('toast--out');clearTimeout(timer);setTimeout(function(){if(el.parentNode)el.parentNode.removeChild(el);},250);}}
document.addEventListener('submit',async function(event){
    var form=event.target.closest('.product-card__stepper form');if(!form||!window.fetch)return;
    event.preventDefault();var button=form.querySelector('button');button.disabled=true;
    try{var response=await fetch(form.getAttribute('action'),{method:'POST',body:new FormData(form),headers:{Accept:'application/json'}});var data=await response.json();if(!response.ok)throw new Error(data.error||'Unable to update cart.');var stepper=form.closest('.product-card__stepper'),value=stepper.querySelector('.qty-input__value'),minus=stepper.querySelector('form[action="/cart/update"] button'),badge=document.querySelector('.cart-count'),tray=document.getElementById('mobile-cart-tray'),trayCount=document.getElementById('mobile-cart-count'),trayLabel=document.getElementById('mobile-cart-label');value.textContent=data.quantity;if(minus)minus.disabled=data.quantity<=0;if(badge)badge.textContent=data.cart_count;if(tray){tray.hidden=data.cart_count<=0;if(trayCount)trayCount.textContent=data.cart_count;if(trayLabel)trayLabel.textContent=data.cart_count===1?'item':'items';}}
    catch(error){showToast(error.message,'error');}finally{button.disabled=form.getAttribute('action')==='/cart/update'&&Number(form.closest('.product-card__stepper').querySelector('.qty-input__value').textContent)<=0;}
});
</script>
<div id="toast-container" role="alert" aria-live="polite"></div>
</body>
</html>
