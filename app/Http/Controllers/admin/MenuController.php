<?php

namespace App\Http\Controllers\admin;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\menu;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menus = menu::latest()->paginate(10);
        return view('Backend.menu.index', compact('menus'));
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
                'title' => 'required | unique:menus'
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
            else{
                $menu=auth()->User()->menu()->create($request->all());
                return response()->json(['success'=>$menu]);
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
    public function edit(menu $menu)
    {
        return view('Backend.menu.create' , compact('menu'));
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
        if($request->content){
            $menu = new menu();
            $menu = $menu->find($request->id);
            $menu->content = $request->content;
            $menu->save();
            return redirect(route('menu.index'));
        }
        if(!$request->content){
            return redirect(route('menu.index'));
        }
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['errorValidate'=>$validator->errors()->all()]);
            }
            $menu = new menu();
            $menu = $menu->find($request->id);
            $menu->title = $request->title;
            $save=$menu->save();
            if($save){
                return response()->json(['success'=>$menu]);
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
