<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopBaleConnectionToken;
use Illuminate\Support\Str;

class BaleConnectionController extends Controller
{
    public function connect()
    {
        $shop = Shop::current();
        $user = auth('shop_admin')->user();

        // حذف توکن‌های قبلی استفاده‌نشده
        ShopBaleConnectionToken::where('shop_id', $shop->id)
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->delete();

        // تولید توکن اتصال
        $token = strtoupper(Str::random(8));

        ShopBaleConnectionToken::create([
            'shop_id'    => $shop->id,
            'user_id'    => $user->id,
            'token'      => $token,
            'expires_at' => now()->addMinutes(10),
        ]);

        return back()->with('bale_connection_token', $token);
    }
    public function disconnect()
    {
        $shop = Shop::current();
        $user = auth('shop_admin')->user();

        $connection = ShopBaleConnection::where('shop_id', $shop->id)
            ->where('user_id', $user->id)
            ->where('active', true)
            ->first();

        if ($connection) {
            $connection->update([
                'active' => false,
            ]);
        }

        return back()->with('success', 'اتصال به بله قطع شد.');
    }
}