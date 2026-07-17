<?php
namespace App\Services;

final class SupportBotService {
    public function __construct(
        private SecretService $secrets = new SecretService(),
        private DatabaseService $store = new DatabaseService(),
        private AgentContextService $agentContext = new AgentContextService()
    ) {}

    public function answer(string $message, ?array $user): array {
        $message = trim($message);
        if ($message === '') throw new \InvalidArgumentException('Message is required.');
        $context = $this->customerContext($user);
        $reply = null;
        $aiReply = $this->googleReply($message, $context);
        if ($aiReply !== null) {
            $candidate = $this->cleanReply($aiReply);
            if (!$this->looksInternal($candidate)) $reply = $candidate;
        }
        $reply ??= $this->fallbackReply($message, $context);
        $result = ['reply' => $reply, 'ticket_id' => null, 'booking_id' => null, 'browser_action' => null, 'memory' => 'browser_session'];
        $email = !empty($user['email']) ? (string)$user['email'] : '';

        $bookingResult = $this->tryCreateBooking($message, $user, $context);
        if ($bookingResult !== null) {
            $result['booking_id'] = $bookingResult['id'];
            $result['reply'] .= ' Your booking has been created. The consultant will confirm the schedule.';
            $result['actions'][] = ['type' => 'navigate', 'label' => 'View My Sessions', 'path' => '/account/dashboard/sessions'];
        }

        $escalated = $this->shouldEscalate($message, $reply, $user);
        if ($escalated && $email !== '' && $result['ticket_id'] === null) {
            try {
                $ticket = (new SupportTicketService())->create($email, $message, 'escalated from support bot');
                $result['ticket_id'] = $ticket['id'];
                $result['reply'] .= ' I have created a support ticket to get a human to review your request.';
            } catch (\Throwable) {}
        }

        $browserAction = $this->tryBrowserAction($message);
        if ($browserAction !== null) $result['browser_action'] = $browserAction;

        $actions = $this->extractActions($reply);
        if ($actions !== []) $result['actions'] = array_merge($result['actions'] ?? [], $actions);
        return $result;
    }

    private function tryCreateBooking(string $message, ?array $user, array $context): ?array {
        if (empty($user['email'])) return null;
        if (!preg_match('/\b(book|booking|appointment|schedule|consult)\b/i', $message)) return null;
        if (!preg_match('/\b(consultant|session|therapy|counsel|follow.?up)\b/i', $message)) return null;
        $slug = null;
        $products = $context['site']['products'] ?? [];
        foreach ($products as $p) {
            if (!empty($p['slug']) && stripos($p['name'] ?? '', 'consult') !== false) {
                $slug = $p['slug'];
                break;
            }
        }
        $slug ??= 'general-consultation';
        $id = bin2hex(random_bytes(8));
        $session = [
            'id' => $id,
            'customer_email' => strtolower(trim((string)$user['email'])),
            'customer_name' => $user['name'] ?? '',
            'consultant_slug' => $slug,
            'mode' => 'booking',
            'session_type' => 'Consultation booking (auto)',
            'status' => 'requested',
            'preferred_date' => date('Y-m-d', strtotime('+1 day')),
            'preferred_time' => '10:00',
            'phone' => $user['phone'] ?? '',
            'notes' => 'Auto-created by support bot: ' . mb_substr($message, 0, 500),
            'date' => date('Y-m-d', strtotime('+1 day')),
            'time' => '10:00',
            'created_at' => date('c'),
        ];
        try {
            (new \App\Services\ResourceService('appointments'))->save($session);
            return $session;
        } catch (\Throwable) {
            return null;
        }
    }

    private function tryBrowserAction(string $message): ?array {
        if (preg_match('/\b(search|look up|find|navigate to|go to|open)\s+(.+)/i', $message, $m)) {
            return ['type' => 'navigate', 'query' => trim($m[2])];
        }
        if (preg_match('/\b(browse|explore)\b/i', $message)) {
            return ['type' => 'navigate', 'query' => $message];
        }
        return null;
    }

    private function customerContext(?array $user): array {
        $cart = array_map(fn($item) => [
            'slug' => $item['slug'] ?? '',
            'qty' => (int)($item['qty'] ?? 0),
            'name' => $item['name'] ?? '',
        ], array_values($_SESSION['cart'] ?? []));
        $base = empty($user['email'])
            ? $this->agentContext->forUserEmail('')
            : $this->agentContext->forUserEmail((string)$user['email']);
        return ['signed_in' => !empty($user['email']), 'cart' => $cart] + $base;
    }

    private function googleReply(string $message, array $context): ?string {
        $secrets = $this->secrets->all();
        $endpoint = rtrim(trim((string)(getenv('API_ENDPOINT') ?: getenv('BAPX_AI_ENDPOINT') ?: ($secrets['api_endpoint'] ?? 'https://generativelanguage.googleapis.com/v1beta/openai/'))), '/');
        $key = trim((string)(getenv('AGENT_API_KEY') ?: getenv('SUPPORT_BOT_GOOGLE_API_KEY') ?: ($secrets['agent_api_key'] ?? $secrets['support_bot_google_api_key'] ?? '')));
        $model = trim((string)(getenv('AGENT_MODEL') ?: getenv('SUPPORT_BOT_MODEL') ?: ($secrets['agent_model'] ?? $secrets['support_bot_model'] ?? 'gemma-4-31b-it'))) ?: 'gemma-4-31b-it';
        if ($key === '' || !function_exists('curl_init')) return null;
        $systemPrompt = 'You are AuraEdu support bot. Answer concisely. Use the context for site-specific data. You can use general knowledge for questions about the AI, the platform, or common knowledge.';
        $payload = json_encode([
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => 'Context: ' . json_encode($context, JSON_UNESCAPED_SLASHES) . "\n\nCustomer: " . $message],
            ],
            'max_tokens' => 220,
            'temperature' => 0.3,
        ], JSON_UNESCAPED_SLASHES);
        $ch = curl_init($endpoint . '/chat/completions');
        $headers = ['Content-Type: application/json', 'Authorization: Bearer ' . $key];
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 12,
        ]);
        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($status >= 300 || !$body) return null;
        $json = json_decode($body, true) ?: [];
        return trim((string)($json['choices'][0]['message']['content'] ?? '')) ?: null;
    }

    private function cleanReply(string $reply): string {
        $reply = preg_replace('/<thought\b[^>]*>.*?<\/thought>/is', '', $reply) ?? $reply;
        $reply = preg_replace('/^\s*[\*\-]\s*/m', '', $reply) ?? $reply;
        $reply = trim($reply, " \t\n\r\0\x0B`*\"");
        if ($reply === '') $reply = 'I can help with products, orders, delivery addresses, payments, and consultant bookings. Please ask one specific question.';
        return strlen($reply) > 900 ? substr($reply, 0, 897) . '...' : $reply;
    }

    private function looksInternal(string $reply): bool {
        return (bool)preg_match('/\b(role:|constraint|the user said|the bot should|the bot needs|generationconfig|tool call)\b/i', $reply);
    }

    private function fallbackReply(string $message, array $context): string {
        $lower = strtolower($message);
        if (!$context['signed_in']) {
            if ($this->isPrivateAccountQuestion($lower)) {
                return 'Please sign in to ask about your personal orders or consultant bookings. I can still explain products, checkout, delivery, and booking.';
            }
            return $this->publicGuestReply($lower, $context);
        }
        if (preg_match('/^(hi|hello|hey|vanakkam|namaste)\b/i', trim($message))) {
            return 'Hello. I can help with products, checkout, saved addresses, orders, and consultant bookings.';
        }
        if (str_contains($lower, 'order')) {
            return empty($context['orders']) ? 'I could not find orders in your account yet.' : 'I found your recent order data in the account panel. Open My Orders for full delivery address, status, shipped time, and review options.';
        }
        if (str_contains($lower, 'talk') || str_contains($lower, 'session')) {
            return empty($context['sessions']) ? 'I could not find sessions in your account yet.' : 'I found recent session records. Open My Sessions to see details.';
        }
        return 'I can help with product orders, delivery addresses, consultant bookings, reviews, and account history. Please ask one specific question.';
    }

    private function publicGuestReply(string $message, array $context): string {
        $site = $context['site'] ?? [];
        $pages = $site['pages'] ?? [];
        $products = array_slice($site['products'] ?? [], 0, 5);
        if (preg_match('/\b(hi|hello|hey|vanakkam|namaste)\b/i', $message)) {
            return 'Hello. I can help you browse education products, place an order, manage delivery addresses, or request a consultant appointment.';
        }
        if (preg_match('/\b(product|available|shop|buy|item|pendant|jewelry|jewellery)\b/i', $message)) {
            $names = array_filter(array_map(fn($p) => trim((string)($p['name'] ?? '')), $products));
            $list = $names ? implode(', ', $names) : 'therapy and wellness products';
            return 'Available products include ' . $list . '. Open ' . ($pages['shop'] ?? '/shop') . ' to browse all products, or add an item to cart from its product page.';
        }
        if (preg_match('/\b(service|consult|booking|book|astrology|call|message|temple)\b/i', $message)) {
            return 'Available services include education product sales, scheduled consultant appointments, and temple guidance. Open ' . ($pages['consult'] ?? '/consult') . ' to request an appointment.';
        }
        if (preg_match('/\b(recharge|wallet|credit|payment)\b/i', $message)) {
            return 'Product payments are completed securely during checkout. Sign in to reuse saved delivery addresses and view confirmed orders.';
        }
        return 'I can help with products, checkout, delivery addresses, temple guidance, and consultant bookings. For personal order or booking history, please sign in first.';
    }

    private function isPrivateAccountQuestion(string $message): bool {
        return (bool)preg_match('/\b(my order|my booking|my session|track|delivery|shipped|history|past session|previous session)\b/i', $message);
    }

    private function shouldEscalate(string $message, string $reply, ?array $user): bool {
        if (empty($user['email'])) return false;
        if (preg_match('/\b(human|agent|escalate|talk to (a|someone)|speak to|contact support)\b/i', $message)) return true;
        if (preg_match('/\b(complaint|refund|cancel|cancellation|return|wrong|broken|not working|issue|problem)\b/i', $message)) return true;
        if (str_contains($reply, 'contact form')) return true;
        return false;
    }

    private function extractActions(string $reply): array {
        preg_match_all('/\/(?:shop|cart|checkout|consult|hospitals|contact|blog(?:\/[a-z0-9-]+|\/category\/[a-z0-9-]+)?|product\/[a-z0-9-]+|account\/dashboard(?:\/orders|\/sessions|\/install)?)(?=[\s.,)\/  ]|$)/i', $reply, $matches);
        $seen = [];
        $actions = [];
        foreach ($matches[0] as $path) {
            $path = strtolower($path);
            if (in_array($path, $seen, true)) continue;
            $seen[] = $path;
            $label = match (true) {
                $path === '/shop' => 'View Shop',
                $path === '/cart' => 'View Cart',
                $path === '/checkout' => 'Go to Checkout',
                $path === '/consult' => 'View Consultants',
                $path === '/contact' => 'Contact Us',
                $path === '/hospitals' => 'View Hospitals',
                $path === '/blog' => 'Read Blog',
                default => 'Open ' . trim(preg_replace('/^\/+/', '', str_replace(['-', '/'], ' ', $path)))
            };
            $actions[] = ['type' => 'navigate', 'label' => $label, 'path' => $path];
        }
        return $actions;
    }
}
