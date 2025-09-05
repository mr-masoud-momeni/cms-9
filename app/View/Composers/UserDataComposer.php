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
        $orderNumber = 0;

        if (auth('buyer')->check()) {
            $buyer = auth('buyer')->user();
            $shopId = ShopHelper::getShopId();

            $loginCart = $buyer->orders()
                ->where('status', 0)
                ->where('shop_id', $shopId)
                ->with('products')
                ->first();

            if ($loginCart) {
                $order = $loginCart;
                $orderCount = $loginCart->products->sum(fn($p) => $p->pivot->quantity);
            $orderNumber = $loginCart->products()->count();
        }
        } elseif (auth('web')->check()) {
            $order = 0;
        } else {
            $sessionCart = session()->get('cart', []);
            $order = $sessionCart;
            $orderCount = is_array($sessionCart) ? array_sum($sessionCart) : 0;
        }

        $view->with(compact('order','orderCount', 'orderNumber'));
    }

}

