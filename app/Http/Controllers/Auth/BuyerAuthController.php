<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Auth\OtpService;
use App\Services\Auth\BuyerAccountService;
use App\Services\Auth\BuyerAuthFlowService;
use App\Helpers\ShopHelper;
use App\Models\Buyer;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BuyerAuthController extends Controller
{
    public function __construct(
        private BuyerAuthFlowService $flow,
        private OtpService $otp,
        private BuyerAccountService $account
    ) {}

    public function showPhone()
    {
        return view('Frontend.auth.phone');
    }

    public function submitPhone(Request $request)
    {
        $shopId = ShopHelper::getShopId();

        if ($this->buyerExistsInShop($request->phone, $shopId)) {
            return view('Frontend.auth.password', [
                'phone' => $request->phone
            ]);
        }

        try {
            $this->otp->send($request->phone, 'register');
            session([
                'otp_expires_at' => now()->addMinutes(3)
            ]);
        } catch (Exception $e) {
            return back()->withErrors(['phone' => $e->getMessage()]);
        }

        return view('Frontend.auth.otp', [
            'phone' => $request->phone,
            'purpose' => 'register'
        ]);
    }
    protected function buyerExistsInShop(string $phone, int $shopId): bool
    {
        return Buyer::where('phone', $phone)
            ->whereHas('shops', function ($q) use ($shopId) {
                $q->where('shops.id', $shopId);
            })
            ->exists();
    }

    public function verifyOtp(Request $request)
    {
        try {
            $this->otp->verify(
                $request->phone,
                'register',
                $request->code
            );
            session()->forget([
                'otp_expires_at',
            ]);
            session([
                'register_context' => [
                    'phone' => $request->phone,
                    'verified_at' => now()->timestamp,
                ]
            ]);
        } catch (Exception $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        return view('Frontend.auth.register', [
            'phone' => $request->phone
        ]);
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

    public function login(Request $request)
    {
        $shopId = ShopHelper::getShopId();

        if (! $shopId) {
            abort(404);
        }

        $this->account->login(
            $request->phone,
            $request->password,
            $shopId
        );

        return redirect()->route('buyer.dashboard');
    }

    public function forgotPassword(Request $request)
    {
        $this->otp->send($request->phone, 'reset');

        return view('buyer.auth.otp', [
            'phone' => $request->phone,
            'purpose' => 'reset'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $shopId = ShopHelper::getShopId();

        if (! $shopId) {
            abort(404);
        }

        $this->account->resetPassword(
            $request->phone,
            $request->password,
            $shopId
        );

        return redirect()->route('buyer.login.path');
    }
    // مدیریت خروج (logout) خریدار
    public function logout()
    {
        $this->account->logout();
    }
}
