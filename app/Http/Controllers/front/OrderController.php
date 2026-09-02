<?php

namespace App\Http\Controllers\front;
use App\Models\Order;
use App\Models\Product;
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
        $currentShop = Shop::current();

        /*
        |--------------------------------------------------------------------------
        | کاربر مهمان
        |--------------------------------------------------------------------------
        */
        if (!$buyer) {

            $cart = session('cart', []);

            // اگر سبد خالی است
            if (empty($cart)) {
                return view(
                    'Frontend.Shop.Pay.Cart',
                    [
                        'products' => collect(),
                        'totalAmount' => 0,
                    ]
                );
            }

            // اگر فروشگاه ورود خریدار را اجباری کرده باشد
            if ($currentShop->buyer_login_required === true) {
                return redirect()->route('buyer.login')
                    ->with('warning', 'برای ادامه خرید باید ثبت‌نام کنید.');
            }

            // دریافت محصولات موجود در سبد
            $products = Product::whereIn('id', array_keys($cart))
                ->where('shop_id', $currentShop->id)
                ->get();

            $totalAmount = 0;

            foreach ($products as $product) {

                $product->cart_quantity = $cart[$product->id] ?? 0;
                $product->cart_price = $product->price;

                $totalAmount +=
                    $product->cart_price *
                    $product->cart_quantity;
            }

            return view(
                'Frontend.Shop.Pay.Cart',
                compact('products', 'totalAmount')
            );
        }


        /*
        |--------------------------------------------------------------------------
        | کاربر لاگین شده
        |--------------------------------------------------------------------------
        */

        $order = $buyer->orders()
            ->where('status', 0)
            ->where('shop_id', $currentShop->id)
            ->with('products')
            ->first();

        if (!$order) {
            return view(
                'Frontend.Shop.Pay.Cart',
                [
                    'products' => collect(),
                    'totalAmount' => 0,
                ]
            );
        }

        $products = $order->products;

        $totalAmount = 0;

        foreach ($products as $product) {

            $product->cart_quantity = $product->pivot->quantity;
            $product->cart_price = $product->pivot->price;

            $totalAmount +=
                $product->cart_price *
                $product->cart_quantity;
        }

        return view(
            'Frontend.Shop.Pay.Cart',
            compact('products', 'totalAmount')
        );
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

            elseif (auth('shop_admin')->check()) {

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

    public function completedOrders()
    {
        $buyer = auth('buyer')->user();
        $currentShop = Shop::current(); // دامنه یا فروشگاه فعلی
        if (!$buyer) {
            return redirect()->route('buyer.show.register')
                ->with('message', 'برای مشاهده سفارش‌ها باید وارد شوید.');
        }

        // فقط سفارش‌هایی که وضعیتشون 1 (پرداخت شده) هست
        $orders = $buyer->orders()->where('status', 1)->where('shop_id', $currentShop->id)->with('products')->get();

        return view('Frontend.Shop.Orders.completeOrders', compact('orders'));
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
        $currentShop = Shop::current();

        $buyer = auth('buyer')->user();

        /*
        |--------------------------------------------------------------------------
        | کاربر مهمان
        |--------------------------------------------------------------------------
        */
        if (!$buyer) {

            // اگر این فروشگاه خرید مهمان را اجازه نمی‌دهد
            if ($currentShop->buyer_login_required === true) {
                return redirect()->route('buyer.login')
                    ->with('warning', 'برای ادامه خرید باید وارد حساب کاربری شوید.');
            }

            $cart = session('cart', []);

            // حذف محصول از سبد Session
            if (isset($cart[$id])) {
                unset($cart[$id]);
            }

            session()->put('cart', $cart);

            return redirect()->back()
                ->with('success', 'محصول از سبد خرید حذف شد.');
        }


        /*
        |--------------------------------------------------------------------------
        | کاربر لاگین شده
        |--------------------------------------------------------------------------
        */

        $order = $buyer->orders()
            ->where('status', 0)
            ->where('shop_id', $currentShop->id)
            ->first();

        if (!$order) {
            return redirect()->back()
                ->with('error', 'سبد خرید پیدا نشد.');
        }

        // حذف کامل محصول از سبد
        $order->products()->detach($id);

        return redirect()->back()
            ->with('success', 'محصول از سبد خرید حذف شد.');
    }
}
