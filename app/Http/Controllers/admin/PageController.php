<?php

namespace App\Http\Controllers\admin;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Models\Page;
use App\Models\User;

class PageController extends Controller
{
    public function index()
    {
        $pages=Page::latest()->paginate(10);
        return view('Backend.page.index', compact('pages'));
    }

    public function create() {}

    public function store(Request $request)
    {
        $id = request('_some_token');
        if($id){
            $page = Page::find($id);
            $html = request('gjs-html');
            dd($html);
            $html = str_replace("&#039;", "'", $html);
            $html = str_replace("<code>", "", $html);
            $html = str_replace("</code>", "", $html);
            $html = str_replace("&gt;", ">", $html);
            $page->html = $html;
            $page->styles= request('gjs-styles');
            $page->css= request('gjs-css');
            $page->components= request('gjs-components');
            $save=$page->save();
        }
        if ($request->ajax()){
            $validator = Validator::make($request->all(), ['title' => 'required', 'url' => 'required']);
            if ($validator->fails()) return response()->json(['error'=>$validator->errors()->all()]);
            $page=auth()->User()->pages()->create($request->all());
            return response()->json(['success'=>$page]);
        }
    }

    public function show(Page $page)
    {
        return view('Backend.page.show' , compact('page'));
    }

    public function edit(Request $request, Page $page)
    {
        return $this->show_gjs_editor($request, $page);
    }

    public function update(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), ['title' => 'required', 'url' => 'required']);
            if ($validator->fails()) return response()->json(['errorValidate'=>$validator->errors()->all()]);
            $page = Page::find($request->id);
            $page->title = $request->title; $page->url = $request->url; $page->status = $request->status;
            $save=$page->save();
            if($save) return response()->json(['success'=>$page]);
            return response()->json(['success'=>'خطای در ثبت رخ داده است.']);
        }
    }

    public function destroy(Request $request)
    {
        if ($request->ajax()){
            $page = Page::find($request->id); $delete = $page->delete();
            if($delete) return response()->json(['success'=>$page]);
        }
    }
}
