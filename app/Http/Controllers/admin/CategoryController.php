<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Controller;
use App\category;
class CategoryController extends Controller
{
    public function create(){
        $LastUrl = request()->segment(count(request()->segments()));
        $Model = "App\\" . $LastUrl;
        $categories = category::where('type', $Model)->get();
        return view('Backend.category.create',compact('categories' , 'Model'));
    }
    public function save(Request $request){

        if ($request->ajax()){
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:categories',
                'slug' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
            else{
               $category = Auth()->user()->category()->create([
                   'parent_id'=>request('parent_id'),
                   'type'=>request('type'),
                   'name'=>request('name'),
                   'slug'=>request('slug'),
               ]);
               if (request('parent_id')){
                   $Parent = category::find(request('parent_id'));
                   $ParentName = $Parent->name;
               }
               else{
                   $ParentName = "";
               }
               return response()->json(['success'=>$category , 'parent'=>$ParentName]);
            }
        }
    }
    public function edit(Request $request){
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'slug' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['errorValidate'=>$validator->errors()->all()]);
            }
            $cat = auth()->User()->category()->find($request->id);
            $cat->parent_id = $request->parent_id;
            $cat->name = $request->name;
            $cat->slug = $request->slug;
            $save=$cat->save();
            if (request('parent_id')){
                $Parent = category::find(request('parent_id'));
                $ParentName = $Parent->name;
            }
            else{
                $ParentName = "";
            }
            if($save){
                return response()->json(['success'=>$cat , 'parent'=>$ParentName]);
            }
            else{
                return response()->json(['success'=>'خطای در ثبت رخ داده است.']);
            }
        }
    }
    public function delete(Request $request){
        if ($request->ajax()){
            $cat = auth()->User()->category()->find($request->id);
            $delete=$cat->delete();
            if($delete){
                return response()->json(['success'=>$cat]);
            }

        }
    }
}
