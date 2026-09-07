<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\customer\CustomerController;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Order_Product;
use Illuminate\Http\Request;
use App\Models\User;
use App\Rules\WhiteList;
use App\Http\Controllers\Controller;

class ProductController extends CustomerController
{
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('Customer.product.index', compact('products'));
    }

    public function create()
    {
        $parentCategories = Category::where('parent_id', 0)->where('type', 'product')->get();
        return view('Customer.product.create', compact('parentCategories'));
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
        return redirect('/shop/product');
    }

    public function show(Product $product) {}

    public function edit(Product $product)
    {
        $parentCategories = Category::where('parent_id', 0)->where('type', 'product')->get();
        return view('Customer.product.edit', compact('product', 'parentCategories'));
    }

    public function update(Request $request, Product $product)
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
        if ($request->input('price-type') != 'cash') {
            $validated['price'] = null;
        }
        if ($request->file('images')) {
            $imageUrl = $this->UploadImages($request->file('images'));
            $productData = array_merge($validated, ['user_id' => $userId, 'shop_id' => $shopId, 'images' => $imageUrl]);
        } else {
            $productData = array_merge($validated, ['user_id' => $userId, 'shop_id' => $shopId]);
        }
        $product->update($productData);
        if (request('category')) {
            $product->categories()->sync(request('category'));
        } else {
            $product->categories()->detach();
        }
        session()->flash('createproduct', 'محصول شما با موفقیت ویرایش شد.');
        return redirect('/customer/product');
    }

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
