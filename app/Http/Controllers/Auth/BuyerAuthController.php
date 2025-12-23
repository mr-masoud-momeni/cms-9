<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Auth\OtpService;
use App\Services\Auth\BuyerAccountService;
use App\Services\Auth\BuyerAuthFlowService;
use App\Helpers\ShopHelper;

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

        if (! $shopId) {
            abort(404);
        }

        $step = $this->flow->handlePhone(
            $request->phone,
            $shopId
        );

        if ($step === 'password') {
            return view('Frontend.auth.password', [
                'phone' => $request->phone
            ]);
        }

        $this->otp->send($request->phone, 'register');

        return view('buyer.auth.otp', [
            'phone' => $request->phone,
            'purpose' => 'register'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $verified = $this->otp->verify(
            $request->phone,
            $request->code,
            $request->purpose
        );

        if (! $verified) {
            return back()->withErrors(['code' => 'کد وارد شده نادرست است']);
        }

        if ($request->purpose === 'register') {
            return view('buyer.auth.register', [
                'phone' => $request->phone
            ]);
        }

        if ($request->purpose === 'reset') {
            return view('buyer.auth.reset-password', [
                'phone' => $request->phone
            ]);
        }
    }

    public function register(Request $request)
    {
        $shopId = ShopHelper::getShopId();

        if (! $shopId) {
            abort(404);
        }

        $this->account->register(
            $request->only('name', 'phone', 'password'),
            $shopId
        );

        return redirect()->route('buyer.dashboard');
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
}
