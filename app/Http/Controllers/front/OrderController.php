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
                'count_product' => 'required|integer',
            ]);
            $product = product::find($request->id_product);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
            else{
                $Order = auth()->User()->order()->where('productnumber', $request->id_product)->first();
                if(isset($Order->total)){
                    $count = $request->count_product + $Order->total;
                    $Order->total = $count;
                    $Order->amount = $count*$product->price;
                    $Order->save();
                    return response()->json(['update'=>$Order]);
                }
                else{
                    $Order = auth()->User()->order()->create([
                        'status' => '1',
                        'productnumber' => $request->id_product,
                        'total' => $request->count_product,
                        'amount' => $request->count_product*$product->price,
                    ]);
                    $Order->products()->attach($request->id_product);
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
