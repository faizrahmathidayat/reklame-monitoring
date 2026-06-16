<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_wilayah',
        'nama_wilayah',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────────────

    public function cabangs()
    {
        return $this->hasMany(Cabang::class);
    }

    public function tokos()
    {
        return $this->hasMany(Toko::class);
    }

    public function reklames()
    {
        return $this->hasMany(Reklame::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
