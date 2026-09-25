<?php

namespace App\Services;

use Intervention\Image\Facades\Image;

class ImageUploadService
{
    public function storeWebp($file, string $directory, string $prefix = 'image', int $maxDimension = 800): string
    {
        $publicPath = rtrim(config('upload.public_path'), DIRECTORY_SEPARATOR);
        $relativeDirectory = trim($directory, '/');

        $targetDirectory = $publicPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativeDirectory);

        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = preg_replace('/[^A-Za-z0-9_-]/', '-', $filename);
        $filename = trim($filename, '-_') ?: $prefix;
        $filename .= '-' . uniqid() . '.webp';

        $image = Image::make($file->getRealPath());

        $image->resize($maxDimension, $maxDimension, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $image->encode('webp', 82);
        $image->save($targetDirectory . DIRECTORY_SEPARATOR . $filename);

        return $relativeDirectory . '/' . $filename;
    }

    public function delete(?string $path): void
    {
        if (!$path) {
            return;
        }

        $publicPath = rtrim(config('upload.public_path'), DIRECTORY_SEPARATOR);
        $relativePath = ltrim($path, '/');
        $fullPath = $publicPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}
