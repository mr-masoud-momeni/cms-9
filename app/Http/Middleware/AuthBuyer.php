<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthBuyer
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

        // بررسی می‌کنیم که آیا کاربر از گارد "buyer" وارد شده است یا نه
        if (!Auth::guard('buyer')->check()) {
            dd('dfdf');
            return redirect()->route('buyer.login.path'); // به صفحه ورود هدایت می‌کنیم
        }
        return $next($request);
    }
}
