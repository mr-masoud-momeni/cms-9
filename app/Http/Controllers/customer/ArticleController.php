<?php

namespace App\Http\Controllers\customer;

use App\Models\Article;
use App\Models\Shop;
use Illuminate\Http\Request;

class ArticleController extends CustomerController
{
    public function index()
    {
        $shop = Shop::where('domain', request()->getHost())->firstOrFail();

        $articles = Article::where('shop_id', $shop->id)
            ->latest()
            ->paginate(10);

        return view('Customer.article.index', compact('articles'));
    }

    public function create()
    {
        return view('Customer.article.create');
    }

    public function store(Request $request)
    {
        $shop = Shop::where('domain', request()->getHost())->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'images' => 'required|image|mimes:jpeg,jpg,bmp,png,webp|max:5120',
        ]);

        $imageUrl = $this->UploadImages($request->file('images'));

        Article::create([
            'user_id' => auth('shop_admin')->id(),
            'shop_id' => $shop->id,
            'title' => $validated['title'],
            'body' => $validated['body'],
            'images' => $imageUrl,
        ]);

        return redirect()->route('shop.article.index')
            ->with('createarticle', 'مقاله شما با موفقیت ثبت شد.');
    }

    public function edit($article)
    {
        $shop = Shop::where('domain', request()->getHost())->firstOrFail();

        $article = Article::where('shop_id', $shop->id)
            ->where('slug', $article)
            ->firstOrFail();

        return view('Customer.article.edit', compact('article'));
    }

    public function update(Request $request, $article)
    {
        $shop = Shop::where('domain', request()->getHost())->firstOrFail();

        $article = Article::where('shop_id', $shop->id)
            ->where('slug', $article)
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'images' => 'nullable|image|mimes:jpeg,jpg,bmp,png,webp|max:5120',
        ]);

        $data = [
            'user_id' => auth('shop_admin')->id(),
            'title' => $validated['title'],
            'body' => $validated['body'],
        ];

        if ($request->file('images')) {
            $oldImages = $article->images;
            $data['images'] = $this->UploadImages($request->file('images'));
            $article->update($data);
            $this->DeleteUploadedImages($oldImages);
        } else {
            $article->update($data);
        }

        return redirect()->route('shop.article.index')
            ->with('articleupdate', 'مقاله شما با موفقیت ویرایش شد.');
    }

    public function destroy($article)
    {
        $shop = Shop::where('domain', request()->getHost())->firstOrFail();

        $article = Article::where('shop_id', $shop->id)
            ->where('slug', $article)
            ->firstOrFail();

        $images = $article->images;
        $article->delete();
        $this->DeleteUploadedImages($images);

        return response()->json(['success' => $article]);
    }

    public function uploadImageInText(Request $request)
    {
        abort(404);
    }
}
