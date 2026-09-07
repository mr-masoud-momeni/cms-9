<?php

namespace App\Http\Controllers\admin;

use Validator;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Order_Product;
use Illuminate\Http\Request;
use App\Models\User;
use App\Rules\WhiteList;
use App\Http\Controllers\Controller;

class ProductController extends AdminController
{
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('Backend.product.index', compact('products'));
    }

    public function create()
    {
        $parentCategories = Category::where('parent_id', 0)->where('type', 'product')->get();
        return view('Backend.product.create', compact('parentCategories'));
    }

    public function category() {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'price-type' => [new WhiteList(['non-membership' => 'non-membership', 'membership' => 'membership', 'special-membership' => 'special-membership', 'cash' => 'cash'])],
            'type' => [new WhiteList(['physical' => 'physical', 'virtual' => 'virtual'])],
            'price' => 'numeric|nullable',
            'images' => 'nullable|mimes:jpeg,jpg,bmp,png',
            'title' => 'required',
            'body' => 'required',
        ]);
        $userId = auth()->id();
        $shopId = auth()->user()->shop()->first()->id;
        $imageUrl = $this->UploadImages($request->file('images'));
        $productData = array_merge($validated, ['user_id' => $userId, 'shop_id' => $shopId, 'images' => $imageUrl]);
        $product = Product::create($productData);
        if (request('category')) {
            $product->categories()->attach(request('category'));
        }
        session()->flash('createproduct', 'محصول شما با موفقیت ثبت شد.');
        return redirect('/admin/product');
    }

    public function show(Product $product) {}

    public function edit(Product $product)
    {
        $parentCategories = Category::where('parent_id', 0)->where('type', 'product')->get();
        return view('Backend.product.edit', compact('product', 'parentCategories'));
    }

    public function update(Request $request, Product $product) {}

    public function destroy(Request $request)
    {
        if ($request->ajax()) {
            $product = new Product();
            $product = $product->find($request->id);
            $delete = $product->delete();
            if ($delete) {
                return response()->json(['success' => $product]);
            }
        }
    }
}
