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
        dd($shopId);
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
            return view('payments.redirect', ['gatewayUrl' => $gateway->gateway_url, 'refId' => $result[1]]);
        }

        $payment->update(['status' => 'failed']);
        return back()->withErrors('خطا در ارتباط با درگاه. کد خطا: ' . $result[0]);
    }
}
