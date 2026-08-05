<?php

namespace App\Services;

use Exception;
use Intervention\Image\Laravel\Facades\Image;
use \App\Contracts\ImageContract;

class ImageSetService
{
    const EXTENTIONS = [
        "image/png" => 'png',
        "image/jpeg" => 'jpg',
        "image/jpg" => 'jpg',
        "image/webp" => 'webp'
    ];

    const MIMES = [
        'png' => "image/png",
        'jpeg' => "image/jpeg",
        'jpg' => "image/jpg",
        'webp' => "image/webp"
    ];

    const SIZES = [
        'sm' => 576,
        'md' => 992,
        'lg' => 1200,
    ];

    const MAX_WIDTH = 1920;

    public static function removeByName(string $name, string $path): void
    {
        $mainName = explode('.', $name)[0];
        $extention = explode('.', $name)[1] == 'webp' ? 'jpg' : 'webp';
        self::removeImage($path . '/' . $mainName . '.' . $extention);
        self::removeImage($path . '/' . $name);

        foreach(self::SIZES as $sizeName => $size) {
            $sPath = $path . '/' . $sizeName . '/';
            self::removeImage($sPath . $mainName . '.' . $extention);
            self::removeImage($sPath . $name);
        }
    }

    public static function saveToStorage($image, string $folderPath): array|bool
    {
        try {
            $name = hash('sha256', (string)microtime(true));
            $image = Image::decode($image);
            $extention = self::EXTENTIONS[$image->origin()->mediaType()];
            $additionalType = $extention != 'webp' ? 'webp' : 'jpg';

            self::saveImage(clone $image, $name, $folderPath . '/', $extention);
            self::saveImage(clone $image, $name, $folderPath . '/', $additionalType);
            $sizes = self::createWidthSet(clone $image, $name, $folderPath, $additionalType);

            return [
                'name' => $extention != 'webp' ? $name . '.' . $extention : $name . '.' . $additionalType,
                'sizes' => $sizes
            ];
        } catch (Exception $error) {
            self::removeByName($name . '.' . $extention, $folderPath);
            \App\Helpers::log($error->getMessage(), __DIR__ . '/ImageServiceErrors');
        }

        return false;
    }

    private static function createWidthSet($image, string $name, string $folderPath, string $additionalType): array
    {
        $width = +$image->width();
        $sizesArr = [];
        foreach (self::SIZES as $sizeName => $size) {
            if (!($width > $size)) continue;
            $path = $folderPath . '/' . $sizeName;

            self::saveImage(
                (clone $image)->scale(width: $size),
                $name,
                $path,
                self::EXTENTIONS[$image->origin()->mediaType()]
            );

            self::saveImage(
                (clone $image)->scale(width: $size),
                $name,
                $path,
                $additionalType
            );
            $sizesArr[] = $sizeName;
        }

        return $sizesArr;
    }

    private static function saveImage($image, string $name, string $path, string $type): void
    {
        if (+$image->width() > self::MAX_WIDTH) {
            $image->scale(width: self::MAX_WIDTH);
        }
        $fullName = $name . '.' . $type;
        if (!file_exists($path)) mkdir($path, 0755, true);
        $imagePath = $path . '/' . $fullName;
        if ($type === 'jpg') {
            $image->save($imagePath, quality: 70, progressive: true);

            return;
        }

        $image->save($imagePath, quality: 70);
    }

    private static function removeImage(string $path): void
    {
        if (file_exists($path)) unlink($path);
    }
}


