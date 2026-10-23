<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'bypass_attendance',
        'monthly_salary',
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
            'bypass_attendance' => 'boolean',
            'monthly_salary' => 'decimal:2',
        ];
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(UserPermission::class);
    }

    public function portalNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    public function clientProfile(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function employerProfile(): HasOne
    {
        return $this->hasOne(EmployerProfile::class);
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isEmployer(): bool
    {
        return $this->role === 'employer';
    }

    public function isOfficeUser(): bool
    {
        return ! $this->isClient() && ! $this->isEmployer();
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

    // WFH / remote — skip IP check-in gate
    public function bypassesAttendance(): bool
    {
        return (bool) $this->bypass_attendance;
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
        if ($this->isClient() || $this->isEmployer()) {
            return false;
        }

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
        if ($this->isClient() || $this->isEmployer()) {
            return false;
        }

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
