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
        $payment = $event->payment->load([
            'order.buyer',
            'order.products',
            'receipt',
            'shop',
        ]);

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

        $text = "🟡 پرداخت کارت‌به‌کارت جدید\n\n"
            . "سفارش: #{$order->id}\n"
            . "مبلغ: " . number_format((float) $payment->amount) . " تومان\n"
            . "مشتری: {$buyerName}\n"
            . "موبایل: {$buyerPhone}\n"
            . "کد پیگیری: {$trackingCode}\n\n"
            . "لطفاً رسید را بررسی و پرداخت را تأیید یا رد کنید.";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '✅ تأیید پرداخت', 'callback_data' => "payment:approve:{$payment->id}"],
                    ['text' => '❌ رد پرداخت', 'callback_data' => "payment:reject:{$payment->id}"],
                ],
            ],
        ];

        $receiptPath = $this->resolveReceiptPath($payment->receipt?->image);

        try {
            $bale = app(BaleService::class);

            if ($receiptPath) {
                $bale->sendPhoto(
                    $connection->bale_chat_id,
                    $receiptPath,
                    $text,
                    $keyboard
                );
            } else {
                Log::warning('Payment receipt file not found for Bale notification', [
                    'payment_id' => $payment->id,
                    'receipt' => $payment->receipt?->image,
                ]);

                $bale->sendMessage(
                    $connection->bale_chat_id,
                    $text . "\n\n⚠️ فایل رسید روی سرور پیدا نشد.",
                    $keyboard
                );
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send card-to-card payment to Bale', [
                'payment_id' => $payment->id,
                'shop_id' => $payment->shop_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Resolve the receipt file for both local development and the shared-host
     * deployment where public_html is next to the Laravel project directory.
     */
    private function resolveReceiptPath(?string $relativePath): ?string
    {
        if (!$relativePath) {
            return null;
        }

        $relativePath = ltrim($relativePath, '/');

        $publicHtmlPath = config('services.bale.public_path');
        $targetPath = $publicHtmlPath
            ? rtrim($publicHtmlPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $relativePath
            : null;

        $laravelPublicPath = public_path($relativePath);

        if ($targetPath && is_file($targetPath)) {
            return $targetPath;
        }

        if ($targetPath && is_file($laravelPublicPath)) {
            $targetDirectory = dirname($targetPath);

            if (!is_dir($targetDirectory)) {
                mkdir($targetDirectory, 0755, true);
            }

            if (@rename($laravelPublicPath, $targetPath)) {
                return $targetPath;
            }

            if (@copy($laravelPublicPath, $targetPath)) {
                @unlink($laravelPublicPath);
                return $targetPath;
            }
        }

        if (is_file($laravelPublicPath)) {
            return $laravelPublicPath;
        }

        return null;
    }
}
