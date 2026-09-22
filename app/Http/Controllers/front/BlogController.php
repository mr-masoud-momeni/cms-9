<?php

namespace App\Http\Controllers\front;

use App\Helpers\ShopHelper;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\page;

class BlogController extends Controller
{
    public function show(Article $article)
    {
        $shop = ShopHelper::getShop();

        abort_unless($article->shop_id === $shop->id, 404);

        return view('Frontend.Store.Pages.article', compact('article', 'shop'));
    }

}
