@extends('layouts.app')

@section('title', 'Master Toko')
@section('page-title', 'Master Toko')

@section('content')

<div class="page-header">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h2><i class="fa-solid fa-store me-2" style="color:var(--accent)"></i>Master Toko</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-dim)">Dashboard</a></li>
                    <li class="breadcrumb-item active">Toko</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="fa-solid fa-plus me-1"></i> Tambah Toko
        </button>
    </div>
</div>

<div class="card-dark mb-3">
    <div class="card-body py-2 px-3">
        <form method="GET" action="{{ route('master.toko.index') }}">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-sm-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari kode / nama toko..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-sm-3">
                    <select name="wilayah_id" class="form-select form-select-sm">
                        <option value="">-- Semua Wilayah --</option>
                        @foreach($wilayahs as $w)
                            <option value="{{ $w->id }}" {{ request('wilayah_id') == $w->id ? 'selected' : '' }}>{{ $w->nama_wilayah }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                    @if(request()->anyFilled(['search','wilayah_id']))
                        <a href="{{ route('master.toko.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card-dark">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title"><i class="fa-solid fa-table me-2"></i>Daftar Toko</h6>
        <span style="color:var(--text-dim);font-size:0.75rem;">Total: {{ $data->total() }} data</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Kode Toko</th>
                        <th>Nama Toko</th>
                        <th>Wilayah / DC</th>
                        <th>Cabang</th>
                        <th class="text-center" width="100">Status</th>
                        <th class="text-center" width="130">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $i => $item)
                    <tr>
                        <td style="color:var(--text-dim)">{{ $data->firstItem() + $i }}</td>
                        <td><span style="font-family:monospace;color:#a5b4fc">{{ $item->kode_toko }}</span></td>
                        <td style="font-weight:500">{{ $item->nama_toko }}</td>
                        <td style="color:var(--text-muted);font-size:0.825rem">{{ optional($item->wilayah)->nama_wilayah ?? '-' }}</td>
                        <td style="color:var(--text-muted);font-size:0.825rem">{{ optional($item->cabang)->nama_cabang ?? '-' }}</td>
                        <td class="text-center">
                            @if($item->is_active)
                                <span class="status-badge status-selesai">Aktif</span>
                            @else
                                <span class="status-badge status-cancel">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <form method="POST" action="{{ route('master.toko.toggle', $item->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm py-1 px-2 {{ $item->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" style="font-size:0.7rem">
                                        <i class="fa-solid {{ $item->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:0.7rem"
                                        data-bs-toggle="modal" data-bs-target="#modalEdit"
                                        data-id="{{ $item->id }}"
                                        data-kode="{{ $item->kode_toko }}"
                                        data-nama="{{ $item->nama_toko }}"
                                        data-wilayah="{{ $item->wilayah_id }}"
                                        data-cabang="{{ $item->cabang_id }}"
                                        data-alamat="{{ $item->alamat }}"
                                        title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form method="POST" action="{{ route('master.toko.destroy', $item->id) }}"
                                      onsubmit="return confirm('Hapus toko \'{{ $item->nama_toko }}\'?')">
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
                        <td colspan="7" class="text-center py-4" style="color:var(--text-dim)">
                            <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>Belum ada data toko.
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

{{-- Modal Create --}}
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-dark">
            <form method="POST" action="{{ route('master.toko.store') }}">
                @csrf
                <div class="modal-header modal-dark-header">
                    <h6 class="modal-title"><i class="fa-solid fa-plus-circle me-2" style="color:var(--accent)"></i>Tambah Toko</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($errors->any() && session('modal') === 'create')
                    <div class="alert mb-3" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;font-size:0.8rem;border-radius:0.5rem;padding:0.5rem 0.75rem">
                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $errors->first() }}
                    </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label">Kode Toko <span class="text-danger">*</span></label>
                            <input type="text" name="kode_toko" class="form-control form-control-sm" value="{{ old('kode_toko') }}" placeholder="Contoh: T001" maxlength="50" required>
                        </div>
                        <div class="col-12 col-md-8">
                            <label class="form-label">Nama Toko <span class="text-danger">*</span></label>
                            <input type="text" name="nama_toko" class="form-control form-control-sm" value="{{ old('nama_toko') }}" placeholder="Nama toko lengkap" maxlength="150" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Wilayah / DC</label>
                            <select name="wilayah_id" id="create_wilayah" class="form-select form-select-sm">
                                <option value="">-- Pilih Wilayah --</option>
                                @foreach($wilayahs as $w)
                                    <option value="{{ $w->id }}" {{ old('wilayah_id') == $w->id ? 'selected' : '' }}>{{ $w->nama_wilayah }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Cabang</label>
                            <select name="cabang_id" id="create_cabang" class="form-select form-select-sm">
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($cabangs as $c)
                                    <option value="{{ $c->id }}" data-wilayah="{{ $c->wilayah_id }}" {{ old('cabang_id') == $c->id ? 'selected' : '' }}>{{ $c->nama_cabang }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <input type="text" name="alamat" class="form-control form-control-sm" value="{{ old('alamat') }}" placeholder="Alamat toko (opsional)" maxlength="255">
                        </div>
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-dark">
            <form method="POST" id="formEdit" action="">
                @csrf @method('PUT')
                <div class="modal-header modal-dark-header">
                    <h6 class="modal-title"><i class="fa-solid fa-pen me-2" style="color:var(--accent)"></i>Edit Toko</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label">Kode Toko <span class="text-danger">*</span></label>
                            <input type="text" name="kode_toko" id="edit_kode" class="form-control form-control-sm" maxlength="50" required>
                        </div>
                        <div class="col-12 col-md-8">
                            <label class="form-label">Nama Toko <span class="text-danger">*</span></label>
                            <input type="text" name="nama_toko" id="edit_nama" class="form-control form-control-sm" maxlength="150" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Wilayah / DC</label>
                            <select name="wilayah_id" id="edit_wilayah" class="form-select form-select-sm">
                                <option value="">-- Pilih Wilayah --</option>
                                @foreach($wilayahs as $w)
                                    <option value="{{ $w->id }}">{{ $w->nama_wilayah }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Cabang</label>
                            <select name="cabang_id" id="edit_cabang" class="form-select form-select-sm">
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($cabangs as $c)
                                    <option value="{{ $c->id }}" data-wilayah="{{ $c->wilayah_id }}">{{ $c->nama_cabang }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <input type="text" name="alamat" id="edit_alamat" class="form-control form-control-sm" maxlength="255">
                        </div>
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

@php
    $cabangsJson = json_encode(
        $cabangs->map(function ($c) {
            return ['id' => $c->id, 'nama' => $c->nama_cabang, 'wilayah_id' => $c->wilayah_id];
        })->values()->toArray()
    );
@endphp

@push('scripts')
<script>
    const allCabangs = {!! $cabangsJson !!};

    function filterCabangs(selectEl, wilayahId, selectedId = null) {
        selectEl.innerHTML = '<option value="">-- Pilih Cabang --</option>';
        const filtered = wilayahId ? allCabangs.filter(c => c.wilayah_id == wilayahId) : allCabangs;
        filtered.forEach(c => {
            const opt = new Option(c.nama, c.id);
            if (selectedId && c.id == selectedId) opt.selected = true;
            selectEl.appendChild(opt);
        });
    }

    // Create modal: filter cabang saat wilayah berubah
    document.getElementById('create_wilayah').addEventListener('change', function () {
        filterCabangs(document.getElementById('create_cabang'), this.value);
    });

    // Edit modal: isi data + filter cabang
    document.getElementById('modalEdit').addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;
        document.getElementById('formEdit').action    = '{{ url("master/toko") }}/' + btn.dataset.id;
        document.getElementById('edit_kode').value    = btn.dataset.kode;
        document.getElementById('edit_nama').value    = btn.dataset.nama;
        document.getElementById('edit_alamat').value  = btn.dataset.alamat !== 'null' ? btn.dataset.alamat : '';
        document.getElementById('edit_wilayah').value = btn.dataset.wilayah;
        filterCabangs(document.getElementById('edit_cabang'), btn.dataset.wilayah, btn.dataset.cabang);
    });

    document.getElementById('edit_wilayah').addEventListener('change', function () {
        filterCabangs(document.getElementById('edit_cabang'), this.value);
    });

    @if($errors->any() && session('modal') === 'create')
        new bootstrap.Modal(document.getElementById('modalCreate')).show();
    @endif
</script>
@endpush
