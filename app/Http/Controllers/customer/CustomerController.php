<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class CustomerController extends Controller
{
    protected function UploadImages($file)
    {
        if (!$file) {
            $url['images']['original'] = "/upload/images/default/post.png";
            $url['thum'] = $url['images']['original'];
        } else {
            $year = Carbon::now()->year;
            $imagePath = "/upload/images/{$year}/";
            $filename = $file->getClientOriginalName();
            $publicPath = config('upload.public_path');

            if (!is_dir($publicPath . $imagePath)) {
                mkdir($publicPath . $imagePath, 0755, true);
            }

            $file = $file->move($publicPath . $imagePath, $filename);
            $sizes = ['300', '600', '800'];
            $url['images'] = $this->resize($file->getRealPath(), $sizes, $filename, $imagePath);
            $url['thum'] = $url['images'][$sizes[0]];
        }

        return $url;
    }

    private function resize($path, $sizes, $filename, $imagePath)
    {
        $images['original'] = $imagePath . $filename;

        foreach ($sizes as $size) {
            $images[$size] = $imagePath . "{$size}_" . $filename;

            Image::make($path)
                ->resize($size, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(config('upload.public_path') . $images[$size]);
        }

        return $images;
    }
}
