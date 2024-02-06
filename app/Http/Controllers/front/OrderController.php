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
        if ($request->ajax()){
            $validator = Validator::make($request->all(), [
                'id_product' => 'required',
                'count_product' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
            else{
                $total = product::all()->find( $request->id_product);
                $total = $total->price*$request->count_product;
//                $test  = Order::find(3);
//                $test = $test->search($request->id_product);
//                dd($test->amount);
                $update = auth()->User()->order()->search($request->id_product)->get('amount');

                dd($update);
                if($update){

                    $total = $total + $update->pivot->amount;
                    auth()->User()->order()->updateExistingPivot(
                        $request->id_product,
                        ['amount'=>$total]
                    );
                    return response()->json(['update'=>1]);
                }
                else{
                    $Order = auth()->User()->order()->create([
                        'status' => '1'
                    ]);
                    $Order->products()->attach($request->id_product , [
                        'amount' => $total,
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
