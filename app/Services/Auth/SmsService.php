<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Http;
use Exception;

class SmsService
{
    public function sendOtp(string $phone, string $code): void
    {
        $response = Http::withHeaders([
            'X-API-KEY' => config('sms.sms_ir.api_key'),
            'Accept'    => 'application/json',
            'Content-Type' => 'application/json',
        ])->post(config('sms.sms_ir.url'), [
            'mobile' => $phone,
            'templateId' => 720492, // ID قالب تایید در sms.ir
            'parameters' => [
                [
                    'name'  => 'CODE',
                    'value' => $code
                ]
            ]
        ]);

        if (! $response->successful()) {
            throw new Exception('خطا در ارسال پیامک');
        }
    }
}
