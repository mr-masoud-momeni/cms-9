<?php


//developer comment: this class have not a artisan command to produce.
// in this class get user data and extract the odd order number.
// in fact, this class is called with provider named "ShareDataServiceProvider".

namespace App\View\Composers;
use Illuminate\Support\Facades\Auth;
use illuminate\View\View;
use App\Helpers\ShopHelper;
class UserDataComposer
{
    public function __construct(){

    }
    public function compose(View $view) {

        // اگر خریدار لاگین کرده بود
        if (auth('buyer')->check()) {
            $cartOrder = auth('buyer')->user()->orders()
                ->where('status', 0)
                ->with('products') // اگر لازم داشتی
                ->latest()
                ->first();

            $orderNumber = $cartOrder ? $cartOrder->products()->count() : 0;
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
