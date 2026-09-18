<?php

namespace App\Http\Controllers\customer;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function create()
    {
        $model = request()->segment(count(request()->segments()));
        $shop = auth('shop_admin')->user()->shop()->firstOrFail();

        $categories = Category::where('shop_id', $shop->id)
            ->where('type', $model)
            ->get();

        return view('Customer.category.create', [
            'categories' => $categories,
            'Model' => $model,
        ]);
    }

    public function save(Request $request)
    {
        if (!$request->ajax()) {
            return null;
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories',
            'slug' => 'required',
            'type' => 'required|in:product,article',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $user = auth('shop_admin')->user();
        $shop = $user->shop()->firstOrFail();
        $parentId = (int) $request->input('parent_id', 0);
        $parentName = '';

        if ($parentId !== 0) {
            $parent = Category::where('shop_id', $shop->id)
                ->where('user_id', $user->id)
                ->where('id', $parentId)
                ->first();

            if (!$parent) {
                return response()->json(['error' => 'تغییر در شناسه والد به وجود آمده است.']);
            }

            $parentName = $parent->name;
        }

        $category = Category::create([
            'shop_id' => $shop->id,
            'user_id' => $user->id,
            'parent_id' => $parentId,
            'type' => $request->type,
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        return response()->json([
            'success' => $category,
            'parent' => $parentName,
        ]);
    }

    public function edit(Request $request)
    {
        if (!$request->ajax()) {
            return null;
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errorValidate' => $validator->errors()->all()]);
        }

        $user = auth('shop_admin')->user();
        $shop = $user->shop()->firstOrFail();

        $category = Category::where('shop_id', $shop->id)
            ->where('user_id', $user->id)
            ->find($request->id);

        if (!$category) {
            return response()->json(['errorValidate' => ['دسته‌بندی پیدا نشد.']], 404);
        }

        $parentId = (int) $request->input('parent_id', 0);

        if ($parentId !== 0) {
            $parent = Category::where('shop_id', $shop->id)
                ->where('user_id', $user->id)
                ->find($parentId);

            if (!$parent) {
                return response()->json(['errorValidate' => ['دسته‌بندی والد نامعتبر است.']], 422);
            }
        }

        $category->parent_id = $parentId;
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->save();

        $parentName = $parentId
            ? Category::where('shop_id', $shop->id)->find($parentId)?->name ?? ''
            : '';

        return response()->json([
            'success' => $category,
            'parent' => $parentName,
        ]);
    }

    public function delete(Request $request)
    {
        if (!$request->ajax()) {
            return null;
        }

        $user = auth('shop_admin')->user();
        $shop = $user->shop()->firstOrFail();

        $category = Category::where('shop_id', $shop->id)
            ->where('user_id', $user->id)
            ->find($request->id);

        if (!$category) {
            return response()->json(['error' => 'دسته‌بندی پیدا نشد.'], 404);
        }

        $category->delete();

        return response()->json(['success' => $category]);
    }
}
