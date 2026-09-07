<?php

namespace App\Listeners;

use App\Events\CardToCardPaymentSubmitted;
use App\Models\ShopBaleConnection;
use App\Services\BaleService;
use Illuminate\Support\Facades\Log;

class SendCardToCardPaymentToBale
{
    public function handle(CardToCardPaymentSubmitted $event): void
    {
        $payment = $event->payment->load(['order.buyer', 'receipt', 'shop']);

        $connection = ShopBaleConnection::where('shop_id', $payment->shop_id)
            ->where('active', true)
            ->latest('id')
            ->first();

        if (!$connection || !$connection->bale_chat_id) {
            Log::warning('Bale connection not found for card-to-card payment', [
                'payment_id' => $payment->id,
                'shop_id' => $payment->shop_id,
            ]);
            return;
        }

        $order = $payment->order;
        $buyerName = $order?->buyer?->name ?? $order?->receiver_name ?? 'مهمان';
        $buyerPhone = $order?->buyer?->phone ?? $order?->receiver_phone ?? '-';
        $trackingCode = $payment->receipt?->tracking_code ?: '-';
        $receiptUrl = $payment->receipt?->image ? url($payment->receipt->image) : null;

        $text = "🟡 پرداخت کارت‌به‌کارت جدید\n\n"
            . "سفارش: #{$order->id}\n"
            . "مبلغ: " . number_format((float) $payment->amount) . " تومان\n"
            . "مشتری: {$buyerName}\n"
            . "موبایل: {$buyerPhone}\n"
            . "کد پیگیری: {$trackingCode}\n\n"
            . "لطفاً رسید را بررسی و پرداخت را تأیید یا رد کنید.";

        $keyboard = [
            'inline_keyboard' => [
                array_filter([
                    $receiptUrl ? ['text' => '🧾 مشاهده رسید', 'url' => $receiptUrl] : null,
                ]),
                [
                    ['text' => '✅ تأیید پرداخت', 'callback_data' => "payment:approve:{$payment->id}"],
                    ['text' => '❌ رد پرداخت', 'callback_data' => "payment:reject:{$payment->id}"],
                ],
            ],
        ];

        try {
            app(BaleService::class)->sendMessage(
                $connection->bale_chat_id,
                $text,
                $keyboard
            );
        } catch (\Throwable $e) {
            Log::error('Failed to send card-to-card payment to Bale', [
                'payment_id' => $payment->id,
                'shop_id' => $payment->shop_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
