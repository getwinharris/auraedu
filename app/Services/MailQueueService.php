<?php
namespace App\Services;

final class MailQueueService {
    /** Brand tokens mirrored from assets/css/band.css so mail matches the site. */
    private const MAROON = '#3a0003';
    private const GOLD = '#d1b368';

    public function __construct(private DatabaseService $store = new DatabaseService()) {}

    /** Heading for the one thing the email is about. */
    public static function heading(string $text): string {
        return '<h1 style="margin:0 0 16px;font-family:Georgia,\'Times New Roman\',serif;font-size:1.4rem;color:' . self::MAROON . ';">' . e($text) . '</h1>';
    }

    /**
     * Call to action. Outlook ignores padding on <a>, so the button colour sits on the
     * link with padding; 44px minimum height is the accepted mobile tap target.
     */
    public static function button(string $label, string $url): string {
        return '<p style="margin:20px 0;"><a href="' . e($url) . '" style="display:inline-block;background:' . self::MAROON . ';color:' . self::GOLD . ';padding:13px 28px;border-radius:999px;text-decoration:none;font-weight:600;min-height:44px;line-height:1;">' . e($label) . '</a></p>';
    }

    private function wrapHtml(string $inner): string {
        $settings = (new SettingsService())->public();
        $logoUrl = $settings['logo_url'] ?? '';
        $siteName = 'AuraEdu';
        $logoHtml = $logoUrl ? "<img src=\"$logoUrl\" alt=\"$siteName\" style=\"max-width:180px;height:auto;margin-bottom:16px;\">" : "<h1 style=\"margin:0 0 16px;font-size:1.5rem;color:#3a0003;\">$siteName</h1>";
        $footerHtml = '<hr style="border:none;border-top:1px solid #e5e5e5;margin:24px 0;">'
            . '<p style="margin:0;font-size:0.8rem;color:#666;">'
            . 'GSTIN: ' . e((string)($settings['gstin'] ?? '')) . '<br>'
            . 'Address: ' . e((string)($settings['gst_address'] ?? '')) . '<br>'
            . 'State: ' . e((string)($settings['gst_state'] ?? '')) . ' (' . e((string)($settings['gst_state_code'] ?? '')) . ')<br>'
            . 'PAN: ' . e((string)($settings['gst_pan'] ?? '')) . '<br>'
            . 'Email: support@auraedu.co.in | Phone: +91-XXXXXXXXXX'
            . '</p>'
            . '<p style="margin-top:16px;font-size:0.75rem;color:#999;">'
            . 'This is an automated email from ' . e($siteName) . '. Please do not reply.'
            . '</p>';

        return '<div style="font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;line-height:1.6;color:#222;max-width:600px;margin:0 auto;padding:24px;">'
            . '<div style="text-align:center;padding-bottom:16px;border-bottom:1px solid #e5e5e5;">' . $logoHtml . '</div>'
            . '<div style="padding:24px 0;">'
            . $inner
            . '</div>'
            . '<div style="text-align:center;">' . $footerHtml . '</div>'
            . '</div>';
    }

    public function all(): array {
        return $this->store->read('mail_queue');
    }

    public function enqueue(string $type, string $to, string $subject, string $html, ?\DateTimeImmutable $availableAt = null, array $meta = []): array {
        $html = $this->wrapHtml($html);
        $record = [
            'id' => bin2hex(random_bytes(8)),
            'type' => $type,
            'to' => $to,
            'subject' => $subject,
            'html' => $html,
            'status' => 'pending',
            'available_at' => ($availableAt ?? new \DateTimeImmutable())->format('c'),
            'meta' => $meta,
            'created_at' => date('c'),
        ];
        $saved = $this->store->upsert('mail_queue', $record);
        (new MailStorageService($this->store))->recordQueuedOutbox($saved);
        return $saved;
    }

    public function enqueuePaymentConfirmation(array $order): ?array {
        $to = trim((string)($order['customer_email'] ?? ''));
        if ($to === '') return null;
        $invoiceHtml = '';
        if (!empty($order['invoice_number'])) {
            $invoiceHtml = '<p>Invoice: <strong>' . e((string)($order['invoice_number'] ?? '')) . '</strong> — '
                . '<a href="' . rtrim(($_ENV['APP_URL'] ?? 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')), '/') . '/account/orders/' . e((string)($order['id'] ?? '')) . '/invoice">View invoice</a></p>';
        }
        $subject = 'AuraEdu payment confirmed';
        $html = '<p>Vanakkam ' . e((string)($order['customer_name'] ?? '')) . ',</p>'
            . '<p>Your payment for order ' . e((string)($order['id'] ?? '')) . ' is confirmed.</p>'
            . $invoiceHtml
            . '<p>Total: ₹' . e((string)($order['total'] ?? 0)) . '</p>';
        return $this->enqueue('payment_confirmation', $to, $subject, $html, null, ['order_id' => $order['id'] ?? '']);
    }

    public function enqueueShipmentNotification(array $order): ?array {
        $to = trim((string)($order['customer_email'] ?? ''));
        if ($to === '') return null;
        $subject = 'AuraEdu order shipped';
        $trackingUrl = trim((string)($order['tracking_url'] ?? ''));
        $trackingId = trim((string)($order['tracking_id'] ?? ''));
        $courier = trim((string)($order['courier_name'] ?? ''));
        $trackingHtml = $trackingUrl !== ''
            ? '<p>Courier: <strong>' . e($courier !== '' ? $courier : 'the courier') . '</strong>'
                . ($trackingId !== '' ? ' (Tracking ID: <code>' . e($trackingId) . '</code>)' : '')
                . '</p><p style="margin-top:4px;"><a href="' . e($trackingUrl) . '" style="background:#3a0003;color:#d1b368;padding:11px 20px;border-radius:999px;text-decoration:none;font-weight:600;display:inline-block;">Track your parcel</a></p>'
            : '';
        $html = '<p>Your order ' . e((string)($order['id'] ?? '')) . ' has been shipped.</p>'
            . $trackingHtml
            . '<p>We will ask for your product review after you have had time to receive and use it.</p>';
        return $this->enqueue('shipment_notification', $to, $subject, $html, null, ['order_id' => $order['id'] ?? '']);
    }

    public function enqueueProductReviewRequest(array $order, int $waitDays = 10): ?array {
        $to = trim((string)($order['customer_email'] ?? ''));
        if ($to === '') return null;
        $shippedAt = new \DateTimeImmutable((string)($order['shipped_at'] ?? 'now'));
        $availableAt = $shippedAt->modify('+' . max(1, $waitDays) . ' days');
        $subject = 'How was your AuraEdu product?';
        $html = '<p>We hope your order ' . e((string)($order['id'] ?? '')) . ' reached you well.</p>'
            . '<p>Please share your product rating from your account orders page.</p>';
        return $this->enqueue('product_review_request', $to, $subject, $html, $availableAt, ['order_id' => $order['id'] ?? '']);
    }

    /**
     * The reset link is emailed, never rendered on screen. Showing it would hand any
     * visitor a working reset for an address they do not control.
     */
    public function enqueuePasswordReset(string $email, string $link): ?array {
        $email = trim($email);
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || trim($link) === '') return null;
        return $this->enqueue('password_reset', $email, 'Reset your AuraEdu password',
            self::heading('Reset your password')
            . '<p>We received a request to reset the password for your AuraEdu account. The link works for one hour.</p>'
            . self::button('Reset your password', $link)
            . '<p style="color:#666;font-size:0.85rem;">If you did not request this, you can ignore this email — your password stays unchanged.</p>');
    }

    /** A failed payment left the customer with nothing; tell them and point at the cart. */
    public function enqueuePaymentFailure(array $order, string $reason = ''): ?array {
        $to = trim((string)($order['customer_email'] ?? ''));
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) return null;
        $detail = $reason !== '' ? '<p>Reason: ' . e($reason) . '</p>' : '';
        return $this->enqueue('payment_failed', $to, 'Your payment could not be completed',
            self::heading('Your payment did not go through')
            . '<p>Vanakkam ' . e((string)($order['customer_name'] ?? '')) . ', your payment for order '
            . e((string)($order['id'] ?? '')) . ' could not be completed, so the order was not placed and <strong>you have not been charged</strong>.</p>'
            . '<p>Your cart has been kept, so nothing needs rebuilding.</p>'
            . self::button('Complete your order', rtrim((string)(getenv('APP_URL') ?: 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')), '/') . '/checkout')
            . $detail);
    }

    /** Security notice so an account holder learns about a sign-in they did not make. */
    public function enqueueLoginNotification(string $email, string $name, string $role): ?array {
        $email = trim($email);
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) return null;
        $when = date('d M Y, H:i');
        $ip = (string)($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        return $this->enqueue('login_notification', $email, 'New sign-in to your AuraEdu account',
            self::heading('New sign-in to your account')
            . '<p>Hello ' . e($name !== '' ? $name : 'there') . ',</p>'
            . '<p>Your account was signed in to on <strong>' . e($when) . '</strong> (IP ' . e($ip) . ').</p>'
            . '<p>If this was you, no action is needed. If it was not, change your password immediately and write to support@auraedu.co.in.</p>');
    }

    public function due(?\DateTimeImmutable $now = null): array {
        $now ??= new \DateTimeImmutable();
        $due = array_values(array_filter($this->all(), function (array $record) use ($now): bool {
            if (($record['status'] ?? 'pending') !== 'pending') return false;
            $availableAt = new \DateTimeImmutable((string)($record['available_at'] ?? 'now'));
            return $availableAt <= $now;
        }));
        usort($due, fn($a, $b) => strcmp((string)($a['available_at'] ?? ''), (string)($b['available_at'] ?? '')));
        return $due;
    }

    public function markSent(string $id): void {
        $this->updateStatus($id, 'sent', ['sent_at' => date('c')]);
    }

    public function markFailed(string $id, string $error): void {
        $this->updateStatus($id, 'failed', ['last_error' => $error, 'failed_at' => date('c')]);
    }

    public function processDue(SmtpMailer $mailer, ?\DateTimeImmutable $now = null, int $limit = 25): int {
        $sent = 0;
        foreach (array_slice($this->due($now), 0, $limit) as $record) {
            try {
                $mailer->send((string)$record['to'], (string)$record['subject'], (string)$record['html']);
                $this->markSent((string)$record['id']);
                (new MailStorageService($this->store))->updateOutboxForQueue((string)$record['id'], 'sent', [
                    'from_email' => $mailer->fromEmail(),
                    'transport' => $mailer->transport(),
                    'sent_at' => date('c'),
                ]);
                $sent++;
            } catch (\Throwable $error) {
                $this->markFailed((string)$record['id'], $error->getMessage());
                (new MailStorageService($this->store))->updateOutboxForQueue((string)$record['id'], 'failed', [
                    'from_email' => $mailer->fromEmail(),
                    'transport' => $mailer->transport(),
                    'last_error' => $error->getMessage(),
                    'failed_at' => date('c'),
                ]);
            }
        }
        return $sent;
    }

    private function updateStatus(string $id, string $status, array $extra): void {
        $records = $this->all();
        foreach ($records as &$record) {
            if (($record['id'] ?? '') !== $id) continue;
            $record = array_merge($record, $extra, ['status' => $status]);
            break;
        }
        unset($record);
        $this->store->write('mail_queue', $records);
    }
}
