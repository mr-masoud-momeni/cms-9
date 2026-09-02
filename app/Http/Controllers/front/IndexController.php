<?php

namespace App\Http\Controllers\front;

use App\Models\Article;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Helpers\ShopHelper;
use function view;

class IndexController extends Controller
{
    public function index()
    {
        $shopId = ShopHelper::getShopId();
        $menu = Menu::where('id', 1)->first();
        $articles = Article::latest()->take(3)->get();
        $products = Product::latest()->take(3)->where('shop_id', $shopId)->get();
        return view('Frontend.Home.index', compact('articles', 'menu', 'products'));
    }

    public function shop()
    {
        $shopId = ShopHelper::getShopId();
        $products = Product::where('shop_id', $shopId)->latest()->paginate(6);
        return view('Frontend.Shop.index', compact('products'));
    }

    public function product(Product $product)
    {
        return view('Frontend.Shop.show', compact('product'));
    }

    public function create() {}
    public function store(Request $request) {}
    public function show($id) {}
    public function edit($id) {}
    public function update(Request $request, $id) {}
    public function destroy($id) {}
}
