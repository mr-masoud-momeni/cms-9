<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Gateway;
use App\Models\Payment;
use App\Models\Shop;
use Illuminate\Http\Request;
use SoapClient;
use SoapFault;
use Throwable;

class PaymentController extends Controller
{
    /**
     * شروع پرداخت
     */
public function init(Request $request)
{
        $buyer = auth('buyer')->user();

        if (!$buyer) {
            return redirect()
                ->route('buyer.login')
                ->with('warning', 'برای پرداخت ابتدا وارد حساب کاربری شوید.');
        }

        $currentShop = Shop::current();

        $order = $buyer->orders()
            ->where('status', 0)
            ->where('shop_id', $currentShop->id)
            ->with('products')
            ->first();

        if (!$order || $order->products->isEmpty()) {
            return back()->withErrors('سبد خرید شما خالی است.');
        }

        // اعتبارسنجی اطلاعات گیرنده
        $validated = $request->validate([
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string|max:20',
            'receiver_province' => 'required|string|max:100',
            'receiver_city' => 'required|string|max:100',
            'receiver_address' => 'required|string|max:1000',
            'receiver_postal_code' => 'required|string|max:20',
        ]);

        // ذخیره اطلاعات گیرنده روی سفارش
        $order->update([
            'receiver_name' => $validated['receiver_name'],
            'receiver_phone' => $validated['receiver_phone'],
            'receiver_province' => $validated['receiver_province'],
            'receiver_city' => $validated['receiver_city'],
            'receiver_address' => $validated['receiver_address'],
            'receiver_postal_code' => $validated['receiver_postal_code'],
        ]);

        /*
         * محاسبه مبلغ از روی قیمت ثبت‌شده در pivot
         */
        $totalAmount = 0;

        foreach ($order->products as $product) {
            $totalAmount +=
                $product->pivot->price *
                $product->pivot->quantity;
        }

        if ($totalAmount <= 0) {
            return back()->withErrors('مبلغ سفارش نامعتبر است.');
        }

        /*
         * پیدا کردن درگاه فعال این فروشگاه
         */
        $gateway = Gateway::where('shop_id', $currentShop->id)
            ->where('active', true)
            ->first();

        if (!$gateway) {
            return back()->withErrors('درگاه پرداخت فعال برای این فروشگاه وجود ندارد.');
        }

        /*
         * ساخت Payment داخلی
         *
         * payment.id بعداً به عنوان orderId
         * به بانک ملت ارسال می‌شود.
         */
        $payment = Payment::create([
            'shop_id'    => $currentShop->id,
            'gateway_id' => $gateway->id,
            'order_id'   => $order->id,
            'amount'     => $totalAmount,
            'status'     => 'pending',
        ]);

        try {

            $client = new SoapClient(
                $gateway->wsdl_url,
                [
                    'trace'      => true,
                    'exceptions' => true,
                ]
            );

            /*
             * اطلاعات مورد نیاز بانک ملت
             */
            $params = [
                'terminalId'   => $gateway->terminal_id,
                'userName'     => $gateway->username,
                'userPassword' => $gateway->password,

                // شناسه پرداخت ما در بانک
                'orderId'      => $payment->id,

                // مبلغ به ریال
                'amount'       => $payment->amount,

                'localDate'    => now()->format('Ymd'),
                'localTime'    => now()->format('His'),

                'additionalData' => '',

                // آدرس Callback
                'callBackUrl' => route('payments.callback'),

                'payerId' => 0,
            ];

            /*
             * درخواست پرداخت
             */
            $response = $client->bpPayRequest($params);

            $result = explode(',', $response->return);

            $responseCode = $result[0] ?? null;
            $refId = $result[1] ?? null;

            /*
             * درخواست موفق بوده
             */
            if ((string) $responseCode === '0' && $refId) {

                $payment->update([
                    'ref_id' => $refId,
                    'status' => 'redirected',
                ]);

                return view(
                    'Frontend.Shop.pay.redirect',
                    [
                        'gatewayUrl' => $gateway->gateway_url,
                        'refId'      => $refId,
                    ]
                );
            }

            /*
             * درخواست پرداخت ناموفق
             */
            $payment->update([
                'status' => 'failed',
            ]);

            return back()->withErrors(
                'خطا در ایجاد تراکنش بانک. کد خطا: ' . $responseCode
            );

        } catch (Throwable $e) {

            $payment->update([
                'status' => 'failed',
            ]);

            report($e);

            return back()->withErrors(
                'خطا در ارتباط با درگاه پرداخت.'
            );
        }
    }


    /**
     * Callback بانک ملت
     */
    public function callback(Request $request)
    {
        /*
         * اطلاعات برگشتی بانک
         */
        $resCode       = $request->input('ResCode');
        $saleOrderId   = $request->input('SaleOrderId');
        $refId         = $request->input('RefId');
        $saleRefId     = $request->input('SaleReferenceId');

        /*
         * اگر شناسه پرداخت برنگشته باشد،
         * نمی‌توانیم Payment خودمان را پیدا کنیم.
         */
        if (!$saleOrderId) {
            return redirect()->route('payments.failed')
                ->with('error', 'شناسه تراکنش دریافت نشد.');
        }

        /*
         * SaleOrderId همان payment.id است
         * که در init به بانک فرستادیم.
         */
        $payment = Payment::with(['order', 'gateway'])
            ->find($saleOrderId);

        if (!$payment) {
            return redirect()->route('payments.failed')
                ->with('error', 'تراکنش پیدا نشد.');
        }

        /*
         * اگر قبلاً پرداخت شده، دوباره Verify/Settle نکن.
         */
        if ($payment->status === 'paid') {
            return redirect()->route('payments.success', $payment);
        }

        /*
         * اگر کاربر در بانک پرداخت را لغو کرده باشد
         */
        if ((string) $resCode !== '0') {

            $payment->update([
                'status' => 'failed',
            ]);

            return redirect()->route('payments.failed', $payment);
        }

        try {

            $gateway = $payment->gateway;

            $client = new SoapClient(
                $gateway->wsdl_url,
                [
                    'trace'      => true,
                    'exceptions' => true,
                ]
            );

            /*
             * -----------------------------
             * مرحله اول: Verify
             * -----------------------------
             *
             * orderId:
             * همان payment.id که موقع PayRequest فرستادیم
             *
             * saleOrderId:
             * شناسه‌ای که بانک در Callback برگردانده
             *
             * saleReferenceId:
             * شناسه مرجع تراکنش بانک
             */
            $verifyResponse = $client->bpVerifyRequest([
                'terminalId'      => $gateway->terminal_id,
                'userName'        => $gateway->username,
                'userPassword'    => $gateway->password,

                'orderId'         => $payment->id,
                'saleOrderId'     => $saleOrderId,
                'saleReferenceId' => $saleRefId,
            ]);

            $verifyResult = (int) $verifyResponse->return;

            /*
             * Verify موفق
             */
            if ($verifyResult !== 0) {

                $payment->update([
                    'status' => 'failed',
                ]);

                return redirect()
                    ->route('payments.failed', $payment)
                    ->with(
                        'error',
                        'تأیید تراکنش توسط بانک انجام نشد. کد: ' . $verifyResult
                    );
            }

            /*
             * -----------------------------
             * مرحله دوم: Settle
             * -----------------------------
             */
            $settleResponse = $client->bpSettleRequest([
                'terminalId'      => $gateway->terminal_id,
                'userName'        => $gateway->username,
                'userPassword'    => $gateway->password,

                'orderId'         => $payment->id,
                'saleOrderId'     => $saleOrderId,
                'saleReferenceId' => $saleRefId,
            ]);

            $settleResult = (int) $settleResponse->return;

            /*
             * Settle موفق
             */
            if ($settleResult === 0) {

                $payment->update([
                    'status'            => 'paid',
                    'ref_id'            => $refId,
                    'sale_order_id'     => $saleOrderId,
                    'sale_reference_id' => $saleRefId,
                ]);

                /*
                 * سفارش را Paid می‌کنیم
                 */
                if ($payment->order) {

                    $payment->order->update([
                        'status'   => 1,
                        'total'    => $payment->amount,
                        'paid_at'  => now(),
                    ]);

                    /*
                     * اگر Event پرداخت موفق داری،
                     * اینجا باید خود Order را بفرستیم.
                     */
                    event(
                        new \App\Events\PaymentWasSuccessful(
                            $payment->order
                        )
                    );
                }

                return redirect()
                    ->route('payments.success', $payment);
            }

            /*
             * Settle ناموفق
             */
            $payment->update([
                'status' => 'failed',
            ]);

            return redirect()
                ->route('payments.failed', $payment)
                ->with(
                    'error',
                    'تسویه تراکنش توسط بانک انجام نشد. کد: ' . $settleResult
                );

        } catch (Throwable $e) {

            report($e);

            $payment->update([
                'status' => 'failed',
            ]);

            return redirect()
                ->route('payments.failed', $payment)
                ->with(
                    'error',
                    'خطا هنگام تأیید تراکنش با بانک.'
                );
        }
    }


    /**
     * صفحه پرداخت موفق
     */
    public function success(Payment $payment)
    {
        return view(
            'Frontend.Shop.pay.success',
            compact('payment')
        );
    }


    /**
     * صفحه پرداخت ناموفق
     */
    public function failed(Payment $payment)
    {
        return view(
            'Frontend.Shop.pay.failed',
            compact('payment')
        );
    }
}