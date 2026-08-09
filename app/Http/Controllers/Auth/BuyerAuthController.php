<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Auth\OtpService;
use App\Services\Auth\BuyerAccountService;
use App\Helpers\ShopHelper;
use App\Models\Buyer;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BuyerAuthController extends Controller
{
    public function __construct(
        private OtpService $otp,
        private BuyerAccountService $account
    ) {}



    // ---------- Login / Phone Controllers ----------
    public function showPhone()
    {
        return view('Frontend.Shop.auth.phone');
    }
    public function submitPhone(Request $request)
    {
        $request->validate([
            'phone' => [
                'required',
                'digits:11',
                'regex:/^09\d{9}$/'
            ],
        ]);
        $shopId = ShopHelper::getShopId();

        if ($this->buyerExistsInShop($request->phone, $shopId)) {
            session(['login_phone' => $request->phone]);
            return redirect()->route('buyer.password.form');
        }

        try {
            $this->otp->send($request->phone, 'register');
            session([
                'otp_expires_at' => now()->addMinutes(3)
            ]);
        } catch (Exception $e) {
            return back()->withErrors(['phone' => $e->getMessage()]);
        }

        return view('Frontend.Shop.auth.otp', [
            'phone' => $request->phone,
            'purpose' => 'register'
        ]);
    }
    public function showPassword()
    {
        $phone = session('login_phone');

        if (! $phone) {
            return redirect()->route('buyer.login');
        }

        return view('Frontend.Shop.auth.password', compact('phone'));
    }
    public function login(Request $request)
    {
        $shopId = ShopHelper::getShopId();

        try {

            $this->account->login(
                $request->phone,
                $request->password,
                $shopId
            );

            return redirect()->route('buyer.dashboard');

        } catch (ValidationException $e) {

            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();

        }
    }
    public function logout(Request $request)
    {
        Auth::guard('buyer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('buyer.login');
    }



    
    // ---------- OTP Controllers ----------
    public function showOtpForm(Request $request)
    {
        return view('Frontend.Shop.auth.otp', [
            'phone'   => $request->phone,
            'purpose'=> $request->purpose,
        ]);
    }
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone'   => 'required',
            'code'    => 'required',
            'purpose' => 'required|in:register,reset',
        ]);

        try {
            $this->otp->verify(
                $request->phone,
                $request->purpose,
                $request->code
            );
        } catch (Exception $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        // OTP UI session رو پاک کن
        session()->forget(['otp_expires_at']);

        // 🔀 مسیر بر اساس هدف
        return match ($request->purpose) {

            'register' => $this->handleRegisterVerified($request->phone),

            'reset'    => $this->handleResetVerified($request->phone),

            default    => abort(400),
        };
    }



    // ---------- Register Controllers ----------
    public function showRegisterForm()
    {
        if (! session('register_context')) {
            abort(403);
        }

        return view('Frontend.Shop.auth.register');
    }
    public function register(Request $request)
    {
        // 1. بررسی context
        $context = session('register_context');

        if (! $context || empty($context['phone'])) {
            abort(403, 'دسترسی غیرمجاز');
        }

        if (now()->timestamp - $context['verified_at'] > 300) {
            session()->forget('register_context');
            abort(403, 'زمان ثبت‌نام منقضی شده');
        }

        $phone  = $context['phone'];
        $shopId = ShopHelper::getShopId();

        if (! $shopId) {
            abort(404, 'فروشگاه نامعتبر');
        }

        // 2. ساخت buyer
        $buyer = Buyer::create([
            'uuid'        => (string) Str::uuid(),
            'phone'       => $phone,
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => $request->password
                ? Hash::make($request->password)
                : null,
            'verified_at' => now(), // 👈 وریفای شد
        ]);

        // 3. اتصال به فروشگاه
        $buyer->shops()->attach($shopId, [
            'email' => $request->email,
            'phone' => $phone,
            'email_verified_at' => now(),
        ]);

        // 4. دادن رول buyer
        $buyer->attachRole('buyer');

        // 5. لاگین
        Auth::guard('buyer')->login($buyer);

        // 6. ست کردن context فروشگاه (برای middleware check.shop.buyer)
        session([
            'buyer_shop_context' => [
                'buyer_id' => $buyer->id,
                'shop_id'  => $shopId,
                'domain'   => request()->getHost(),
                'created_at' => now()->toDateTimeString(),
            ]
        ]);

        // 7. پاکسازی session موقت
        session()->forget('register_context');

        return redirect()->intended('/buyer/dashboard');
    }


    // ---------- Forgot / Reset Controllers ----------
    public function showForgotForm()
    {
        return view('Frontend.Shop.auth.forgot-phone');
    }
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required'
        ]);

        $shopId = ShopHelper::getShopId();

        $buyer = Buyer::where('phone', $request->phone)
            ->whereHas('shops', fn ($q) => $q->where('shops.id', $shopId))
            ->first();

        if (! $buyer) {
            return back()->withErrors(['phone' => 'کاربری با این شماره وجود ندارد']);
        }

        $this->otp->send($request->phone, 'reset');

        session([
            'reset_context' => [
                'phone' => $request->phone,
                'verified' => false,
            ]
        ]);

        return redirect()->route('buyer.otp.form', [
            'phone' => $request->phone,
            'purpose' => 'reset'
        ]);
    }
    public function showResetForm()
    {
        if (! session('reset_context.verified')) {
            abort(403);
        }

        return view('Frontend.Shop.auth.reset-password');
    }
    public function resetPassword(Request $request)
    {
        $context = session('reset_context');

        if (! $context || ! $context['verified']) {
            abort(403);
        }

        $request->validate([
            'password' => 'required|confirmed|min:6'
        ]);

        $buyer = Buyer::where('phone', $context['phone'])->firstOrFail();

        $buyer->update([
            'password' => Hash::make($request->password)
        ]);

        session()->forget('reset_context');

        return redirect()->route('buyer.login')
            ->with('success', 'رمز عبور تغییر کرد');
    }


    

    protected function buyerExistsInShop(string $phone, int $shopId): bool
    {
        return Buyer::where('phone', $phone)
            ->whereHas('shops', function ($q) use ($shopId) {
                $q->where('shops.id', $shopId);
            })
            ->exists();
    }


    protected function handleRegisterVerified(string $phone)
    {
        session([
            'register_context' => [
                'phone' => $phone,
                'verified_at' => now()->timestamp,
            ]
        ]);

        return redirect()->route('buyer.register.form');
    }
    protected function handleResetVerified(string $phone)
    {
        session([
            'reset_context' => [
                'phone'    => $phone,
                'verified' => true,
                'verified_at' => now()->timestamp,
            ]
        ]);

        return redirect()->route('buyer.reset.form');
    }

}
