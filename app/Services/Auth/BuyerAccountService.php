<?php

namespace App\Services\Auth;

use App\Models\Buyer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class BuyerAccountService
{
    public function register(array $data, int $shopId): Buyer
    {
        $buyer = Buyer::create([
            'name'     => $data['name'],
            'phone'    => $data['phone'],
            'shop_id'  => $shopId,
            'password' => Hash::make($data['password']),
        ]);

        Auth::guard('buyer')->login($buyer);

        return $buyer;
    }

    public function login(string $phone, string $password, int $shopId): Buyer
    {
        $buyer = Buyer::where('phone', $phone)
            ->whereHas('shops', function ($q) use ($shopId) {
                $q->where('shops.id', $shopId);
            })
            ->firstOrFail();

        $credentials = [
            'id'       => $buyer->id,      // خیلی مهم
            'password' => $password,
        ];

        if (! Auth::guard('buyer')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'password' => 'رمز عبور اشتباه است'
            ]);
        }

        session([
            'buyer_shop_context' => [
                'buyer_id'   => $buyer->id,
                'shop_id'    => $shopId,
                'domain'     => request()->getHost(),
                'created_at' => now()->toDateTimeString(),
            ]
        ]);

        return Auth::guard('buyer')->user();
    }


    public function resetPassword(string $phone, string $password, int $shopId): void
    {
        $buyer = Buyer::where('phone', $phone)
            ->where('shop_id', $shopId)
            ->firstOrFail();

        $buyer->update([
            'password' => Hash::make($password)
        ]);
    }
    // مدیریت خروج (logout) خریدار
//    public function logout()
//    {
//        Auth::guard('buyer')->logout();
//
//        return redirect()->route('buyer.login')->with('message', __('message.logout_msg'));
//    }
    public function logout(Request $request)
    {
        Auth::guard('buyer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('buyer.login');
    }

}
