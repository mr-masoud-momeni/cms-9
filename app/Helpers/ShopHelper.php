<?php

namespace App\Helpers;

use App\Models\Shop;
use Illuminate\Support\Facades\Cache;

class ShopHelper
{
    public static function getShop()
    {
        if (request()->attributes->has('current_shop')) {
            return request()->attributes->get('current_shop');
        }

        $host = request()->getHost();
        $cacheKey = 'shop:domain:' . $host;

        $shop = Cache::rememberForever($cacheKey, function () use ($host) {
            return Shop::where('domain', $host)->first();
        });

        if (!$shop) {
            abort(404);
        }

        request()->attributes->set('current_shop', $shop);

        return $shop;
    }

    public static function getShopId()
    {
        return self::getShop()?->id;
    }

    public static function getShippingCost(): int
    {
        return (int) (self::getShop()->shipping_cost ?? 0);
    }

    public static function getGuestCart(): array
    {
        $key = self::guestCartKey();

        if (session()->has($key)) {
            return session()->get($key, []);
        }

        // Migrate the old unscoped cart once after deployment.
        if (session()->has('cart')) {
            $cart = session()->get('cart', []);
            session()->put($key, $cart);
            session()->forget('cart');

            return $cart;
        }

        return [];
    }

    public static function putGuestCart(array $cart): void
    {
        session()->put(self::guestCartKey(), $cart);
    }

    public static function forgetGuestCart(): void
    {
        session()->forget(self::guestCartKey());
        session()->forget('cart');
    }

    public static function getCheckoutOrderId(): ?int
    {
        return session()->get(self::checkoutOrderKey());
    }

    public static function putCheckoutOrderId(int $orderId): void
    {
        session()->put(self::checkoutOrderKey(), $orderId);
    }

    public static function forgetCheckoutOrderId(): void
    {
        session()->forget(self::checkoutOrderKey());
        session()->forget('checkout_order_id');
    }

    public static function forgetShopCache(?string $domain = null): void
    {
        $domain ??= request()->getHost();

        Cache::forget('shop:domain:' . $domain);
    }

    private static function guestCartKey(): string
    {
        return 'cart.shop.' . self::getShopId();
    }

    private static function checkoutOrderKey(): string
    {
        return 'checkout_order_id.shop.' . self::getShopId();
    }
}
