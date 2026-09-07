<?php
// app/Helpers/ShopHelper.php

namespace App\Helpers;

use App\Models\Shop;

class ShopHelper
{
    public static function getShop()
    {
        if (request()->attributes->has('current_shop')) {
            return request()->attributes->get('current_shop');
        }

        $host = request()->getHost();
        $context = session('current_shop');

        if (!$context || ($context['domain'] ?? null) !== $host) {
            $shop = Shop::where('domain', $host)->first();

            if (!$shop) {
                abort(404);
            }

            $context = [
                'id' => $shop->id,
                'user_id' => $shop->user_id,
                'name' => $shop->name,
                'domain' => $shop->domain,
                'slug' => $shop->slug,
                'buyer_login_required' => $shop->buyer_login_required,
            ];

            session()->put('current_shop', $context);
        }

        $shop = new Shop($context);
        $shop->exists = true;
        $shop->setRawAttributes($context);

        request()->attributes->set('current_shop', $shop);

        return $shop;
    }

    public static function getShopId()
    {
        return self::getShop()?->id;
    }
}
