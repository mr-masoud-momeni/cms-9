<?php

namespace App\Listeners;

use App\Helpers\ShopHelper;
use App\Models\Product;
use Illuminate\Support\Facades\Session;

class MergeCartAfterLogin
{
    public function __construct()
    {
        //
    }

    public function handle($event)
    {
        if (!auth('buyer')->check()) {
            return;
        }

        $buyer = auth('buyer')->user();
        $shopId = ShopHelper::getShopId();

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
            foreach ($sessionCart as $productId => $quantity) {
                $product = Product::where('id', $productId)
                    ->where('shop_id', $shopId)
                    ->first();

                if (!$product) {
                    continue;
                }

                $existing = $loginCart->products->firstWhere('id', $productId);

                if ($existing) {
                    $loginCart->products()->updateExistingPivot($productId, [
                        'quantity' => $existing->pivot->quantity + $quantity,
                        'price' => $product->price,
                    ]);
                } else {
                    $loginCart->products()->attach($productId, [
                        'quantity' => $quantity,
                        'price' => $product->price,
                    ]);
                }
            }
        } else {
            $loginCart = $buyer->orders()->create([
                'shop_id' => $shopId,
                'status' => 0,
            ]);

            foreach ($sessionCart as $productId => $quantity) {
                $product = Product::where('id', $productId)
                    ->where('shop_id', $shopId)
                    ->first();

                if (!$product) {
                    continue;
                }

                $loginCart->products()->attach($productId, [
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);
            }
        }

        Session::forget('cart');
    }
}
