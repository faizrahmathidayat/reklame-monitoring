@extends($isMobile ? 'layouts.mobile' : 'layouts.app')

@section('title', 'Master PIC')
@section('page-title', 'Master PIC')

@section('content')

@if($isMobile)
{{-- ═══════════════════════════════════════════ MOBILE PIC ═══ --}}

<form method="GET" action="{{ route('master.pic.index') }}" class="mb-3">
    <div class="input-group input-group-sm">
        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Cari nama / jabatan..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary btn-sm px-3">Cari</button>
        @if(request('search'))
        <a href="{{ route('master.pic.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-times"></i>
        </a>
        @endif
    </div>
</form>

<div style="font-size:0.72rem;color:var(--text-dim);margin-bottom:8px">
    Total: <strong style="color:var(--text-primary)">{{ $data->total() }}</strong> PIC
</div>

@forelse($data as $item)
<div class="m-card mb-2">
    <div class="d-flex justify-content-between align-items-start mb-1">
        <div>
            <div style="font-weight:600;font-size:0.875rem;color:var(--text-primary)">{{ $item->nama_pic }}</div>
            @if($item->jabatan)
            <div style="font-size:0.75rem;color:var(--text-muted)">{{ $item->jabatan }}</div>
            @endif
            @if($item->telepon)
            <div style="font-size:0.72rem;color:var(--text-dim);font-family:monospace">
                <i class="fa-solid fa-phone me-1" style="opacity:0.5"></i>{{ $item->telepon }}
            </div>
            @endif
        </div>
        @if($item->is_active)
            <span class="status-badge status-selesai" style="font-size:0.6rem;padding:2px 7px">Aktif</span>
        @else
            <span class="status-badge status-cancel" style="font-size:0.6rem;padding:2px 7px">Nonaktif</span>
        @endif
    </div>
    <div class="d-flex gap-1 mt-2">
        <form method="POST" action="{{ route('master.pic.toggle', $item->id) }}" class="flex-fill">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-sm w-100 py-1 {{ $item->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" style="font-size:0.72rem">
                <i class="fa-solid {{ $item->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} me-1"></i>
                {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
            </button>
        </form>
        <button type="button" class="btn btn-sm btn-outline-secondary flex-fill py-1" style="font-size:0.72rem"
                data-bs-toggle="modal" data-bs-target="#modalEdit"
                data-id="{{ $item->id }}"
                data-nama="{{ $item->nama_pic }}"
                data-jabatan="{{ $item->jabatan }}"
                data-telepon="{{ $item->telepon }}">
            <i class="fa-solid fa-pen me-1"></i>Edit
        </button>
        <form method="POST" action="{{ route('master.pic.destroy', $item->id) }}"
              onsubmit="return confirm('Hapus PIC \'{{ $item->nama_pic }}\'?')">
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
    <span style="font-size:0.85rem">Belum ada data PIC.</span>
</div>
@endforelse

@if($data->hasPages())
<div class="mt-3 d-flex justify-content-center">{{ $data->links() }}</div>
@endif

<button class="m-fab" data-bs-toggle="modal" data-bs-target="#modalCreate">
    <i class="fa-solid fa-plus"></i>
</button>

@else
{{-- ═══════════════════════════════════════════ DESKTOP PIC ═══ --}}

<div class="page-header">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h2><i class="fa-solid fa-user-tie me-2" style="color:var(--accent)"></i>Master PIC</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-dim)">Dashboard</a></li>
                    <li class="breadcrumb-item active">PIC</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="fa-solid fa-plus me-1"></i> Tambah PIC
        </button>
    </div>
</div>

<div class="card-dark mb-3">
    <div class="card-body py-2 px-3">
        <form method="GET" action="{{ route('master.pic.index') }}">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari nama / jabatan..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('master.pic.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card-dark">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title"><i class="fa-solid fa-table me-2"></i>Daftar PIC</h6>
        <span style="color:var(--text-dim);font-size:0.75rem;">Total: {{ $data->total() }} data</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Nama PIC</th>
                        <th>Jabatan</th>
                        <th>Telepon</th>
                        <th class="text-center" width="100">Status</th>
                        <th class="text-center" width="130">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $i => $item)
                    <tr>
                        <td style="color:var(--text-dim)">{{ $data->firstItem() + $i }}</td>
                        <td style="font-weight:500">{{ $item->nama_pic }}</td>
                        <td style="color:var(--text-muted);font-size:0.825rem">{{ $item->jabatan ?? '-' }}</td>
                        <td style="color:var(--text-muted);font-size:0.825rem;font-family:monospace">{{ $item->telepon ?? '-' }}</td>
                        <td class="text-center">
                            @if($item->is_active)
                                <span class="status-badge status-selesai">Aktif</span>
                            @else
                                <span class="status-badge status-cancel">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <form method="POST" action="{{ route('master.pic.toggle', $item->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm py-1 px-2 {{ $item->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" style="font-size:0.7rem">
                                        <i class="fa-solid {{ $item->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:0.7rem"
                                        data-bs-toggle="modal" data-bs-target="#modalEdit"
                                        data-id="{{ $item->id }}"
                                        data-nama="{{ $item->nama_pic }}"
                                        data-jabatan="{{ $item->jabatan }}"
                                        data-telepon="{{ $item->telepon }}"
                                        title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form method="POST" action="{{ route('master.pic.destroy', $item->id) }}"
                                      onsubmit="return confirm('Hapus PIC \'{{ $item->nama_pic }}\'?')">
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
                            <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>Belum ada data PIC.
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
            <form method="POST" action="{{ route('master.pic.store') }}">
                @csrf
                <div class="modal-header modal-dark-header">
                    <h6 class="modal-title"><i class="fa-solid fa-plus-circle me-2" style="color:var(--accent)"></i>Tambah PIC</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($errors->any() && session('modal') === 'create')
                    <div class="alert mb-3" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;font-size:0.8rem;border-radius:0.5rem;padding:0.5rem 0.75rem">
                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $errors->first() }}
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Nama PIC <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pic" class="form-control form-control-sm" value="{{ old('nama_pic') }}" placeholder="Nama lengkap" maxlength="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="jabatan" class="form-control form-control-sm" value="{{ old('jabatan') }}" placeholder="Opsional" maxlength="100">
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" class="form-control form-control-sm" value="{{ old('telepon') }}" placeholder="Opsional" maxlength="20">
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
                    <h6 class="modal-title"><i class="fa-solid fa-pen me-2" style="color:var(--accent)"></i>Edit PIC</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama PIC <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pic" id="edit_nama" class="form-control form-control-sm" maxlength="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="jabatan" id="edit_jabatan" class="form-control form-control-sm" maxlength="100">
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" id="edit_telepon" class="form-control form-control-sm" maxlength="20">
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
        document.getElementById('formEdit').action  = '{{ url("master/pic") }}/' + btn.dataset.id;
        document.getElementById('edit_nama').value    = btn.dataset.nama;
        document.getElementById('edit_jabatan').value = btn.dataset.jabatan !== 'null' ? btn.dataset.jabatan : '';
        document.getElementById('edit_telepon').value = btn.dataset.telepon !== 'null' ? btn.dataset.telepon : '';
    });
    @if($errors->any() && session('modal') === 'create')
        new bootstrap.Modal(document.getElementById('modalCreate')).show();
    @endif
</script>
@endpush
