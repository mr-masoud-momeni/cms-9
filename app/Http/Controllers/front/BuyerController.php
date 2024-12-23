<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buyer;
use App\Models\Shop;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Auth;

class BuyerController extends Controller
{
    public $pass;

    public function index()
    {
        return view('Frontend.Register.RegisterUser');
    }
    // ثبت‌نام خریدار و ارسال ایمیل تأیید
    public function register(Request $request)
    {
        // اعتبارسنجی اطلاعات
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
        ]);
        $scheme = $request->getScheme();// برمی‌گرداند 'http' یا 'https'
        // استخراج دامنه
        $host = $request->getHost();  // دریافت دامنه از درخواست
        $port = $request->getPort(); // پورت (مثل 8000 یا null)

        // اضافه کردن پورت فقط اگر پورت پیش‌فرض نباشد
        if ($port && ($port != 80 && $port != 443)) {
            $domain = "{$scheme}://{$host}:{$port}";
        } else {
            $domain = "{$scheme}://{$host}";
        }
        $shop = Shop::where('domain', $host)->firstOrFail(); // پیدا کردن فروشگاه با استفاده از دامنه

        // بررسی اینکه آیا خریدار قبلاً ثبت‌نام کرده است یا نه
        $buyer = Buyer::where('email', $request->email)
                        ->orwhere('phone', $request->phone)
                        ->first();
        if ($buyer) {
            // بررسی اینکه آیا این خریدار در این فروشگاه قبلاً ثبت‌نام کرده است یا نه
            $existingEntry = $buyer->shops()->where('shop_id', $shop->id)->exists();

            if ($existingEntry) {
                $existingEntry1 = $buyer->shops()->wherePivot('shop_id', $shop->id)->first();
                if($existingEntry1->pivot->email_verification_token){
                    return redirect()->back()->withErrors(['email1' => 'قبلا در این فروشگاه ثبت نام کرده اید ولی هنوز روی لینک فعالسازی ارسال شده به ایمیلتان کلیک نکرده اید.']);
                }else{
                    return redirect()->back()->withErrors(['email' => 'قبلا در این فروشگاه ثبت نام کرده اید.']);
                }
            }
        } else {
            $this->pass = Str::random(8);
            // اگر خریدار قبلاً در سیستم نبوده باشد، یک خریدار جدید ایجاد کنید
            $buyer = Buyer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($this->pass),
                'uuid' => Str::uuid(),
            ]);
        }
        if(isset($buyer)){
            // تولید توکن تأیید ایمیل
            $token = Str::random(60);

            // اتصال خریدار به فروشگاه با اطلاعات ایمیل و توکن تأیید
            $buyer->shops()->attach($shop->id, [
                'email' => $request->email,
                'phone' => $request->phone,
                'email_verification_token' => $token,
            ]);

            // ایجاد لینک تأیید ایمیل
            $verificationLink = "{$domain}/verify-email-user/{$buyer->uuid}/{$token}";

//                route('buyer.verify.email', ['uuid' => $buyer->uuid, 'token' => $token]);

            // ارسال ایمیل تأیید
            Mail::to($buyer->email)->send(new EmailVerificationMail($verificationLink, $this->pass));

            return redirect()->back()->with('message', 'ثبت نام شما با موفقیت انجام شد. لینک تایید و پسورد به ایمیل شما ارسال شده است.');
        }

    }

    // تأیید ایمیل
    public function verifyEmail(Request $request, $uuid, $token)
    {
        $scheme = $request->getScheme(); // 'http' یا 'https'
        $host = $request->getHost(); // نام دامنه (مثل 'localhost' یا 'example.com')
        $port = $request->getPort(); // پورت (مثل 8000 یا null)

        // اضافه کردن پورت فقط اگر پورت پیش‌فرض نباشد
        if ($port && ($port != 80 && $port != 443)) {
            $domain = "{$scheme}://{$host}:{$port}";
        } else {
            $domain = "{$scheme}://{$host}";
        }
        $shop = Shop::where('domain', $domain)->firstOrFail(); // پیدا کردن فروشگاه با استفاده از دامنه

        // پیدا کردن خریدار در فروشگاه مرتبط
        $buyer = Buyer::where('uuid', $uuid)->first();

        if ($buyer && $shop) {
            // بررسی اینکه آیا این خریدار در این فروشگاه قبلاً ثبت‌نام کرده است یا نه
            $existingEntry = $buyer->shops()->where('shop_id', $shop->id)->exists();

            if ($existingEntry) {
                // پیدا کردن رکورد در جدول واسط با استفاده از توکن
                $buyerShop = $buyer->shops()->where('email_verification_token', $token)->first();

                if ($buyerShop  && !$buyerShop ->pivot->email_verified_at) {
                    // تأیید ایمیل و حذف توکن تأیید
                    $buyerShop ->pivot->email_verified_at = now();
                    $buyerShop ->pivot->email_verification_token = null;
                    $buyerShop ->pivot->save();

                    return response()->json(['message' => 'Email verified successfully']);
                }

                return response()->json(['error' => 'Invalid verification token or email already verified'], 422);
            }
        } else {
            return response()->json(['message' => 'domain or user does not exist']);
        }

    }
    // نمایش فرم لاگین
    public function showLoginForm()
    {
        return view('Frontend.Login.loginUser');
    }

    // مدیریت لاگین خریدار
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // استفاده از گارد buyer برای لاگین
        if (Auth::guard('buyer')->attempt($credentials)) {
            return redirect()->intended('/buyer/dashboard')->with('message', 'Login successful!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
    // مدیریت خروج (logout) خریدار
    public function logout()
    {
        Auth::guard('buyer')->logout();

        return redirect()->route('buyer.login')->with('message', 'You have been logged out.');
    }
}
