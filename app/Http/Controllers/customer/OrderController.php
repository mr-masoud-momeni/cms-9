<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Order;
use App\Services\PaymentConfirmationService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $shop = Shop::current();

        $paymentMethod = $request->query('payment_method');

        $orders = Order::with([
            'buyer',
            'payment',
            'products',
        ])
            ->where('shop_id', $shop->id)
            ->when($paymentMethod, function ($query) use ($paymentMethod) {
                $query->whereHas('payment', function ($paymentQuery) use ($paymentMethod) {
                    $paymentQuery->where('method', $paymentMethod);
                });
            })
            ->latest()
            ->get();

        return view('Customer.Orders.index', compact('orders', 'paymentMethod'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $shop = Shop::current();

        $order = Order::where('shop_id', $shop->id)
            ->with(['buyer', 'payment.receipt', 'products'])
            ->findOrFail($order->id);

        return view('Customer.orders.show', compact('order'));
    }

    public function approvePayment(Order $order)
    {
        $shop = Shop::current();

        $order = Order::where('shop_id', $shop->id)
            ->with('payment')
            ->findOrFail($order->id);

        if (!$order->payment || !$order->payment->isCardToCard()) {
            return back()->withErrors('پرداخت کارت‌به‌کارت برای این سفارش پیدا نشد.');
        }

        try {
            app(PaymentConfirmationService::class)->approve($order->payment);
        } catch (\Throwable $e) {
            return back()->withErrors($e->getMessage());
        }

        return back()->with('success', 'پرداخت کارت‌به‌کارت تأیید شد.');
    }

    public function rejectPayment(Order $order)
    {
        $shop = Shop::current();

        $order = Order::where('shop_id', $shop->id)
            ->with('payment')
            ->findOrFail($order->id);

        if (!$order->payment || !$order->payment->isCardToCard()) {
            return back()->withErrors('پرداخت کارت‌به‌کارت برای این سفارش پیدا نشد.');
        }

        try {
            app(PaymentConfirmationService::class)->reject($order->payment);
        } catch (\Throwable $e) {
            return back()->withErrors($e->getMessage());
        }

        return back()->with('success', 'پرداخت کارت‌به‌کارت رد شد.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', [
                Order::STATUS_PENDING,
                Order::STATUS_RESERVED,
                Order::STATUS_PAID,
                Order::STATUS_SHIPPED,
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
            ]),
        ]);

        $data = [
            'status' => $request->status,
        ];
        $shop = Shop::current();

        $order = Order::where('shop_id', $shop->id)
            ->findOrFail($order->id);

        $order->update($data);

        return back()->with('success', 'وضعیت سفارش بروزرسانی شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateTrackingCode(Request $request, Order $order)
    {
        $request->validate([
            'tracking_code' => 'required|string|max:255',
        ]);

        $shop = Shop::current();

        $order = Order::where('shop_id', $shop->id)
            ->findOrFail($order->id);

        $order->update([
            'tracking_code' => $request->tracking_code,
        ]);

        return back()->with('success', 'کد رهگیری ثبت شد.');
    }

    public function destroy($id)
    {
        //
    }
}
