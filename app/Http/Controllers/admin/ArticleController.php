<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use App\Models\article;
use App\Models\user;
use App\Models\category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends AdminController
{
    public function index(){
        $articles=article::latest()->paginate(10);
        return view('Backend.article.index',compact('articles'));
    }
    public function show(article $article){
        $categories=category::all()->sortBy('id')->pluck('name','id');
        return view('Backend.article.edit',compact('article' ,'categories'));
    }
    public function create(){
        $parentCategories=category::where('parent_id',0)->where('type','article')->get();
        return view('Backend.article.create',compact('parentCategories'));
    }
    public function save(Request $request){
        $this->validate($request,[
            'images'=>'mimes:jpeg,jpg,bmp,png',
            'title'=>'required',
            'body'=>'required',
        ]);
        $imageUrl['thum']="/uploads/default/post.png";
        $imageUrl=$this->UploadImages($request->file('images'));
        $articles=auth()->User()->article()->create(array_merge($request->all(),['images'=>$imageUrl]));
        $category=request('category');
        if($category){
            $articles->categories()->attach(request('category'));
        }
        session()->flash('createpost','پست شما با موفقیت ثبت شد.');
        return redirect('/admin/article');
    }
    public function edit(article $article){
//        $categories=category::all()->sortBy('id')->pluck('name','id');
        $parentCategories=category::where('parent_id',0)->where('type','article')->get();
        return view('Backend.article.edit',compact('article' ,'parentCategories'));
    }
    public function update(Request $request, article $article){
        $this->validate(request(),[
            'images'=>'mimes:jpeg,jpg,bmp,png',
            'title'=>'required',
            'body'=>'required',
        ]);
        if($request->file('images')){
            $user = Auth::user();
            $article->user_id = $user->id;
            $article->title = $request->title;
            $article->slug = $request->title;
            $article->body = $request->body;
            $imageUrl = $this->UploadImages($request->file('images'));
            $article->images = $imageUrl;
            $article->save();
        }else{
            $user = Auth::user();
            $article->user_id = $user->id;
            $imageUrl = $article->images;
            $imageUrl['thum'] = $request->imageThum;
            $article->images = $imageUrl;
            $article->title = $request->title;
            $article->slug = $request->title;
            $article->body = $request->body;
            $article->save();
        }
        if($request->category){
            $article->categories()->sync(request('category'));
        }
        else{
            $article->categories()->detach();
        }

        session()->flash('article.update','پست شما با موفقیت ویرایش شد.');
        return redirect('/admin/article');
    }
    public function delete(Request $request ){
        if ($request->ajax()){
            $article = auth()->User()->article()->find($request->id);
            $delete = $article->delete();
            if($delete){
                return response()->json(['success'=>$article]);
            }

        }
    }
}
