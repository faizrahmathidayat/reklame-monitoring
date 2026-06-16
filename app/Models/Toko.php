<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_toko',
        'nama_toko',
        'wilayah_id',
        'cabang_id',
        'alamat',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────────────

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
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
