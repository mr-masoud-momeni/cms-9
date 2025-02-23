<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(product $product){
        return view('Frontend.Shop.show',compact('product'));
    }
}
