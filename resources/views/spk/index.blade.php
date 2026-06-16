@extends($isMobile ? 'layouts.mobile' : 'layouts.app')

@section('title', 'Data SPK')
@section('page-title', 'Data SPK')

@section('content')

@if($isMobile)
{{-- ═══════════════════════════════════════════ MOBILE SPK INDEX ═══ --}}

{{-- Search + Filter --}}
<form method="GET" action="{{ route('spk.index') }}" class="mb-3">
    <div class="input-group input-group-sm mb-2">
        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Cari No. SPK..." value="{{ $search }}">
        <button type="submit" class="btn btn-primary btn-sm px-3">Cari</button>
    </div>
    <div class="row g-1">
        <div class="col-6">
            <select name="wilayah_id" class="form-select form-select-sm">
                <option value="">-- Semua DC --</option>
                @foreach($wilayahs as $w)
                    <option value="{{ $w->id }}" {{ $wilayahId == $w->id ? 'selected' : '' }}>{{ $w->nama_wilayah }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6">
            <select name="brand_id" class="form-select form-select-sm">
                <option value="">-- Semua Brand --</option>
                @foreach($brands as $b)
                    <option value="{{ $b->id }}" {{ $brandId == $b->id ? 'selected' : '' }}>{{ $b->nama_brand }}</option>
                @endforeach
            </select>
        </div>
        @if($search || $wilayahId || $brandId || $tglDari || $tglSampai)
        <div class="col-12">
            <a href="{{ route('spk.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                <i class="fa-solid fa-times me-1"></i>Reset Filter
            </a>
        </div>
        @endif
    </div>
</form>

{{-- Total info --}}
<div style="font-size:0.72rem;color:var(--text-dim);margin-bottom:8px">
    Total: <strong style="color:var(--text-primary)">{{ $data->total() }}</strong> SPK
</div>

{{-- SPK Cards --}}
@forelse($data as $spk)
<a href="{{ route('spk.show', $spk) }}" class="m-card d-block text-decoration-none mb-2">
    <div class="d-flex justify-content-between align-items-start mb-1">
        <span style="font-family:monospace;font-weight:700;font-size:0.85rem;color:var(--accent)">{{ $spk->no_spk }}</span>
        <span style="background:rgba(99,102,241,0.15);color:#a5b4fc;padding:2px 8px;border-radius:6px;font-size:0.72rem;font-weight:600">
            {{ $spk->jumlah_toko }} toko
        </span>
    </div>
    <div class="d-flex gap-2 mb-1" style="font-size:0.78rem;color:var(--text-muted)">
        <span><i class="fa-solid fa-map-marker-alt me-1" style="color:var(--accent);opacity:0.7"></i>{{ optional($spk->wilayah)->nama_wilayah ?? '—' }}</span>
        <span><i class="fa-solid fa-tag me-1" style="opacity:0.5"></i>{{ optional($spk->brand)->nama_brand ?? '—' }}</span>
    </div>
    <div class="d-flex justify-content-between align-items-center" style="font-size:0.72rem;color:var(--text-dim)">
        <span><i class="fa-solid fa-calendar me-1"></i>{{ optional($spk->tgl_spk)->format('d/m/Y') }}</span>
        @if($spk->deadline)
        @php $dl = now()->diffInDays($spk->deadline, false); @endphp
        <span style="color:{{ $dl <= 0 ? '#fca5a5' : ($dl <= 7 ? '#fcd34d' : 'var(--text-dim)') }}">
            <i class="fa-solid fa-clock me-1"></i>{{ $spk->deadline->format('d/m/Y') }}
        </span>
        @endif
    </div>
    @if($spk->pic)
    <div style="font-size:0.68rem;color:var(--text-dim);margin-top:4px">
        <i class="fa-solid fa-user-tie me-1"></i>{{ $spk->pic->nama_pic }}
    </div>
    @endif
</a>
@empty
<div class="m-card text-center py-4" style="color:var(--text-dim)">
    <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
    <span style="font-size:0.85rem">Belum ada data SPK.</span>
</div>
@endforelse

{{-- Pagination --}}
@if($data->hasPages())
<div class="mt-3 d-flex justify-content-center">
    {{ $data->links() }}
</div>
@endif

{{-- FAB Tambah SPK --}}
@if(auth()->user()->hasRole(['superadmin','staff']))
<a href="{{ route('spk.create') }}" class="m-fab">
    <i class="fa-solid fa-plus"></i>
</a>
@endif

@else
{{-- ═══════════════════════════════════════════ DESKTOP SPK INDEX ═══ --}}

<div class="page-header">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h2><i class="fa-solid fa-file-contract me-2" style="color:var(--accent)"></i>Data SPK</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-dim)">Dashboard</a></li>
                    <li class="breadcrumb-item active">SPK</li>
                </ol>
            </nav>
        </div>
        @if(auth()->user()->hasRole(['superadmin','staff']))
        <a href="{{ route('spk.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Tambah SPK
        </a>
        @endif
    </div>
</div>

{{-- Filter --}}
<div class="card-dark mb-3">
    <div class="card-body py-2 px-3">
        <form method="GET" action="{{ route('spk.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-sm-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari No. SPK..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-6 col-sm-2">
                    <select name="wilayah_id" class="form-select form-select-sm">
                        <option value="">-- Semua DC --</option>
                        @foreach($wilayahs as $w)
                            <option value="{{ $w->id }}" {{ $wilayahId == $w->id ? 'selected' : '' }}>{{ $w->nama_wilayah }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-2">
                    <select name="brand_id" class="form-select form-select-sm">
                        <option value="">-- Semua Brand --</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}" {{ $brandId == $b->id ? 'selected' : '' }}>{{ $b->nama_brand }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-2">
                    <input type="date" name="tgl_dari" class="form-control form-control-sm" value="{{ $tglDari }}" title="Dari Tgl SPK">
                </div>
                <div class="col-6 col-sm-2">
                    <input type="date" name="tgl_sampai" class="form-control form-control-sm" value="{{ $tglSampai }}" title="Sampai Tgl SPK">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                    @if($search || $wilayahId || $brandId || $tglDari || $tglSampai)
                        <a href="{{ route('spk.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card-dark">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title"><i class="fa-solid fa-table me-2"></i>Daftar SPK</h6>
        <span style="color:var(--text-dim);font-size:0.75rem">Total: {{ $data->total() }} SPK</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>No. SPK</th>
                        <th class="text-center" width="100">Tgl. SPK</th>
                        <th>DC / Wilayah</th>
                        <th>Brand</th>
                        <th class="text-center" width="80">Toko</th>
                        <th class="text-center" width="100">Deadline</th>
                        <th>PIC</th>
                        <th class="text-center" width="110">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $i => $spk)
                    <tr>
                        <td style="color:var(--text-dim)">{{ $data->firstItem() + $i }}</td>
                        <td>
                            <a href="{{ route('spk.show', $spk) }}" style="color:var(--accent);font-weight:600;font-family:monospace;font-size:0.825rem;text-decoration:none">
                                {{ $spk->no_spk }}
                            </a>
                        </td>
                        <td class="text-center" style="font-size:0.8rem;color:var(--text-muted)">
                            {{ optional($spk->tgl_spk)->format('d/m/Y') }}
                        </td>
                        <td>
                            <span style="font-size:0.825rem">{{ optional($spk->wilayah)->nama_wilayah }}</span>
                        </td>
                        <td style="font-size:0.825rem">{{ optional($spk->brand)->nama_brand }}</td>
                        <td class="text-center">
                            <span style="background:rgba(99,102,241,0.15);color:#a5b4fc;padding:0.2rem 0.6rem;border-radius:0.3rem;font-size:0.75rem;font-weight:600">
                                {{ $spk->jumlah_toko }}
                            </span>
                        </td>
                        <td class="text-center" style="font-size:0.8rem">
                            @if($spk->deadline)
                                @php $daysLeft = now()->diffInDays($spk->deadline, false); @endphp
                                <span style="color:{{ $daysLeft <= 0 ? '#fca5a5' : ($daysLeft <= 7 ? '#fcd34d' : 'var(--text-muted)') }}">
                                    {{ $spk->deadline->format('d/m/Y') }}
                                </span>
                            @else
                                <span style="color:var(--text-dim)">—</span>
                            @endif
                        </td>
                        <td style="font-size:0.8rem;color:var(--text-muted)">{{ optional($spk->pic)->nama_pic ?? '—' }}</td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('spk.show', $spk) }}" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:0.7rem" title="Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if(auth()->user()->hasRole(['superadmin','staff']))
                                @if($spk->jumlah_toko > 0)
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:0.7rem;opacity:0.4;cursor:not-allowed"
                                        title="Tidak dapat diedit — SPK sudah memiliki data reklame" disabled>
                                    <i class="fa-solid fa-lock"></i>
                                </button>
                                @else
                                <a href="{{ route('spk.edit', $spk) }}" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:0.7rem" title="Edit SPK">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                @endif
                                @endif
                                @if(auth()->user()->isSuperadmin())
                                @if($spk->jumlah_toko > 0)
                                <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size:0.7rem;opacity:0.4;cursor:not-allowed"
                                        title="Tidak dapat dihapus — SPK sudah memiliki data reklame" disabled>
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                @else
                                <form method="POST" action="{{ route('spk.destroy', $spk) }}"
                                      onsubmit="return confirm('Hapus SPK \'{{ addslashes($spk->no_spk) }}\'?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size:0.7rem" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4" style="color:var(--text-dim)">
                            <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>Belum ada data SPK.
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
{{-- ═══════════════════════════════════════════════════════════════ --}}

@endsection
