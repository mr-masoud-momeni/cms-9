<?php
namespace app\Http\Controllers\admin\Api\V1;

use App\article;
use App\Http\Controllers\Controller;

class ArticlesController extends Controller
{
    public function articles(){

        $articles= article::latest()->get();
        dd(articles);
        return response(['data' => ['articles' => $articles ] , 200] , 200 );
    }

}