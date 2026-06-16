<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reklame extends Model
{
    use HasFactory, SoftDeletes;

    // ── Status Constants ──────────────────────────────────────────────────

    const STATUS_CANCEL               = 'CANCEL';
    const STATUS_INPUT_WEB            = 'INPUT WEB';
    const STATUS_INVOICE_TERKIRIM     = 'INVOICE TERKIRIM';
    const STATUS_MENUNGGU_IPR         = 'MENUNGGU IPR';
    const STATUS_PEMBERKASAN          = 'PEMBERKASAN';
    const STATUS_PETUGAS_PELAYANAN    = 'PETUGAS PELAYANAN';
    const STATUS_PROSES_INVOICE       = 'PROSES INVOICE';
    const STATUS_PROSES_PEMBAYARAN    = 'PROSES PEMBAYARAN';
    const STATUS_SELESAI              = 'SELESAI';
    const STATUS_SELESAI_INV_TERKIRIM = 'SELESAI INV TERKIRIM';
    const STATUS_SELESAI_TERBIT_SKPD  = 'SELESAI TERBIT SKPD';

    // ── Pengelompokan status untuk agregasi dashboard ─────────────────────

    const GROUP_PROSES_INTERNAL = [
        self::STATUS_INPUT_WEB,
        self::STATUS_PEMBERKASAN,
        self::STATUS_PETUGAS_PELAYANAN,
    ];

    const GROUP_PROSES_INPUT_VERIF = [
        self::STATUS_INPUT_WEB,
        self::STATUS_PEMBERKASAN,
        self::STATUS_PETUGAS_PELAYANAN,
        self::STATUS_MENUNGGU_IPR,
    ];

    const GROUP_SIAP_DIBAYAR = [
        self::STATUS_PROSES_PEMBAYARAN,
    ];

    const GROUP_PROSES_TERBIT_SKPD = [
        self::STATUS_MENUNGGU_IPR,
        self::STATUS_SELESAI_TERBIT_SKPD,
    ];

    const GROUP_PROSES_INVOICE = [
        self::STATUS_PROSES_INVOICE,
    ];

    const GROUP_INVOICE_TERKIRIM = [
        self::STATUS_INVOICE_TERKIRIM,
        self::STATUS_SELESAI_INV_TERKIRIM,
    ];

    const GROUP_CANCEL = [
        self::STATUS_CANCEL,
    ];

    const GROUP_SELESAI = [
        self::STATUS_SELESAI,
        self::STATUS_SELESAI_INV_TERKIRIM,
        self::STATUS_SELESAI_TERBIT_SKPD,
    ];

    // ── Fillable ──────────────────────────────────────────────────────────

    protected $fillable = [
        'spk_id',
        'tgl_spk',
        'deadline',
        'no_spk',
        'wilayah_id',
        'toko_id',
        'brand_id',
        'cabang_id',
        'pic_id',
        'kode_toko',
        'ukuran_reklame',
        'jumlah_objek',
        'tanggal_awal',
        'tanggal_awal_toko_baru',
        'tanggal_akhir_toko_baru',
        'nominal',
        'tgl_terbit_skpd_baru',
        'nomor_bayar',
        'jatuh_tempo',
        'mulai_tanggal_input',
        'tanggal_update',
        'status',
        'di_tolak',
        'tgl_pengajuan_ulang',
        'keterangan',
        'note',
        'created_by',
        'updated_by',
    ];

    // ── Casts ─────────────────────────────────────────────────────────────

    protected $casts = [
        'jumlah_objek'            => 'integer',
        'tgl_spk'                 => 'date',
        'deadline'                => 'date',
        'tanggal_awal'            => 'date',
        'tanggal_awal_toko_baru'  => 'date',
        'tanggal_akhir_toko_baru' => 'date',
        'tgl_terbit_skpd_baru'    => 'date',
        'jatuh_tempo'             => 'date',
        'mulai_tanggal_input'     => 'date',
        'tanggal_update'          => 'date',
        'tgl_pengajuan_ulang'     => 'date',
        'nominal'                 => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────────────────

    public function spk()      { return $this->belongsTo(Spk::class); }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function pic()
    {
        return $this->belongsTo(Pic::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeByWilayah($query, $wilayahId)
    {
        return $query->where('wilayah_id', $wilayahId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('tgl_spk', [$from, $to]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    public static function allStatuses(): array
    {
        return [
            self::STATUS_CANCEL,
            self::STATUS_INPUT_WEB,
            self::STATUS_INVOICE_TERKIRIM,
            self::STATUS_MENUNGGU_IPR,
            self::STATUS_PEMBERKASAN,
            self::STATUS_PETUGAS_PELAYANAN,
            self::STATUS_PROSES_INVOICE,
            self::STATUS_PROSES_PEMBAYARAN,
            self::STATUS_SELESAI,
            self::STATUS_SELESAI_INV_TERKIRIM,
            self::STATUS_SELESAI_TERBIT_SKPD,
        ];
    }

    public function isCancel(): bool
    {
        return $this->status === self::STATUS_CANCEL;
    }

    public function isSelesai(): bool
    {
        return in_array($this->status, self::GROUP_SELESAI);
    }

    public function statusBadgeClass(): string
    {
        $map = [
            self::STATUS_CANCEL               => 'status-cancel',
            self::STATUS_INPUT_WEB            => 'status-input-web',
            self::STATUS_INVOICE_TERKIRIM     => 'status-inv-terkirim',
            self::STATUS_MENUNGGU_IPR         => 'status-menunggu-ipr',
            self::STATUS_PEMBERKASAN          => 'status-pemberkasan',
            self::STATUS_PETUGAS_PELAYANAN    => 'status-petugas',
            self::STATUS_PROSES_INVOICE       => 'status-proses-invoice',
            self::STATUS_PROSES_PEMBAYARAN    => 'status-proses-bayar',
            self::STATUS_SELESAI              => 'status-selesai',
            self::STATUS_SELESAI_INV_TERKIRIM => 'status-inv-terkirim',
            self::STATUS_SELESAI_TERBIT_SKPD  => 'status-selesai-skpd',
        ];
        return $map[$this->status] ?? 'status-input-web';
    }
}
