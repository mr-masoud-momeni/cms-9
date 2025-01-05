<?php

namespace App\Http\Controllers\Auth\customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function dashboard(){
        return view('Customer.Layouts.Master');
    }
    public function showLoginForm($path)
    {
        $user = User::where('path', $path)->first();


        if (!$user) {
            abort(404); // اگر توکن معتبر نیست، خطای 404 نمایش دهید
        }

        // هدایت به صفحه لاگین بر اساس نقش کاربر
        if ($user->hasRole('shop_owner')) {
            return view('Customer.auth.login', compact('path'));
        } elseif ($user->hasRole('buyer')) {
            return view('auth.buyer-login', compact('user'));
        }

        return redirect()->route('home'); // هدایت به صفحه اصلی در صورت عدم تطابق
    }
    public function login(Request $request, $path)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $pathuser = $user->path;
            if($path == $pathuser){
                // اگر لاگین موفق بود
                $request->session()->regenerate();

                return redirect()->intended('customer/dashboard');
            }
            else{
                Auth::logout();
                // اگر لاگین موفق نبود
                return back()->withErrors([
                    'email' => 'اطلاعات ورود صحیح نمی‌باشد.',
                ])->onlyInput('email');
            }

        }
        // اگر لاگین موفق نبود
        return back()->withErrors([
            'email' => 'اطلاعات ورود صحیح نمی‌باشد.',
        ])->onlyInput('email');
    }

    // خروج مشتری
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

}
