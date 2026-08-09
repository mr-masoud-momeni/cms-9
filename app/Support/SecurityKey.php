<?php

namespace App\Support;

class SecurityKey
{
    public static function login(string $phone): string
    {
        return 'login:' . self::ip() . ':' . $phone;
    }

    public static function otpSend(string $purpose, string $phone): string
    {
        return 'otp:send' . $purpose . ':' . self::ip() . ':' . $phone;
    }

    public static function passwordReset(string $phone): string
    {
        return 'reset:' . self::ip() . ':' . $phone;
    }

    public static function custom(string $key): string
    {
        return $key;
    }
    protected static function ip(): string
    {
        return request()->ip();
    }
}