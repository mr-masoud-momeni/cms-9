<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;
class CheckBuyerActiveStatus
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
        $user = Auth::guard('buyer')->user();

        if ($user) {

            $host = $request->getHost(); // دریافت دامنه از درخواست
            $shop = Shop::where('domain', $host)->first();
            $isActive = $user->shops()->where('shop_id', $shop->id)->wherePivot('email_verified_at', '!=', null)->exists();

            if (!$isActive) {
                Auth::guard('buyer')->logout();
                return redirect()->route('buyer.login')->with('message', 'You have been logged out.');
            }
        }

        return $next($request);
    }
}
