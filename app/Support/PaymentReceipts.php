<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PaymentReceipts
{
    public static function disk(): string
    {
        return 'local';
    }

    public static function maxKilobytes(): int
    {
        return (int) config('portal.client_files.max_kilobytes', 5120);
    }

    public static function store(int $paymentId, UploadedFile|TemporaryUploadedFile $file): string
    {
        ClientPrivateFiles::assertSafeContent($file, true);

        if ($file instanceof TemporaryUploadedFile) {
            ClientPrivateFiles::compressTemporaryIfNeeded($file, true);
        }

        $kind = ClientPrivateFiles::detectKind($file);
        $ext = $kind === 'png' ? 'png' : 'jpg';
        $directory = 'payment_receipts/'.$paymentId;

        return $file->storeAs($directory, 'screenshot.'.$ext, self::disk());
    }

    public static function delete(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        if (Storage::disk(self::disk())->exists($path)) {
            Storage::disk(self::disk())->delete($path);
        }
    }

    public static function deleteFolder(int $paymentId): void
    {
        $directory = 'payment_receipts/'.$paymentId;

        if (Storage::disk(self::disk())->exists($directory)) {
            Storage::disk(self::disk())->deleteDirectory($directory);
        }
    }

    public static function pathBelongsToPayment(int $paymentId, ?string $path): bool
    {
        if ($path === null || $path === '') {
            return false;
        }

        $path = str_replace('\\', '/', $path);

        if (str_contains($path, '..') || str_starts_with($path, '/')) {
            return false;
        }

        return str_starts_with($path, 'payment_receipts/'.$paymentId.'/');
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

        if ($user->canView('accounts', 'payments')
            || $user->canManage('accounts', 'payments')
            || $user->canView('accounts', 'reports')) {
            return true;
        }

        return $user->canView('clients', 'list')
            || $user->canManage('clients', 'list');
    }
}
