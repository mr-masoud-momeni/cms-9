<?php

namespace App\Services;

use Carbon\Carbon;
use Intervention\Image\Facades\Image;

class ShopLogoService
{
    public function upload($file): string
    {
        $year = Carbon::now()->year;
        $imagePath = "/upload/images/{$year}/";
        $publicPath = config('upload.public_path');

        if (!is_dir($publicPath . $imagePath)) {
            mkdir($publicPath . $imagePath, 0755, true);
        }

        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = preg_replace('/[^A-Za-z0-9_-]/', '-', $filename);
        $filename = trim($filename, '-_') ?: 'logo';
        $filename .= '-' . uniqid() . '.webp';

        $image = Image::make($file->getRealPath());
        $image->resize(600, 600, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $image->encode('webp', 85);
        $image->save($publicPath . $imagePath . $filename);

        return $imagePath . $filename;
    }

    public function delete(?string $path): void
    {
        if (!$path) {
            return;
        }

        $publicPath = config('upload.public_path');
        $fullPath = $publicPath . '/' . ltrim($path, '/');

        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}
