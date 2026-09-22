<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class OrderReservationService
{
    public function reserve(Order $order): array
    {
        return DB::transaction(function () use ($order) {
            $order->load('products');
            $productIds = $order->products->pluck('id')->sort()->values()->all();

            if (!$productIds) {
                return ['success' => false, 'message' => 'سبد خرید شما خالی است.'];
            }

            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $now = now();

            foreach ($order->products as $orderProduct) {
                $product = $products->get($orderProduct->id);
                $quantity = (int) $orderProduct->pivot->quantity;

                if (!$product) {
                    return ['success' => false, 'message' => Order::MESSAGE_PRODUCT_UNAVAILABLE];
                }

                $reserved = $product->orders()
                    ->where('orders.status', Order::STATUS_RESERVED)
                    ->where(function ($query) use ($now) {
                        $query->where('orders.reservation_expires_at', '>', $now)
                            ->orWhereHas('payment', function ($paymentQuery) {
                                $paymentQuery->where('status', 'waiting_confirmation');
                            });
                    })
                    ->sum('order_product.quantity');

                $available = max(0, (int) $product->stock - (int) $reserved);

                if ($quantity > $available) {
                    return [
                        'success' => false,
                        'message' => $available > 0
                            ? Order::MESSAGE_STOCK_CONFLICT . ' حدود ' . $available . ' ' . $product->unit . ' قابل رزرو است.'
                            : Order::MESSAGE_STOCK_CONFLICT,
                    ];
                }
            }

            $order->update([
                'status' => Order::STATUS_RESERVED,
                'reserved_at' => $now,
                'reservation_expires_at' => $now->copy()->addMinutes(Order::RESERVATION_MINUTES),
            ]);

            return ['success' => true, 'message' => $order->reservationMessage()];
        });
    }

    public function commitPayment(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $payment = Payment::lockForUpdate()->findOrFail($payment->id);

            if ($payment->status === 'paid') {
                return;
            }

            $order = Order::lockForUpdate()->with('products')->findOrFail($payment->order_id);

            if ($order->status === Order::STATUS_PAID) {
                $payment->update(['status' => 'paid']);
                return;
            }

            $this->decrementStockAndMarkPaid($order);
            $payment->update(['status' => 'paid']);
        });
    }

    private function decrementStockAndMarkPaid(Order $order): void
    {
        $products = Product::whereIn('id', $order->products->pluck('id'))
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($order->products as $orderProduct) {
            $product = $products->get($orderProduct->id);
            $quantity = (int) $orderProduct->pivot->quantity;

            if (!$product || $product->stock < $quantity) {
                throw new \RuntimeException(Order::MESSAGE_STOCK_COMMIT_FAILED);
            }

            $product->decrement('stock', $quantity);
        }

        $order->update([
            'status' => Order::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }

}
