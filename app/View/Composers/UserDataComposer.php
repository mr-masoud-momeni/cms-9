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
            $buyer = auth('buyer')->user();
            $shopId = ShopHelper::getShopId();
            $order = $buyer->orders()
                ->where('status', 0)
                ->where('shop_id', $shopId)
                ->with('products') // پیش‌بارگذاری برای دسترسی راحت‌تر
                ->first();
            //دریافت اطلاعات محصول کاربر از سشن
            $cart = session()->get('cart', []);
            if($order){
                foreach ($cart as $productId => $quantity) {
                    $existing = $order->products->firstWhere('id', $productId);

                    if ($existing) {
                        $existing->quantity += $quantity;
                        $existing->save();
                    } else {
                        CartItem::create([
                            'user_id' => $user->id,
                            'product_id' => $productId,
                            'quantity' => $quantity,
                        ]);
                    }
                }
            }
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
