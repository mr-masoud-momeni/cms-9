<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\permission;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Users = User::all()->sortBy('id');
        $permissions = permission::all()->sortBy('id');
        $Roles = Role::all()->sortBy('id');
        return view('Backend.auth.register',compact('permissions', 'Roles','Users'));
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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        if(isset($request['permission'])){
            $user->attachPermissions($request['permission']);
        }

        if(isset($request['Role'])){
            $user->attachRoles($request['Role']);
        }
        if(isset($request['nameStore'])){
            $user->shop()->create([
               'name' => $request->nameStore,
               'domain' => $request->domain,
               'slug' => $request->nameStoreEn,
            ]);
        }
        return back()->withInput();
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
    public function edit($uuid)
    {
        $User = User::all()->where('uuid' , $uuid)->first();
        $permissions = permission::all()->sortBy('id');
        $Roles = Role::all()->sortBy('id');
        return view('Backend.auth.EditUser' , compact('User','permissions' , 'Roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['string', 'max:255'],
        ]);
        $user = auth()->user();
        $user = $user->find($id);
        $user->name = $request->name;
        if($request->email == !$user->email){
            $request->validate([
                'email' => ['string', 'email', 'max:255', 'unique:users'],
            ]);
            $user->email= $request->email;
            $user->email_verified_at = NULL;
        }
        if($request->password){
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->password= Hash::make($request->password);
        }
        if(! $request->permission){
            $permissions = Permission::all();
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
        if(isset($request['nameStore'])){
            $user->shop()->update([
                'name' => $request->nameStore,
                'domain' => $request->domain,
                'slug' => $request->nameStoreEn,
            ]);
        }
        $user->save();
        return redirect(route('register.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
