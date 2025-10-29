<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckShopContext
{
public function handle($request, Closure $next){

    if (!Auth::guard('shop_admin')->check()) {
        return redirect()->route('shop.login');
    }
    $context = session('shop_context');
    if (!$context) {
        Auth::guard('shop_admin')->logout();
        return redirect()->route('shop.login');
    }
    // مقایسه دامنه یا path فعلی با context ذخیره‌شده
    if ($request->getHost() !== $context['domain']) {
        Auth::guard('shop_admin')->logout();
        session()->forget('shop_context');
        return redirect()->route('shop.login')
        ->withErrors(['email' => 'دسترسی به این دامنه مجاز نیست.']);
    }
    return $next($request);
}
}
