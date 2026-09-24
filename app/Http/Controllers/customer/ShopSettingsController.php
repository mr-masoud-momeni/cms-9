<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Services\ShopLogoService;
use App\Helpers\ShopHelper;
use Illuminate\Http\Request;

class ShopSettingsController extends Controller
{
    public function edit()
    {
        $shop = auth('shop_admin')->user()->shop;

        abort_unless($shop, 404);

        return view('Customer.shop.settings', compact('shop'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'shipping_cost' => ['required', 'integer', 'min:0'],
        ]);

        $shop = auth('shop_admin')->user()->shop;

        abort_unless($shop, 404);

        $oldLogo = $shop->logo;

        if ($request->file('logo')) {
            $shop->logo = app(ShopLogoService::class)->upload($request->file('logo'));
        }

        $shop->name = $request->name;
        $shop->description = $request->description;
        $shop->shipping_cost = $request->shipping_cost;
        $shop->save();

        if ($request->file('logo') && $oldLogo) {
            app(ShopLogoService::class)->delete($oldLogo);
        }

        return back()->with('shop_updated', 'مشخصات فروشگاه با موفقیت به‌روزرسانی شد.');
    }
}
