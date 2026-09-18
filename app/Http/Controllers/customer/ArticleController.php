<?php

namespace App\Http\Controllers\customer;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class ArticleController extends CustomerController
{
    public function index()
    {
        $shop = auth('shop_admin')->user()->shop()->firstOrFail();

        $articles = Article::where('shop_id', $shop->id)
            ->latest()
            ->paginate(10);

        return view('Customer.article.index', compact('articles'));
    }

    public function create()
    {
        $shop = auth('shop_admin')->user()->shop()->firstOrFail();

        $parentCategories = Category::where('shop_id', $shop->id)
            ->where('parent_id', 0)
            ->where('type', 'article')
            ->get();

        return view('Customer.article.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $shop = auth('shop_admin')->user()->shop()->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required',
            'images' => 'nullable|mimes:jpeg,jpg,bmp,png',
            'category' => 'nullable|array',
            'category.*' => 'integer',
        ]);

        $imageUrl = $this->UploadImages($request->file('images'));

        $article = Article::create([
            'user_id' => auth('shop_admin')->id(),
            'shop_id' => $shop->id,
            'title' => $validated['title'],
            'body' => $validated['body'],
            'images' => $imageUrl,
        ]);

        $categoryIds = $this->shopCategoryIds($shop->id, $validated['category'] ?? []);
        if ($categoryIds) {
            $article->categories()->attach($categoryIds);
        }

        return redirect()->route('shop.article.index')
            ->with('createarticle', 'مقاله شما با موفقیت ثبت شد.');
    }

    public function edit($article)
    {
        $shop = auth('shop_admin')->user()->shop()->firstOrFail();

        $article = Article::where('shop_id', $shop->id)
            ->where('slug', $article)
            ->firstOrFail();

        $parentCategories = Category::where('shop_id', $shop->id)
            ->where('parent_id', 0)
            ->where('type', 'article')
            ->get();

        return view('Customer.article.edit', compact('article', 'parentCategories'));
    }

    public function update(Request $request, $article)
    {
        $shop = auth('shop_admin')->user()->shop()->firstOrFail();

        $article = Article::where('shop_id', $shop->id)
            ->where('slug', $article)
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required',
            'images' => 'nullable|mimes:jpeg,jpg,bmp,png',
            'imageThum' => 'nullable|string',
            'category' => 'nullable|array',
            'category.*' => 'integer',
        ]);

        $data = [
            'user_id' => auth('shop_admin')->id(),
            'title' => $validated['title'],
            'body' => $validated['body'],
        ];

        if ($request->file('images')) {
            $data['images'] = $this->UploadImages($request->file('images'));
        } else {
            $images = $article->images;
            if ($request->filled('imageThum') && is_array($images)) {
                $images['thum'] = $request->imageThum;
                $data['images'] = $images;
            }
        }

        $article->update($data);

        $categoryIds = $this->shopCategoryIds($shop->id, $validated['category'] ?? []);
        $article->categories()->sync($categoryIds);

        return redirect()->route('shop.article.index')
            ->with('articleupdate', 'مقاله شما با موفقیت ویرایش شد.');
    }

    public function destroy(Request $request)
    {
        $shop = auth('shop_admin')->user()->shop()->firstOrFail();

        $article = Article::where('shop_id', $shop->id)
            ->findOrFail($request->id);

        $article->delete();

        return response()->json(['success' => $article]);
    }

    public function uploadImageInText(Request $request)
    {
        $request->validate([
            'upload' => 'required|mimes:jpeg,jpg,bmp,png',
        ]);

        $year = now()->year;
        $imagePath = "/upload/images/{$year}/";
        $file = $request->file('upload');
        $filename = $file->getClientOriginalName();

        if (file_exists(public_path($imagePath) . $filename)) {
            $filename = now()->timestamp . '_' . $filename;
        }

        $file->move(public_path($imagePath), $filename);

        $url = asset('public/' . $imagePath . $filename);

        return "<script>window.parent.CKEDITOR.tools.callFunction(1,'{$url}', '')</script>";
    }

    private function shopCategoryIds($shopId, array $ids): array
    {
        if (!$ids) {
            return [];
        }

        return Category::where('shop_id', $shopId)
            ->where('type', 'article')
            ->whereIn('id', $ids)
            ->pluck('id')
            ->all();
    }
}
