<?php


//developer comment: this class have not a artisan command to produce.
// in this class get user data and extract the odd order number.
// in fact, this class is called with provider named "ShareDataServiceProvider".

namespace App\View\Composers;
use Illuminate\Support\Facades\Auth;
use illuminate\View\View;

class UserDataComposer
{
    public function __construct(){

    }
    public function compose(View $view) {

        // اگر خریدار لاگین کرده بود
        if (auth('buyer')->check()) {
            $buyer = auth('buyer')->user();
            $orderNumber = $buyer->orders()->get();
            $orderNumber = $orderNumber->count();
        }

        elseif (auth('web')->check()) {
            // اگر ادمین یا یوزر لاگین باشد
            $orderNumber = 0;
        }
        else {
            //دریافت اطلاعات محصول کاربر از سشن
            $cart = session()->get('cart', []);
            $orderNumber = count($cart);
        }
        $view->with('orderNumber', $orderNumber);
    }
}
