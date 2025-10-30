<?php

namespace App\Http\Controllers\admin;

use App\Models\Permission;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{

    public function __construct()
    {
    }
    public function index()
    {
        $Permissions= Permission::latest()->get();
        return view('Backend.Users.Permission',compact('Permissions'));
    }


    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        if ($request->ajax()){
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'display_name' => 'required',
                'description' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
            else{
                $Permission = new Permission();
                $Permission->name = request('name');
                $Permission->display_name = request('display_name');
                $Permission->description  = request('description');
                $Permission->save();
                return response()->json(['success'=>$Permission]);
            }
        }
    }

    public function show(Permission $permission)
    {
        //
    }

    public function edit(Permission $permission)
    {
        //
    }
    public function update(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'display_name' => 'required',
                'description' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['errorValidate'=>$validator->errors()->all()]);
            }
            $Permission = new Permission();
            $Permission = $Permission->find($request->id);
            $Permission->name = $request->name;
            $Permission->display_name= $request->display_name;
            $Permission->description= $request->description;
            $save=$Permission->save();
            if($save){
                return response()->json(['success'=>$Permission]);
            }
            else{
                return response()->json(['success'=>'خطای در ثبت رخ داده است.']);
            }
        }
    }
    public function destroy(Request $request)
    {
        if ($request->ajax()){
            $Permission = new Permission();
            $Permission = $Permission->find($request->id);
            $delete=$Permission->delete();
            if($delete){
                return response()->json(['success'=>$Permission]);
            }

        }
    }
}
