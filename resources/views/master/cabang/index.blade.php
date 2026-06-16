@extends($isMobile ? 'layouts.mobile' : 'layouts.app')

@section('title', 'Master Cabang')
@section('page-title', 'Master Cabang')

@section('content')

@if($isMobile)
{{-- ═══════════════════════════════════════════ MOBILE CABANG ═══ --}}

<form method="GET" action="{{ route('master.cabang.index') }}" class="mb-3">
    <div class="input-group input-group-sm mb-2">
        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Cari kode / nama cabang..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary btn-sm px-3">Cari</button>
    </div>
    <div class="row g-1">
        <div class="col-9">
            <select name="wilayah_id" class="form-select form-select-sm">
                <option value="">-- Semua Wilayah --</option>
                @foreach($wilayahs as $w)
                    <option value="{{ $w->id }}" {{ request('wilayah_id') == $w->id ? 'selected' : '' }}>{{ $w->nama_wilayah }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-3">
            @if(request()->anyFilled(['search','wilayah_id']))
            <a href="{{ route('master.cabang.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                <i class="fa-solid fa-times"></i>
            </a>
            @endif
        </div>
    </div>
</form>

<div style="font-size:0.72rem;color:var(--text-dim);margin-bottom:8px">
    Total: <strong style="color:var(--text-primary)">{{ $data->total() }}</strong> cabang
</div>

@forelse($data as $item)
<div class="m-card mb-2">
    <div class="d-flex justify-content-between align-items-start mb-1">
        <div>
            <div style="font-weight:600;font-size:0.875rem;color:var(--text-primary)">{{ $item->nama_cabang }}</div>
            <div style="font-family:monospace;font-size:0.75rem;color:#a5b4fc">{{ $item->kode_cabang }}</div>
            <div style="font-size:0.72rem;color:var(--text-dim)">{{ optional($item->wilayah)->nama_wilayah ?? '-' }}</div>
        </div>
        @if($item->is_active)
            <span class="status-badge status-selesai" style="font-size:0.6rem;padding:2px 7px">Aktif</span>
        @else
            <span class="status-badge status-cancel" style="font-size:0.6rem;padding:2px 7px">Nonaktif</span>
        @endif
    </div>
    <div class="d-flex gap-1 mt-2">
        <form method="POST" action="{{ route('master.cabang.toggle', $item->id) }}" class="flex-fill">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-sm w-100 py-1 {{ $item->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" style="font-size:0.72rem">
                <i class="fa-solid {{ $item->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} me-1"></i>
                {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
            </button>
        </form>
        <button type="button" class="btn btn-sm btn-outline-secondary flex-fill py-1" style="font-size:0.72rem"
                data-bs-toggle="modal" data-bs-target="#modalEdit"
                data-id="{{ $item->id }}"
                data-kode="{{ $item->kode_cabang }}"
                data-nama="{{ $item->nama_cabang }}"
                data-wilayah="{{ $item->wilayah_id }}">
            <i class="fa-solid fa-pen me-1"></i>Edit
        </button>
        <form method="POST" action="{{ route('master.cabang.destroy', $item->id) }}"
              onsubmit="return confirm('Hapus cabang \'{{ $item->nama_cabang }}\'?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size:0.72rem">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>
</div>
@empty
<div class="m-card text-center py-4" style="color:var(--text-dim)">
    <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
    <span style="font-size:0.85rem">Belum ada data cabang.</span>
</div>
@endforelse

@if($data->hasPages())
<div class="mt-3 d-flex justify-content-center">{{ $data->links() }}</div>
@endif

<button class="m-fab" data-bs-toggle="modal" data-bs-target="#modalCreate">
    <i class="fa-solid fa-plus"></i>
</button>

@else
{{-- ═══════════════════════════════════════════ DESKTOP CABANG ═══ --}}

<div class="page-header">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h2><i class="fa-solid fa-code-branch me-2" style="color:var(--accent)"></i>Master Cabang</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-dim)">Dashboard</a></li>
                    <li class="breadcrumb-item active">Cabang</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="fa-solid fa-plus me-1"></i> Tambah Cabang
        </button>
    </div>
</div>

<div class="card-dark mb-3">
    <div class="card-body py-2 px-3">
        <form method="GET" action="{{ route('master.cabang.index') }}">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-sm-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari kode / nama cabang..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <select name="wilayah_id" class="form-select form-select-sm">
                        <option value="">-- Semua Wilayah --</option>
                        @foreach($wilayahs as $w)
                            <option value="{{ $w->id }}" {{ request('wilayah_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->nama_wilayah }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                    @if(request()->anyFilled(['search','wilayah_id']))
                        <a href="{{ route('master.cabang.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card-dark">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title"><i class="fa-solid fa-table me-2"></i>Daftar Cabang</h6>
        <span style="color:var(--text-dim);font-size:0.75rem;">Total: {{ $data->total() }} data</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Kode</th>
                        <th>Nama Cabang</th>
                        <th>Wilayah / DC</th>
                        <th class="text-center" width="100">Status</th>
                        <th class="text-center" width="130">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $i => $item)
                    <tr>
                        <td style="color:var(--text-dim)">{{ $data->firstItem() + $i }}</td>
                        <td><span style="font-family:monospace;color:#a5b4fc">{{ $item->kode_cabang }}</span></td>
                        <td style="font-weight:500">{{ $item->nama_cabang }}</td>
                        <td style="color:var(--text-muted);font-size:0.825rem">
                            {{ optional($item->wilayah)->nama_wilayah ?? '-' }}
                        </td>
                        <td class="text-center">
                            @if($item->is_active)
                                <span class="status-badge status-selesai">Aktif</span>
                            @else
                                <span class="status-badge status-cancel">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <form method="POST" action="{{ route('master.cabang.toggle', $item->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm py-1 px-2 {{ $item->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" style="font-size:0.7rem">
                                        <i class="fa-solid {{ $item->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:0.7rem"
                                        data-bs-toggle="modal" data-bs-target="#modalEdit"
                                        data-id="{{ $item->id }}"
                                        data-kode="{{ $item->kode_cabang }}"
                                        data-nama="{{ $item->nama_cabang }}"
                                        data-wilayah="{{ $item->wilayah_id }}"
                                        title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form method="POST" action="{{ route('master.cabang.destroy', $item->id) }}"
                                      onsubmit="return confirm('Hapus cabang \'{{ $item->nama_cabang }}\'?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size:0.7rem" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4" style="color:var(--text-dim)">
                            <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>Belum ada data cabang.
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


{{-- Modal Create --}}
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <form method="POST" action="{{ route('master.cabang.store') }}">
                @csrf
                <div class="modal-header modal-dark-header">
                    <h6 class="modal-title"><i class="fa-solid fa-plus-circle me-2" style="color:var(--accent)"></i>Tambah Cabang</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($errors->any() && session('modal') === 'create')
                    <div class="alert mb-3" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;font-size:0.8rem;border-radius:0.5rem;padding:0.5rem 0.75rem">
                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $errors->first() }}
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Kode Cabang <span class="text-danger">*</span></label>
                        <input type="text" name="kode_cabang" class="form-control form-control-sm" value="{{ old('kode_cabang') }}" placeholder="Contoh: CBG-001" maxlength="20" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Cabang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_cabang" class="form-control form-control-sm" value="{{ old('nama_cabang') }}" placeholder="Nama cabang" maxlength="100" required>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Wilayah / DC</label>
                        <select name="wilayah_id" class="form-select form-select-sm">
                            <option value="">-- Pilih Wilayah --</option>
                            @foreach($wilayahs as $w)
                                <option value="{{ $w->id }}" {{ old('wilayah_id') == $w->id ? 'selected' : '' }}>
                                    {{ $w->nama_wilayah }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer modal-dark-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <form method="POST" id="formEdit" action="">
                @csrf @method('PUT')
                <div class="modal-header modal-dark-header">
                    <h6 class="modal-title"><i class="fa-solid fa-pen me-2" style="color:var(--accent)"></i>Edit Cabang</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Cabang <span class="text-danger">*</span></label>
                        <input type="text" name="kode_cabang" id="edit_kode" class="form-control form-control-sm" maxlength="20" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Cabang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_cabang" id="edit_nama" class="form-control form-control-sm" maxlength="100" required>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Wilayah / DC</label>
                        <select name="wilayah_id" id="edit_wilayah" class="form-select form-select-sm">
                            <option value="">-- Pilih Wilayah --</option>
                            @foreach($wilayahs as $w)
                                <option value="{{ $w->id }}">{{ $w->nama_wilayah }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer modal-dark-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-save me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('modalEdit').addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;
        document.getElementById('formEdit').action  = '{{ url("master/cabang") }}/' + btn.dataset.id;
        document.getElementById('edit_kode').value  = btn.dataset.kode;
        document.getElementById('edit_nama').value  = btn.dataset.nama;
        document.getElementById('edit_wilayah').value = btn.dataset.wilayah;
    });
    @if($errors->any() && session('modal') === 'create')
        new bootstrap.Modal(document.getElementById('modalCreate')).show();
    @endif
</script>
@endpush
