<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {

            // مسیرهای مربوط به خریدار
            if ($request->is('buyer/*')) {
                return route('buyer.login');
            }

            // مسیرهای مربوط به ادمین
            if ($request->is('admin/*')) {
                return route('admin.login');
            }

            // مسیرهای مربوط به فروشگاه
            if ($request->is('shop/*')) {
                // چون shop/{path} برای لاگین نیاز به path دارد،
                // اینجا فعلاً نمی‌توانیم route را بدون path بسازیم.
                return url('/shop');
            }

            // پیش‌فرض
            return route('buyer.login');
        }
    }
}