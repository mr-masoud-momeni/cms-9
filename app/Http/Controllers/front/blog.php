<?php

namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\page;
use Illuminate\Support\Facades\View;

class blog extends Controller
{
    public function show(Article $article)
    {
        $categories = Category::all()->sortBy('id')->pluck('name', 'id');
        return view('Frontend.blog.ShowPost', compact('article', 'categories'));
    }

    public function show1(page $page)
    {
        $path = resource_path('views/Frontend/blog/')."Page.blade.php";
        $f = @fopen($path, "r+");
        ftruncate($f, 0);
        fclose($f);
        $template = "<html><head><style>".$page->css."</style></head><body>".$page->html."</body></html>";
        file_put_contents($path, trim($template));
        $categories = $page->title;
        return view('Frontend.blog.Page', compact('page', 'categories'));
    }
}
