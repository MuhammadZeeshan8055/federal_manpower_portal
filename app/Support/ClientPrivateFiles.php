<?php

namespace App\Support;

use App\Models\User;
use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ClientPrivateFiles
{
    public static function disk(): string
    {
        return 'local';
    }

    public static function maxKilobytes(): int
    {
        return (int) config('portal.client_files.max_kilobytes', 8192);
    }

    public static function uploadMaxKilobytes(): int
    {
        return (int) config('portal.client_files.upload_max_kilobytes', 25600);
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

    // Read the file bytes (not the name) — jpeg | png | pdf | null
    public static function detectKind(UploadedFile|TemporaryUploadedFile $file): ?string
    {
        $path = $file->getRealPath();

        if (! $path || ! is_file($path)) {
            return null;
        }

        $handle = fopen($path, 'rb');

        if (! $handle) {
            return null;
        }

        $header = fread($handle, 8);
        fclose($handle);

        if (! is_string($header) || strlen($header) < 4) {
            return null;
        }

        if (str_starts_with($header, '%PDF')) {
            return 'pdf';
        }

        if (str_starts_with($header, "\xFF\xD8\xFF")) {
            return 'jpeg';
        }

        if (str_starts_with($header, "\x89PNG\r\n\x1a\n")) {
            return 'png';
        }

        return null;
    }

    public static function matchesAllowedContent(UploadedFile|TemporaryUploadedFile $file, bool $photosOnly = false): bool
    {
        $kind = self::detectKind($file);

        if ($kind === null) {
            return false;
        }

        if ($photosOnly) {
            return in_array($kind, ['jpeg', 'png'], true);
        }

        return in_array($kind, ['jpeg', 'png', 'pdf'], true);
    }

    // Laravel validation rule: reject renamed / fake file types
    public static function contentRule(bool $photosOnly = false): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($photosOnly) {
            if ($value === null || $value === '') {
                return;
            }

            if (! $value instanceof UploadedFile && ! $value instanceof TemporaryUploadedFile) {
                return;
            }

            if (! self::matchesAllowedContent($value, $photosOnly)) {
                $fail($photosOnly
                    ? 'Photo must be a real JPG or PNG file.'
                    : 'File must be a real PDF, JPG, or PNG (not renamed).');
            }
        };
    }

    public static function assertSafeContent(UploadedFile|TemporaryUploadedFile $file, bool $photosOnly = false): void
    {
        if (self::matchesAllowedContent($file, $photosOnly)) {
            return;
        }

        throw ValidationException::withMessages([
            'file' => $photosOnly
                ? 'Photo must be a real JPG or PNG file.'
                : 'File must be a real PDF, JPG, or PNG (not renamed).',
        ]);
    }

    public static function storeForClient(int $clientId, string $name, UploadedFile|TemporaryUploadedFile $file): string
    {
        $photosOnly = $name === 'photo';
        self::assertSafeContent($file, $photosOnly);

        $kind = self::detectKind($file);
        $ext = match ($kind) {
            'jpeg' => 'jpg',
            'png' => 'png',
            'pdf' => 'pdf',
            default => 'bin',
        };

        $safeName = preg_replace('/[^a-z0-9_\-]/i', '', $name) ?: 'file';
        $directory = 'clients/'.$clientId;

        return $file->storeAs($directory, $safeName.'.'.$ext, self::disk());
    }

    /**
     * Shrink large JPG/PNG uploads under the stored size limit (silent).
     * Keeps high JPEG quality and large dimensions so visa scans stay clear.
     * PDF files are left as-is.
     */
    public static function compressTemporaryIfNeeded(TemporaryUploadedFile $file, bool $photosOnly = false): TemporaryUploadedFile
    {
        if (! self::matchesAllowedContent($file, $photosOnly)) {
            return $file;
        }

        $path = $file->getRealPath();

        if (! $path || ! is_file($path)) {
            return $file;
        }

        if ($file->getSize() <= self::maxBytes()) {
            return $file;
        }

        $kind = self::detectKind($file);

        if (! in_array($kind, ['jpeg', 'png'], true)) {
            return $file;
        }

        if (! function_exists('imagecreatetruecolor')) {
            return $file;
        }

        $source = $kind === 'png'
            ? @imagecreatefrompng($path)
            : @imagecreatefromjpeg($path);

        if (! $source) {
            return $file;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $cfg = config('portal.client_files.compress', []);
        $quality = (int) ($cfg['start_quality'] ?? 92);
        $minQuality = (int) ($cfg['min_quality'] ?? 80);
        $maxDim = (int) ($cfg['max_dimension'] ?? 3200);
        $minDim = (int) ($cfg['min_dimension'] ?? 1800);
        $written = false;
        $bytes = null;

        // Prefer lowering size via mild re-encode first; only shrink dimensions if needed.
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $scale = min(1.0, $maxDim / max($width, $height, 1));
            $newW = max(1, (int) round($width * $scale));
            $newH = max(1, (int) round($height * $scale));

            $canvas = imagecreatetruecolor($newW, $newH);
            $white = imagecolorallocate($canvas, 255, 255, 255);
            imagefill($canvas, 0, 0, $white);

            if ($kind === 'png') {
                imagealphablending($canvas, true);
            }

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

            if ($quality > $minQuality) {
                $quality = max($minQuality, $quality - 3);
                continue;
            }

            $nextDim = (int) round($maxDim * 0.9);

            if ($nextDim < $minDim) {
                break;
            }

            $maxDim = $nextDim;
        }

        if (! $written && is_string($bytes) && $bytes !== '') {
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
