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
            $publicPath = config('upload.public_path');

            if (!is_dir($publicPath . $imagePath)) {
                mkdir($publicPath . $imagePath, 0755, true);
            }

            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = preg_replace('/[^A-Za-z0-9_-]/', '-', $filename);
            $filename = trim($filename, '-_') ?: 'image';
            $filename .= '-' . uniqid() . '.webp';

            $image = Image::make($file->getRealPath());

            $image->resize(800, 800, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $image->encode('webp', 82);
            $image->save($publicPath . $imagePath . $filename);

            $imageUrl = $imagePath . $filename;

            $url['images']['original'] = $imageUrl;
            $url['thum'] = $imageUrl;
        }

        return $url;
    }
}
