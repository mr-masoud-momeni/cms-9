<?php

namespace App\Listeners;

use App\Helpers\ShopHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\product;
class MergeCartAfterLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        // اگر خریدار لاگین کرده بود
        if (auth('buyer')->check()) {
            $buyer = auth('buyer')->user();
            $shopId = ShopHelper::getShopId();
            $order = $buyer->orders()
                ->where('status', 0)
                ->where('shop_id', $shopId)
                ->with('products') // پیش‌بارگذاری برای دسترسی راحت‌تر
                ->first();
            //دریافت اطلاعات محصول کاربر از سشن
            $cart = session()->get('cart', []);
            if($cart){
                foreach ($cart as $productId => $quantity) {
                    $existing = $order->products->firstWhere('id', $productId);
                    if ($existing) {
                        // گرفتن مقدار قبلی از pivot
                        $oldQty = $existing->pivot->quantity ?? 0;
                        $order->products()->updateExistingPivot($productId, [
                            'quantity' => $oldQty + $quantity
                        ]);
                    } else {
                        $product= product::where('id', $productId)->first();
                        $order->products()->attach($productId, [
                            'quantity' => $quantity,
                            'price' => $product->price,
                        ]);
                    }
                }
            }
        }
        session()->forget('cart');
    }
}
