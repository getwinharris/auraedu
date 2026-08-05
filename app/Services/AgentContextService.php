<?php
namespace App\Services;

final class AgentContextService {
    public function __construct(private DatabaseService $store = new DatabaseService(), private SchemaService $schema = new SchemaService()) {}

    public function forUserEmail(string $email): array {
        $email = strtolower(trim($email));
        if ($email === '') return ['user'=>null, 'orders'=>[], 'sessions'=>[], 'settings'=>$this->publicSettings(), 'site'=>$this->siteContext()];
        return [
            'user' => $this->firstOwned('users', 'email', $email),
            'orders' => $this->owned('orders', 'customer_email', $email),
            'sessions' => $this->owned('appointments', 'customer_email', $email),
            'settings' => $this->publicSettings(),
            'site' => $this->siteContext(),
        ];
    }

    private function owned(string $collection, string $field, string $email): array {
        $records = array_values(array_filter($this->store->read($collection), fn($item) => strtolower((string)($item[$field] ?? '')) === $email));
        $fields = $this->schema->agentContextFields($collection);
        return $fields ? array_map(fn($item) => array_intersect_key($item, array_flip($fields)), $records) : $records;
    }

    private function firstOwned(string $collection, string $field, string $email): ?array {
        $records = $this->owned($collection, $field, $email);
        return $records[0] ?? null;
    }

    private function publicSettings(): array {
        $settings = $this->store->read('settings')[0] ?? [];
        return array_intersect_key($settings, array_flip(['currency', 'timezone', 'shipping_mode', 'flat_rate']));
    }

    private function siteContext(): array {
        $products = array_map(fn($item) => [
            'name' => $item['name'] ?? '',
            'slug' => $item['slug'] ?? '',
            'url' => '/product/' . ($item['slug'] ?? ''),
            'price' => $item['offer_price'] ?? $item['price'] ?? null,
            'stock_status' => $item['stock_status'] ?? '',
        ], array_slice($this->store->read('products'), 0, 20));
        $courses = array_map(fn($c) => [
            'slug' => $c['slug'] ?? '',
            'title' => $c['title'] ?? '',
            'short' => $c['short'] ?? '',
            'url' => '/courses/' . ($c['slug'] ?? ''),
            'duration' => $c['duration'] ?? '',
            'eligibility' => $c['eligibility'] ?? '',
        ], (new CourseService())->all());
        $blog = array_map(fn($p) => [
            'title' => $p['title'] ?? '',
            'slug' => $p['slug'] ?? '',
            'category' => $p['category'] ?? '',
            'url' => '/blog/' . ($p['slug'] ?? ''),
            'summary' => $p['summary'] ?? $p['excerpt'] ?? '',
        ], array_slice((new BlogService())->all(), 0, 10));
        return [
            'pages' => [
                'shop' => '/shop',
                'cart' => '/cart',
                'checkout' => '/checkout',
                'courses' => '/courses',
                'consult' => '/consult',
                'hospitals' => '/hospitals',
                'blog' => '/blog',
                'contact' => '/contact',
                'eligibility' => '/eligibility',
                'scope' => '/scope',
                'orders' => '/account/dashboard/orders',
                'sessions' => '/account/dashboard/sessions',
            ],
            'products' => $products,
            'courses' => $courses,
            'blog' => $blog,
            'catalog_note' => 'Courses (B.E.M.S., M.D.E.H., D.Acu, M.Acu, D.H.M.) are education programmes at Aura Medical Institute. Products are physical therapy/wellness items sold on the shop. Consultant appointments are booked via /consult, and consultants can also be reached through /hospitals.',
            'support_scope' => 'Answer only from this JSON context and public site links. Do not access tools, files, admin data, or other users.',
        ];
    }
}
