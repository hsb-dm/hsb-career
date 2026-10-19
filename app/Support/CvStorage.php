<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class CvStorage
{
    public static function writeDisk(): string
    {
        return (string) config('filesystems.cv_disk', 'local');
    }

    public static function readDisk(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Storage::disk('local')->exists($path)) {
            return 'local';
        }

        $disk = self::writeDisk();

        return $disk !== 'local' && Storage::disk($disk)->exists($path) ? $disk : null;
    }
}
