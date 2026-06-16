@extends('layouts.app')

@section('title', 'Detail Reklame — ' . $reklame->no_spk)
@section('page-title', 'Detail Data Reklame')

@section('content')

@php
    // PHP 7.4 compatible date formatter
    $fd = function($d) { return $d ? $d->format('d/m/Y') : '<span style="color:var(--text-dim)">-</span>'; };
    $fs = function($s) { return $s ? htmlspecialchars($s) : '<span style="color:var(--text-dim)">-</span>'; };
@endphp

<div class="page-header">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h2><i class="fa-solid fa-eye me-2" style="color:var(--accent)"></i>Detail Data Reklame</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-dim)">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reklame.index') }}" style="color:var(--text-dim)">Data Reklame</a></li>
                    <li class="breadcrumb-item active">{{ $reklame->no_spk }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('reklame.edit', $reklame) }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-pen me-1"></i>Edit
            </a>
            @if(auth()->user()->isSuperadmin())
            <form method="POST" action="{{ route('reklame.destroy', $reklame) }}"
                  onsubmit="return confirm('Hapus data reklame ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="fa-solid fa-trash me-1"></i>Hapus
                </button>
            </form>
            @endif
            <a href="{{ route('reklame.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
</div>

{{-- ── Status Header ── --}}
@php
    $deadlinePast = $reklame->deadline && $reklame->deadline->isPast()
                    && !$reklame->isSelesai() && !$reklame->isCancel();
@endphp
<div class="card-dark mb-3">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <span class="status-badge {{ $reklame->statusBadgeClass() }}" style="font-size:0.8rem;padding:0.35rem 0.8rem">
                    {{ $reklame->status }}
                </span>
            </div>
            <div class="col">
                <div style="font-size:1.1rem;font-weight:700;color:#a5b4fc;font-family:monospace">
                    {{ $reklame->no_spk }}
                </div>
                <div style="font-size:0.8rem;color:var(--text-dim)">
                    Tgl. SPK: {{ $reklame->tgl_spk ? $reklame->tgl_spk->format('d/m/Y') : '-' }}
                    @if($reklame->deadline)
                    &nbsp;·&nbsp; Deadline:
                    <span @if($deadlinePast) style="color:#fca5a5;font-weight:600" @endif>
                        {{ $reklame->deadline->format('d/m/Y') }}
                        @if($deadlinePast) <i class="fa-solid fa-triangle-exclamation ms-1"></i> @endif
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Lokasi & Entitas ── --}}
<div class="card-dark mb-3">
    <div class="card-header">
        <h6 class="card-title"><i class="fa-solid fa-map-pin me-2" style="color:var(--accent)"></i>Lokasi &amp; Entitas</h6>
    </div>
    <div class="card-body">
        <div class="row g-3" style="font-size:0.875rem">
            <div class="col-6 col-md-3">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Wilayah / DC</div>
                <div style="font-weight:500">{{ optional($reklame->wilayah)->nama_wilayah ?? '-' }}</div>
            </div>
            <div class="col-6 col-md-3">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Cabang</div>
                <div>{{ optional($reklame->cabang)->nama_cabang ?? '-' }}</div>
            </div>
            <div class="col-6 col-md-3">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Brand</div>
                <div>{{ optional($reklame->brand)->nama_brand ?? '-' }}</div>
            </div>
            <div class="col-6 col-md-3">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">PIC</div>
                <div>{{ optional($reklame->pic)->nama_pic ?? '-' }}</div>
            </div>
            <div class="col-6 col-md-4">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Nama Toko</div>
                <div style="font-weight:600">{{ optional($reklame->toko)->nama_toko ?? '-' }}</div>
            </div>
            <div class="col-6 col-md-2">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Kode Toko</div>
                <div style="font-family:monospace;color:#a5b4fc">{{ $reklame->kode_toko ?? '-' }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Detail Reklame ── --}}
<div class="card-dark mb-3">
    <div class="card-header">
        <h6 class="card-title"><i class="fa-solid fa-rectangle-ad me-2" style="color:var(--accent)"></i>Detail Reklame</h6>
    </div>
    <div class="card-body">
        <div class="row g-3" style="font-size:0.875rem">
            <div class="col-6 col-md-3">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Ukuran</div>
                <div>{!! $fs($reklame->ukuran_reklame) !!}</div>
            </div>
            <div class="col-6 col-md-3">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Tanggal Awal</div>
                <div>{!! $fd($reklame->tanggal_awal) !!}</div>
            </div>
            <div class="col-6 col-md-3">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Tgl. Awal Toko Baru</div>
                <div>{!! $fd($reklame->tanggal_awal_toko_baru) !!}</div>
            </div>
            <div class="col-6 col-md-3">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Tgl. Akhir Toko Baru</div>
                <div>{!! $fd($reklame->tanggal_akhir_toko_baru) !!}</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Proses & Timeline ── --}}
<div class="card-dark mb-3">
    <div class="card-header">
        <h6 class="card-title"><i class="fa-solid fa-timeline me-2" style="color:var(--accent)"></i>Proses &amp; Timeline</h6>
    </div>
    <div class="card-body">
        <div class="row g-3" style="font-size:0.875rem">
            <div class="col-6 col-md-3">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Mulai Tgl. Input</div>
                <div>{!! $fd($reklame->mulai_tanggal_input) !!}</div>
            </div>
            <div class="col-6 col-md-3">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Tgl. Update</div>
                <div>{!! $fd($reklame->tanggal_update) !!}</div>
            </div>
            <div class="col-6 col-md-4">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Di Tolak</div>
                <div>{!! $fs($reklame->di_tolak) !!}</div>
            </div>
            <div class="col-6 col-md-2">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Tgl. Pengajuan Ulang</div>
                <div>{!! $fd($reklame->tgl_pengajuan_ulang) !!}</div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->hasRole(['superadmin','finance']))
{{-- ── Data Keuangan ── --}}
<div class="card-dark mb-3" style="border-color:rgba(245,158,11,0.35)">
    <div class="card-header" style="border-bottom-color:rgba(245,158,11,0.2)">
        <h6 class="card-title">
            <i class="fa-solid fa-coins me-2" style="color:#f59e0b"></i>Data Keuangan
            <span class="ms-2" style="background:rgba(245,158,11,0.15);color:#fcd34d;font-size:0.65rem;font-weight:600;padding:0.15rem 0.5rem;border-radius:0.3rem;text-transform:uppercase;letter-spacing:0.04em">Finance</span>
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-3" style="font-size:0.875rem">
            <div class="col-6 col-md-3">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Nominal</div>
                <div style="font-weight:600;color:#fcd34d">
                    @if($reklame->nominal)
                        Rp {{ number_format($reklame->nominal, 0, ',', '.') }}
                    @else
                        <span style="color:var(--text-dim)">-</span>
                    @endif
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">No. Bayar</div>
                <div>{!! $fs($reklame->nomor_bayar) !!}</div>
            </div>
            <div class="col-6 col-md-3">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Jatuh Tempo</div>
                <div>{!! $fd($reklame->jatuh_tempo) !!}</div>
            </div>
            <div class="col-6 col-md-3">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Tgl. Terbit SKPD Baru</div>
                <div>{!! $fd($reklame->tgl_terbit_skpd_baru) !!}</div>
            </div>
        </div>
    </div>
</div>
@endif

@if($reklame->keterangan || $reklame->note)
{{-- ── Catatan ── --}}
<div class="card-dark mb-3">
    <div class="card-header">
        <h6 class="card-title"><i class="fa-solid fa-note-sticky me-2" style="color:var(--accent)"></i>Catatan</h6>
    </div>
    <div class="card-body">
        <div class="row g-3" style="font-size:0.875rem">
            @if($reklame->keterangan)
            <div class="col-12 col-md-6">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px">Keterangan</div>
                <div>{{ $reklame->keterangan }}</div>
            </div>
            @endif
            @if($reklame->note)
            <div class="col-12 col-md-6">
                <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px">Note Internal</div>
                <div>{{ $reklame->note }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

{{-- ── Audit Trail ── --}}
<div class="card-dark mb-3" style="border-color:rgba(99,102,241,0.12)">
    <div class="card-body py-2">
        <div class="row g-2" style="font-size:0.78rem;color:var(--text-dim)">
            <div class="col-6 col-md-3">
                <i class="fa-solid fa-user-plus me-1"></i>
                Dibuat: <span style="color:var(--text-muted)">{{ optional($reklame->createdBy)->name ?? '-' }}</span>
            </div>
            <div class="col-6 col-md-3">
                <i class="fa-solid fa-clock me-1"></i>
                {{ $reklame->created_at ? $reklame->created_at->format('d/m/Y H:i') : '-' }}
            </div>
            <div class="col-6 col-md-3">
                <i class="fa-solid fa-user-pen me-1"></i>
                Diupdate: <span style="color:var(--text-muted)">{{ optional($reklame->updatedBy)->name ?? '-' }}</span>
            </div>
            <div class="col-6 col-md-3">
                <i class="fa-solid fa-clock me-1"></i>
                {{ $reklame->updated_at ? $reklame->updated_at->format('d/m/Y H:i') : '-' }}
            </div>
        </div>
    </div>
</div>

@endsection
