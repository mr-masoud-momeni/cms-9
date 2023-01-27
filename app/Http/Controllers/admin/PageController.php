<?php

namespace App\Http\Controllers\admin;

use App\comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Page;
use App\user;
class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages=page::latest()->paginate(10);
        return view('Backend.page.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id = request('_some_token');
        if($id){
            $Page = new Page();
            $Page = $Page->find($id);
            $html = request('gjs-html');
            $html = str_replace("&#039;", "'", $html);
            $html = str_replace("<code>", "", $html);
            $html = str_replace("</code>", "", $html);
            $html = str_replace("&gt;", ">", $html);
            $Page->html = $html;
            $Page->styles= request('gjs-styles');
            $Page->css= request('gjs-css');
            $Page->components= request('gjs-components');
            $save=$Page->save();
        }
        if ($request->ajax()){
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'url' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
            else{
                $Page=auth()->User()->Page()->create($request->all());
                return response()->json(['success'=>$Page]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(page $page)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(page $page)
    {
        return view('Backend.page.create' , compact('page'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'url' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['errorValidate'=>$validator->errors()->all()]);
            }
            $Page = new Page();
            $Page = $Page->find($request->id);
            $Page->title = $request->title;
            $Page->url = $request->url;
            $Page->status = $request->status;
            $save=$Page->save();
            if($save){
                return response()->json(['success'=>$Page]);
            }
            else{
                return response()->json(['success'=>'خطای در ثبت رخ داده است.']);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->ajax()){
            $Page = new Page();
            $Page = $Page->find($request->id);
            $delete = $Page->delete();
            if($delete){
                return response()->json(['success'=>$Page]);
            }

        }
    }
}
