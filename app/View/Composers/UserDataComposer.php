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
        $user = Auth::user();
        if(!$user){
            $orderNumber = 0;
        }else{
            $orderNumber = $user->order()->get();
            $orderNumber = $orderNumber->count();
        }
        $view->with('orderNumber', $orderNumber);
    }
}
