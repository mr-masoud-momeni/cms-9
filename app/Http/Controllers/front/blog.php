<?php

namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\article;
use App\category;
use App\page;
use Illuminate\Support\Facades\View;
class blog extends Controller
{
    public function show(article $article){
        $categories=category::all()->sortBy('id')->pluck('name','id');
        return view('Frontend.blog.ShowPost',compact('article' ,'categories'));
    }
    public function show1(page $page){
        $path = resource_path('views/Frontend/blog/')."Page.blade.php";
        $f = @fopen($path , "r+");
        ftruncate($f, 0);
        fclose($f);
        $template = "<html><head><style>".$page->css."</style></head><body>".$page->html."</body></html>";
        file_put_contents($path, trim($template));
        $categories= $page->title;
        return view('Frontend.blog.Page',compact('page' , 'categories'));
//        return view('Frontend.blog.Page',compact('page'));

//        $filename = uniqid('blade_');
//        $path = resource_path('views/tmp/').$filename.".blade.php";
//        $path = resource_path('views/tmp/').$filename.".blade.php";

//        return view('Frontend.blog.Page',compact('page'));
//        dd($path);
////        if (!file_exists(resource_path('views/tmp/'))) {
////            mkdir(resource_path('views/tmp/'), 0777, true);
////        }
//        file_put_contents($path, trim($template));
////        $view = "tmp.". $filename;
//        $view = "Frontend.blog.page";
//        $rendered = (View::make($view, [
//            'categories' => $title,
//        ]))->render();
//        return $rendered;
//        unlink($path);
//        $categories= $page->title;
//        return view('Frontend.blog.Page',compact('page' , 'categories'));
    }
}
