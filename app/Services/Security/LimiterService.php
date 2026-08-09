<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\RateLimiter;

class LimiterService
{
    /**
     * آیا به سقف رسیده؟
     */
    public function tooManyAttempts(string $key, int $maxAttempts): bool
    {
        return RateLimiter::tooManyAttempts($key, $maxAttempts);
    }

    /**
     * ثبت یک تلاش
     */
    public function hit(string $key, int $decaySeconds): int
    {
        return RateLimiter::hit($key, $decaySeconds);
    }

    /**
     * پاک کردن تلاش‌ها
     */
    public function clear(string $key): void
    {
        RateLimiter::clear($key);
    }

    /**
     * چند ثانیه دیگر آزاد می‌شود؟
     */
    public function availableIn(string $key): int
    {
        return RateLimiter::availableIn($key);
    }

    /**
     * تعداد تلاش انجام شده
     */
    public function attempts(string $key): int
    {
        return RateLimiter::attempts($key);
    }

    /**
     * چند تلاش باقی مانده؟
     */
    public function remaining(string $key, int $maxAttempts): int
    {
        return RateLimiter::remaining($key, $maxAttempts);
    }
}