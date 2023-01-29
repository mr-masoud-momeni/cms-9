<?php

namespace App\Http\Controllers\admin;
use App\Models\Role;
use Validator;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Permissions=Permission::latest()->get();
        $Roles=Role::latest()->get();
        return view('Backend.Users.Role', compact('Roles','Permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Backend.page.create');
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
                'des-role' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
            else{
                $role = new role();
                $role->name = request('name');
                $role->display_name = request('display_name');
                $role->description  = request('des-role');
                $role->save();
                $role->attachPermissions(request('permission'));
                return response()->json(['success'=>$role]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $Role)
    {
        $permissions=Permission::all()->sortBy('id')->pluck('name','id');
        return(view('Backend.Users.EditRole',compact('Role' ,'permissions')));
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
        $this->validate($request,[
            'name' => 'required',
            'display_name' => 'required',
            'des_role' => 'required',
        ]);
        $role = new role();
        $role = $role->find($request->id);
        $role->name = $request->name;
        $role->display_name= $request->display_name;
        $role->description= $request->des_role;
        $role->save();
        if(!$request->permission){
            $permissions = permission::all();
            $role->detachPermissions($permissions);
        }
        if($request->permission){
            $role->syncPermissions(request('permission'));
        }
        return redirect('/admin/role');
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
            $Role = new Role();
            $Role = $Role->find($request->id);
            $delete= $Role->delete();
            if($delete){
                return response()->json(['success'=>$Role]);
            }

        }
    }
}
