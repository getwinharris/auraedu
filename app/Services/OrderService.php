<?php
namespace App\Services;

final class OrderService {
    public function __construct(
        private DatabaseService $store = new DatabaseService(),
        private ?MailQueueService $mailQueue = null
    ) {
        $this->mailQueue ??= new MailQueueService($this->store);
    }

    public function all(): array {
        return $this->store->read('orders');
    }

    /**
     * @param array $tracking ['courier' => courier-code, 'tracking_id' => string]
     *
     * Marking an order shipped requires a courier chosen from the registry plus a
     * tracking number; the tracking link is built from the courier's fixed base URL
     * and stored. Validated before any database read so the rule is testable offline.
     */
    public function updateStatus(string $id, string $status, ?\DateTimeImmutable $now = null, array $tracking = []): array {
        $courier = trim((string)($tracking['courier'] ?? ''));
        $trackingId = trim((string)($tracking['tracking_id'] ?? ''));
        $shipping = new ShippingService();
        if ($status === 'shipped') {
            if ($courier === '') throw new \InvalidArgumentException('Choose a courier to mark an order shipped.');
            if (!array_key_exists($courier, $shipping->couriers())) throw new \InvalidArgumentException('Unknown courier.');
            if ($trackingId === '') throw new \InvalidArgumentException('A courier tracking ID is required to mark an order shipped.');
        }

        $orders = $this->all();
        $updated = null;
        $now ??= new \DateTimeImmutable();
        foreach ($orders as &$order) {
            if (($order['id'] ?? '') !== $id) continue;
            $order['status'] = $status;
            $order['updated_at'] = $now->format('c');
            if (in_array($status, ['shipped', 'delivered'], true)) {
                if ($courier !== '') {
                    $order['courier'] = $courier;
                    $order['courier_name'] = $shipping->label($courier);
                    $order['tracking_id'] = $trackingId;
                    $order['tracking_url'] = $shipping->trackingUrl($courier, $trackingId);
                }
                $order['shipped_at'] = $order['shipped_at'] ?? $now->format('c');
                $shippedAt = new \DateTimeImmutable($order['shipped_at']);
                $order['review_request_after_at'] = $shippedAt->modify('+10 days')->format('c');
                $this->mailQueue->enqueueShipmentNotification($order);
                $this->mailQueue->enqueueProductReviewRequest($order, 10);
            }
            $updated = $order;
            break;
        }
        unset($order);
        if (!$updated) {
            throw new \RuntimeException('Order not found.');
        }
        $this->store->write('orders', $orders);
        return $updated;
    }
}