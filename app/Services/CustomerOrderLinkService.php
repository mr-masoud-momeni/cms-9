<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shop;

class CustomerOrderLinkService
{
    public function makeOrderUrl(Shop $shop, Order $order, int $expiresInDays = 30): string
    {
        $expiresAt = now()->addDays($expiresInDays)->timestamp;
        $token = $this->makeToken($shop, $order, $expiresAt);
        $domain = preg_replace('#^https?://#i', '', trim((string) $shop->domain));
        $domain = rtrim($domain, '/');

        return 'https://' . $domain
            . '/order/' . $order->id
            . '/track/' . $expiresAt
            . '/' . $token;
    }

    public function makeSmsUrl(string $phone, string $message): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        return 'sms:' . ($phone ?: '') . '?body=' . rawurlencode($message);
    }

    public function makeToken(Shop $shop, Order $order, int $expiresAt): string
    {
        return hash_hmac(
            'sha256',
            $shop->id . '|' . $order->id . '|' . $expiresAt,
            (string) config('app.key')
        );
    }
}
