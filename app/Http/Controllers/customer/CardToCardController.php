<?php

namespace App\Http\Controllers\customer;
use App\Http\Controllers\Controller;
use App\Models\ShopBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardToCardController extends Controller
{

    /**
     * ذخیره / بروزرسانی اطلاعات کارت
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'card_number' => ['required', 'digits:16'],
            'owner_name'  => ['required', 'string', 'max:255'],
            'iban'        => ['required', 'string', 'max:34'],
        ]);

        $shop = $request->user()->shop;

        $shop->bankAccount()->updateOrCreate(
            ['shop_id' => $shop->id],
            [
                'card_number' => $validated['card_number'],
                'account_holder' => $validated['owner_name'],
                'sheba' => $validated['iban'],
            ]
        );

        return back()->with('success', 'اطلاعات کارت بانکی با موفقیت ذخیره شد.');
    }
}