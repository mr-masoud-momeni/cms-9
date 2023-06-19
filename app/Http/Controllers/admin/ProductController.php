<?php

namespace App\Http\Controllers\admin;
use Validator;
use App\Models\category;
use App\Models\product;
use App\Models\Order;
use App\Order_Product;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;

class ProductController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products=product::latest()->paginate(10);
        return view('Backend.product.index' , compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parentCategories=category::where('parent_id',0)->where('type','product')->get();
        return view('Backend.product.create', compact('parentCategories'));
    }
    public function category()
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
        $this->validate($request,[
            'images'=>'mimes:jpeg,jpg,bmp,png',
            'title'=>'required',
            'body'=>'required',
        ]);
        $imageUrl['thum']="/uploads/default/post.png";
        $imageUrl=$this->UploadImages($request->file('images'));
        $products=auth()->User()->product()->create(array_merge($request->all(),['images'=>$imageUrl]));
        $category=request('category');
        if($category){
            $products->categories()->attach(request('category'));
        }
//        $orders= auth()->User()->Order()->create([
//            "total"=>1,
//        ]);
//        $attatchData =
//            [
//                '1'  => [ 'amount' =>  20],
//                '2'  => [ 'amount' =>  40],
//                '3'  => [ 'amount' =>  50],
//            ];
//        $orders->products()->attach($attatchData);
//        $products=User::find(1)->order;
//        dd($products);
        session()->flash('createproduct','محصول شما با موفقیت ثبت شد.');
        return redirect('/admin/product');
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->ajax()){
            $product = new product();
            $product = $product->find($request->id);
            $delete = $product->delete();
            if($delete){
                return response()->json(['success'=>$product]);
            }

        }
    }
}
