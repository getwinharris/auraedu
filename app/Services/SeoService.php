<?php
namespace App\Services;
final class SeoService {
    private string $siteName;
    private string $defaultOgImage;
    private string $twitterHandle;
    private array $telephone;

    public function __construct(array $secrets = []) {
        $envName = getenv('APP_NAME') ?: 'AuraEdu';
        $this->siteName = $secrets['seo_site_name'] ?? $envName;
        $this->defaultOgImage = $secrets['seo_default_og_image'] ?? 'https://' . ($_SERVER['HTTP_HOST'] ?? 'auraedu.co.in') . '/assets/images/og-image.jpg';
        $this->twitterHandle = $secrets['seo_twitter_handle'] ?? '';
        $phone = $secrets['phone'] ?? getenv('CONTACT_PHONE') ?: '';
        $this->telephone = $phone !== '' ? [$phone] : ['+919790221065'];
    }

    public function page(string $key, array $overrides = []): array {
        $host = $_SERVER['HTTP_HOST'] ?? 'auraedu.co.in';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $url = $scheme . '://' . $host . $uri;

        $defaults = $this->defaults($key);
        $meta = array_merge($defaults, $overrides);

        if (!empty($overrides['keywords'])) {
            $meta['keywords'] = $overrides['keywords'];
        } elseif (!isset($meta['keywords'])) {
            $meta['keywords'] = $defaults['keywords'] ?? '';
        }

        $meta['canonical'] ??= $url;
        $meta['og_url'] ??= $url;
        $meta['og_site_name'] = $this->siteName;
        $meta['og_image'] ??= $this->defaultOgImage;
        $meta['twitter_image'] ??= $meta['og_image'];
        $meta['twitter_title'] ??= $meta['og_title'] ?? $meta['title'];
        $meta['twitter_description'] ??= $meta['og_description'] ?? $meta['description'];
        $meta['og_title'] ??= $meta['title'];
        $meta['og_description'] ??= $meta['description'];

        return $meta;
    }

    private function defaults(string $key): array {
        $brand = 'Aura Medical Institute of Electropathy and Hospital';
        $short = 'Aura Medical Institute';
        $desc = 'Aura Medical Institute of Electropathy and Hospital — electropathy, acupuncture, and allied-health healthcare-skilling in Coimbatore. B.E.M.S., M.D.E.H., D.Acu, M.Acu and Hotel Management courses. Admissions open.';
        $maps = [
            'home' => [
                'title' => $short . ' — Electropathy, Acupuncture & Allied-Health Courses',
                'description' => $desc,
                'og_type' => 'website',
                'robots' => 'index, follow',
            ],
            'shop' => [
                'title' => 'Therapy & Acupuncture Products – ' . $short,
                'description' => 'Clinically oriented acupuncture and electropathy therapy products from ' . $short . ', Coimbatore.',
                'og_type' => 'website',
                'robots' => 'index, follow',
            ],
            'product' => [
                'title' => 'Therapy Products Online',
                'description' => 'Browse our collection of acupuncture and electropathy therapy products.',
                'og_type' => 'product',
                'robots' => 'index, follow',
            ],
            'therapies' => [
                'title' => 'Therapies & Hospital Services – ' . $short,
                'description' => 'Explore therapy and hospital services at ' . $short . ', Coimbatore — clinical training, acupuncture, and allied-health care.',
                'og_type' => 'website',
                'robots' => 'index, follow',
            ],
            'hospital' => [
                'title' => 'Hospital Facilities – ' . $short,
                'description' => 'Learn about hospital facilities and patient care at ' . $short . ', Coimbatore.',
                'og_type' => 'website',
                'robots' => 'index, follow',
            ],
            'about' => [
                'title' => 'About ' . $brand . ' – Coimbatore\'s Medical Education & Hospital',
                'description' => 'Learn about ' . $brand . ', Coimbatore\'s trusted destination for electropathy medical education, hospital care, and wellness products.',
                'og_type' => 'website',
                'robots' => 'index, follow',
            ],
            'contact' => [
                'title' => 'Contact ' . $brand . ' – Medical Education & Hospital',
                'description' => 'Reach out to ' . $brand . ' for inquiries about education programmes, hospital services, or bulk orders. Call or email us.',
                'og_type' => 'website',
                'robots' => 'index, follow',
            ],
            'education' => [
                'title' => $short . ' — B.E.M.S. Electropathy Programme',
                'description' => 'B.E.M.S. (Bachelor of Electro-Medical Sciences) at ' . $short . ', Coimbatore — electropathy medical education with hospital training. No NEET, no age bar.',
                'og_type' => 'website',
                'robots' => 'index, follow',
            ],
            'courses' => [
                'title' => 'Courses Offered — ' . $short,
                'description' => 'Electropathy, acupuncture, and allied-health programmes at ' . $short . ', Coimbatore — B.E.M.S., M.D.E.H., D.Acu, M.Acu, and Hotel Management. Admissions open.',
                'og_type' => 'website',
                'robots' => 'index, follow',
            ],
            'course' => [
                'title' => 'Course — ' . $short,
                'description' => 'Programme details, duration, and eligibility at ' . $short . ', Coimbatore.',
                'og_type' => 'website',
                'robots' => 'index, follow',
            ],
            'eligibility' => [
                'title' => 'Eligibility & Admissions — ' . $short,
                'description' => 'Admissions at ' . $short . ': no NEET, no age bar. Documents, eligibility, and how to apply for B.E.M.S. and allied-health courses.',
                'og_type' => 'website',
                'robots' => 'index, follow',
            ],
            'scope' => [
                'title' => 'Career Scope — ' . $short,
                'description' => 'Career pathways after electropathy, acupuncture, and allied-health training at ' . $short . ', Coimbatore.',
                'og_type' => 'website',
                'robots' => 'index, follow',
            ],
            'gallery' => [
                'title' => 'Gallery — ' . $short,
                'description' => 'Campus, hospital, therapy practice, and student life at ' . $short . ', Coimbatore.',
                'og_type' => 'website',
                'robots' => 'index, follow',
            ],
            'faculty' => [
                'title' => 'Faculty & Administration — ' . $short,
                'description' => 'Meet the faculty and administration behind ' . $short . ', Coimbatore.',
                'og_type' => 'website',
                'robots' => 'index, follow',
            ],
            'cart' => [
                'title' => 'Shopping Cart – ' . $brand,
                'description' => 'Review your shopping cart at ' . $brand . '. Proceed to checkout for quality education products and wellness items.',
                'og_type' => 'website',
                'robots' => 'noindex, follow',
            ],
            'checkout' => [
                'title' => 'Checkout – ' . $brand,
                'description' => 'Complete your purchase at ' . $brand . '. Secure payment for education products and wellness items.',
                'og_type' => 'website',
                'robots' => 'noindex, follow',
            ],
            'privacy' => [
                'title' => 'Privacy Policy – ' . $brand,
                'description' => 'Read the privacy policy of ' . $brand . '. Learn how we collect, use, and protect your personal information when you use our education programmes and hospital services.',
                'og_type' => 'website',
                'robots' => 'index, follow',
            ],
            'terms' => [
                'title' => 'Terms & Conditions – ' . $brand,
                'description' => 'Read the terms and conditions of ' . $brand . '. Understand the guidelines for using our education programmes and purchasing products.',
                'og_type' => 'website',
                'robots' => 'index, follow',
            ],
            'login' => [
                'title' => 'Sign In – ' . $brand,
                'description' => 'Sign in to your ' . $brand . ' account to manage orders, saved addresses, and appointments.',
                'og_type' => 'website',
                'robots' => 'noindex, follow',
            ],
            'register' => [
                'title' => 'Create Account – ' . $brand,
                'description' => 'Create your ' . $brand . ' account to save addresses, order products, and book appointments.',
                'og_type' => 'website',
                'robots' => 'noindex, follow',
            ],
            'forgot-password' => [
                'title' => 'Forgot Password – ' . $brand,
                'description' => 'Reset your ' . $brand . ' account password.',
                'og_type' => 'website',
                'robots' => 'noindex, follow',
            ],
            'reset-password' => [
                'title' => 'Reset Password – ' . $brand,
                'description' => 'Reset your ' . $brand . ' account password.',
                'og_type' => 'website',
                'robots' => 'noindex, follow',
            ],
            'account' => [
                'title' => 'My Account – ' . $brand,
                'description' => 'Manage your ' . $brand . ' account, view orders and track appointments.',
                'og_type' => 'website',
                'robots' => 'noindex, nofollow',
            ],
            'blog' => [
                'title' => 'Blog & Updates – ' . $brand,
                'description' => 'Read the latest blog posts, feature updates, and education guides from ' . $brand . '.',
                'og_type' => 'website',
                'robots' => 'index, follow',
                'keywords' => 'electropathy, medical education, acupuncture, allied health, BEMS',
            ],
            'blog.post' => [
                'title' => 'Blog Post – ' . $brand,
                'description' => 'Read articles, guides, and updates from ' . $brand . '.',
                'og_type' => 'article',
                'robots' => 'index, follow',
                'keywords' => 'electropathy, medical education, acupuncture, allied health',
            ],
            'blog.category' => [
                'title' => 'Blog Category – ' . $brand,
                'description' => 'Browse blog articles by category at ' . $brand . '.',
                'og_type' => 'website',
                'robots' => 'index, follow',
            ],
            'admin' => [
                'title' => 'Admin Panel – ' . $brand,
                'description' => '',
                'robots' => 'noindex, nofollow',
            ],
        ];
        return $maps[$key] ?? [
            'title' => $brand,
            'description' => $desc,
            'og_type' => 'website',
            'robots' => 'index, follow',
        ];
    }

    public function jsonLdScript(array $data): string {
        if (!$data) return '';
        return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }

    public function organizationSchema(): array {
        return [
            '@context' => 'https://schema.org',
            '@type' => ['Organization', 'OnlineStore'],
            'name' => $this->siteName,
            'description' => 'Aura Medical Institute of Electropathy and Hospital — electropathy, acupuncture, and allied-health healthcare-skilling in Coimbatore.',
            'url' => $this->pageUrl(''),
            'telephone' => $this->telephone,
            'email' => 'auramieh2017@gmail.com',
        ];
    }

    public function breadcrumbSchema(array $items): array {
        $itemList = [];
        $position = 1;
        foreach ($items as $item) {
            $entry = ['@type' => 'ListItem', 'position' => $position++];
            if (is_string($item)) {
                $entry['name'] = $item;
                $entry['item'] = $this->pageUrl($item === 'Home' ? '' : strtolower(str_replace(' ', '-', $item)));
            } else {
                $entry['name'] = $item['name'] ?? '';
                $entry['item'] = $item['url'] ?? '';
            }
            $itemList[] = $entry;
        }
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemList,
        ];
    }

    public function productSchema(array $product): array {
        $price = $product['offer_price'] ?? $product['price'] ?? 0;
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product['name'] ?? '',
            'description' => $product['description'] ?? '',
            'image' => $product['image_url'] ?? $this->defaultOgImage,
            'offers' => [
                '@type' => 'Offer',
                'price' => (float)$price,
                'priceCurrency' => 'INR',
                'availability' => 'https://schema.org/InStock',
                'url' => $this->pageUrl('/product/' . ($product['slug'] ?? '')),
            ],
        ];
    }

    public function aboutPageSchema(): array {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'AboutPage',
            'name' => 'About ' . $this->siteName,
            'description' => 'Learn about ' . $this->siteName . ', Coimbatore\'s trusted destination for electropathy medical education and hospital care.',
            'mainEntity' => $this->organizationSchema(),
        ];
    }

    public function contactPageSchema(): array {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ContactPage',
            'name' => 'Contact ' . $this->siteName,
            'description' => 'Get in touch with ' . $this->siteName . ' for education programmes, hospital services, and wellness products.',
            'mainEntity' => $this->organizationSchema(),
        ];
    }

    public function faqPageSchema(array $questions): array {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(function ($q) {
                return [
                    '@type' => 'Question',
                    'name' => $q['question'] ?? '',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $q['answer'] ?? '',
                    ],
                ];
            }, $questions),
        ];
    }

    private function pageUrl(string $path): string {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'auraedu.co.in';
        return $scheme . '://' . $host . '/' . ltrim($path, '/');
    }
}
