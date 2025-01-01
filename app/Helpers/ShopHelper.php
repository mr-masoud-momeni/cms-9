<?php
// app/Helpers/ShopHelper.php

namespace App\Helpers;

use App\Models\Shop;

class ShopHelper
{
    // تابع برای گرفتن shop_id از روی دامنه
    public static function getShopIdFromDomain()
    {
        // استخراج دامنه
        $host = request()->getHost();  // دریافت دامنه از درخواست

        $shop = Shop::where('domain', $host)->firstOrFail(); // پیدا کردن فروشگاه با استفاده از دامنه

        return $shop ? $shop->id : null;  // اگر فروشگاه پیدا شد، id آن را برمی‌گرداند
    }
}
