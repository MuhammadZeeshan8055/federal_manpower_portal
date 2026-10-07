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
        $safeName = preg_replace('/[^a-z0-9_\-]/i', '', $name) ?: 'file';
        $directory = 'clients/'.$clientId;

        return $file->storeAs($directory, $safeName.'.'.$ext, self::disk());
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
