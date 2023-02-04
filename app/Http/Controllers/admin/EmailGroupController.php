<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EmailGroup;
use App\Models\user;
use Illuminate\Support\Facades\Auth;

class EmailGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $EmailGroups = EmailGroup::all();
        return view('Backend.Notices.EmailGroup' , compact('EmailGroups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'title'=>'required',
            'email'=>'required',
        ]);
        auth()->User()->EmailGroup()->create([
            'name'=>request('title'),
            'emails'=>request('email'),
        ]);
        session()->flash('CreateEmailGroup','دسته بندی ایمیل با موفقیت ثبت شد.');
        return redirect('/admin/email-group/create');
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
    public function edit(EmailGroup $EmailGroup)
    {
        return view('Backend.Notices.EditEmailGroup',compact('EmailGroup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmailGroup $EmailGroup)
    {
        $this->validate($request,[
            'title'=>'required',
            'email'=>'required',
        ]);
        $user = Auth::user();
        $EmailGroup->user_id = $user->id;
        $EmailGroup->name = $request->title;
        $EmailGroup->emails = $request->email;
        $EmailGroup->save();
        session()->flash('EditEmailGroup','دسته بندی ایمیل با موفقیت ویرایش شد.');
        return redirect('/admin/email-group/create');
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
            $EmailGroup = auth()->User()->EmailGroup()->find($request->id);
            $delete = $EmailGroup->delete();
            if($delete){
                return response()->json(['success' => $EmailGroup]);
            }

        }
    }
}
