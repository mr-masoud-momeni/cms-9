<?php

namespace App\Http\Controllers\front;

use App\Models\Article;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Helpers\ShopHelper;
use function view;

class IndexController extends Controller
{
    public function index()
    {
        $shop = ShopHelper::getShop();
        $shopId = $shop?->id;
        $menu = Menu::where('id', 1)->first();
        $articles = Article::where('shop_id', $shopId)->latest()->take(3)->get();
        $products = Product::where('shop_id', $shopId)->latest()->take(3)->get();
        return view('Frontend.Home.index', compact('articles', 'menu', 'products'));
    }

    public function shop()
    {
        $shop = ShopHelper::getShop();
        $shopId = $shop?->id;
        $menu = Menu::where('id', 1)->first();

        $products = Product::where('shop_id', $shopId)
            ->latest()
            ->paginate(9);

        $articles = Article::where('shop_id', $shopId)
            ->latest()
            ->take(12)
            ->get();

        $productCount = Product::where('shop_id', $shopId)->count();
        $postCount = Article::where('shop_id', $shopId)->count();

        return view('Frontend.Store.index', compact(
            'products',
            'articles',
            'shop',
            'menu',
            'productCount',
            'postCount'
        ));
    }

    public function product(Product $product)
    {
        $shop = ShopHelper::getShop();

        abort_unless($product->shop_id === $shop->id, 404);

        $menu = Menu::where('id', 1)->first();

        return view('Frontend.Store.show', compact('product', 'menu'));
    }

    public function create() {}
    public function store(Request $request) {}
    public function show($id) {}
    public function edit($id) {}
    public function update(Request $request, $id) {}
    public function destroy($id) {}
}
