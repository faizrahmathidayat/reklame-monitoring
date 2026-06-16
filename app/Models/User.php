<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_SUPERADMIN = 'superadmin';
    const ROLE_STAFF      = 'staff';
    const ROLE_FINANCE    = 'finance';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active'         => 'boolean',
    ];

    // ── Role helpers ──────────────────────────────────────────────────────

    public function isSuperadmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function isFinance(): bool
    {
        return $this->role === self::ROLE_FINANCE;
    }

    public function hasRole($roles): bool
    {
        return in_array($this->role, (array) $roles);
    }

    // ── Relationships ─────────────────────────────────────────────────────

    public function reklamesCreated()
    {
        return $this->hasMany(Reklame::class, 'created_by');
    }

    public function reklamesUpdated()
    {
        return $this->hasMany(Reklame::class, 'updated_by');
    }
}
