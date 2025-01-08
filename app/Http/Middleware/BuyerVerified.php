<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\ShopHelper;
use Illuminate\Support\Facades\Auth;
class BuyerVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $shopId = ShopHelper::getShopIdFromDomain();

        if ($shopId) {
            $user = Auth::guard('buyer')->user();  // گرفتن خریدار از گارد buyer

            // بررسی اینکه آیا خریدار تایید شده است
            if ($user && $user->shops()->where('shop_id', $shopId)
                    ->wherePivot('email_verified_at', '!=', null)
                    ->exists()) {

                return $next($request);  // ادامه درخواست
            }
            return redirect()->route('buyer.login')->with('error', 'Please verify your email.');
        } else {
            return redirect()->route('home')->with('error', 'Shop not found');
        }

    }
}
