<?php

namespace App\Http\Controllers\admin;

use App\Models\Permission;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {

//        $this->middleware('role:index-permission')->only('index');
//        $this->middleware('role:store-permission')->only('store');
//        $this->middleware('role:update-permission')->only('update');
    }
    public function index()
    {
        $Permissions= Permission::latest()->get();
        return view('Backend.Users.Permission',compact('Permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
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
