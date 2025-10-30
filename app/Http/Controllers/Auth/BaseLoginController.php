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

    public function showLoginForm($path=null)
    {
        if($path){
            $user = User::where('path', $path)->first();
            if (!$user) {
                abort(404);
            }

            return view($this->view);
        }
        return view($this->view);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $currentDomain = $request->getHost();

        // پیدا کردن کاربر بر اساس ایمیل
        $user = User::where('email', $credentials['email'])->first();

        if (! $user) {
            return back()->withErrors(['email' => 'کاربر یافت نشد']);
        }

        // اگر کاربر نقش ادمین اصلی دارد
        if ($user->role === 'admin') {
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended($this->redirectTo);
            }

            return back()->withErrors(['email' => 'اطلاعات ورود اشتباه است']);
        }

        // اگر ادمین فروشگاه است آیا به فروشگاهی وصل است؟
        if (! method_exists($user, 'shop')) {
            return back()->withErrors(['domain' => 'حساب شما به فروشگاه خاصی متصل نیست.']);
        }

        $shop = $user->shop()->first();
        if (! $shop) {
            return back()->withErrors(['domain' => 'حساب شما به هیچ فروشگاهی متصل نیست.']);
        }

        // بررسی دامنه
        if ($shop->domain !== $currentDomain) {
            return back()->withErrors(['domain' => 'ورود از این دامنه مجاز نیست.']);
        }

        // حالا لاگین کن و سشن بساز
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

        return back()->withErrors(['email' => 'اطلاعات ورود اشتباه است']);
    }


    public function logout(Request $request)
    {
        Auth::guard($this->guard)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
