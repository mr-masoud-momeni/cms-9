<?php

namespace App\Services\Auth;

use App\Models\Otp;
use App\Models\Buyer;
use Exception;
use Illuminate\Support\Facades\Hash;
use function now;
use App\Services\Security\LimiterService;
use App\Support\SecurityKey;

class OtpService
{
    const MAX_ATTEMPTS = 3;
    const EXPIRE_MINUTES = 3;
    const BLOCK_MINUTES = 5;

    public function __construct(
        private SmsService $sms,
        private LimiterService $limiter,
    ) {}

    public function send(string $phone, string $purpose): void
    {
        $key = SecurityKey::otpSend($purpose, $phone);

        if ($this->limiter->tooManyAttempts($key, 1)) {
            throw new Exception(
                'لطفاً ' . $this->limiter->availableIn($key) . ' ثانیه دیگر دوباره تلاش کنید.'
            );
        }

        Otp::where('phone', $phone)
        ->where('purpose', $purpose)
        ->delete();

        $code = random_int(100000, 999999);

        $otp = Otp::create([
            'phone'      => $phone,
            'purpose'    => $purpose,
            'code_hash'  => Hash::make($code),
            'expires_at' => now()->addMinutes(self::EXPIRE_MINUTES),
        ]);

        try {
            $this->sms->sendOtp($phone, $code);
            $this->limiter->hit($key, self::EXPIRE_MINUTES * 60);
        } catch (\Throwable $e) {
            $otp->delete();
            throw $e;
        }
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

            $otp->refresh();
            
            if ($otp->attempts >= self::MAX_ATTEMPTS) {
                $otp->update([
                    'blocked_until' => now()->addMinutes(self::BLOCK_MINUTES)
                ]);
            }

            throw new Exception('کد اشتباه است');
        }

        $otp->delete();
    }
}

