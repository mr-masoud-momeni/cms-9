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

        return app(Shop::class);
    }

    public static function getShopId()
    {
        $shop = self::getShop();
        return $shop?->id;
    }
}
