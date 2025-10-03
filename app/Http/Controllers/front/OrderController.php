<?php

namespace App\Http\Controllers\front;
use App\Models\Order;
use App\Models\product;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $buyer = auth('buyer')->user();
        if(!$buyer){
            $orders = session('cart');
            if($orders){
                return redirect()->route('buyer.show.register')
                    ->with('message', 'برای ادامه خرید باید ثبت‌نام کنید.');
            }
            return view('Frontend.Shop.Pay.Cart');
        }else{
            $orders = $buyer->orders()->where('status',0)->get();
            return view('Frontend.Shop.Pay.Cart' , compact('orders'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if ($request->ajax()){
            $validator = Validator::make($request->all(), [
                'product_id' => 'required',
                'count_product' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
            $shop = Shop::current();
            $product = Product::findOrFail($request->product_id);
            $quantity = $request->count_product; // جلوگیری از مقادیر نامعتبر


            // اگر خریدار لاگین کرده بود
            if (auth('buyer')->check()) {
                $buyer = auth('buyer')->user();
                // پیدا کردن آخرین سفارش باز (در حال پرداخت) یا ساخت سفارش جدید
                $order = Order::firstOrCreate(
                    [
                        'buyer_id' => $buyer->id,
                        'shop_id' => $shop->id,
                        'status' => 0,
                    ],
                    ['created_at' => now()]
                );

                // اگر محصول قبلاً در سفارش بود، فقط تعداد رو زیاد کن
                if ($order->products()->where('product_id', $product->id)->exists()) {
                    $pivot = $order->products()->where('product_id', $product->id)->first()->pivot;
                    $order->products()->updateExistingPivot($product->id, [
                        'quantity' => $pivot->quantity + $quantity,
                        'price' => $product->price, // قیمت لحظه‌ای
                    ]);
                    $addToCart= 0;
                } else {
                    // در غیر این صورت، محصول جدید اضافه کن
                    $order->products()->attach($product->id, [
                        'quantity' => $quantity,
                        'price' => $product->price,
                    ]);
                    $addToCart= 1;
                }

                return response()->json(['success' => $addToCart, 'message' => 'به سبد خرید شما اضافه شد.']);
            }

            elseif (auth('web')->check()) {

                // اگر ادمین یا یوزر لاگین باشد
                return response()->json(['message' => 'ادمین نمی‌تواند محصول به سبد خرید اضافه کند.']);
            }
            else {
                // استخراج مقادیر از درخواست
                $product_id = $request->input('product_id');
                $quantity = $request->input('count_product', 1);
                // ذخیره در سشن برای کاربران مهمان
                $cart = session()->get('cart', []);
                // اگر محصول از قبل در سبد بود، تعداد را به‌روز می‌کنیم
                if (isset($cart[$product_id])) {
                    $cart[$product_id] += $quantity;
                    $addToCart= 0;
                } else {
                    $cart[$product_id] = $quantity;
                    $addToCart= 1;
                }
                session()->put('cart', $cart);
                return response()->json(['success' => $addToCart,'message' => 'محصول به سبد خرید مهمان اضافه شد.']);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
