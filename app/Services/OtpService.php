<?php

namespace App\Services;

use App\Models\Otp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;

class OtpService
{
    const MAX_ACTIVE_OTP = 3;
    const MAX_ATTEMPTS = 3;
    const EXPIRE_MINUTES = 2;
    const BLOCK_MINUTES = 5;

    public function generate($otpable, string $purpose): string
    {
        $this->cleanupOldOtps($otpable, $purpose);

        $activeCount = Otp::where('otpable_type', get_class($otpable))
            ->where('otpable_id', $otpable->id)
            ->where('purpose', $purpose)
            ->where('expires_at', '>', now())
            ->count();

        if ($activeCount >= self::MAX_ACTIVE_OTP) {
            throw new Exception('Too many active OTP requests.');
        }

        $code = random_int(100000, 999999);

        Otp::create([
            'otpable_type' => get_class($otpable),
            'otpable_id'   => $otpable->id,
            'purpose'      => $purpose,
            'code_hash'    => Hash::make($code),
            'expires_at'   => now()->addMinutes(self::EXPIRE_MINUTES),
            'attempts'     => 0,
        ]);

        return (string)$code;
    }

    public function verify($otpable, string $purpose, string $code): void
    {
        $otp = Otp::where('otpable_type', get_class($otpable))
            ->where('otpable_id', $otpable->id)
            ->where('purpose', $purpose)
            ->latest()
            ->first();

        if (!$otp) {
            throw new Exception('OTP not found.');
        }

        if ($otp->blocked_until && $otp->blocked_until->isFuture()) {
            throw new Exception('OTP temporarily blocked.');
        }

        if ($otp->expires_at->isPast()) {
            $otp->delete();
            throw new Exception('OTP expired.');
        }

        if (!Hash::check($code, $otp->code_hash)) {
            $otp->increment('attempts');

            if ($otp->attempts >= self::MAX_ATTEMPTS) {
                $otp->update([
                    'blocked_until' => now()->addMinutes(self::BLOCK_MINUTES)
                ]);
            }

            throw new Exception('Invalid OTP.');
        }

        // Success
        $otp->delete();
    }

    protected function cleanupOldOtps($otpable, string $purpose): void
    {
        Otp::where('otpable_type', get_class($otpable))
            ->where('otpable_id', $otpable->id)
            ->where('purpose', $purpose)
            ->where('expires_at', '<', now())
            ->delete();
    }
}
