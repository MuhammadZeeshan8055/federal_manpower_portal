<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(UserPermission::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->isSuperAdmin();
    }

    public function isHr(): bool
    {
        return $this->role === 'hr';
    }

    public function isAccounts(): bool
    {
        return $this->role === 'accounts';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function roleLabel(): string
    {
        return config('portal.roles.'.$this->role, $this->role ?? 'Staff');
    }

    public function canView(string $module, string $feature): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->permissions()
            ->where('module_key', $module)
            ->where('feature_key', $feature)
            ->exists();
    }

    public function canManage(string $module, string $feature): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->permissions()
            ->where('module_key', $module)
            ->where('feature_key', $feature)
            ->where('access', 'manage')
            ->exists();
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }
}
