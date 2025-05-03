<?php
// app/Helpers/ShopHelper.php

namespace App\Helpers;

use App\Models\Shop;

class ShopHelper
{

    // گرفتن مدل فروشگاه از دامنه
    public static function getShop()
    {
        $host = request()->getHost();
        return Shop::where('domain', $host)->first(); // orFail نذار که 500 نده
    }

    // گرفتن فقط id فروشگاه
    public static function getShopId()
    {
        $shop = self::getShop();
        return $shop ? $shop->id : null;
    }
}
