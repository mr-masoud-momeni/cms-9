<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentConfirmationService
{
    public function approve(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $payment = Payment::lockForUpdate()->findOrFail($payment->id);

            if ($payment->status === 'paid') {
                return;
            }

            if ($payment->method !== 'card_to_card' || $payment->status !== 'waiting_confirmation') {
                throw new \RuntimeException('این پرداخت قابل تأیید نیست.');
            }

            app(OrderReservationService::class)->commitPayment($payment);

            $order = $payment->order()->lockForUpdate()->first();

            if ($order) {
                $order->update([
                    'total' => $payment->amount,
                ]);
            }
        });
    }

    public function reject(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $payment = Payment::lockForUpdate()->findOrFail($payment->id);

            if ($payment->status === 'rejected') {
                return;
            }

            if ($payment->method !== 'card_to_card' || $payment->status !== 'waiting_confirmation') {
                throw new \RuntimeException('این پرداخت قابل رد کردن نیست.');
            }

            $payment->update([
                'status' => 'rejected',
            ]);

            $order = $payment->order()->lockForUpdate()->first();

            if ($order) {
                $order->update([
                    'status' => Order::STATUS_CANCELLED,
                ]);
            }
        });
    }
}
