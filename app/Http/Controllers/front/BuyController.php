<?php

namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\lib\zarinpal;

class BuyController extends Controller
{
    public function add_order(Request $request)
    {

        $order = new zarinpal();
        $res = $order->pay($request->price,"myroxo24@gmail.com","0912111111");
        return redirect('https://www.zarinpal.com/pg/StartPay/' . $res);

    }
}
