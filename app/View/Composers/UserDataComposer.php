<?php


//developer comment: this class have not a artisan command to produce.
// in this class get user data and extract the odd order number.
// in fact, this class is called with provider named "ShareDataServiceProvider".

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Helpers\ShopHelper;
use App\Models\Order;

class UserDataComposer
{
    public function __construct(){}

    public function compose(View $view)
    {
        $order = null;
        $orderCount = 0;

        if (auth('buyer')->check()) {
<<<<<<< HEAD
            $buyer = auth('buyer')->user();
            $shopId = ShopHelper::getShopId();

            $loginCart = $buyer->orders()
                ->where('status', 0)
                ->where('shop_id', $shopId)
                ->with('products')
                ->first();

            $sessionCart = session()->get('cart', []);

            if ($loginCart) {
                foreach ($sessionCart as $productId => $quantity) {
                    $existing = $loginCart->products->firstWhere('id', $productId);
                    if ($existing) {
                        $loginCart->products()->updateExistingPivot($productId, [
                            'quantity' => $existing->pivot->quantity + $quantity
                        ]);
                    } else {
                        $loginCart->products()->attach($productId, ['quantity' => $quantity]);
                    }
                }
            } else {
                $loginCart = $buyer->orders()->create([
                    'shop_id' => $shopId,
                    'status' => 0,
                ]);

                foreach ($sessionCart as $productId => $quantity) {
                    $loginCart->products()->attach($productId, ['quantity' => $quantity]);
                }
            }

            session()->forget('cart');
            $order = $loginCart;
            $orderCount = $order->products->sum(fn($p) => $p->pivot->quantity);
=======
            $cartOrder = auth('buyer')->user()->orders()
                ->where('status', 0)
                ->with('products') // اگر لازم داشتی
                ->latest()
                ->first();

            $orderNumber = $cartOrder ? $cartOrder->products()->count() : 0;
>>>>>>> 297761ff61621ad906c5aa631ec33b63a35c006e
        }

        elseif (auth('web')->check()) {
            $order = 0;
            $orderCount = 0;
        }

        else {
            $sessionCart = session()->get('cart', []);
            $order = $sessionCart;

            // ساختار session: [productId => quantity]
            $orderCount = is_array($sessionCart)
                ? array_sum($sessionCart)
                : 0;
        }

        $view->with('order', $order);
        $view->with('orderCount', $orderCount);
    }
}

