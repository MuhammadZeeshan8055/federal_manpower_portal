<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ClientPrivateFiles
{
    public static function disk(): string
    {
        return 'local';
    }

    public static function maxKilobytes(): int
    {
        return (int) config('portal.client_files.max_kilobytes', 5120);
    }

    public static function maxBytes(): int
    {
        return self::maxKilobytes() * 1024;
    }

    public static function allowedMimes(): array
    {
        return config('portal.client_files.mimes', ['pdf', 'jpg', 'jpeg', 'png']);
    }

    public static function photoMimes(): array
    {
        return config('portal.client_files.photo_mimes', ['jpg', 'jpeg', 'png']);
    }

    public static function storeForClient(int $clientId, string $name, UploadedFile|TemporaryUploadedFile $file): string
    {
        $ext = strtolower((string) ($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin'));

        // Compression may rewrite PNG bytes as JPEG — store with matching extension
        $realPath = $file->getRealPath();
        if ($realPath && is_file($realPath)) {
            $header = (string) file_get_contents($realPath, false, null, 0, 3);
            if ($header === "\xFF\xD8\xFF") {
                $ext = 'jpg';
            }
        }

        $safeName = preg_replace('/[^a-z0-9_\-]/i', '', $name) ?: 'file';
        $directory = 'clients/'.$clientId;

        return $file->storeAs($directory, $safeName.'.'.$ext, self::disk());
    }

    /**
     * Shrink large JPG/PNG uploads under the portal size limit (silent).
     * PDF files are left as-is.
     */
    public static function compressTemporaryIfNeeded(TemporaryUploadedFile $file): TemporaryUploadedFile
    {
        $path = $file->getRealPath();

        if (! $path || ! is_file($path)) {
            return $file;
        }

        if ($file->getSize() <= self::maxBytes()) {
            return $file;
        }

        $ext = strtolower((string) ($file->getClientOriginalExtension() ?: $file->extension() ?: ''));

        if (! in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            return $file;
        }

        if (! function_exists('imagecreatetruecolor')) {
            return $file;
        }

        $source = match ($ext) {
            'png' => @imagecreatefrompng($path),
            default => @imagecreatefromjpeg($path),
        };

        if (! $source) {
            return $file;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $quality = 85;
        $maxDim = 2200;
        $written = false;

        for ($attempt = 0; $attempt < 8; $attempt++) {
            $scale = min(1.0, $maxDim / max($width, $height, 1));
            $newW = max(1, (int) round($width * $scale));
            $newH = max(1, (int) round($height * $scale));

            $canvas = imagecreatetruecolor($newW, $newH);
            $white = imagecolorallocate($canvas, 255, 255, 255);
            imagefill($canvas, 0, 0, $white);
            imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newW, $newH, $width, $height);

            ob_start();
            imagejpeg($canvas, null, $quality);
            $bytes = ob_get_clean();
            imagedestroy($canvas);

            if ($bytes !== false && strlen($bytes) <= self::maxBytes()) {
                file_put_contents($path, $bytes);
                $written = true;
                break;
            }

            $quality = max(40, $quality - 8);
            $maxDim = max(900, (int) round($maxDim * 0.85));
        }

        if (! $written && isset($bytes) && is_string($bytes) && $bytes !== '') {
            file_put_contents($path, $bytes);
        }

        imagedestroy($source);

        clearstatcache(true, $path);

        return $file;
    }

    public static function pathBelongsToClient(int $clientId, ?string $path): bool
    {
        if ($path === null || $path === '') {
            return false;
        }

        $path = str_replace('\\', '/', $path);

        if (str_contains($path, '..') || str_starts_with($path, '/')) {
            return false;
        }

        return str_starts_with($path, 'clients/'.$clientId.'/');
    }

    public static function exists(?string $path): bool
    {
        return $path !== null
            && $path !== ''
            && Storage::disk(self::disk())->exists($path);
    }

    public static function userCanAccess(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        foreach (['create', 'list', 'documents'] as $feature) {
            if ($user->canView('clients', $feature) || $user->canManage('clients', $feature)) {
                return true;
            }
        }

        return false;
    }
}
