<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Http;
use Exception;

class SmsService
{
    private function sendPattern(string $phone, int $templateId, array $parameters): void
    {
        $response = Http::withHeaders([
            'X-API-KEY'    => config('sms.sms_ir.api_key'),
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ])->post(config('sms.sms_ir.url'), [
            'mobile'      => $phone,
            'templateId'  => $templateId,
            'parameters'  => $parameters,
        ]);

        if (! $response->successful()) {
            throw new Exception('خطا در ارسال پیامک');
        }
    }


    public function sendOtp(string $phone, string $code): void
    {
        $this->sendPattern(
            $phone,
            720492,
            [
                [
                    'name'  => 'CODE',
                    'value' => $code,
                ]
            ]
        );
    }


    public function sendOrderShipped(string $phone, string $trackingCode): void
    {
        $this->sendPattern(
            $phone,
            123456, // ID قالب پیامک ارسال سفارش
            [
                [
                    'name'  => 'TRACKING_CODE',
                    'value' => $trackingCode,
                ]
            ]
        );
    }
}