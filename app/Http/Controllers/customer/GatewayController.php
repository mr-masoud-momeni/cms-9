<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gateway;
use App\Models\Shop;

class GatewayController extends Controller
{
    public function edit(Shop $shop)
    {
        $shop = auth()->user()->shop;
        // لود همه درگاه‌های این فروشگاه
        $gateways = $shop->gateways()->get()->keyBy('gateway');

        return view('Customer.gateways.edit', compact('shop', 'gateways'));
    }

    public function update(Request $request, Shop $shop)
    {
        foreach ($request->gateways as $data) {
            Gateway::updateOrCreate(
                [
                    'shop_id' => auth()->user()->shop->id ?? null,
                    'gateway' => $data['gateway'],
                ],
                [
                    'merchant_id' => $data['merchant_id'] ?? null,
                    'api_key' => $data['api_key'] ?? null,
                    'secret' => $data['secret'] ?? null,
                    'callback_url' => $data['callback_url'] ?? null,
                    'sandbox' => isset($data['sandbox']),
                ]
            );
        }

        return back()->with('success', 'اطلاعات درگاه‌ها ذخیره شدند');
    }
}
