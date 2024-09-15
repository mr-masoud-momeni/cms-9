<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buyer;
use App\Models\Shop;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;

class BuyerController extends Controller
{

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

        // استخراج دامنه
        $host = $request->getHost();  // دریافت دامنه از درخواست
        $shop = Shop::where('domain', $host)->firstOrFail(); // پیدا کردن فروشگاه با استفاده از دامنه

        // بررسی اینکه آیا خریدار قبلاً در این فروشگاه ثبت‌نام کرده است یا نه
        $buyer = Buyer::where('email', $request->email)->first();

        if ($buyer) {
            // بررسی اینکه آیا این خریدار در این فروشگاه قبلاً ثبت‌نام کرده است یا نه
            $existingEntry = $buyer->shops()->where('store_id', $shop->id)->exists();

            if ($existingEntry) {
                return redirect()->back()->withErrors(['email' => 'This buyer has already registered in this store.']);
            }
        } else {
            // اگر خریدار قبلاً در سیستم نبوده باشد، یک خریدار جدید ایجاد کنید
            $buyer = Buyer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'uuid' => Str::uuid(),
            ]);
        }

        // تولید توکن تأیید ایمیل
        $token = Str::random(60);

        // اتصال خریدار به فروشگاه با اطلاعات ایمیل و توکن تأیید
        $buyer->shops()->attach($shop->id, [
            'email' => $request->email,
            'phone' => $request->phone,
            'email_verification_token' => $token,
        ]);

        // ایجاد لینک تأیید ایمیل
        $verificationLink = route('verify.email', ['uuid' => $buyer->uuid, 'token' => $token]);

        // ارسال ایمیل تأیید
        Mail::to($buyer->email)->send(new EmailVerificationMail($verificationLink));

        return redirect()->back()->with('message', 'Registration successful. Verification email sent.');
    }

    // تأیید ایمیل
    public function verifyEmail($uuid, $token)
    {
        // پیدا کردن خریدار با UUID
        $buyer = Buyer::where('uuid', $uuid)->firstOrFail();

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
}
