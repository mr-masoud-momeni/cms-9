<?php

namespace App\Http\Controllers\customer;
use App\Http\Controllers\customer\CustomerController;
use Validator;
use App\Models\category;
use App\Models\product;
use App\Models\Order;
use App\Order_Product;
use Illuminate\Http\Request;
use App\Models\User;
use App\Rules\WhiteList;
use App\Http\Controllers\Controller;

class ProductController extends CustomerController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products=product::latest()->paginate(10);
        return view('Customer.product.index' , compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parentCategories=category::where('parent_id',0)->where('type','product')->get();
        return view('Customer.product.create', compact('parentCategories'));
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
        $validated = $request->validate([
            // new validation that check values of price-type
            //for more information visit the link: https://docs.google.com/document/d/1dQGotVLWKT0ezYnV2vb81dl-eWqm8H3cVhIspm80FNs/edit#bookmark=id.x9ao4csj1dp4
            'price-type'=> [new WhiteList([ 'non-membership' => 'non-membership',
                                            'membership' => 'membership',
                                            'special-membership'=>'special-membership',
                                            'cash'=>'cash'])],
            'type'=> [new WhiteList(['physical' => 'physical',
                                     'virtual' => 'virtual'])],
            'price'=>'numeric|nullable',
            'images'=>'nullable|mimes:jpeg,jpg,bmp,png',
            'title'=>'required',
            'body'=>'required',
        ]);
        $userId = auth()->id();
        $shopId = auth()->user()->shop()->first()->id;
        $imageUrl['thum']="/uploads/default/post.png";
        $imageUrl=$this->UploadImages($request->file('images'));
        $productData = array_merge($validated, [
            'user_id' => $userId,
            'shop_id' => $shopId,
            'images' => $imageUrl
        ]);
        $product = Product::create($productData);
        $category=request('category');
        if($category){
            $product->categories()->attach(request('category'));
        }
        session()->flash('createproduct','محصول شما با موفقیت ثبت شد.');
        return redirect('/customer/product');
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
        $parentCategories=category::where('parent_id',0)->where('type','product')->get();
        return view('Customer.product.edit',compact('product' ,'parentCategories'));
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
        $validated = $request->validate([
            // new validation that check values of price-type
            //for more information visit the link: https://docs.google.com/document/d/1dQGotVLWKT0ezYnV2vb81dl-eWqm8H3cVhIspm80FNs/edit#bookmark=id.x9ao4csj1dp4
            'price-type'=> [new WhiteList([ 'non-membership' => 'non-membership',
                'membership' => 'membership',
                'special-membership'=>'special-membership',
                'cash'=>'cash'])],
            'type'=> [new WhiteList(['physical' => 'physical',
                'virtual' => 'virtual'])],
            'price'=>'numeric|nullable',
            'images'=>'nullable|mimes:jpeg,jpg,bmp,png',
            'title'=>'required',
            'body'=>'required',
        ]);
        $userId = auth()->id();
        $shopId = auth()->user()->shop()->first()->id;
        if($request->input('price-type') != 'cash'){
            $validated['price']=null;
        }
        if($request->file('images')){
            $imageUrl['thum']="/uploads/default/post.png";
            $imageUrl=$this->UploadImages($request->file('images'));
            $productData = array_merge($validated, [
                'user_id' => $userId,
                'shop_id' => $shopId,
                'images' => $imageUrl
            ]);
        }else{
            $productData = array_merge($validated, [
                'user_id' => $userId,
                'shop_id' => $shopId,
            ]);
        }

        $product->update($productData);
        $category=request('category');
        if($category){
            $product->categories()->sync(request('category'));
        }
        else{
            $product->categories()->detach();
        }

        session()->flash('createproduct','محصول شما با موفقیت ویرایش شد.');
        return redirect('/customer/product');
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
