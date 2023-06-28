<?php

namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\lib\zarinpal;

class BuyController extends Controller
{
    public function add_order(Request $request)
    {

//        $order = new zarinpal();
//        $res = $order->pay($request->price,"myroxo24@gmail.com","0912111111");
//        return redirect('https://www.zarinpal.com/pg/StartPay/' . $res);
        $response = zarinpal()
            ->amount(100) // مبلغ تراکنش
            ->request()
            ->description('transaction info') // توضیحات تراکنش
            ->callbackUrl('https://domain.com/verification') // آدرس برگشت پس از پرداخت
            ->mobile('09123456789') // شماره موبایل مشتری - اختیاری
            ->email('name@domain.com') // ایمیل مشتری - اختیاری
            ->send();

        if (!$response->success()) {
            return $response->error()->message();
        }

// ذخیره اطلاعات در دیتابیس
// $response->authority();

// هدایت مشتری به درگاه پرداخت
        return $response->redirect();

    }
}
