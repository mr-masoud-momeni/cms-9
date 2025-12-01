<?php
namespace App\Http\Middleware;

use App\Models\Shop;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBuyerShopContext
{
    public function handle(Request $request, Closure $next)
    {
        // 1) لاگین هست؟
        if (! Auth::guard('buyer')->check()) {
            return redirect()->route('buyer.login');
        }

        $buyer = Auth::guard('buyer')->user();

        // 2) سشن context داریم؟
        $context = session('buyer_shop_context');
        if (! $context) {
            Auth::guard('buyer')->logout();
            return redirect()->route('buyer.login');
        }

        // 3) دامنه فعلی
        $currentDomain = $request->getHost();

        // دامنه باید با context ذخیره‌شده یکی باشه
        if ($currentDomain !== $context['domain']) {
            Auth::guard('buyer')->logout();
            session()->forget('buyer_shop_context');
            return redirect()->route('buyer.login')
                ->withErrors(['email' => 'خطایی به وجود آمده است.']);
        }

        // 4) چک کن shop وجود داره و buyer واقعاً به این shop وصله
        $shop = Shop::where('id', $context['shop_id'])
            ->where('domain', $currentDomain)
            ->first();

        if (! $shop) {
            Auth::guard('buyer')->logout();
            session()->forget('buyer_shop_context');
            return redirect()->route('buyer.login')
                ->withErrors(['email' => 'فروشگاه معتبر نیست.']);
        }

        $hasAccess = $buyer->shops()
            ->where('shops.id', $shop->id)
            ->exists();

        if (! $hasAccess) {
            Auth::guard('buyer')->logout();
            session()->forget('buyer_shop_context');
            return redirect()->route('buyer.login')
                ->withErrors(['email' => 'حساب شما برای این فروشگاه فعال نیست.']);
        }

        // اگر خواستی، میتونی shop رو توی ریکوئست ست کنی
        $request->attributes->set('shop', $shop);
        app()->instance('currentShop', $shop);

        return $next($request);
    }
}
