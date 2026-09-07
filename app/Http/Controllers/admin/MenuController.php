<?php

namespace App\Http\Controllers\admin;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index(){
        $menus = Menu::latest()->paginate(10);
        return view('Backend.menu.index', compact('menus'));
    }

    public function create() {}

    public function store(Request $request){
        if ($request->ajax()){
            $validator = Validator::make($request->all(), ['title' => 'required | unique:menus']);
            if ($validator->fails()) return response()->json(['error'=>$validator->errors()->all()]);
            $menu=auth()->User()->menus()->create($request->all());
            return response()->json(['success'=>$menu]);
        }
    }

    public function show($id) {}

    public function edit(Menu $menu){
        return view('Backend.menu.create', compact('menu'));
    }

    public function update(Request $request){
        if($request->content){
            $menu = Menu::find($request->id);
            $menu->content = $request->content;
            $menu->save();
            return redirect(route('menu.index'));
        }
        if(!$request->content) return redirect(route('menu.index'));
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), ['title' => 'required']);
            if ($validator->fails()) return response()->json(['errorValidate'=>$validator->errors()->all()]);
            $menu = Menu::find($request->id);
            $menu->title = $request->title;
            $save=$menu->save();
            if($save) return response()->json(['success'=>$menu]);
            return response()->json(['success'=>'خطای در ثبت رخ داده است.']);
        }
    }

    public function destroy($id) {}
}
