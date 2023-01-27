<?php

namespace App\Http\Controllers\admin\Auth;

use App\Http\Requests\UpdateUser;
use App\Permission;
use App\Role;
use App\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserRegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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
    public function edit(USer $User)
    {
        $permissions = permission::all()->sortBy('id');
        $Roles = Role::all()->sortBy('id');
        return view('Backend.auth.EditUser',compact('permissions', 'Roles','User'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUser $request)
    {
        $id=request()->route()->parameter('user');
        $user = new User();
        $user = $user->find($id);
        $user->name = $request->name;
        $user->email= $request->email;
        if($request->password){
            $user->password= bcrypt($request->password);
        }
        if(! $request->permission){
            $permissions = permission::all();
            $user->detachPermissions($permissions);
        }
        if(! $request->Role){
            $Roles = Role::all();
            $user->detachRoles($Roles);
        }
        if($request->Role){
            $user->syncRoles($request->Role);
        }
        if($request->permission){
            $user->syncPermissions($request->permission);
        }
        $user->save();
        return redirect()->route("register");
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
            $User = new User();
            $User = $User->find($request->id);
            $delete= $User->delete();
            if($delete){
                return response()->json(['success'=>$User]);
            }

        }
    }
}
