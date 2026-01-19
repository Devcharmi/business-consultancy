<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;

class ImageHelper
{
    public static function uploadImageWithThumbnail($file, $targetWidth, $targetHeight, $folder, $createThumbnail = true, $thumbWidth = 200, $thumbHeight = 200, $thumbFolder = null)
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);

        $filename = now()->format('Ymd_His') . '_' . Str::random(6) . '.jpg';
        $path = public_path($folder);

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $fullPath = $path . '/' . $filename;
        $relativePath = $folder . '/' . $filename;

        // Resize main image
        $image->resize($targetWidth, $targetHeight, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $image->save($fullPath, 90);

        // Create thumbnail if requested
        if ($createThumbnail) {
            $thumbFolder = $thumbFolder ?: ($folder . '/thumbnails');
            if (!File::exists(public_path($thumbFolder))) {
                File::makeDirectory(public_path($thumbFolder), 0755, true);
            }

            $thumbPath = public_path($thumbFolder . '/' . $filename);
            $image->resize($thumbWidth, $thumbHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $image->save($thumbPath, 90);
        }

        return $relativePath;
    }

    public static function deleteImage(?string $imagePath, ?string $thumbFolder = null)
    {
        if (!$imagePath) {
            return;
        }

        // Delete main image
        $fullPath = public_path($imagePath);
        if (File::exists($fullPath)) {
            unlink($fullPath);
        }

        // Delete thumbnail
        if ($thumbFolder) {
            $thumbPath = public_path($thumbFolder . '/' . basename($imagePath));
        } else {
            // Default thumbnails folder same as helper convention
            $thumbPath = public_path(dirname($imagePath) . '/thumbnails/' . basename($imagePath));
        }

        if (File::exists($thumbPath)) {
            unlink($thumbPath);
        }
    }

    public static function uploadImageSimple($file, $folder, $targetWidth = null, $targetHeight = null)
    {
        // Log::info('folder - ' . $folder);
        // Log::info('file - ' . $file);
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);

        $filename = now()->format('Ymd_His') . '_' . Str::random(6) . '.jpg';
        $path = public_path($folder);

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $fullPath = $path . '/' . $filename;
        $relativePath = $folder . '/' . $filename;
        // Log::info('relativePath - ' . $relativePath);

        // Resize main image
        // $image->resize($targetWidth, $targetHeight, function ($constraint) {
        //     $constraint->aspectRatio();
        //     $constraint->upsize();
        // });
        $image->save($fullPath, 90);

        return $relativePath;
    }
    
    public static function processImageSmartCrop($file, $folder, $targetWidth, $targetHeight)
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);

        $originalExtension = strtolower($file->getClientOriginalExtension());
        $filename = now()->format('Ymd_His') . '_' . Str::random(8) . '.' . $originalExtension;

        $path = public_path($folder);
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $fullPath = $path . '/' . $filename;
        $relativePath = $folder . '/' . $filename;

        $originalWidth = $image->width();
        $originalHeight = $image->height();

        if ($originalWidth > $targetWidth || $originalHeight > $targetHeight) {
            $originalAspectRatio = $originalWidth / $originalHeight;
            $targetAspectRatio = $targetWidth / $targetHeight;

            if ($originalAspectRatio > $targetAspectRatio) {
                $newHeight = $originalHeight;
                $newWidth = $originalHeight * $targetAspectRatio;
                $cropX = ($originalWidth - $newWidth) / 2;
                $cropY = 0;
            } else {
                $newWidth = $originalWidth;
                $newHeight = $originalWidth / $targetAspectRatio;
                $cropX = 0;
                $cropY = ($originalHeight - $newHeight) / 2;
            }

            $image->crop((int)$newWidth, (int)$newHeight, (int)$cropX, (int)$cropY)
                ->resize($targetWidth, $targetHeight);
        }

        switch ($originalExtension) {
            case 'png':
                $image->toPng()->save($fullPath); // No compression for PNG
                break;
            case 'webp':
                $image->toWebp(100)->save($fullPath); // Maximum quality for WebP
                break;
            case 'gif':
                $image->toGif()->save($fullPath); // GIF maintains original
                break;
            default:
                $image->toJpeg(100)->save($fullPath); // 100% quality for JPEG
                break;
        }

        return $relativePath;
    }
}
