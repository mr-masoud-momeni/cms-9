<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\customer\CustomerController;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
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
        $shop = Shop::where('domain', request()->getHost())->firstOrFail();

        $products = Product::where('shop_id', $shop->id)
            ->latest()
            ->paginate(10);

        return view('Customer.product.index', compact('products'));
    }

    public function create()
    {
        $shop = Shop::where('domain', request()->getHost())->firstOrFail();

        $parentCategories = Category::where('shop_id', $shop->id)
            ->where('parent_id', 0)
            ->where('type', 'product')
            ->get();

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
        $userId = auth('shop_admin')->id();
        $shop = Shop::where('domain', request()->getHost())->firstOrFail();
        $shopId = $shop->id;
        $imageUrl = $this->UploadImages($request->file('images'));
        $productData = array_merge($validated, ['user_id' => $userId, 'shop_id' => $shopId, 'images' => $imageUrl]);
        $product = Product::create($productData);
        if ($request->has('category')) {
            $product->categories()->attach($request->input('category'));
        }
        session()->flash('createproduct', 'محصول شما با موفقیت ثبت شد.');
        return redirect('/shop/product');
    }

    public function show(Product $product) {}

    public function edit($product)
    {
        $shop = Shop::where('domain', request()->getHost())->firstOrFail();

        $product = Product::where('shop_id', $shop->id)
            ->where('slug', $product)
            ->firstOrFail();

        $parentCategories = Category::where('shop_id', $shop->id)
            ->where('parent_id', 0)
            ->where('type', 'product')
            ->get();

        return view('Customer.product.edit', compact('product', 'parentCategories'));
    }

    public function update(Request $request, $product)
    {
        $shop = Shop::where('domain', request()->getHost())->firstOrFail();

        $product = Product::where('shop_id', $shop->id)
            ->where('slug', $product)
            ->firstOrFail();

        $validated = $request->validate([
            'price-type' => [new WhiteList(['non-membership' => 'non-membership', 'membership' => 'membership', 'special-membership' => 'special-membership', 'cash' => 'cash'])],
            'type' => [new WhiteList(['physical' => 'physical', 'virtual' => 'virtual'])],
            'price' => 'numeric|nullable',
            'images' => 'nullable|mimes:jpeg,jpg,bmp,png',
            'title' => 'required',
            'body' => 'required',
        ]);
        $userId = auth('shop_admin')->id();
        $shopId = $shop->id;
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

        // Category UI is disabled for launch; only change relations when the field is submitted.
        if ($request->has('category')) {
            $product->categories()->sync($request->input('category', []));
        }

        session()->flash('createproduct', 'محصول شما با موفقیت ویرایش شد.');
        return redirect()->route('shop.product.index');
    }

    public function destroy(Request $request, $product)
    {
        $shop = Shop::where('domain', request()->getHost())->firstOrFail();

        $product = Product::where('shop_id', $shop->id)
            ->findOrFail($product);

        $product->delete();

        return response()->json(['success' => $product]);
    }
}
