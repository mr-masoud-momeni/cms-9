<?php

namespace App\Http\Controllers\front;

use App\Models\Article;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Helpers\ShopHelper;

class IndexController extends Controller
{
    public function shop()
    {
        $shopId = ShopHelper::getShopId();

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
            'productCount',
            'postCount'
        ));
    }

    public function product(Product $product)
    {
        $shop = ShopHelper::getShop();

        abort_unless($product->shop_id === $shop->id, 404);

        return view('Frontend.Store.show', compact('product'));
    }
}
