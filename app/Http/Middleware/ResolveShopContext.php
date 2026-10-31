<?php

namespace App\Http\Middleware;

use App\Helpers\ShopHelper;
use Closure;
use Illuminate\Support\Facades\View;

class ResolveShopContext
{
    public function handle($request, Closure $next)
    {
        $platformDomain = preg_replace('/^www\\./', '', config('app.platform_domain'));
        $requestHost = preg_replace('/^www\\./', '', $request->getHost());

        // دامنه اصلی پلتفرم فروشگاه مستقلی نیست و نباید shop context بگیرد.
        if ($requestHost === $platformDomain) {
            return $next($request);
        }

        $shop = ShopHelper::getShop();

        View::share('shop', $shop);
        View::share('shippingCost', (int) ($shop->shipping_cost ?? 0));

        return $next($request);
    }
}
