<?php

namespace App\Services\Auth;

use App\Models\Buyer;

class BuyerAuthFlowService
{
    public function handlePhone(string $phone, int $shopId): string
    {
        $exists = Buyer::where('phone', $phone)
            ->whereHas('shops', function ($q) use ($shopId) {
                $q->where('shops.id', $shopId);
            })
            ->exists();

        return $exists ? 'password' : 'otp_register';
    }
}
