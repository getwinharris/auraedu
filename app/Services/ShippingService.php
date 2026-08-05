<?php
namespace App\Services;

/**
 * Courier registry for order fulfilment. Admins pick a courier from this list and
 * supply only a tracking number; the tracking page URL is built from that courier's
 * fixed base URL, so no free-text URL ever needs to be pasted in (and such a field,
 * left to guesswork, produces dead links and support "where is my parcel" queries).
 */
final class ShippingService {
    /** Courier code => [public label, fixed tracking-page base URL]. */
    private const COURIERS = [
        'dtdc'      => ['DTDC',          'https://www.dtdc.com/track-your-shipment/'],
        'bluedart'  => ['BlueDart',      'https://www.bluedart.com/tracking'],
        'indiapost' => ['India Post',    'http://www.indiapost.gov.in/tracking'],
        'fedex'     => ['FedEx',         'https://www.fedex.com/en-in/tracking.html'],
        'stcourier' => ['ST Courier',    'https://stcourier.com/track/shipment'],
        'tpcglobe'  => ['TPC Globe',     'https://tpcglobe.com/'],
        'franc'     => ['Franc Express', 'https://franchexpress.com/courier-tracking/'],
    ];

    /** @return array<string, array{0:string,1:string}> keyed by courier code. */
    public function couriers(): array {
        return self::COURIERS;
    }

    public function label(string $code): string {
        return self::COURIERS[$code][0] ?? $code;
    }

    /** Tracking page for a courier and ID. Unknown courier or empty ID => ''. */
    public function trackingUrl(string $code, string $trackingId): string {
        $trackingId = trim($trackingId);
        if (!isset(self::COURIERS[$code]) || $trackingId === '') return '';
        return rtrim(self::COURIERS[$code][1], '/') . '/' . rawurlencode($trackingId);
    }
}