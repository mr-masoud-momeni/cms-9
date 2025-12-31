<?php

namespace App\Services\Auth;

use App\Models\Otp;
use App\Models\Buyer;
use Exception;
use Illuminate\Support\Facades\Hash;
use function now;

class OtpService
{
    const MAX_ACTIVE_OTP = 1;
    const MAX_ATTEMPTS = 3;
    const EXPIRE_MINUTES = 3;
    const BLOCK_MINUTES = 5;

    public function __construct(
        private SmsService $sms,
    ) {}

    public function send(string $phone, string $purpose): void
    {
        $this->ensureCanSend($phone, $purpose);

        $code = random_int(100000, 999999);

        Otp::create([
            'phone'      => $phone,
            'purpose'    => $purpose,
            'code_hash'  => Hash::make($code),
            'expires_at' => now()->addMinutes(self::EXPIRE_MINUTES),
        ]);

        $this->sms->sendOtp($phone, $code);
    }

    public function verify(string $phone, string $purpose, string $code): void
    {
        $otp = Otp::where('phone', $phone)
            ->where('purpose', $purpose)
            ->latest()
            ->firstOrFail();

        if ($otp->blocked_until && $otp->blocked_until->isFuture()) {
            throw new Exception('موقتاً مسدود شده');
        }

        if ($otp->expires_at->isPast()) {
            $otp->delete();
            throw new Exception('کد منقضی شده');
        }

        if (!Hash::check($code, $otp->code_hash)) {
            $otp->increment('attempts');

            if ($otp->attempts >= self::MAX_ATTEMPTS) {
                $otp->update([
                    'blocked_until' => now()->addMinutes(self::BLOCK_MINUTES)
                ]);
            }

            throw new Exception('کد اشتباه است');
        }

        $otp->delete();
    }

    protected function ensureCanSend(string $phone, string $purpose): void
    {
        $active = Otp::where('phone', $phone)
            ->where('purpose', $purpose)
            ->where('expires_at', '>', now())
            ->exists();

        if ($active) {
            throw new Exception('تا پایان زمان باید صبر کنید');
        }
    }
}

