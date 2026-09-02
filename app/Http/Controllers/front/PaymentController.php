<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Gateway;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentReceipt;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SoapClient;
use Throwable;

class PaymentController extends Controller
{
    /**
     * ذخیره اطلاعات گیرنده و رفتن به صفحه انتخاب روش پرداخت.
     * این مرحله برای خریدار لاگین‌شده و مهمان هر دو کار می‌کند.
     */
    public function checkout(Request $request)
    {
        $shop = Shop::current();

        if (!$shop) {
            abort(404);
        }

        $validated = $request->validate([
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string|max:20',
            'receiver_province' => 'required|string|max:100',
            'receiver_city' => 'required|string|max:100',
            'receiver_address' => 'required|string|max:1000',
            'receiver_postal_code' => 'required|string|max:20',
        ]);

        $buyer = auth('buyer')->user();
        $order = null;

        if ($buyer) {
            $order = $buyer->orders()
                ->where('status', 0)
                ->where('shop_id', $shop->id)
                ->with('products')
                ->first();
        } else {
            if ($shop->buyer_login_required) {
                return redirect()->route('buyer.login')
                    ->with('warning', 'برای ادامه خرید باید وارد حساب کاربری شوید.');
            }

            $cart = session('cart', []);

            if (empty($cart)) {
                return back()->withErrors('سبد خرید شما خالی است.');
            }

            // اگر قبلاً برای همین checkout یک سفارش مهمان ساخته شده، همان را ادامه بده.
            $existingOrderId = session('checkout_order_id');
            if ($existingOrderId) {
                $order = Order::where('id', $existingOrderId)
                    ->where('shop_id', $shop->id)
                    ->whereNull('buyer_id')
                    ->where('status', 0)
                    ->with('products')
                    ->first();
            }

            if (!$order) {
                $products = Product::whereIn('id', array_keys($cart))
                    ->where('shop_id', $shop->id)
                    ->get();

                if ($products->isEmpty()) {
                    return back()->withErrors('محصولات سبد خرید پیدا نشدند.');
                }

                $order = DB::transaction(function () use ($shop, $products, $cart) {
                    $order = Order::create([
                        'buyer_id' => null,
                        'shop_id' => $shop->id,
                        'status' => 0,
                    ]);

                    foreach ($products as $product) {
                        $quantity = max(1, (int) ($cart[$product->id] ?? 0));

                        $order->products()->attach($product->id, [
                            'quantity' => $quantity,
                            'price' => $product->price,
                        ]);
                    }

                    return $order;
                });

                session()->put('checkout_order_id', $order->id);
            }
        }

        if (!$order || $order->products->isEmpty()) {
            return back()->withErrors('سبد خرید شما خالی است.');
        }

        $order->update($validated);

        return redirect()->route('payment.index');
    }

    /**
     * صفحه انتخاب روش پرداخت.
     */
    public function index()
    {
        $shop = Shop::current();
        $order = $this->checkoutOrder($shop);

        if (!$order || $order->products->isEmpty()) {
            return redirect()->route('order.index')
                ->withErrors('سبد خرید شما خالی است.');
        }

        $totalAmount = $this->orderAmount($order);
        $gateway = Gateway::where('shop_id', $shop->id)
            ->where('active', true)
            ->first();
        $bankAccount = $shop->bankAccount;

        return view('Frontend.Shop.Pay.payment', compact(
            'order',
            'totalAmount',
            'gateway',
            'bankAccount'
        ));
    }

    /**
     * شروع پرداخت آنلاین بانک ملت.
     */
    public function init(Request $request)
    {
        $shop = Shop::current();
        $order = $this->checkoutOrder($shop);

        if (!$order || $order->products->isEmpty()) {
            return redirect()->route('order.index')
                ->withErrors('سفارش قابل پرداخت پیدا نشد.');
        }

        $gateway = Gateway::where('shop_id', $shop->id)
            ->where('active', true)
            ->first();

        if (!$gateway) {
            return back()->withErrors('درگاه پرداخت فعال برای این فروشگاه وجود ندارد.');
        }

        $totalAmount = $this->orderAmount($order);

        if ($totalAmount <= 0) {
            return back()->withErrors('مبلغ سفارش نامعتبر است.');
        }

        $payment = Payment::create([
            'shop_id' => $shop->id,
            'gateway_id' => $gateway->id,
            'order_id' => $order->id,
            'method' => 'online',
            'amount' => $totalAmount,
            'status' => 'pending',
        ]);

        try {
            $client = new SoapClient($gateway->wsdl_url, [
                'trace' => true,
                'exceptions' => true,
            ]);

            $response = $client->bpPayRequest([
                'terminalId' => $gateway->terminal_id,
                'userName' => $gateway->username,
                'userPassword' => $gateway->password,
                'orderId' => $payment->id,
                'amount' => $payment->amount,
                'localDate' => now()->format('Ymd'),
                'localTime' => now()->format('His'),
                'additionalData' => '',
                'callBackUrl' => route('payments.callback'),
                'payerId' => 0,
            ]);

            $result = explode(',', $response->return);
            $responseCode = $result[0] ?? null;
            $refId = $result[1] ?? null;

            if ((string) $responseCode === '0' && $refId) {
                $payment->update([
                    'ref_id' => $refId,
                    'status' => 'redirected',
                ]);

                return view('Frontend.Shop.pay.redirect', [
                    'gatewayUrl' => $gateway->gateway_url,
                    'refId' => $refId,
                ]);
            }

            $payment->update(['status' => 'failed']);

            return back()->withErrors(
                'خطا در ایجاد تراکنش بانک. کد خطا: ' . $responseCode
            );
        } catch (Throwable $e) {
            $payment->update(['status' => 'failed']);
            report($e);

            return back()->withErrors('خطا در ارتباط با درگاه پرداخت.');
        }
    }

    /**
     * ثبت پرداخت کارت‌به‌کارت و رسید.
     */
    public function cardToCard(Request $request)
    {
        $shop = Shop::current();
        $order = $this->checkoutOrder($shop);

        if (!$order || $order->products->isEmpty()) {
            return redirect()->route('order.index')
                ->withErrors('سفارش قابل پرداخت پیدا نشد.');
        }

        if (!$shop->bankAccount) {
            return back()->withErrors('اطلاعات کارت بانکی این فروشگاه ثبت نشده است.');
        }

        $validated = $request->validate([
            'receipt' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'tracking_code' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        $totalAmount = $this->orderAmount($order);

        if ($totalAmount <= 0) {
            return back()->withErrors('مبلغ سفارش نامعتبر است.');
        }

        $payment = Payment::where('order_id', $order->id)
            ->whereIn('status', ['pending', 'waiting_confirmation'])
            ->latest('id')
            ->first();

        if (!$payment) {
            $payment = Payment::create([
                'shop_id' => $shop->id,
                'gateway_id' => null,
                'order_id' => $order->id,
                'method' => 'card_to_card',
                'amount' => $totalAmount,
                'status' => 'pending',
            ]);
        }

        $directory = public_path('uploads/payment-receipts');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = uniqid('receipt_', true) . '.' . $request->file('receipt')->extension();
        $request->file('receipt')->move($directory, $filename);

        PaymentReceipt::updateOrCreate(
            ['payment_id' => $payment->id],
            [
                'image' => 'uploads/payment-receipts/' . $filename,
                'tracking_code' => $validated['tracking_code'] ?? null,
                'description' => $validated['description'] ?? null,
            ]
        );

        $payment->update([
            'method' => 'card_to_card',
            'status' => 'waiting_confirmation',
            'amount' => $totalAmount,
        ]);

        return view('Frontend.Shop.Pay.card-to-card-success', compact('payment', 'order'));
    }

    /**
     * Callback بانک ملت
     */
    public function callback(Request $request)
    {
        $resCode = $request->input('ResCode');
        $saleOrderId = $request->input('SaleOrderId');
        $refId = $request->input('RefId');
        $saleRefId = $request->input('SaleReferenceId');

        if (!$saleOrderId) {
            return redirect()->route('payments.failed')
                ->with('error', 'شناسه تراکنش دریافت نشد.');
        }

        $payment = Payment::with(['order', 'gateway'])->find($saleOrderId);

        if (!$payment) {
            return redirect()->route('payments.failed')
                ->with('error', 'تراکنش پیدا نشد.');
        }

        if ($payment->status === 'paid') {
            return redirect()->route('payments.success', $payment);
        }

        if ((string) $resCode !== '0') {
            $payment->update(['status' => 'failed']);
            return redirect()->route('payments.failed', $payment);
        }

        try {
            $gateway = $payment->gateway;

            if (!$gateway) {
                throw new \RuntimeException('درگاه پرداخت تراکنش پیدا نشد.');
            }

            $client = new SoapClient($gateway->wsdl_url, [
                'trace' => true,
                'exceptions' => true,
            ]);

            $verifyResponse = $client->bpVerifyRequest([
                'terminalId' => $gateway->terminal_id,
                'userName' => $gateway->username,
                'userPassword' => $gateway->password,
                'orderId' => $payment->id,
                'saleOrderId' => $saleOrderId,
                'saleReferenceId' => $saleRefId,
            ]);

            $verifyResult = (int) $verifyResponse->return;

            if ($verifyResult !== 0) {
                $payment->update(['status' => 'failed']);
                return redirect()->route('payments.failed', $payment)
                    ->with('error', 'تأیید تراکنش توسط بانک انجام نشد. کد: ' . $verifyResult);
            }

            $settleResponse = $client->bpSettleRequest([
                'terminalId' => $gateway->terminal_id,
                'userName' => $gateway->username,
                'userPassword' => $gateway->password,
                'orderId' => $payment->id,
                'saleOrderId' => $saleOrderId,
                'saleReferenceId' => $saleRefId,
            ]);

            $settleResult = (int) $settleResponse->return;

            if ($settleResult === 0) {
                $payment->update([
                    'status' => 'paid',
                    'ref_id' => $refId,
                    'sale_order_id' => $saleOrderId,
                    'sale_reference_id' => $saleRefId,
                ]);

                if ($payment->order) {
                    $payment->order->update([
                        'status' => 1,
                        'total' => $payment->amount,
                        'paid_at' => now(),
                    ]);

                    session()->forget('cart');
                    session()->forget('checkout_order_id');

                    event(new \App\Events\PaymentWasSuccessful($payment->order));
                }

                return redirect()->route('payments.success', $payment);
            }

            $payment->update(['status' => 'failed']);

            return redirect()->route('payments.failed', $payment)
                ->with('error', 'تسویه تراکنش توسط بانک انجام نشد. کد: ' . $settleResult);
        } catch (Throwable $e) {
            report($e);
            $payment->update(['status' => 'failed']);

            return redirect()->route('payments.failed', $payment)
                ->with('error', 'خطا هنگام تأیید تراکنش با بانک.');
        }
    }

    public function success(Payment $payment)
    {
        return view('Frontend.Shop.pay.success', compact('payment'));
    }

    public function failed(Payment $payment)
    {
        return view('Frontend.Shop.pay.failed', compact('payment'));
    }

    /**
     * سفارش فعلی checkout را برای کاربر لاگین‌شده یا مهمان پیدا می‌کند.
     */
    private function checkoutOrder(Shop $shop)
    {
        $buyer = auth('buyer')->user();

        if ($buyer) {
            return $buyer->orders()
                ->where('status', 0)
                ->where('shop_id', $shop->id)
                ->with('products')
                ->first();
        }

        if ($shop->buyer_login_required) {
            return null;
        }

        $orderId = session('checkout_order_id');

        if (!$orderId) {
            return null;
        }

        return Order::where('id', $orderId)
            ->where('shop_id', $shop->id)
            ->whereNull('buyer_id')
            ->where('status', 0)
            ->with('products')
            ->first();
    }

    private function orderAmount(Order $order)
    {
        return $order->products->sum(function ($product) {
            return $product->pivot->price * $product->pivot->quantity;
        });
    }
}
