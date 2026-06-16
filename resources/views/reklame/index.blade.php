@extends($isMobile ? 'layouts.mobile' : 'layouts.app')

@section('title', 'Data Reklame')
@section('page-title', 'Data Reklame')

@section('content')

@if($isMobile)
{{-- ═══════════════════════════════════════════ MOBILE REKLAME INDEX ═══ --}}

{{-- Search + Filter --}}
<form method="GET" action="{{ route('reklame.index') }}" class="mb-3">
    <div class="input-group input-group-sm mb-2">
        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="No. SPK / Kode / Nama Toko..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary btn-sm px-3">Cari</button>
    </div>
    <div class="row g-1">
        <div class="col-6">
            <select name="wilayah_id" class="form-select form-select-sm">
                <option value="">-- Semua Wilayah --</option>
                @foreach($wilayahs as $w)
                    <option value="{{ $w->id }}" {{ request('wilayah_id') == $w->id ? 'selected' : '' }}>{{ $w->nama_wilayah }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6">
            <select name="status" class="form-select form-select-sm">
                <option value="">-- Semua Status --</option>
                @foreach($statuses as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        @if(request()->anyFilled(['search','wilayah_id','status','tgl_dari','tgl_sampai']))
        <div class="col-12 mt-1">
            <a href="{{ route('reklame.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                <i class="fa-solid fa-times me-1"></i>Reset Filter
            </a>
        </div>
        @endif
    </div>
</form>

{{-- Total info --}}
<div style="font-size:0.72rem;color:var(--text-dim);margin-bottom:8px">
    Total: <strong style="color:var(--text-primary)">{{ $data->total() }}</strong> data reklame
</div>

{{-- Reklame Cards --}}
@forelse($data as $item)
@php $deadlinePast = $item->deadline && $item->deadline->isPast() && !$item->isSelesai() && !$item->isCancel(); @endphp
<a href="{{ route('reklame.show', $item->id) }}" class="m-card d-block text-decoration-none mb-2">
    <div class="d-flex justify-content-between align-items-start mb-1">
        <span style="font-family:monospace;font-weight:700;font-size:0.82rem;color:var(--accent)">{{ $item->no_spk }}</span>
        <span class="status-badge {{ $item->statusBadgeClass() }}" style="font-size:0.65rem;padding:2px 7px">{{ $item->status }}</span>
    </div>
    <div style="font-weight:600;font-size:0.875rem;color:var(--text-primary);margin-bottom:3px">
        {{ optional($item->toko)->nama_toko ?? '-' }}
        @if($item->kode_toko)<span style="font-family:monospace;color:var(--text-dim);font-size:0.7rem;margin-left:4px">{{ $item->kode_toko }}</span>@endif
    </div>
    <div class="d-flex gap-2" style="font-size:0.75rem;color:var(--text-muted)">
        <span><i class="fa-solid fa-map-marker-alt me-1" style="color:var(--accent);opacity:0.7"></i>{{ optional($item->wilayah)->nama_wilayah ?? '-' }}</span>
        <span><i class="fa-solid fa-tag me-1" style="opacity:0.5"></i>{{ optional($item->brand)->nama_brand ?? '-' }}</span>
    </div>
    @if($item->deadline)
    <div style="font-size:0.7rem;margin-top:4px;color:{{ $deadlinePast ? '#fca5a5' : 'var(--text-dim)' }}">
        <i class="fa-solid fa-clock me-1"></i>Deadline: {{ $item->deadline->format('d/m/Y') }}
        @if($deadlinePast)<i class="fa-solid fa-triangle-exclamation ms-1" style="color:#ef4444"></i>@endif
    </div>
    @endif
</a>
@empty
<div class="m-card text-center py-4" style="color:var(--text-dim)">
    <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
    <span style="font-size:0.85rem">Belum ada data reklame.</span>
</div>
@endforelse

{{-- Pagination --}}
@if($data->hasPages())
<div class="mt-3 d-flex justify-content-center">
    {{ $data->links() }}
</div>
@endif

{{-- FAB Tambah --}}
@if(auth()->user()->hasRole(['superadmin','staff']))
<a href="{{ route('reklame.create') }}" class="m-fab">
    <i class="fa-solid fa-plus"></i>
</a>
@endif

@else
{{-- ═══════════════════════════════════════════ DESKTOP REKLAME INDEX ═══ --}}

<div class="page-header">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h2><i class="fa-solid fa-layer-group me-2" style="color:var(--accent)"></i>Data Reklame</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-dim)">Dashboard</a></li>
                    <li class="breadcrumb-item active">Data Reklame</li>
                </ol>
            </nav>
        </div>
        @if(auth()->user()->hasRole(['superadmin','staff']))
        <a href="{{ route('reklame.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Tambah Data
        </a>
        @endif
    </div>
</div>

{{-- Filter Bar --}}
<div class="card-dark mb-3">
    <div class="card-body py-2 px-3">
        <form method="GET" action="{{ route('reklame.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-sm-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" name="search" class="form-control"
                            placeholder="No. SPK / Kode / Nama Toko..."
                            value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-6 col-sm-2">
                    <select name="wilayah_id" class="form-select form-select-sm">
                        <option value="">-- Semua Wilayah --</option>
                        @foreach($wilayahs as $w)
                        <option value="{{ $w->id }}" {{ request('wilayah_id') == $w->id ? 'selected' : '' }}>
                            {{ $w->nama_wilayah }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- Semua Status --</option>
                        @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-2">
                    <input type="date" name="tgl_dari" class="form-control form-control-sm"
                        value="{{ request('tgl_dari') }}" title="Tgl. SPK Dari">
                </div>
                <div class="col-6 col-sm-2">
                    <input type="date" name="tgl_sampai" class="form-control form-control-sm"
                        value="{{ request('tgl_sampai') }}" title="Tgl. SPK Sampai">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                    @if(request()->anyFilled(['search','wilayah_id','status','tgl_dari','tgl_sampai']))
                    <a href="{{ route('reklame.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card-dark">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title"><i class="fa-solid fa-table me-2"></i>Daftar Data Reklame</h6>
        <span style="color:var(--text-dim);font-size:0.75rem;">Total: {{ $data->total() }} data</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th width="42">#</th>
                        <th width="95">Tgl. SPK</th>
                        <th>No. SPK</th>
                        <th>Toko</th>
                        <th width="120">Wilayah</th>
                        <th width="100">Brand</th>
                        <th class="text-center" width="170">Status</th>
                        <th width="95">Deadline</th>
                        <th class="text-center" width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $i => $item)
                    @php
                        $deadlinePast = $item->deadline && $item->deadline->isPast()
                                        && !$item->isSelesai() && !$item->isCancel();
                    @endphp
                    <tr>
                        <td style="color:var(--text-dim)">{{ $data->firstItem() + $i }}</td>
                        <td style="font-size:0.8rem;white-space:nowrap">
                            {{ $item->tgl_spk ? $item->tgl_spk->format('d/m/Y') : '-' }}
                        </td>
                        <td>
                            <span style="font-family:monospace;color:#a5b4fc;font-size:0.875rem">
                                {{ $item->no_spk }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight:500;font-size:0.875rem">
                                {{ optional($item->toko)->nama_toko ?? '-' }}
                            </div>
                            @if($item->kode_toko)
                            <div style="font-family:monospace;color:var(--text-dim);font-size:0.75rem">
                                {{ $item->kode_toko }}
                            </div>
                            @endif
                        </td>
                        <td style="font-size:0.8rem;color:var(--text-muted)">
                            {{ optional($item->wilayah)->nama_wilayah ?? '-' }}
                        </td>
                        <td style="font-size:0.8rem">{{ optional($item->brand)->nama_brand ?? '-' }}</td>
                        <td class="text-center">
                            <span class="status-badge {{ $item->statusBadgeClass() }}">{{ $item->status }}</span>
                        </td>
                        <td style="font-size:0.8rem;white-space:nowrap;{{ $deadlinePast ? 'color:#fca5a5;font-weight:600' : '' }}">
                            @if($item->deadline)
                                @if($deadlinePast)
                                    <i class="fa-solid fa-triangle-exclamation me-1" style="color:#ef4444"></i>
                                @endif
                                {{ $item->deadline->format('d/m/Y') }}
                            @else
                                <span style="color:var(--text-dim)">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('reklame.show', $item->id) }}"
                                   class="btn btn-sm btn-outline-secondary py-1 px-2"
                                   style="font-size:0.7rem" title="Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('reklame.edit', $item->id) }}"
                                   class="btn btn-sm btn-outline-secondary py-1 px-2"
                                   style="font-size:0.7rem" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                @if(auth()->user()->isSuperadmin())
                                <form method="POST" action="{{ route('reklame.destroy', $item->id) }}"
                                      onsubmit="return confirm('Hapus data reklame \'{{ $item->no_spk }}\'?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2"
                                            style="font-size:0.7rem" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5" style="color:var(--text-dim)">
                            <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                            Belum ada data reklame.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($data->hasPages())
        <div class="px-3 py-2 border-top" style="border-color:var(--border-color)!important">
            {{ $data->links() }}
        </div>
        @endif
    </div>
</div>

@endif
{{-- ═══════════════════════════════════════════════════════════════════ --}}

@endsection
