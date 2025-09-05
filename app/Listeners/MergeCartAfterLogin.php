<?php

namespace App\Listeners;

use App\Helpers\ShopHelper;
use App\Models\Product;          // مطمئن شو نام کلاس دقیق همین است (P بزرگ!)
use Illuminate\Support\Facades\Session;

class MergeCartAfterLogin
{
    public function __construct()
    {
        //
    }

    public function handle($event)
    {
        // فقط وقتی کاربر buyer لاگین است
        if (!auth('buyer')->check()) {
            return;
        }

        $buyer  = auth('buyer')->user();
        $shopId = ShopHelper::getShopId();

        // سفارش باز کاربر
        $loginCart = $buyer->orders()
            ->where('status', 0)
            ->where('shop_id', $shopId)
            ->with('products')
            ->first();

        $sessionCart = Session::get('cart', []);

        if (!$sessionCart) {
            return;
        }

        if ($loginCart) {
            // اگر سفارش باز موجود است، محصولات سشن را با آن ادغام کن
            foreach ($sessionCart as $productId => $quantity) {
                // قیمت محصول را از جدول محصول بگیر
                $product = Product::find($productId);
                if (!$product) {
                    continue; // اگر محصول پیدا نشد
                }

                $existing = $loginCart->products->firstWhere('id', $productId);

                if ($existing) {
                    $loginCart->products()->updateExistingPivot($productId, [
                        'quantity' => $existing->pivot->quantity + $quantity,
                        'price'    => $product->price,  // قیمت را ذخیره کن
                    ]);
                } else {
                    $loginCart->products()->attach($productId, [
                        'quantity' => $quantity,
                        'price'    => $product->price,
                    ]);
                }
            }
        } else {
            // اگر سفارش باز نداریم، بساز
            $loginCart = $buyer->orders()->create([
                'shop_id' => $shopId,
                'status'  => 0,
            ]);

            foreach ($sessionCart as $productId => $quantity) {
                $product = Product::find($productId);
                if (!$product) {
                    continue;
                }

                $loginCart->products()->attach($productId, [
                    'quantity' => $quantity,
                    'price'    => $product->price,
                ]);
            }
        }

        Session::forget('cart');
    }
}
