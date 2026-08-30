<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaleWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Bale Webhook', $request->all());

        return response()->json([
            'ok' => true,
        ]);
    }
}
