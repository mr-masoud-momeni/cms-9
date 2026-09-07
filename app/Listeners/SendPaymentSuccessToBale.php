<?php

namespace App\Listeners;

use App\Events\PaymentWasSuccessful;
use App\Models\ShopBaleConnection;
use App\Services\BaleService;
use Illuminate\Support\Facades\Log;

class SendPaymentSuccessToBale
{
    public function handle(PaymentWasSuccessful $event): void
    {
        $payment = $event->payment->load(['order.buyer', 'shop']);

        if (!$payment) {
            return;
        }

        $connection = ShopBaleConnection::where('shop_id', $payment->shop_id)
            ->where('active', true)
            ->latest('id')
            ->first();

        if (!$connection || !$connection->bale_chat_id) {
            Log::warning('Bale connection not found for successful payment', [
                'payment_id' => $payment->id,
                'shop_id' => $payment->shop_id,
            ]);
            return;
        }

        $order = $payment->order;
        $buyerName = $order?->buyer?->name ?? $order?->receiver_name ?? 'مهمان';
        $buyerPhone = $order?->buyer?->phone ?? $order?->receiver_phone ?? '-';

        $text = "🟢 پرداخت آنلاین موفق\n\n"
            . "سفارش: #{$order->id}\n"
            . "مبلغ: " . number_format((float) $payment->amount) . " تومان\n"
            . "مشتری: {$buyerName}\n"
            . "موبایل: {$buyerPhone}\n"
            . "شماره مرجع: " . ($payment->sale_reference_id ?: '-') . "\n\n"
            . "پرداخت با موفقیت تأیید و تسویه شد.";

        try {
            app(BaleService::class)->sendMessage(
                $connection->bale_chat_id,
                $text
            );
        } catch (\Throwable $e) {
            Log::error('Failed to send successful payment to Bale', [
                'payment_id' => $payment->id,
                'shop_id' => $payment->shop_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
