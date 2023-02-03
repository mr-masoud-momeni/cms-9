<?php

namespace App\Http\Controllers\admin;
use App\Jobs\SendMail;
use App\Mail\EmailSender;
use App\Models\User;
use App\Models\EmailGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SendEmail extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Users= User::latest()->get();
        $EmailGroups = EmailGroup::all()->sortBy('id')->pluck('name','id');
        return view('Backend.Notices.Email' , compact('Users','EmailGroups'));
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
    $this->validate($request,[
        'Received'=>'required',
    ]);
    $title=Request('title');
    $Received=explode(',',Request('Received'));
    $cc=explode(',',Request('Received-cc'));
    $body=Request('body');
    $EmailGroups = Request('EmailGroups');
    $job = new SendMail($title,$Received,$cc,$body);
    dispatch($job);
    foreach ($Received as $Receive){
        $job = new SendMail($title,$Receive,$cc,$body);
        dispatch($job)->delay(now()->addSecond(10));
    }

    if($EmailGroups){
        foreach ($EmailGroups as $EmailGroup){
            $Emails = EmailGroup::where('id',$EmailGroup)->pluck('emails');
            $Emails = explode(PHP_EOL, $Emails[0]);
            foreach ($Emails as $Email){
                $job = new SendMail($title,$Email,$cc,$body);
                dispatch($job)->delay(now()->addSecond(10));
            }
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
    public function update(Request $request, $id)
    {
        //
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
