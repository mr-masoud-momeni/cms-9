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
            $orders = 0;
        }else{
            $orders = $user->order()->get();
        }
        return view('Frontend.Checkout.CheckOrders' , compact('orders'));
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
//        $query = auth()->User()->order()->with('search')->get();
//        foreach($query as $h){
//            echo $h->pivot->amount;
//        }
//        $content = $request->id_product;
//        $Repetitious = auth()->User()->order()->with(['search'=> function($query) use ($content){
//            $query->wherePivot('product_id', $content);
//        }])->get()->toArray();
//        dd($companies[0]['id']);
//        $pivot = auth()->User()->order()->get()->toArray();
//        dd($pivot);
//        foreach($companies as $h){
//            echo $h->id;
//        }
//
//        $users = auth()->User()->order()->search($request->id_product);
//        dd($users);

        if ($request->ajax()){
            $validator = Validator::make($request->all(), [
                'id_product' => 'required',
                'count_product' => 'required',
            ]);
            $product = product::find($request->id_product);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
            else{
                $content = $request->id_product;
//                $Repetitious = auth()->User()->order()->with(['search'=> function($query) use ($content){
//                    $query->wherePivot('product_id', $content);
//                }])->get();
                $Repetitious = auth()->User()->order()->whereHas('products', function ($query) use($content) {
                    $query->where('products.id', $content);
                })->get();
                if(isset($Repetitious[0]['id'])){
                    $id_order = $Repetitious[0]['id'];
                    $total_order = $Repetitious[0]['total'];
                    $count = $request->count_product + $total_order;
                    $Order = Order::find($id_order);
                    $Order->total = $count;
                    $Order->save();
                    $Order->products()->updateExistingPivot($request->id_product , [
                        'amount' => $count*$product->price,
                    ]);
                    return response()->json(['update'=>1]);
                }
                else{

                    $Order = auth()->User()->order()->create([
                        'total' => $request->count_product,
                        'status' => '1'
                    ]);
                    $Order->products()->attach($request->id_product , [
                        'amount' => $request->count_product*$product->price,
                    ]);
                    return response()->json(['success'=>$Order]);
                }
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
