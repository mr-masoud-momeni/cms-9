<?php

namespace App\Http\Middleware;

use App\Helpers\ShopHelper;
use Closure;
use Illuminate\Support\Facades\View;

class ResolveShopContext
{
    public function handle($request, Closure $next)
    {
        $shop = ShopHelper::getShop();

        View::share('shop', $shop);
        View::share('shippingCost', (int) ($shop->shipping_cost ?? 0));

        return $next($request);
    }
}
