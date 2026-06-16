@extends($isMobile ? 'layouts.mobile' : 'layouts.app')

@section('title', 'Laporan Reklame')
@section('page-title', 'Laporan')

@section('content')

@if($isMobile)
{{-- ═══════════════════════════════════════════ MOBILE LAPORAN ═══ --}}

{{-- Filter Form --}}
<form method="GET" action="{{ route('report') }}" class="mb-3">
    <div class="row g-1 mb-1">
        <div class="col-6">
            <input type="date" name="tgl_dari" class="form-control form-control-sm" value="{{ $tglDari }}" title="Dari Tgl SPK">
        </div>
        <div class="col-6">
            <input type="date" name="tgl_sampai" class="form-control form-control-sm" value="{{ $tglSampai }}" title="Sampai">
        </div>
    </div>
    <div class="row g-1 mb-1">
        <div class="col-6">
            <select name="brand_id" class="form-select form-select-sm">
                <option value="">-- Semua Brand --</option>
                @foreach($allBrands as $b)
                    <option value="{{ $b->id }}" {{ $brandId == $b->id ? 'selected' : '' }}>{{ $b->nama_brand }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6">
            <select name="wilayah_id" class="form-select form-select-sm">
                <option value="">-- Semua Wilayah --</option>
                @foreach($allWilayahs as $w)
                    <option value="{{ $w->id }}" {{ $wilayahId == $w->id ? 'selected' : '' }}>{{ $w->nama_wilayah }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row g-1">
        <div class="col-8">
            <select name="status" class="form-select form-select-sm">
                <option value="">-- Semua Status --</option>
                @foreach($statuses as $s)
                    <option value="{{ $s }}" {{ $statusFilter === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-4">
            <button type="submit" class="btn btn-primary btn-sm w-100">
                <i class="fa-solid fa-filter me-1"></i>Filter
            </button>
        </div>
        @if($tglDari || $tglSampai || $wilayahId || $brandId || $statusFilter)
        <div class="col-12">
            <a href="{{ route('report') }}" class="btn btn-outline-secondary btn-sm w-100">
                <i class="fa-solid fa-times me-1"></i>Reset
            </a>
        </div>
        @endif
    </div>
</form>

{{-- Stat Cards 2x2 --}}
<div class="row g-2 mb-3">
    <div class="col-6">
        <div class="m-card text-center py-2">
            <div style="font-size:1.4rem;font-weight:800;color:var(--accent)">{{ number_format($totalSpk) }}</div>
            <div style="font-size:0.68rem;color:var(--text-dim);text-transform:uppercase;letter-spacing:0.05em">Total SPK</div>
        </div>
    </div>
    <div class="col-6">
        <div class="m-card text-center py-2">
            <div style="font-size:1.4rem;font-weight:800;color:#fb923c">{{ number_format($totalProses) }}</div>
            <div style="font-size:0.68rem;color:var(--text-dim);text-transform:uppercase;letter-spacing:0.05em">Proses Aktif</div>
        </div>
    </div>
    <div class="col-6">
        <div class="m-card text-center py-2">
            <div style="font-size:1.4rem;font-weight:800;color:#4ade80">{{ number_format($totalSelesai) }}</div>
            <div style="font-size:0.68rem;color:var(--text-dim);text-transform:uppercase;letter-spacing:0.05em">Selesai</div>
        </div>
    </div>
    <div class="col-6">
        <div class="m-card text-center py-2">
            <div style="font-size:1.4rem;font-weight:800;color:#f87171">{{ number_format($totalCancel) }}</div>
            <div style="font-size:0.68rem;color:var(--text-dim);text-transform:uppercase;letter-spacing:0.05em">Cancel</div>
        </div>
    </div>
</div>

{{-- Breakdown per Brand --}}
<div class="m-section-title mb-2">
    <i class="fa-solid fa-chart-bar me-1"></i>Breakdown per Brand
</div>

@forelse($brandMatrix as $group)
<div class="m-card mb-2">
    {{-- Brand header --}}
    <div style="font-weight:700;font-size:0.8rem;color:#c4b5fd;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px">
        <i class="fa-solid fa-tag me-1"></i>{{ $group['brand']->nama_brand }}
        <span style="font-size:0.72rem;color:#818cf8;margin-left:6px">{{ $group['total'] }} total</span>
    </div>
    {{-- DC rows --}}
    @foreach($group['wilayahs'] as $row)
    <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:0.78rem;padding-left:8px;border-left:2px solid rgba(99,102,241,0.3)">
        <span style="color:var(--text-muted)">{{ $row['wilayah']->nama_wilayah }}</span>
        <div class="d-flex gap-2">
            @foreach($statuses as $s)
            @if($row['data'][$s] > 0)
            <span style="font-size:0.68rem;background:rgba(99,102,241,0.12);color:#a5b4fc;padding:1px 6px;border-radius:4px">
                {{ $s }}: {{ $row['data'][$s] }}
            </span>
            @endif
            @endforeach
            <span style="font-weight:700;color:#818cf8;font-size:0.75rem">{{ $row['total'] }}</span>
        </div>
    </div>
    @endforeach
    {{-- Subtotal --}}
    <div class="d-flex justify-content-between align-items-center mt-1 pt-1" style="border-top:1px solid rgba(99,102,241,0.2);font-size:0.75rem">
        <span style="color:#a5b4fc;font-weight:600">Subtotal</span>
        <span style="font-weight:700;color:#818cf8">{{ $group['total'] }}</span>
    </div>
</div>
@empty
<div class="m-card text-center py-4" style="color:var(--text-dim)">
    <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
    <span style="font-size:0.85rem">Belum ada data laporan.</span>
</div>
@endforelse

{{-- Link ke Cetak --}}
@php $cetakParams = array_merge(request()->only(['tgl_dari', 'tgl_sampai', 'brand_id', 'wilayah_id', 'status'])); @endphp
<div class="mt-3 d-flex gap-2">
    <a href="{{ route('report.cetak', $cetakParams) }}" target="_blank" class="btn btn-outline-secondary btn-sm flex-fill">
        <i class="fa-solid fa-print me-1"></i>Cetak PDF
    </a>
    @php $exportParams = array_merge(request()->query(), ['export' => 'csv']); @endphp
    <a href="{{ route('report', $exportParams) }}" class="btn btn-outline-success btn-sm flex-fill">
        <i class="fa-solid fa-file-csv me-1"></i>Export CSV
    </a>
</div>

@else
{{-- ═══════════════════════════════════════════ DESKTOP LAPORAN ═══ --}}

<div class="page-header">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h2><i class="fa-solid fa-chart-bar me-2" style="color:var(--accent)"></i>Laporan Reklame</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}" style="color:var(--text-dim)">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Laporan</li>
                </ol>
            </nav>
        </div>
        @php
            $exportParams = array_merge(request()->query(), ['export' => 'csv']);
        @endphp
        <div class="d-flex gap-2 flex-wrap">
            @php $cetakParams = array_merge(request()->only(['tgl_dari', 'tgl_sampai', 'brand_id', 'wilayah_id', 'status'])); @endphp
            <a href="{{ route('report.cetak', $cetakParams) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-print me-1"></i> Cetak
            </a>
            <a href="{{ route('report', $exportParams) }}" class="btn btn-sm btn-outline-success">
                <i class="fa-solid fa-file-csv me-1"></i> Export CSV
            </a>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card-dark mb-4">
    <div class="card-body py-2 px-3">
        <form method="GET" action="{{ route('report') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-sm-auto">
                    <label class="form-label mb-1" style="font-size:0.8rem;color:var(--text-dim)">Dari Tgl SPK</label>
                    <input type="date" name="tgl_dari" class="form-control form-control-sm" value="{{ $tglDari }}" style="width:150px">
                </div>
                <div class="col-12 col-sm-auto">
                    <label class="form-label mb-1" style="font-size:0.8rem;color:var(--text-dim)">Sampai</label>
                    <input type="date" name="tgl_sampai" class="form-control form-control-sm" value="{{ $tglSampai }}" style="width:150px">
                </div>
                <div class="col-12 col-sm-3">
                    <label class="form-label mb-1" style="font-size:0.8rem;color:var(--text-dim)">Wilayah</label>
                    <select name="wilayah_id" class="form-select form-select-sm">
                        <option value="">-- Semua Wilayah --</option>
                        @foreach($allWilayahs as $w)
                            <option value="{{ $w->id }}" {{ $wilayahId == $w->id ? 'selected' : '' }}>
                                {{ $w->nama_wilayah }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-3">
                    <label class="form-label mb-1" style="font-size:0.8rem;color:var(--text-dim)">Brand</label>
                    <select name="brand_id" class="form-select form-select-sm">
                        <option value="">-- Semua Brand --</option>
                        @foreach($allBrands as $b)
                            <option value="{{ $b->id }}" {{ $brandId == $b->id ? 'selected' : '' }}>
                                {{ $b->nama_brand }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-3">
                    <label class="form-label mb-1" style="font-size:0.8rem;color:var(--text-dim)">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- Semua Status --</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s }}" {{ $statusFilter === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-filter me-1"></i>Terapkan
                    </button>
                    @if($tglDari || $tglSampai || $wilayahId || $brandId || $statusFilter)
                        <a href="{{ route('report') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    @endif
                </div>
                @if($tglDari || $tglSampai)
                <div class="col-auto ms-auto">
                    <span style="font-size:0.78rem;color:var(--accent)">
                        <i class="fa-solid fa-calendar-check me-1"></i>
                        {{ $tglDari ?? '...' }} – {{ $tglSampai ?? '...' }}
                    </span>
                </div>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(99,102,241,0.15);">
                <i class="fa-solid fa-file-contract" style="color:#818cf8;"></i>
            </div>
            <div class="stat-value">{{ number_format($totalSpk) }}</div>
            <div class="stat-label">Total SPK</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(251,146,60,0.15);">
                <i class="fa-solid fa-spinner" style="color:#fb923c;"></i>
            </div>
            <div class="stat-value">{{ number_format($totalProses) }}</div>
            <div class="stat-label">Proses Aktif</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(34,197,94,0.15);">
                <i class="fa-solid fa-circle-check" style="color:#4ade80;"></i>
            </div>
            <div class="stat-value">{{ number_format($totalSelesai) }}</div>
            <div class="stat-label">Selesai</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(239,68,68,0.15);">
                <i class="fa-solid fa-ban" style="color:#f87171;"></i>
            </div>
            <div class="stat-value">{{ number_format($totalCancel) }}</div>
            <div class="stat-label">Cancel</div>
        </div>
    </div>
</div>

{{-- Full Breakdown Matrix --}}
<div class="card-dark">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="card-title">
            <i class="fa-solid fa-table me-2" style="color:var(--accent)"></i>Breakdown per DC / Status
        </h6>
        <span style="color:var(--text-dim);font-size:0.75rem">
            @if($tglDari || $tglSampai)
                Periode: {{ $tglDari ?? '...' }} s/d {{ $tglSampai ?? '...' }}
            @else
                Semua periode
            @endif
            @if($wilayahId)
                &bull; {{ optional($allWilayahs->firstWhere('id', $wilayahId))->nama_wilayah }}
            @endif
            @if($brandId)
                &bull; {{ optional($allBrands->firstWhere('id', $brandId))->nama_brand }}
            @endif
            @if($statusFilter)
                &bull; {{ $statusFilter }}
            @endif
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0" style="font-size:0.78rem;min-width:960px">
                <thead>
                    <tr>
                        <th style="min-width:130px;position:sticky;left:0;background:var(--bg-surface);z-index:1">
                            DC / Wilayah
                        </th>
                        @foreach($statuses as $s)
                        <th class="text-center" style="min-width:75px;font-size:0.68rem;line-height:1.2;white-space:nowrap">
                            {{ $s }}
                        </th>
                        @endforeach
                        <th class="text-center" style="min-width:65px;background:rgba(99,102,241,0.12);position:sticky;right:0">
                            TOTAL
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($brandMatrix as $group)
                    {{-- Brand header --}}
                    <tr style="background:rgba(99,102,241,0.18)">
                        <td colspan="{{ count($statuses) + 2 }}"
                            style="font-weight:700;font-size:0.78rem;color:#c4b5fd;letter-spacing:0.06em;text-transform:uppercase;position:sticky;left:0;background:rgba(99,102,241,0.18);padding:6px 12px">
                            <i class="fa-solid fa-tag me-1"></i> {{ $group['brand']->nama_brand }}
                        </td>
                    </tr>
                    {{-- DC rows --}}
                    @foreach($group['wilayahs'] as $row)
                    <tr>
                        <td style="position:sticky;left:0;background:var(--bg-surface);z-index:1;padding-left:20px">
                            <div style="font-weight:600">{{ $row['wilayah']->nama_wilayah }}</div>
                            <div style="color:var(--text-dim);font-size:0.68rem;font-family:monospace">{{ $row['wilayah']->kode_wilayah }}</div>
                        </td>
                        @foreach($statuses as $s)
                        <td class="text-center">
                            @if($row['data'][$s] > 0)
                                <span style="font-weight:600;color:var(--text-primary)">{{ $row['data'][$s] }}</span>
                            @else
                                <span style="color:var(--text-dim)">–</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="text-center" style="background:rgba(99,102,241,0.08);position:sticky;right:0">
                            <span style="font-weight:700;color:#818cf8">{{ $row['total'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                    {{-- Brand subtotal --}}
                    <tr style="background:rgba(99,102,241,0.08);border-top:1px solid rgba(99,102,241,0.3)">
                        <td style="font-weight:700;font-size:0.75rem;color:#a5b4fc;position:sticky;left:0;background:rgba(30,41,59,0.95);padding-left:20px">
                            Subtotal {{ strtoupper($group['brand']->nama_brand) }}
                        </td>
                        @foreach($statuses as $s)
                        <td class="text-center">
                            <span style="font-weight:700;color:{{ $group['subtotal'][$s] > 0 ? '#c4b5fd' : 'var(--text-dim)' }}">
                                {{ $group['subtotal'][$s] > 0 ? $group['subtotal'][$s] : '–' }}
                            </span>
                        </td>
                        @endforeach
                        <td class="text-center" style="background:rgba(99,102,241,0.12);position:sticky;right:0">
                            <span style="font-weight:700;color:#818cf8">{{ $group['total'] }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($statuses) + 2 }}" class="text-center py-4" style="color:var(--text-dim)">
                            <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                            Belum ada data.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(!empty($matrix))
                <tfoot>
                    <tr style="border-top:2px solid rgba(99,102,241,0.35);background:rgba(99,102,241,0.06)">
                        <td style="font-weight:700;color:#818cf8;position:sticky;left:0;background:rgba(30,41,59,0.95)">
                            TOTAL
                        </td>
                        @foreach($statuses as $s)
                        <td class="text-center">
                            <span style="font-weight:700;color:{{ $colTotals[$s] > 0 ? '#c4b5fd' : 'var(--text-dim)' }}">
                                {{ $colTotals[$s] > 0 ? $colTotals[$s] : '–' }}
                            </span>
                        </td>
                        @endforeach
                        <td class="text-center" style="background:rgba(99,102,241,0.12);position:sticky;right:0">
                            <span style="font-weight:700;color:#818cf8">{{ $grandTotal }}</span>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

@endif
{{-- ═══════════════════════════════════════════════════════════════════ --}}

@endsection
