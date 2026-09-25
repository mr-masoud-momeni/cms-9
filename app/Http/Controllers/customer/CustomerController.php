<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Services\ImageUploadService;

class CustomerController extends Controller
{
    protected function UploadImages($file)
    {
        if (!$file) {
            $url['images']['original'] = "/upload/images/default/post.png";
            $url['thum'] = $url['images']['original'];
        } else {
            $year = Carbon::now()->year;
            $imagePath = "upload/images/{$year}";

            $imageUrl = app(ImageUploadService::class)->storeWebp(
                $file,
                $imagePath,
                'image'
            );

            $imageUrl = '/' . ltrim($imageUrl, '/');

            $url['images']['original'] = $imageUrl;
            $url['thum'] = $imageUrl;
        }

        return $url;
    }

    protected function DeleteUploadedImages($images)
    {
        if (!is_array($images)) {
            return;
        }

        $paths = array_unique(array_filter([
            $images['original'] ?? null,
            $images['thum'] ?? null,
        ]));

        $imageService = app(ImageUploadService::class);

        foreach ($paths as $path) {
            $imageService->delete($path);
        }
    }
}
