<?php

namespace App\Http\Controllers\admin;
use App\Events\NotificationEvent;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Notifications= Notification::latest()->get();
        return view('Backend.Notices.Notification',compact('Notifications'));
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
                'title' => 'required',
                'slug' => 'required',
                'body' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
            else{
                $Notification=auth()->User()->Notification()->create($request->all());
                event( new \App\Events\NotificationEvent($Notification));
                return response()->json(['success'=>$Notification]);
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
    public function edit($id)
    {
        //
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
                'slug' => 'required',
                'body' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['errorValidate'=>$validator->errors()->all()]);
            }
            $Notification = new Notification();
            $Notification = $Notification->find($request->id);
            $Notification->title = $request->title;
            $Notification->user_id = $request->user()->id;
            $Notification->slug= $request->slug;
            $Notification->body= $request->body;
            $save=$Notification->save();
            if($save){
                return response()->json(['success'=>$Notification]);
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
    public function destroy($id)
    {
        //
    }
}
