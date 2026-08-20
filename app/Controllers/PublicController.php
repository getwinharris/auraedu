<?php
namespace App\Controllers;
use App\Services\{BlogService,ProductService,CategoryService,SecretService,SeoService,ContactService,ReviewService,MarkdownRenderer,TempleService,CourseService};
final class PublicController extends BaseController {
    
    public function home(): void {
        $this->detectApiRequest();
        $this->seoKey = 'home';
        try { $categories = (new CategoryService())->all(); } catch (\Throwable $e) { $categories = []; }
        try { $products = (new ProductService())->all(); } catch (\Throwable $e) { $products = []; }
        try { $temples = (new TempleService())->all(); } catch (\Throwable $e) { $temples = []; }
        $this->render('public/home', [
            'products' => $products,
            'temples' => $temples,
            'categories' => $categories,
        ]);
    }
    
    public function about(): void { 
        $this->detectApiRequest();
        $this->seoKey = 'about';
        $this->render('public/about'); 
    }

    public function education(): void {
        $this->detectApiRequest();
        $this->seoKey = 'education';
        $this->render('public/education');
    }

    public function courses(): void {
        $this->detectApiRequest();
        $this->seoKey = 'courses';
        $this->render('public/courses', ['courses' => (new CourseService())->all()]);
    }

    public function course(string $slug): void {
        $this->detectApiRequest();
        $all = (new CourseService())->all();
        $course = $all[$slug] ?? null;
        if (!$course) {
            http_response_code(404);
            $this->render('public/404');
            return;
        }
        $this->seoKey = 'course';
        $this->seoOverrides = [
            'title' => $course['title'] . ' — ' . $course['short'],
            'description' => $course['lede'],
        ];
        $this->render('public/course', ['course' => $course, 'courses' => $all]);
    }

    public function eligibility(): void {
        $this->detectApiRequest();
        $this->seoKey = 'eligibility';
        $this->render('public/eligibility');
    }

    public function scope(): void {
        $this->detectApiRequest();
        $this->seoKey = 'scope';
        $this->render('public/scope');
    }

    public function gallery(): void {
        $this->detectApiRequest();
        $this->seoKey = 'gallery';
        $this->render('public/gallery');
    }

    public function faculty(): void {
        $this->detectApiRequest();
        $this->seoKey = 'faculty';
        $this->render('public/faculty');
    }

    
    public function terms(): void { 
        $this->detectApiRequest();
        $this->seoKey = 'terms';
        $this->render('public/terms', ['document' => $this->markdownDocument('content/legal/terms.md')]);
    }
    
    public function privacy(): void { 
        $this->detectApiRequest();
        $this->seoKey = 'privacy';
        $this->render('public/privacy', ['document' => $this->markdownDocument('content/legal/privacy.md')]);
    }
    
    public function therapies(): void {
        $this->detectApiRequest();
        $this->seoKey = 'consult';
        $reviews = new ReviewService();
        $this->render('public/consult', ['items' => [], 'reviews' => $reviews]);
    }

    public function hospitals(): void { 
        $this->detectApiRequest();
        $this->seoKey = 'temples';
        $this->render('public/hospitals', ['items' => (new TempleService())->all()]); 
    }
    
    public function hospital(string $slug): void { 
        $this->detectApiRequest();
        $temple = (new TempleService())->findBySlug($slug);
        $this->seoKey = 'temples';
        $this->seoOverrides = [
            'title' => ($temple['name'] ?? 'Facility') . ' – Aura Medical Campus & Hospital Facilities',
            'description' => ($temple['name'] ?? 'This facility') . ' at Aura Medical Institute of Electropathy and Hospital, Coimbatore. ' . ($temple['description'] ?? ''),
            'og_image' => $temple['image_url'] ?? '',
        ];
        $this->render('public/hospital', ['slug' => $slug, 'temple' => $temple]); 
    }
    
    public function shop(): void {
        $this->detectApiRequest();
        $category = $_GET['category'] ?? '';
        try { $categories = (new CategoryService())->all(); } catch (\Throwable $e) { $categories = []; }
        try { $items = (new ProductService())->all(); } catch (\Throwable $e) { $items = []; }
        $this->seoKey = 'shop';
        if ($category) {
            $items = array_values(array_filter($items, function ($item) use ($category) {
                $categoryList = $item['categories'] ?? [$item['category'] ?? ''];
                if (!is_array($categoryList)) {
                    $categoryList = preg_split('/[\r\n,]+/', (string)$categoryList) ?: [];
                }
                $categoryList[] = $item['category'] ?? '';
                return in_array($category, array_filter(array_map('trim', $categoryList)), true);
            }));
            $catName = '';
            foreach ($categories as $c) {
                if (($c['slug'] ?? '') === $category || ($c['name'] ?? '') === $category) {
                    $catName = $c['name'];
                    break;
                }
            }
            if ($catName) {
                $this->seoOverrides = [
                    'title' => 'Buy ' . $catName . ' Online – education Products at AuraEdu',
                    'description' => 'Shop authentic ' . $catName . ' online at AuraEdu. Browse our collection of therapy and wellness products. Fast shipping across India.',
                ];
            }
        }
        $this->render('public/shop', compact('items', 'categories', 'category'));
    }

    public function categories(): void {
        $this->detectApiRequest();
        $categories = (new CategoryService())->all();
        if ($this->isApiRequest) {
            $this->jsonResponse($categories);
            return;
        }
        $this->seoKey = 'shop';
        $this->render('public/categories', ['items' => (new ProductService())->all(), 'categories' => $categories, 'category' => '']);
    }
    
    public function product(string $slug): void {
        $this->detectApiRequest();
        $product = (new ProductService())->findBySlug($slug);
        $related = [];
        if ($product) {
            $all = (new ProductService())->all();
            $related = array_values(array_filter($all, fn($p) => ($p['slug'] ?? '') !== $slug));
            $this->seoKey = 'product';
            $price = $product['offer_price'] ?? $product['price'] ?? 0;
            $schema = (new SeoService((new SecretService())->all()))->productSchema($product);
            $this->seoOverrides = [
                'title' => ($product['name'] ?? 'Product') . ' – Buy Online at AuraEdu',
                'description' => 'Buy ' . ($product['name'] ?? 'this product') . ' online at AuraEdu. ' . ($product['description'] ?? '') . ' Price: ₹' . $price . '. Authentic education product with fast shipping.',
                'og_image' => $product['image_url'] ?? '',
                'json_ld' => '<script type="application/ld+json">' . json_encode($schema) . '</script>',
            ];
        }
        $reviewSummary = (new ReviewService())->summary('product', $slug);
        $this->render('public/product', compact('product', 'related', 'reviewSummary'));
    }
    
    public function cart(): void {
        $this->detectApiRequest();
        $this->seoKey = 'cart';
        $items = $this->resolveCartItems();
        $this->render('public/cart', ['items' => $items, 'total' => $this->cartTotal($items)]);
    }
    
    public function checkout(): void {
        $this->detectApiRequest();
        $this->seoKey = 'checkout';
        $items = $this->resolveCartItems();
        $secretService = new SecretService();
        $secrets = $secretService->all();
        $razorpayReady = $secretService->razorpayReadyForCurrentHost($secrets);
        $addresses = !empty($_SESSION['user']['email']) ? (new \App\Services\AddressService())->forCustomer($_SESSION['user']['email']) : [];
        $settings = (new \App\Services\SettingsService())->public();
        $this->render('public/checkout', ['items' => $items, 'total' => $this->cartTotal($items), 'secrets' => $secrets, 'addresses' => $addresses, 'razorpayReady' => $razorpayReady, 'settings' => $settings]);
    }
    
    public function sitemap(): void {
        header('Content-Type: application/xml; charset=utf-8');
        $host = $_SERVER['HTTP_HOST'] ?? 'auraedu.co.in';
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $base = $scheme . '://' . $host;

        $pages = [
            '/', '/about', '/consult', '/temples', '/shop', '/contact', '/blog',
            '/terms', '/privacy', '/education',
        ];
        $products = [];
        try { $products = (new ProductService())->all(); } catch (\Throwable) {}
        $blogPosts = [];
        try { $blogPosts = (new BlogService())->all(); } catch (\Throwable) {}

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($pages as $path) {
            $xml .= '  <url><loc>' . $base . $path . '</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>' . "\n";
        }

        foreach ($products as $p) {
            if (!empty($p['slug'])) {
                $xml .= '  <url><loc>' . $base . '/product/' . e($p['slug']) . '</loc><changefreq>weekly</changefreq><priority>0.7</priority></url>' . "\n";
            }
        }

        foreach ($blogPosts as $post) {
            if (!empty($post['slug']) && !empty($post['published'])) {
                $xml .= '  <url><loc>' . $base . '/blog/' . e($post['slug']) . '</loc><lastmod>' . e(substr((string)($post['updated_at'] ?? $post['published_at'] ?? ''), 0, 10)) . '</lastmod><changefreq>monthly</changefreq><priority>0.6</priority></url>' . "\n";
            }
        }

        $xml .= '</urlset>';
        echo $xml;
        exit;
    }

    public function contact(): void {
        $this->detectApiRequest();
        $this->seoKey = 'contact';
        $success = false;
        $subject = $_GET['subject'] ?? '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $this->checkRateLimit('contact', 3, 120);
            $contactService = new ContactService();
            $contactService->save([
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'subject' => $_POST['subject'] ?? '',
                'message' => $_POST['message'] ?? '',
            ]);
            $success = true;
        }
        $this->render('public/contact', ['success' => $success, 'subject' => $subject]);
    }
    
    public function login(): void { 
        $this->detectApiRequest();
        // Someone already signed in has no business on the sign-in page; send them to
        // their dashboard instead of offering a second login.
        if (!empty($_SESSION['user'])) $this->redirect((($_SESSION['user']['role'] ?? '') === 'admin') ? '/admin' : '/account/dashboard');
        $this->seoKey = 'login';
        $secrets = (new \App\Services\SecretService())->all();
        $this->render('public/login', [
            'googleAuthEnabled' => !empty($secrets['google_client_id']) && !empty($secrets['google_client_secret']),
        ]); 
    }

    public function docs(): void {
        $this->redirect('/blog/category/help');
    }

    public function doc(string $slug): void {
        $slug = preg_replace('/[^a-z0-9-]/', '', strtolower($slug));
        $this->redirect('/blog/' . $slug);
    }

    private function parseContentDocument(string $raw, string $fallbackSlug): array
    {
        $meta = [];
        $body = $raw;
        if (str_starts_with($raw, '---')) {
            $parts = explode('---', $raw, 3);
            if (count($parts) === 3) {
                foreach (explode("\n", trim($parts[1])) as $line) {
                    if (!str_contains($line, ':')) continue;
                    [$key, $value] = explode(':', $line, 2);
                    $meta[trim($key)] = trim(trim($value), "\"'");
                }
                $body = trim($parts[2]);
            }
        }
        preg_match('/^#\s+(.+)$/m', $body, $heading);
        $title = trim((string)($meta['title'] ?? $heading[1] ?? ucfirst(str_replace('-', ' ', $fallbackSlug))));
        $body = trim((string)preg_replace('/^#\s+.+\R?/m', '', $body, 1));
        return [
            'title' => $title,
            'slug' => (string)($meta['slug'] ?? $fallbackSlug),
            'summary' => (string)($meta['summary'] ?? ''),
            'order' => (int)($meta['order'] ?? 100),
            'icon' => (string)($meta['icon'] ?? 'guide'),
            'html' => (new MarkdownRenderer())->render($body),
        ];
    }

    /**
     * Markdown page with YAML frontmatter, the same file shape blogs use.
     *
     * This previously stripped only a leading "# Heading" and rendered everything else,
     * so the frontmatter block was published as body text — Terms and Privacy opened
     * with "title: Terms & Conditions description: ... category: legal". Frontmatter is
     * metadata and must never reach the page.
     */
    private function markdownDocument(string $relativePath): array
    {
        $raw = (string)@file_get_contents(app_path($relativePath));
        $meta = [];
        $body = $raw;

        if (preg_match('/\A\x{FEFF}?\s*---\R(.*?)\R---\R?(.*)\z/su', $raw, $m)) {
            foreach (preg_split('/\R/', $m[1]) as $line) {
                if (!str_contains($line, ':')) continue;
                [$key, $value] = explode(':', $line, 2);
                $meta[trim($key)] = trim(trim($value), " \"'");
            }
            $body = $m[2];
        }

        // Frontmatter title wins; fall back to a leading H1, which is then removed so it
        // is not printed twice under the page heading.
        $title = trim((string)($meta['title'] ?? ''));
        if ($title === '') {
            preg_match('/^#\s+(.+)$/m', $body, $heading);
            $title = trim($heading[1] ?? 'Document');
        }
        $body = trim((string)preg_replace('/^#\s+.+\R?/m', '', $body, 1));

        return [
            'title' => $title,
            'description' => (string)($meta['description'] ?? ''),
            'html' => (new MarkdownRenderer())->render($body),
        ];
    }
}
