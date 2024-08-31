<?php

namespace App\Http\Controllers\customer;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Controller;
use App\Models\category;
class CategoryController extends Controller
{
    public function create(){
        $Model = request()->segment(count(request()->segments()));
        $categories = category::where('type', $Model)->get();
        return view('Customer.category.create',compact('categories' , 'Model'));
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
               $userId = auth()->id();
               $shopId = auth()->user()->shop()->first()->id;
                if (request('parent_id') != 0 ){
                    $userId = auth()->id();
                    $Parent = category::where([
                        ['user_id', '=', $userId],
                        ['id', '=', request('parent_id')],
                    ])->first();
                    if(isset($Parent->id)){
                        $ParentId = $Parent->id;
                        $ParentName = $Parent->name;
                    }
                    else{
                        return response()->json([
                            'error' => 'تغییر در id والد به وجود آمده است.'
                        ]);
                    }
                }
                else{
                    $ParentId = 0;
                    $ParentName = "";
                }
               $category = category::create([
                   'shop_id' => $shopId,
                   'user_id' => $userId,
                   'parent_id'=> $ParentId,
                   'type'=>request('type'),
                   'name'=>request('name'),
                   'slug'=>request('slug'),
               ]);

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
