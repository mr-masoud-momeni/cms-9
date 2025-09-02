<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gateway;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;

class GatewayController extends Controller
{
    public function edit(Shop $shop)
    {
        $shop = auth()->user()->shop;
        // لود همه درگاه‌های این فروشگاه
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

        $shop = Auth::user()->shop; // فرض بر اینه هر یوزر یک فروشگاه داره

        $shop->gateways()->create($validated);

        return back()->with('success', 'اطلاعات درگاه‌ها ذخیره شدند');
    }
}
