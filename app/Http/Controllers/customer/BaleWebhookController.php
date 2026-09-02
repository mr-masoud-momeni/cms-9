<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\ShopBaleConnection;
use App\Models\ShopBaleConnectionToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BaleWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Bale Webhook', $request->all());

        $message = $request->input('message');

        if (!$message) {
            return response()->json(['ok' => true]);
        }

        $text = $message['text'] ?? '';

        // فقط /start را پردازش می‌کنیم
        if (!str_starts_with($text, '/start')) {
            return response()->json(['ok' => true]);
        }

        // استخراج Token از /start TOKEN
        $token = trim(substr($text, 6));

        if (!$token) {
            return response()->json(['ok' => true]);
        }

        // بررسی Token
        $connectionToken = ShopBaleConnectionToken::where('token', $token)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$connectionToken) {
            Log::warning('Invalid Bale connection token', [
                'token' => $token,
            ]);

            return response()->json(['ok' => true]);
        }

        // اطلاعات کاربر Bale
        $baleUserId = $message['from']['id'] ?? null;
        $baleChatId = $message['chat']['id'] ?? null;

        if (!$baleUserId || !$baleChatId) {
            return response()->json(['ok' => true]);
        }

        // ایجاد / بروزرسانی اتصال
        $connection = ShopBaleConnection::updateOrCreate(
            [
                'shop_id' => $connectionToken->shop_id,
                'user_id' => $connectionToken->user_id,
            ],
            [
                'bale_user_id' => $baleUserId,
                'bale_chat_id' => $baleChatId,
                'active' => true,
                'connected_at' => now(),
            ]
        );

        // Token مصرف شد
        $connectionToken->update([
            'used_at' => now(),
        ]);

        Log::info('Bale connection created', [
            'shop_id' => $connection->shop_id,
            'user_id' => $connection->user_id,
            'bale_user_id' => $baleUserId,
            'bale_chat_id' => $baleChatId,
        ]);

        return response()->json([
            'ok' => true,
        ]);
    }
}