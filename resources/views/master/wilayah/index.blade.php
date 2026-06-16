@extends('layouts.app')

@section('title', 'Master Wilayah')
@section('page-title', 'Master Wilayah / DC')

@section('content')

<div class="page-header">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h2><i class="fa-solid fa-map-location-dot me-2" style="color:var(--accent)"></i>Master Wilayah / DC</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-dim)">Dashboard</a></li>
                    <li class="breadcrumb-item active">Wilayah</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="fa-solid fa-plus me-1"></i> Tambah Wilayah
        </button>
    </div>
</div>

{{-- Search --}}
<div class="card-dark mb-3">
    <div class="card-body py-2 px-3">
        <form method="GET" action="{{ route('master.wilayah.index') }}">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari kode / nama wilayah..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('master.wilayah.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card-dark">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title"><i class="fa-solid fa-table me-2"></i>Daftar Wilayah</h6>
        <span style="color:var(--text-dim);font-size:0.75rem;">Total: {{ $data->total() }} data</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Kode</th>
                        <th>Nama Wilayah / DC</th>
                        <th>Keterangan</th>
                        <th class="text-center" width="100">Status</th>
                        <th class="text-center" width="130">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $i => $item)
                    <tr>
                        <td style="color:var(--text-dim)">{{ $data->firstItem() + $i }}</td>
                        <td><span style="font-family:monospace;color:#a5b4fc">{{ $item->kode_wilayah }}</span></td>
                        <td style="font-weight:500">{{ $item->nama_wilayah }}</td>
                        <td style="color:var(--text-muted);font-size:0.825rem">{{ $item->keterangan ?? '-' }}</td>
                        <td class="text-center">
                            @if($item->is_active)
                                <span class="status-badge status-selesai">Aktif</span>
                            @else
                                <span class="status-badge status-cancel">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                {{-- Toggle Active --}}
                                <form method="POST" action="{{ route('master.wilayah.toggle', $item->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm py-1 px-2 {{ $item->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                            title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}" style="font-size:0.7rem">
                                        <i class="fa-solid {{ $item->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                    </button>
                                </form>
                                {{-- Edit --}}
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:0.7rem"
                                        data-bs-toggle="modal" data-bs-target="#modalEdit"
                                        data-id="{{ $item->id }}"
                                        data-kode="{{ $item->kode_wilayah }}"
                                        data-nama="{{ $item->nama_wilayah }}"
                                        data-ket="{{ $item->keterangan }}"
                                        title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                {{-- Delete --}}
                                <form method="POST" action="{{ route('master.wilayah.destroy', $item->id) }}"
                                      onsubmit="return confirm('Hapus wilayah \'{{ $item->nama_wilayah }}\'?')">
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
                            <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                            Belum ada data wilayah.
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

{{-- ── Modal Create ── --}}
<div class="modal fade" id="modalCreate" tabindex="-1" aria-labelledby="modalCreateLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <form method="POST" action="{{ route('master.wilayah.store') }}">
                @csrf
                <div class="modal-header modal-dark-header">
                    <h6 class="modal-title" id="modalCreateLabel">
                        <i class="fa-solid fa-plus-circle me-2" style="color:var(--accent)"></i>Tambah Wilayah
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($errors->any() && session('modal') === 'create')
                    <div class="alert alert-sm mb-3" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;font-size:0.8rem;border-radius:0.5rem;padding:0.5rem 0.75rem">
                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $errors->first() }}
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Kode Wilayah <span class="text-danger">*</span></label>
                        <input type="text" name="kode_wilayah" class="form-control form-control-sm @error('kode_wilayah') is-invalid @enderror"
                               value="{{ old('kode_wilayah') }}" placeholder="Contoh: DC-CBN" maxlength="20" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Wilayah / DC <span class="text-danger">*</span></label>
                        <input type="text" name="nama_wilayah" class="form-control form-control-sm @error('nama_wilayah') is-invalid @enderror"
                               value="{{ old('nama_wilayah') }}" placeholder="Contoh: DC Cibinong" maxlength="100" required>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control form-control-sm"
                               value="{{ old('keterangan') }}" placeholder="Opsional" maxlength="255">
                    </div>
                </div>
                <div class="modal-footer modal-dark-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-save me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Modal Edit ── --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <form method="POST" id="formEdit" action="">
                @csrf @method('PUT')
                <div class="modal-header modal-dark-header">
                    <h6 class="modal-title">
                        <i class="fa-solid fa-pen me-2" style="color:var(--accent)"></i>Edit Wilayah
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($errors->any() && session('modal') === 'edit')
                    <div class="alert alert-sm mb-3" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;font-size:0.8rem;border-radius:0.5rem;padding:0.5rem 0.75rem">
                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $errors->first() }}
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Kode Wilayah <span class="text-danger">*</span></label>
                        <input type="text" name="kode_wilayah" id="edit_kode_wilayah" class="form-control form-control-sm" maxlength="20" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Wilayah / DC <span class="text-danger">*</span></label>
                        <input type="text" name="nama_wilayah" id="edit_nama_wilayah" class="form-control form-control-sm" maxlength="100" required>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" id="edit_keterangan" class="form-control form-control-sm" maxlength="255">
                    </div>
                </div>
                <div class="modal-footer modal-dark-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-save me-1"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Populate edit modal
    document.getElementById('modalEdit').addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;
        document.getElementById('formEdit').action = '{{ url("master/wilayah") }}/' + btn.dataset.id;
        document.getElementById('edit_kode_wilayah').value = btn.dataset.kode;
        document.getElementById('edit_nama_wilayah').value = btn.dataset.nama;
        document.getElementById('edit_keterangan').value   = btn.dataset.ket !== 'null' ? btn.dataset.ket : '';
    });

    // Auto-open modal jika ada error validasi
    @if($errors->any() && session('modal') === 'create')
        new bootstrap.Modal(document.getElementById('modalCreate')).show();
    @elseif($errors->any() && session('modal') === 'edit')
        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    @endif
</script>
@endpush
