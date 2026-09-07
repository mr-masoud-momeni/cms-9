<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\ShopBaleConnection;
use App\Models\ShopBaleConnectionToken;
use App\Services\BaleService;
use App\Services\CustomerOrderLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BaleWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Bale Webhook', $request->all());

        if ($request->input('callback_query')) {
            return $this->handleCallbackQuery($request->input('callback_query'));
        }

        $message = $request->input('message');

        if (!$message) {
            return response()->json(['ok' => true]);
        }

        $text = $message['text'] ?? '';

        if (!str_starts_with($text, '/start')) {
            return response()->json(['ok' => true]);
        }

        $token = trim(substr($text, 6));

        if (!$token) {
            return response()->json(['ok' => true]);
        }

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

        $baleUserId = $message['from']['id'] ?? null;
        $baleChatId = $message['chat']['id'] ?? null;

        if (!$baleUserId || !$baleChatId) {
            return response()->json(['ok' => true]);
        }

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

        $connectionToken->update([
            'used_at' => now(),
        ]);

        Log::info('Bale connection created', [
            'shop_id' => $connection->shop_id,
            'user_id' => $connection->user_id,
            'bale_user_id' => $baleUserId,
            'bale_chat_id' => $baleChatId,
        ]);

        return response()->json(['ok' => true]);
    }

    private function handleCallbackQuery(array $callback): \Illuminate\Http\JsonResponse
    {
        $callbackId = $callback['id'] ?? null;
        $data = $callback['data'] ?? '';
        $message = $callback['message'] ?? [];
        $chatId = $message['chat']['id'] ?? null;

        if (!$callbackId || !$chatId) {
            return response()->json(['ok' => true]);
        }

        try {
            $parts = explode(':', $data);

            if (count($parts) !== 3 || $parts[0] !== 'payment') {
                app(BaleService::class)->answerCallbackQuery($callbackId, 'عملیات نامعتبر است.');
                return response()->json(['ok' => true]);
            }

            [, $action, $paymentId] = $parts;

            if (!in_array($action, ['approve', 'reject'], true) || !ctype_digit($paymentId)) {
                app(BaleService::class)->answerCallbackQuery($callbackId, 'عملیات نامعتبر است.');
                return response()->json(['ok' => true]);
            }

            $payment = Payment::with('order')->find((int) $paymentId);

            if (!$payment) {
                app(BaleService::class)->answerCallbackQuery($callbackId, 'پرداخت پیدا نشد.');
                return response()->json(['ok' => true]);
            }

            $authorized = ShopBaleConnection::where('shop_id', $payment->shop_id)
                ->where('bale_chat_id', $chatId)
                ->where('active', true)
                ->exists();

            if (!$authorized) {
                app(BaleService::class)->answerCallbackQuery($callbackId, 'شما دسترسی به این فروشگاه ندارید.');
                return response()->json(['ok' => true]);
            }

            if ($payment->method !== 'card_to_card' || $payment->status !== 'waiting_confirmation') {
                app(BaleService::class)->answerCallbackQuery($callbackId, 'این پرداخت قبلاً بررسی شده است.');
                return response()->json(['ok' => true]);
            }

            DB::transaction(function () use ($payment, $action) {
                $payment = Payment::lockForUpdate()->find($payment->id);

                if (!$payment || $payment->status !== 'waiting_confirmation') {
                    return;
                }

                if ($action === 'approve') {
                    $payment->update(['status' => 'paid']);

                    if ($payment->order) {
                        $payment->order->update([
                            'status' => 1,
                            'total' => $payment->amount,
                            'paid_at' => now(),
                        ]);
                    }
                } else {
                    $payment->update(['status' => 'rejected']);
                }
            });

            $payment->refresh()->load([
                'order.buyer',
                'order.products',
                'order.shop',
            ]);

            $bale = app(BaleService::class);

            if ($action === 'approve') {
                $order = $payment->order;
                $shop = $order?->shop;
                $buyerName = $order?->buyer?->name ?? $order?->receiver_name ?? 'مشتری';
                $buyerPhone = $order?->buyer?->phone ?? $order?->receiver_phone ?? '';

                $orderUrl = $shop
                    ? app(CustomerOrderLinkService::class)->makeOrderUrl($shop, $order)
                    : null;

                $smsText = ($shop?->name ?? 'فروشگاه') . "\n"
                    . "مشتری گرامی {$buyerName}،\n"
                    . "پرداخت سفارش #{$order?->id} با موفقیت تأیید شد.\n"
                    . "مبلغ: " . number_format((float) $payment->amount) . " تومان\n"
                    . ($orderUrl ? "مشاهده جزئیات سفارش:\n{$orderUrl}" : '');

                $keyboard = [];

                if ($buyerPhone) {
                    $keyboard = [
                        'inline_keyboard' => [
                            [
                                [
                                    'text' => '📱 ارسال تأیید به مشتری',
                                    'url' => app(CustomerOrderLinkService::class)->makeSmsUrl($buyerPhone, $smsText),
                                ],
                            ],
                        ],
                    ];
                }

                $messageText = "✅ پرداخت تأیید شد\n\n"
                    . "سفارش: #{$order?->id}\n"
                    . "مبلغ: " . number_format((float) $payment->amount) . " تومان\n"
                    . "مشتری: {$buyerName}";
                $callbackText = 'پرداخت تأیید شد.';
            } else {
                $messageText = "❌ پرداخت رد شد\n\n"
                    . "سفارش: #{$payment->order?->id}\n"
                    . "مبلغ: " . number_format((float) $payment->amount) . " تومان";
                $callbackText = 'پرداخت رد شد.';
                $keyboard = [];
            }

            $bale->answerCallbackQuery($callbackId, $callbackText);
            $bale->editMessageReplyMarkup($chatId, (int) $message['message_id'], $keyboard);
            $bale->sendMessage($chatId, $messageText);
        } catch (\Throwable $e) {
            Log::error('Bale payment callback failed', [
                'callback' => $callback,
                'error' => $e->getMessage(),
            ]);

            try {
                app(BaleService::class)->answerCallbackQuery(
                    $callbackId,
                    'خطایی رخ داد. دوباره تلاش کنید.'
                );
            } catch (\Throwable $ignored) {
                // Do not fail the webhook response if Bale itself is unavailable.
            }
        }

        return response()->json(['ok' => true]);
    }
}
