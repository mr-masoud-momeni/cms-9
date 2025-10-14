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
use Illuminate\Support\Facades\Hash;
use App\Helpers\ShopHelper;
class BuyerController extends Controller
{
    public $pass;

    public function index()
    {
        return view('Frontend.Shop.Register.RegisterUser');
    }
    public function dashboard(){
        return view('Frontend.Shop.Dashboard.DashboardUser');
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
            // ارسال ایمیل تأیید
            Mail::to($buyer->email)->send(new EmailVerificationMail($verificationLink, $this->pass));

            return redirect()->back()->with('message', 'ثبت نام شما با موفقیت انجام شد. لینک تایید و پسورد به ایمیل شما ارسال شده است.');
        }

    }

    // تأیید ایمیل
    public function verifyEmail(Request $request, $uuid, $token)
    {
        $host = preg_replace('/^www\./', '', $request->getHost());
        $parts = explode('.', $host);

        if (count($parts) > 2) {
            abort(403, 'Subdomains are not allowed');
        }

        $mainDomain = implode('.', array_slice($parts, -2));

        $shop = Shop::where('domain', $mainDomain)->firstOrFail();
        $buyer = Buyer::where('uuid', $uuid)->first();

        if (!$buyer) {
            return response()->json(['message' => 'کاربر مورد نظر وجود ندارد!']);
        }

        if (!$buyer->shops()->where('shop_id', $shop->id)->exists()) {
            return response()->json(['message' => 'این کاربر در این فروشگاه ثبت‌نام نکرده است!'], 422);
        }

        $buyerShop = $buyer->shops()
            ->wherePivot('email_verification_token', $token)
            ->first();

        if (!$buyerShop) {
            return response()->json(['error' => 'توکن معتبر نیست!'], 422);
        }

        if ($buyerShop->pivot->email_verified_at) {
            return response()->json(['error' => 'ایمیل شما قبلا اعتبارسنجی شده!'], 422);
        }

        $buyer->shops()->updateExistingPivot($shop->id, [
            'email_verified_at' => now(),
            'email_verification_token' => null,
        ]);

        $buyer->attachRole('buyer');

        return response()->json(['message' => 'ایمیل با موفقیت اعتبارسنجی شد.']);
    }

    // نمایش فرم لاگین
    public function showLoginForm()
    {
        return view('Frontend.Shop.Login.loginUser');
    }

    // مدیریت لاگین خریدار
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $shop = ShopHelper::getShop();

        if (!$shop) {
            return back()->withErrors(['shop' => 'فروشگاه پیدا نشد.']);
        }

        // خریدار را بر اساس ایمیل و فروشگاه پیدا کن
        $buyer = Buyer::where('email', $credentials['email'])
            ->whereHas('shops', fn($q) => $q->where('shops.id', $shop->id))
            ->first();

        if (!$buyer) {
            return back()->withErrors(['email' => 'این ایمیل در این فروشگاه ثبت نشده است.']);
        }

        // استفاده از attempt برای هندل درست لاگین
        if (Auth::guard('buyer')->attempt($credentials)) {
            $request->session()->regenerate(); // جلوگیری از session fixation
            return redirect()->intended('/buyer/dashboard');
        }

        return back()->withErrors(['email' => 'ایمیل یا رمز عبور نادرست است.']);
    }

    // مدیریت خروج (logout) خریدار
    public function logout()
    {
        Auth::guard('buyer')->logout();

        return redirect()->route('buyer.login')->with('message', __('message.logout_msg'));
    }
}
