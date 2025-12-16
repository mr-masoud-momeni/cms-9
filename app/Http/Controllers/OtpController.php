<?php

namespace App\Http\Controllers;
use App\Models\Buyer;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class OtpController extends Controller
{
    public function send(Request $request, OtpService $otpService)
    {
        $request->validate([
            'mobile' => 'required'
        ]);

        $buyer = Buyer::firstOrCreate(
            ['phone' => $request->mobile],
            ['email' => Str::uuid().'@temp.local']
        );

        $code = $otpService->generate($buyer, 'login');

        // TODO: send sms
        // SmsService::send($buyer->phone, $code);

        return response()->json(['message' => 'OTP sent']);
    }

    public function verify(Request $request, OtpService $otpService)
    {
        $request->validate([
            'mobile' => 'required',
            'code'   => 'required'
        ]);

        $buyer = Buyer::where('phone', $request->mobile)->firstOrFail();

        $otpService->verify($buyer, 'login', $request->code);

        return response()->json(['verified' => true]);
    }
}

