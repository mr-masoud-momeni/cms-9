<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\product;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Gateway;
use App\Helpers\ShopHelper;
use SoapClient;

class PaymentController extends Controller
{
    public function init(Request $request)
    {
        $buyer = auth('buyer')->user();
        $currentShop = Shop::current(); // دامنه یا فروشگاه فعلی
        $order = $buyer->orders()
            ->where('status', 0)
            ->where('shop_id', $currentShop->id)
            ->with('products')->first();
        $totalAmount = 0;
        if ($order) {
            foreach ($order->products as $product) {
                $found = Product::find($product->id);
                $price = $found ? $found->price : null;
                $order->products()->updateExistingPivot($product->id, [
                    'price' => $price,
                ]);
                if (!is_null($price)) {
                    $totalAmount += $price * $product->pivot->quantity;
                }
            }
        }
        $shopId = $currentShop->id;
        $gateway = Gateway::where('shop_id', $shopId)
            ->where('active', true)
            ->firstOrFail();
        $payment = Payment::create([
            'shop_id'  => $gateway->shop_id,
            'gateway_id'=> $gateway->id,
            'order_id'  => $order->id ?? null,
            'amount'    => $totalAmount,
            'status'    => 'pending'
        ]);

        $client = new SoapClient($gateway->wsdl_url);

        $params = [
            'terminalId'     => $gateway->terminal_id,
            'userName'       => $gateway->username,
            'userPassword'   => $gateway->password,
            'orderId'        => $payment->id, // به عنوان شناسه سفارش بانک
            'amount'         => $payment->amount,
            'localDate'      => date("Ymd"),
            'localTime'      => date("His"),
            'additionalData' => '',
            'callBackUrl'    => route('payments.callback'),
            'payerId'        => 0
        ];

        $res = $client->bpPayRequest($params);
        $result = explode(',', $res->return);

        if ($result[0] == "0") {
            $payment->update([
                'ref_id' => $result[1],
                'status' => 'redirected'
            ]);
            return view('Frontend.Shop.pay.redirect', ['gatewayUrl' => $gateway->gateway_url, 'refId' => $result[1]]);
        }

        $payment->update(['status' => 'failed']);
        return back()->withErrors('خطا در ارتباط با درگاه. کد خطا: ' . $result[0]);
    }

    public function callback(Request $request)
    {
        $resCode = $request->input('ResCode');
        $orderId = $request->input('SaleOrderId');
        $refId   = $request->input('RefId');
        $saleRefId = $request->input('SaleReferenceId');

        $payment = Payment::where('id', $orderId)->firstOrFail();

        if ($resCode == 0) {
            // SOAP Verify
            $gateway = $payment->gateway;
            $client = new SoapClient($gateway->wsdl_url);
            $verify = $client->bpVerifyRequest([
                'terminalId'      => $gateway->terminal_id,
                'userName'        => $gateway->username,
                'userPassword'    => $gateway->password,
                'orderId'         => $payment->order_id,
                'saleOrderId'     => $orderId,
                'saleReferenceId' => $saleRefId,
            ]);
            $result = (int) $verify->return;

            if ($result === 0) {
                // SOAP Settle
                $settle = $client->bpSettleRequest([
                    'terminalId'      => $gateway->terminal_id,
                    'userName'        => $gateway->username,
                    'userPassword'    => $gateway->password,
                    'orderId'         => $payment->order_id,
                    'saleOrderId'     => $orderId,
                    'saleReferenceId' => $saleRefId,
                ]);

                $resultsettle = (int) $settle->return;
                if ($resultsettle === 0) {
                    $payment->update([
                        'status'  => 'paid',
                        'ref_id'  => $refId,
                        'sale_order_id'      => $orderId,
                        'sale_reference_id'  => $saleRefId,
                    ]);

                    // آپدیت سفارش مربوطه
                    if ($payment->order) {
                        $payment->order->update([
                            'status'  => 'paid',
                            'total' => $payment->amount,
                            'paid_at' => now(),
                        ]);
                    }
//                    $order = $payment->order;
//                    $order->update([
//                       'status' => '1'
//                    ]);
                    // 🔥 ایونت رو اینجا فایر می‌کنیم
                    event(new \App\Events\PaymentWasSuccessful($order));
                    return redirect()->route('payments.success', $payment);
                }
            }
        }

        // اگر به هر دلیلی موفق نبود
        $payment->update(['status' => 'failed']);
        return redirect()->route('payments.failed', $payment);
    }
    public function success(Payment $payment)
    {
        return view('Frontend.Shop.pay.success', compact('payment'));
    }

    public function failed(Payment $payment)
    {
        return view('Frontend.Shop.pay.failed', compact('payment'));
    }


}
