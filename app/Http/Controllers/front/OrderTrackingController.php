<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shop;
use App\Services\CustomerOrderLinkService;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function show(Request $request, $order, $expires, $token)
    {
        if (!ctype_digit((string) $order) || !ctype_digit((string) $expires) || (int) $expires < now()->timestamp) {
            abort(404);
        }

        $shop = Shop::current();
        if (!$shop) {
            abort(404);
        }

        $orderModel = Order::with('products')
            ->where('id', (int) $order)
            ->where('shop_id', $shop->id)
            ->firstOrFail();

        $expectedToken = app(CustomerOrderLinkService::class)
            ->makeToken($shop, $orderModel, (int) $expires);

        if (!hash_equals($expectedToken, (string) $token)) {
            abort(404);
        }

        return view('Frontend.Shop.Orders.track', [
            'order' => $orderModel,
            'shop' => $shop,
        ]);
    }
}
