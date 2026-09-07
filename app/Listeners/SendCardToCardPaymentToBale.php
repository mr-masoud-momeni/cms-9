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
        $shopName = $payment->shop?->name ?? 'فروشگاه';

        $text = "🟡 پرداخت کارت‌به‌کارت جدید\n\n"
            . "سفارش: #{$order->id}\n"
            . "مبلغ: " . number_format((float) $payment->amount) . " تومان\n"
            . "مشتری: {$buyerName}\n"
            . "موبایل: {$buyerPhone}\n"
            . "کد پیگیری: {$trackingCode}\n\n"
            . "لطفاً رسید را بررسی و پرداخت را تأیید یا رد کنید.";

        $smsText = $this->buildCustomerSms(
            $shopName,
            $order,
            $buyerName,
            (float) $payment->amount
        );

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '✅ تأیید پرداخت', 'callback_data' => "payment:approve:{$payment->id}"],
                    ['text' => '❌ رد پرداخت', 'callback_data' => "payment:reject:{$payment->id}"],
                ],
                [
                    [
                        'text' => '📱 ارسال تأیید به مشتری',
                        'url' => $this->buildSmsUrl($buyerPhone, $smsText),
                    ],
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

                // اگر فایل واقعاً در دسترس نبود، حداقل اطلاعات سفارش را ارسال کن.
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

    private function buildCustomerSms(
        string $shopName,
        $order,
        string $buyerName,
        float $amount
    ): string {
        $sms = "{$shopName}\n"
            . "مشتری گرامی {$buyerName}،\n"
            . "پرداخت سفارش #{$order->id} با موفقیت تأیید شد.\n"
            . "مبلغ: " . number_format($amount) . " تومان\n";

        $products = $order->products ?? collect();

        if ($products->isNotEmpty()) {
            $sms .= "اقلام سفارش:\n";

            foreach ($products as $product) {
                $quantity = (int) ($product->pivot->quantity ?? 1);
                $sms .= "- {$product->name} × {$quantity}\n";
            }
        }

        $sms .= "سفارش شما در حال پردازش است.";

        return $sms;
    }

    private function buildSmsUrl(?string $phone, string $message): string
    {
        $phone = preg_replace('/[^0-9+]/', '', (string) $phone);

        // اگر شماره مشتری معتبر نباشد، لینک را غیرفعال نکن؛ گوشی مقصد را خالی می‌گذارد
        // تا فروشنده بتواند شماره را در اپ پیامک وارد کند.
        $recipient = $phone ?: '';

        return 'sms:' . $recipient . '?body=' . rawurlencode($message);
    }

    /**
     * Resolve the receipt file for both local development and the shared-host
     * deployment where public_html is next to the Laravel project directory.
     *
     * If an older upload was written to Laravel/public, move it to public_html
     * so the same relative URL also remains publicly accessible.
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

        // Deployment: Laravel project and public_html are sibling directories.
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

        // Local development / installations where public_path is the real web root.
        if (is_file($laravelPublicPath)) {
            return $laravelPublicPath;
        }

        return null;
    }
}
