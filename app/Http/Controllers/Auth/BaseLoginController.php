<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

abstract class BaseLoginController extends Controller
{
    protected string $guard;
    protected string $redirectTo;
    protected string $view;

    public function showLoginForm($path = null)
    {
        if ($path) {

            // پیدا کردن یوزر
            $user = User::where('path', $path)->first();
            if (!$user) {

                abort(404); // پیام عمومی
            }

            // اطلاعات فروشگاه
            $shop = $user->shop;
            if (!$shop) {
                abort(404);
            }

            // دامنه درخواست
            $requestDomain = request()->getHost();

            // دامنه فروشگاه
            $shopDomain = $shop->domain;

            // مقایسه دامنه‌ها
            if ($requestDomain !== $shopDomain) {
                abort(404); // بدون افشای علت دقیق
            }

            return view($this->view);
        }

        return view($this->view);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        $currentDomain = $request->getHost();

        // پیدا کردن کاربر (بدون پیام افشاگر)
        $user = User::where('email', $credentials['email'])->first();

        if (! $user) {
            return back()->withErrors(['email' => 'ورود نامعتبر است.']);
        }

        // اگر نقش ادمین اصلی دارد
        if ($user->hasRole('admin')) {

            if (Auth::guard($this->guard)->attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended($this->redirectTo);
            }

            return back()->withErrors(['email' => 'ورود نامعتبر است.']);
        }

         // اگر یوزر متدی برای فروشگاه ندارد
        //برای جلوگبری از خطای در خط بعدی
        if (! method_exists($user, 'shop')) {
            return back()->withErrors(['email' => 'ورود نامعتبر است.']);
        }

        // بررسی فروشگاه
        $shop = $user->shop()->first();
        if (! $shop) {
            return back()->withErrors(['email' => 'ورود نامعتبر است.']);
        }

        // بررسی دامنه
        if ($shop->domain !== $currentDomain) {
            return back()->withErrors(['email' => 'ورود نامعتبر است.']);
        }

        // تلاش برای ورود
        if (Auth::guard($this->guard)->attempt($credentials)) {

            $user = Auth::guard($this->guard)->user();

            session([
                'shop_context' => [
                    'id' => $shop->id,
                    'domain' => $shop->domain,
                    'user_id' => $user->id,
                    'created_at' => now()->toDateTimeString(),
                ],
            ]);

            return redirect()->intended($this->redirectTo);
        }

        return back()->withErrors(['email' => 'ورود نامعتبر است.']);
    }


    public function logout(Request $request)
    {
        Auth::guard($this->guard)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
