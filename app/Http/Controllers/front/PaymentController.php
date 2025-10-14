<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Gateway;
use App\Helpers\ShopHelper;
use SoapClient;

class PaymentController extends Controller
{
    public function init(Request $request)
    {
        $shopId = ShopHelper::getShopId();

        $gateway = Gateway::where('shop_id', $shopId)
            ->where('active', true)
            ->firstOrFail();

        $payment = Payment::create([
            'shop_id'  => $gateway->shop_id,
            'gateway_id'=> $gateway->id,
            'order_id'  => $request->order_id ?? null,
            'amount'    => $request->amount,
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
                            'paid_at' => now(),
                        ]);
                    }

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
