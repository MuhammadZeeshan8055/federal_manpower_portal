<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class JobPosters
{
    public static function disk(): string
    {
        return 'public';
    }

    public static function maxKilobytes(): int
    {
        return 5120;
    }

    public static function store(int $jobId, UploadedFile|TemporaryUploadedFile $file): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');

        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $ext = 'jpg';
        }

        $directory = 'job_posters/'.$jobId;

        self::deleteFolder($jobId);

        return $file->storeAs($directory, 'poster.'.$ext, self::disk());
    }

    public static function deleteFolder(int $jobId): void
    {
        $directory = 'job_posters/'.$jobId;

        if (Storage::disk(self::disk())->exists($directory)) {
            Storage::disk(self::disk())->deleteDirectory($directory);
        }
    }

    public static function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (! Storage::disk(self::disk())->exists($path)) {
            return null;
        }

        return Storage::disk(self::disk())->url($path);
    }
}
