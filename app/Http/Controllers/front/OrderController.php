<?php

namespace App\Http\Controllers\front;
use App\Models\Order;
use App\Models\product;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if(!$user){
            return view('Frontend.Checkout.CheckOrders');
        }else{
            $orders = $user->order()->get();
            return view('Frontend.Checkout.CheckOrders' , compact('orders'));
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
            $product = product::find($request->product_id);
            $product_id = $request->product_id;
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
            if (auth('buyer')->check()) {
                // اگر خریدار لاگین کرده باشد، ذخیره در جدول مرتبط
                $buyer = auth('buyer')->user();
                $buyer->orders()->create([
                    'product_id' => $product_id,
                    'quantity' => 1,
                ]);
                return response()->json(['message' => 'محصول برای خریدار ثبت شد.']);
            }
            elseif (auth('web')->check()) {

                // اگر ادمین یا یوزر لاگین باشد
                return response()->json(['message' => 'ادمین نمی‌تواند محصول به سبد خرید اضافه کند.']);
            }
            else {
                // ذخیره در سشن برای کاربران مهمان
                $cart = session()->get('cart', []);
                $cart[$product_id] = ($cart[$product_id] ?? 0) + 1;
                session()->put('cart', $cart);
                return response()->json(['message' => 'محصول به سبد خرید مهمان اضافه شد.']);
            }
//            else{
//                $Order = auth()->User()->order()->where('productnumber', $request->id_product)->first();
//                if(isset($Order->total)){
//                    $count = $request->count_product + $Order->total;
//                    $Order->total = $count;
//                    $Order->amount = $count*$product->price;
//                    $Order->save();
//                    return response()->json(['update'=>$Order]);
//                }
//                else{
//                    $Order = auth()->User()->order()->create([
//                        'status' => '1',
//                        'productnumber' => $request->id_product,
//                        'total' => $request->count_product,
//                        'amount' => $request->count_product*$product->price,
//                    ]);
//                    $Order->products()->attach($request->id_product);
//                    return response()->json(['success'=>$Order]);
//                }
//            }
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
