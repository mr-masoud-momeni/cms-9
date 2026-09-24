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

    public static function forgetShopCache(?string $domain = null): void
    {
        $domain ??= request()->getHost();

        Cache::forget('shop:domain:' . $domain);
    }
}
