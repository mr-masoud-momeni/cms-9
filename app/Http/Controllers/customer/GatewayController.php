<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gateway;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;

class GatewayController extends Controller
{
    public function edit()
    {
        $shop = Shop::current();
        // لود درگاه بانکی
        $gateways = $shop->gateways()->get();

        return view('Customer.gateways.edit', compact('shop', 'gateways'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string',
            'terminal_id' => 'required|string',
            'username'    => 'required|string',
            'password'    => 'required|string',
            'wsdl_url'    => 'required|url',
            'gateway_url' => 'required|url',
        ]);

        $shop = Shop::current();

        // فقط یک درگاه برای هر فروشگاه
        $shop->gateways()->updateOrCreate(
            ['shop_id' => $shop->id], // شرط
            $validated                 // داده‌ها
        );

        return back()->with('success', 'اطلاعات درگاه ذخیره شد');
    }

}
