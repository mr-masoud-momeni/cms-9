<?php

namespace App\Http\Controllers\admin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class panelAdmin extends Controller
{
    public function UploadImageInText(Request $request)
    {
        $this->validate(request(), [
            'upload' => 'required|mimes:jpeg,jpg,bmp,png'
        ]);

        $year = carbon::now()->year;

        $imagePath = "/upload/images/{$year}/";

        $file = $request->file('upload');

        $filename = $file->getClientOriginalName();

        if (file_exists(public_path($imagePath) . $filename)) {
            $filename = carbon::now()->timestamp . $filename;
        }
        $file->move(public_path($imagePath), $filename);
        $url = asset('public/'.$imagePath.$filename) ;
        return "<script>window.parent.CKEDITOR.tools.callFunction(1,'{$url}', '')</script>";
    }
}
