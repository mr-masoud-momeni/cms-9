<?php

namespace App\Http\Controllers\front;

use App\Models\article;
use App\Http\Controllers\Controller;
use App\Models\menu;
use App\Models\Order;
use App\Models\product;
use Illuminate\Http\Request;
use App\Helpers\ShopHelper;
use function view;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shopId = ShopHelper::getShopId();


        $menu = menu::where('id', 1)->first();
        $articles= article::latest()->take(3)->get();
        $products= product::latest()->take(3)->where('shop_id', $shopId)->get();
        return view('Frontend.Home.index',compact('articles' , 'menu' , 'products'));
    }
    public function shop()
    {
        $shopId = ShopHelper::getShopId();
        // به جای get از paginate استفاده می‌کنیم
        $products = Product::where('shop_id', $shopId)
            ->latest()
            ->paginate(6); // مثلاً هر صفحه 12 محصول
        return view('Frontend.Shop.index',compact( 'products'));
    }

    public function product(product $product){
        return view('Frontend.Shop.show',compact('product'));
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
        //
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
