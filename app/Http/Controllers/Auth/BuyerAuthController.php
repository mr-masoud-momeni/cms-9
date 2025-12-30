<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Auth\OtpService;
use App\Services\Auth\BuyerAccountService;
use App\Services\Auth\BuyerAuthFlowService;
use App\Helpers\ShopHelper;
use App\Models\Buyer;

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
        } catch (Exception $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        return view('buyer.auth.register', [
            'phone' => $request->phone
        ]);
    }


    public function register(RegisterRequest $request)
    {
        $shopId = ShopHelper::getShopId();

        $buyer = Buyer::create([
            'uuid'     => Str::uuid(),
            'name'     => $request->name,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $buyer->shops()->attach($shopId);

        Auth::guard('buyer')->login($buyer);

        return redirect('/buyer/dashboard');
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
