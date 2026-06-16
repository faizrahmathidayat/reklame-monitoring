@extends($isMobile ? 'layouts.mobile' : 'layouts.app')

@section('title', 'Detail SPK — ' . $spk->no_spk)
@section('page-title', 'Detail SPK')

@section('content')

@if($isMobile)
{{-- ═══════════════════════════════════════════ MOBILE SPK SHOW ═══ --}}

{{-- SPK Header Info --}}
<div class="m-card mb-3">
    <div style="font-family:monospace;font-size:1rem;font-weight:700;color:var(--accent);margin-bottom:8px">
        {{ $spk->no_spk }}
    </div>
    <div class="row g-2" style="font-size:0.8rem">
        <div class="col-6">
            <div style="color:var(--text-dim);font-size:0.65rem;text-transform:uppercase;letter-spacing:0.05em">DC / Wilayah</div>
            <div style="font-weight:500">{{ optional($spk->wilayah)->nama_wilayah ?? '—' }}</div>
        </div>
        <div class="col-6">
            <div style="color:var(--text-dim);font-size:0.65rem;text-transform:uppercase;letter-spacing:0.05em">Brand</div>
            <div style="font-weight:500">{{ optional($spk->brand)->nama_brand ?? '—' }}</div>
        </div>
        <div class="col-6">
            <div style="color:var(--text-dim);font-size:0.65rem;text-transform:uppercase;letter-spacing:0.05em">Tgl. SPK</div>
            <div>{{ optional($spk->tgl_spk)->format('d/m/Y') }}</div>
        </div>
        <div class="col-6">
            <div style="color:var(--text-dim);font-size:0.65rem;text-transform:uppercase;letter-spacing:0.05em">Deadline</div>
            <div>{{ optional($spk->deadline)->format('d/m/Y') ?? '—' }}</div>
        </div>
        @if($spk->pic)
        <div class="col-6">
            <div style="color:var(--text-dim);font-size:0.65rem;text-transform:uppercase;letter-spacing:0.05em">PIC</div>
            <div>{{ $spk->pic->nama_pic }}</div>
        </div>
        @endif
        @if($spk->keterangan)
        <div class="col-12">
            <div style="color:var(--text-dim);font-size:0.65rem;text-transform:uppercase;letter-spacing:0.05em">Keterangan</div>
            <div style="color:var(--text-muted)">{{ $spk->keterangan }}</div>
        </div>
        @endif
    </div>
</div>

{{-- Stats --}}
<div class="row g-2 mb-3">
    <div class="col-6">
        <div class="stat-card text-center">
            <div class="stat-value">{{ $spk->reklames->count() }}</div>
            <div class="stat-label">Total Toko</div>
        </div>
    </div>
    <div class="col-6">
        <div class="stat-card text-center">
            <div class="stat-value">{{ $jumlahObjek }}</div>
            <div class="stat-label">Total Objek</div>
        </div>
    </div>
</div>

{{-- Status Summary --}}
@if($statusSummary->count() > 0)
<div class="d-flex flex-wrap gap-1 mb-3">
    @foreach($statusSummary as $status => $count)
    <span class="status-badge status-{{ Illuminate\Support\Str::slug($status) }}" style="font-size:0.65rem">
        {{ $status }} ({{ $count }})
    </span>
    @endforeach
</div>
@endif

{{-- Action Button --}}
@if(auth()->user()->hasRole(['superadmin','staff']))
<a href="{{ route('reklame.create', ['spk_id' => $spk->id]) }}" class="btn btn-primary btn-sm w-100 mb-3">
    <i class="fa-solid fa-plus me-1"></i> Tambah Toko ke SPK ini
</a>
@endif

{{-- Toko List --}}
<div class="m-section-title">Daftar Toko ({{ $spk->reklames->count() }})</div>

@forelse($spk->reklames as $i => $r)
<div class="m-card mb-2">
    <div class="d-flex justify-content-between align-items-start mb-1">
        <div>
            <div style="font-weight:600;font-size:0.85rem">{{ optional($r->toko)->nama_toko ?? '—' }}</div>
            <div style="font-size:0.7rem;color:var(--text-dim);font-family:monospace">{{ $r->kode_toko }}</div>
        </div>
        <span class="status-badge {{ $r->statusBadgeClass() }}" style="font-size:0.6rem">{{ $r->status }}</span>
    </div>
    <div class="d-flex justify-content-between align-items-center" style="font-size:0.72rem;color:var(--text-dim)">
        <span>{{ $r->ukuran_reklame ?? '—' }} | {{ $r->jumlah_objek ?? 1 }} objek</span>
        <span>{{ optional($r->tanggal_awal)->format('d/m/Y') ?? '—' }}</span>
    </div>
    <div class="d-flex gap-1 mt-2">
        <a href="{{ route('reklame.show', $r) }}" class="btn btn-sm btn-outline-secondary py-1 px-2 flex-fill text-center" style="font-size:0.72rem">
            <i class="fa-solid fa-eye me-1"></i>Detail
        </a>
        @if(auth()->user()->hasRole(['superadmin','staff','finance']))
        <a href="{{ route('reklame.edit', $r) }}" class="btn btn-sm btn-outline-secondary py-1 px-2 flex-fill text-center" style="font-size:0.72rem">
            <i class="fa-solid fa-pen me-1"></i>Edit
        </a>
        @endif
        @if(auth()->user()->isSuperadmin())
        <form method="POST" action="{{ route('spk.toko.destroy', [$spk, $r]) }}"
              onsubmit="return confirm('Hapus toko ini dari SPK?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size:0.72rem">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
        @endif
    </div>
</div>
@empty
<div class="m-card text-center py-4" style="color:var(--text-dim)">
    <i class="fa-solid fa-store-slash fa-2x mb-2 d-block"></i>
    <span style="font-size:0.85rem">Belum ada toko.</span>
</div>
@endforelse

{{-- Edit SPK button --}}
@if(auth()->user()->hasRole(['superadmin','staff']))
<div class="mt-3">
    @if($spk->reklames->isNotEmpty())
    <button type="button" class="btn btn-outline-secondary btn-sm w-100" style="opacity:0.45;cursor:not-allowed" disabled>
        <i class="fa-solid fa-lock me-1"></i>SPK Terkunci (memiliki data reklame)
    </button>
    @else
    <a href="{{ route('spk.edit', $spk) }}" class="btn btn-outline-secondary btn-sm w-100">
        <i class="fa-solid fa-pen me-1"></i>Edit SPK
    </a>
    @endif
</div>
@endif

@else
{{-- ═══════════════════════════════════════════ DESKTOP SPK SHOW ═══ --}}

<div class="page-header">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h2><i class="fa-solid fa-file-contract me-2" style="color:var(--accent)"></i>
                <span style="font-family:monospace">{{ $spk->no_spk }}</span>
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-dim)">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('spk.index') }}" style="color:var(--text-dim)">SPK</a></li>
                    <li class="breadcrumb-item active">{{ $spk->no_spk }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if(auth()->user()->hasRole(['superadmin','staff']))
            <a href="{{ route('reklame.create', ['spk_id' => $spk->id]) }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus me-1"></i> Tambah Toko
            </a>
            @if($spk->reklames->isNotEmpty())
            <button type="button" class="btn btn-outline-secondary btn-sm" style="opacity:0.45;cursor:not-allowed"
                    title="Tidak dapat diedit — SPK sudah memiliki data reklame" disabled>
                <i class="fa-solid fa-lock me-1"></i> Edit SPK
            </button>
            @else
            <a href="{{ route('spk.edit', $spk) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-pen me-1"></i> Edit SPK
            </a>
            @endif
            @endif
        </div>
    </div>
</div>

{{-- SPK Info Card --}}
<div class="row g-3 mb-3">
    <div class="col-12 col-lg-8">
        <div class="card-dark h-100">
            <div class="card-header">
                <h6 class="card-title"><i class="fa-solid fa-circle-info me-2" style="color:var(--accent)"></i>Informasi SPK</h6>
            </div>
            <div class="card-body">
                <div class="row g-3" style="font-size:0.875rem">
                    <div class="col-6 col-md-3">
                        <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:3px">Tgl. SPK</div>
                        <div style="font-weight:600">{{ optional($spk->tgl_spk)->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:3px">Deadline</div>
                        <div style="font-weight:600">{{ optional($spk->deadline)->format('d/m/Y') ?? '—' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:3px">DC / Wilayah</div>
                        <div>{{ optional($spk->wilayah)->nama_wilayah }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:3px">Brand</div>
                        <div>{{ optional($spk->brand)->nama_brand }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:3px">Cabang</div>
                        <div>{{ optional($spk->cabang)->nama_cabang ?? '—' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:3px">PIC</div>
                        <div>{{ optional($spk->pic)->nama_pic ?? '—' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:3px">Mulai Input</div>
                        <div>{{ optional($spk->mulai_tanggal_input)->format('d/m/Y') ?? '—' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:3px">Dibuat Oleh</div>
                        <div>{{ optional($spk->createdBy)->name ?? '—' }}</div>
                    </div>
                    @if($spk->keterangan)
                    <div class="col-12">
                        <div style="color:var(--text-dim);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:3px">Keterangan</div>
                        <div style="color:var(--text-muted)">{{ $spk->keterangan }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="row g-3 h-100">
            <div class="col-6 col-lg-12">
                <div class="stat-card h-100">
                    <div class="stat-icon" style="background:rgba(99,102,241,0.15);color:#a5b4fc">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <div class="stat-value">{{ $spk->reklames->count() }}</div>
                    <div class="stat-label">Total Toko</div>
                </div>
            </div>
            <div class="col-6 col-lg-12">
                <div class="stat-card h-100">
                    <div class="stat-icon" style="background:rgba(34,197,94,0.15);color:#86efac">
                        <i class="fa-solid fa-rectangle-ad"></i>
                    </div>
                    <div class="stat-value">{{ $jumlahObjek }}</div>
                    <div class="stat-label">Total Objek Reklame</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Status Summary --}}
@if($statusSummary->count() > 0)
<div class="card-dark mb-3">
    <div class="card-body py-2 px-3">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span style="font-size:0.78rem;color:var(--text-dim)">Status:</span>
            @foreach($statusSummary as $status => $count)
            <span class="status-badge status-{{ Illuminate\Support\Str::slug($status) }}" style="font-size:0.7rem">
                {{ $status }} ({{ $count }})
            </span>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Toko List --}}
<div class="card-dark">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title"><i class="fa-solid fa-store me-2"></i>Daftar Toko dalam SPK ini</h6>
        <span style="color:var(--text-dim);font-size:0.75rem">{{ $spk->reklames->count() }} toko</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Toko</th>
                        <th>Kode</th>
                        <th class="text-center" width="160">Status</th>
                        <th class="text-center" width="90">Ukuran</th>
                        <th class="text-center" width="70">Obj</th>
                        <th class="text-center" width="100">Tgl. Awal</th>
                        <th class="text-center" width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($spk->reklames as $i => $r)
                    <tr>
                        <td style="color:var(--text-dim)">{{ $i + 1 }}</td>
                        <td style="font-weight:500;font-size:0.875rem">{{ optional($r->toko)->nama_toko ?? '—' }}</td>
                        <td style="font-family:monospace;font-size:0.78rem;color:var(--text-muted)">{{ $r->kode_toko }}</td>
                        <td class="text-center">
                            <span class="status-badge {{ $r->statusBadgeClass() }}">{{ $r->status }}</span>
                        </td>
                        <td class="text-center" style="font-size:0.8rem;color:var(--text-muted)">{{ $r->ukuran_reklame ?? '—' }}</td>
                        <td class="text-center" style="font-size:0.8rem">{{ $r->jumlah_objek ?? 1 }}</td>
                        <td class="text-center" style="font-size:0.8rem;color:var(--text-muted)">
                            {{ optional($r->tanggal_awal)->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('reklame.show', $r) }}" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:0.7rem" title="Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if(auth()->user()->hasRole(['superadmin','staff','finance']))
                                <a href="{{ route('reklame.edit', $r) }}" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:0.7rem" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                @endif
                                @if(auth()->user()->isSuperadmin())
                                <form method="POST" action="{{ route('spk.toko.destroy', [$spk, $r]) }}"
                                      onsubmit="return confirm('Hapus toko \'{{ addslashes(optional($r->toko)->nama_toko ?? $r->kode_toko) }}\' dari SPK ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size:0.7rem" title="Hapus dari SPK">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4" style="color:var(--text-dim)">
                            <i class="fa-solid fa-store-slash fa-2x mb-2 d-block"></i>
                            Belum ada toko. Klik <strong>Tambah Toko</strong> untuk menambahkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


@endif
{{-- ═══════════════════════════════════════════════════════════════ --}}

@endsection
