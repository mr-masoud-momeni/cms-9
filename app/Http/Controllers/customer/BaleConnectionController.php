<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopBaleConnectionToken;
use App\Models\ShopBaleConnection;
use Illuminate\Support\Str;

class BaleConnectionController extends Controller
{
    public function connect()
    {
        $shop = Shop::current();

        $userId = auth('shop_admin')->id();

        // حذف توکن‌های قبلی
        ShopBaleConnectionToken::where('shop_id', $shop->id)
            ->where('user_id', $userId)
            ->whereNull('used_at')
            ->delete();

        // ساخت Token
        $token = Str::random(32);

        ShopBaleConnectionToken::create([
            'shop_id' => $shop->id,
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => now()->addMinutes(10),
        ]);

        $botUsername = config('services.bale.bot_username');

        // Deep Link
        $baleUrl = "https://ble.ir/{$botUsername}?start={$token}";

        return response()->json([
            'url' => $baleUrl
        ]);
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