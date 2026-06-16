<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Spk extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'no_spk',
        'tgl_spk',
        'deadline',
        'wilayah_id',
        'brand_id',
        'cabang_id',
        'pic_id',
        'mulai_tanggal_input',
        'keterangan',
        'note',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tgl_spk'             => 'date',
        'deadline'            => 'date',
        'mulai_tanggal_input' => 'date',
    ];

    public function wilayah()  { return $this->belongsTo(Wilayah::class); }
    public function brand()    { return $this->belongsTo(Brand::class); }
    public function cabang()   { return $this->belongsTo(Cabang::class); }
    public function pic()      { return $this->belongsTo(Pic::class); }
    public function reklames() { return $this->hasMany(Reklame::class); }
    public function createdBy(){ return $this->belongsTo(User::class, 'created_by'); }
    public function updatedBy(){ return $this->belongsTo(User::class, 'updated_by'); }
}
