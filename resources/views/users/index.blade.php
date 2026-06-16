@extends($isMobile ? 'layouts.mobile' : 'layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert mb-3" style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);color:#86efac;font-size:0.85rem;border-radius:0.5rem;padding:0.6rem 0.85rem">
    <i class="fa-solid fa-circle-check me-1"></i>{{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert mb-3" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;font-size:0.85rem;border-radius:0.5rem;padding:0.6rem 0.85rem">
    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ session('error') }}
</div>
@endif

@if($isMobile)
{{-- ═══════════════════════════════════════════ MOBILE USERS ═══ --}}

{{-- Search + Role filter --}}
<form method="GET" action="{{ route('users.index') }}" class="mb-3">
    <div class="input-group input-group-sm mb-2">
        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Cari nama / username / email..." value="{{ $search }}">
        <button type="submit" class="btn btn-primary btn-sm px-3">Cari</button>
    </div>
    <div class="row g-1">
        <div class="col-8">
            <select name="role" class="form-select form-select-sm">
                <option value="">-- Semua Role --</option>
                <option value="superadmin" {{ $role === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                <option value="staff"      {{ $role === 'staff'      ? 'selected' : '' }}>Staff</option>
                <option value="finance"    {{ $role === 'finance'    ? 'selected' : '' }}>Finance</option>
            </select>
        </div>
        <div class="col-4">
            @if($search || $role)
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
            @endif
        </div>
    </div>
</form>

<div style="font-size:0.72rem;color:var(--text-dim);margin-bottom:8px">
    Total: <strong style="color:var(--text-primary)">{{ $data->total() }}</strong> user
</div>

{{-- User Cards --}}
@forelse($data as $item)
<div class="m-card mb-2">
    <div class="d-flex justify-content-between align-items-start mb-1">
        <div>
            <div style="font-weight:600;font-size:0.875rem;color:var(--text-primary)">
                {{ $item->name }}
                @if($item->id === auth()->id())
                <span style="font-size:0.65rem;color:var(--accent);margin-left:4px"><i class="fa-solid fa-user me-1"></i>Anda</span>
                @endif
            </div>
            <div style="font-family:monospace;font-size:0.75rem;color:var(--text-dim)">{{ $item->username ?? '—' }}</div>
            <div style="font-size:0.72rem;color:var(--text-muted)">{{ $item->email }}</div>
        </div>
        <div class="d-flex flex-column align-items-end gap-1">
            <span class="role-badge {{ $item->role }}">{{ $item->role }}</span>
            @if($item->is_active)
                <span class="status-badge status-selesai" style="font-size:0.6rem;padding:1px 6px">Aktif</span>
            @else
                <span class="status-badge status-cancel" style="font-size:0.6rem;padding:1px 6px">Nonaktif</span>
            @endif
        </div>
    </div>
    <div class="d-flex gap-1 mt-2">
        {{-- Toggle --}}
        <form method="POST" action="{{ route('users.toggle', $item->id) }}" class="flex-fill">
            @csrf @method('PATCH')
            <button type="submit"
                    class="btn btn-sm w-100 py-1 {{ $item->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                    style="font-size:0.72rem"
                    {{ $item->id === auth()->id() ? 'disabled' : '' }}>
                <i class="fa-solid {{ $item->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} me-1"></i>
                {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
            </button>
        </form>
        {{-- Edit --}}
        <button type="button"
                class="btn btn-sm btn-outline-secondary flex-fill py-1"
                style="font-size:0.72rem"
                data-bs-toggle="modal" data-bs-target="#modalEdit"
                data-id="{{ $item->id }}"
                data-name="{{ $item->name }}"
                data-username="{{ $item->username }}"
                data-email="{{ $item->email }}"
                data-role="{{ $item->role }}">
            <i class="fa-solid fa-pen me-1"></i>Edit
        </button>
        {{-- Hapus --}}
        @if($item->id !== auth()->id())
        <form method="POST" action="{{ route('users.destroy', $item->id) }}"
              onsubmit="return confirm('Hapus user \'{{ addslashes($item->name) }}\'?')">
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
    <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
    <span style="font-size:0.85rem">Belum ada user.</span>
</div>
@endforelse

@if($data->hasPages())
<div class="mt-3 d-flex justify-content-center">{{ $data->links() }}</div>
@endif

{{-- FAB --}}
<button class="m-fab" data-bs-toggle="modal" data-bs-target="#modalCreate">
    <i class="fa-solid fa-plus"></i>
</button>

@else
{{-- ═══════════════════════════════════════════ DESKTOP USERS ═══ --}}

<div class="page-header">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h2><i class="fa-solid fa-users-gear me-2" style="color:var(--accent)"></i>Manajemen User</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-dim)">Dashboard</a></li>
                    <li class="breadcrumb-item active">Manajemen User</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="fa-solid fa-plus me-1"></i> Tambah User
        </button>
    </div>
</div>

{{-- Filter --}}
<div class="card-dark mb-3">
    <div class="card-body py-2 px-3">
        <form method="GET" action="{{ route('users.index') }}">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-sm-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari nama / email..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-12 col-sm-3">
                    <select name="role" class="form-select form-select-sm">
                        <option value="">-- Semua Role --</option>
                        <option value="superadmin" {{ $role === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                        <option value="staff"      {{ $role === 'staff'      ? 'selected' : '' }}>Staff</option>
                        <option value="finance"    {{ $role === 'finance'    ? 'selected' : '' }}>Finance</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                    @if($search || $role)
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card-dark">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title"><i class="fa-solid fa-table me-2"></i>Daftar User</h6>
        <span style="color:var(--text-dim);font-size:0.75rem">Total: {{ $data->total() }} user</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Nama</th>
                        <th width="130">Username</th>
                        <th>Email</th>
                        <th class="text-center" width="120">Role</th>
                        <th class="text-center" width="90">Status</th>
                        <th class="text-center" width="130">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $i => $item)
                    <tr>
                        <td style="color:var(--text-dim)">{{ $data->firstItem() + $i }}</td>
                        <td>
                            <div style="font-weight:500">{{ $item->name }}</div>
                            @if($item->id === auth()->id())
                                <small style="color:var(--accent);font-size:0.7rem"><i class="fa-solid fa-user me-1"></i>Anda</small>
                            @endif
                        </td>
                        <td style="font-family:monospace;font-size:0.8rem;color:var(--text-muted)">
                            {{ $item->username ?? '—' }}
                        </td>
                        <td style="color:var(--text-muted);font-size:0.825rem">{{ $item->email }}</td>
                        <td class="text-center">
                            <span class="role-badge {{ $item->role }}">{{ $item->role }}</span>
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
                                <form method="POST" action="{{ route('users.toggle', $item->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="btn btn-sm py-1 px-2 {{ $item->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                            style="font-size:0.7rem"
                                            title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                            {{ $item->id === auth()->id() ? 'disabled' : '' }}>
                                        <i class="fa-solid {{ $item->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                    </button>
                                </form>
                                <button type="button"
                                        class="btn btn-sm btn-outline-secondary py-1 px-2"
                                        style="font-size:0.7rem"
                                        data-bs-toggle="modal" data-bs-target="#modalEdit"
                                        data-id="{{ $item->id }}"
                                        data-name="{{ $item->name }}"
                                        data-username="{{ $item->username }}"
                                        data-email="{{ $item->email }}"
                                        data-role="{{ $item->role }}"
                                        title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                @if($item->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $item->id) }}"
                                      onsubmit="return confirm('Hapus user \'{{ addslashes($item->name) }}\'?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size:0.7rem" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4" style="color:var(--text-dim)">
                            <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>Belum ada user.
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


{{-- ── Modal Create ── --}}
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content modal-dark">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-header modal-dark-header">
                    <h6 class="modal-title"><i class="fa-solid fa-plus-circle me-2" style="color:var(--accent)"></i>Tambah User</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($errors->any() && session('modal') === 'create')
                    <div class="alert mb-3" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;font-size:0.8rem;border-radius:0.5rem;padding:0.5rem 0.75rem">
                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $errors->first() }}
                    </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm"
                                   value="{{ old('name') }}" placeholder="Nama lengkap" maxlength="100" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control form-control-sm"
                                   value="{{ old('username') }}" placeholder="huruf, angka, - , _" maxlength="50" required>
                            <div style="font-size:0.72rem;color:var(--text-dim);margin-top:3px">Digunakan untuk login.</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control form-control-sm"
                                   value="{{ old('email') }}" placeholder="email@domain.com" maxlength="100" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control form-control-sm"
                                   placeholder="Min. 6 karakter" minlength="6" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control form-control-sm"
                                   placeholder="Ulangi password" minlength="6" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                                <option value="staff"      {{ old('role') === 'staff'      ? 'selected' : '' }}>Staff</option>
                                <option value="finance"    {{ old('role') === 'finance'    ? 'selected' : '' }}>Finance</option>
                            </select>
                            <div style="margin-top:0.4rem;font-size:0.75rem;color:var(--text-dim)">
                                <i class="fa-solid fa-circle-info me-1"></i>
                                <strong style="color:#818cf8">Superadmin</strong> akses penuh &bull;
                                <strong style="color:#34d399">Staff</strong> input &amp; update reklame &bull;
                                <strong style="color:#60a5fa">Finance</strong> data keuangan
                            </div>
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


{{-- ── Modal Edit ── --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content modal-dark">
            <form method="POST" id="formEdit" action="">
                @csrf @method('PUT')
                <div class="modal-header modal-dark-header">
                    <h6 class="modal-title"><i class="fa-solid fa-pen me-2" style="color:var(--accent)"></i>Edit User</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control form-control-sm" maxlength="100" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="edit_username" class="form-control form-control-sm"
                                   placeholder="huruf, angka, - , _" maxlength="50" required>
                            <div style="font-size:0.72rem;color:var(--text-dim);margin-top:3px">Digunakan untuk login.</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control form-control-sm" maxlength="100" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control form-control-sm"
                                   placeholder="Kosongkan jika tidak diganti" minlength="6">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control form-control-sm"
                                   placeholder="Kosongkan jika tidak diganti" minlength="6">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" id="edit_role" class="form-select form-select-sm" required>
                                <option value="superadmin">Superadmin</option>
                                <option value="staff">Staff</option>
                                <option value="finance">Finance</option>
                            </select>
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

@push('scripts')
<script>
    document.getElementById('modalEdit').addEventListener('show.bs.modal', function (e) {
        var btn = e.relatedTarget;
        document.getElementById('formEdit').action       = '{{ url("users") }}/' + btn.dataset.id;
        document.getElementById('edit_name').value      = btn.dataset.name;
        document.getElementById('edit_username').value  = btn.dataset.username || '';
        document.getElementById('edit_email').value     = btn.dataset.email;
        document.getElementById('edit_role').value      = btn.dataset.role;
        document.querySelectorAll('#modalEdit input[type="password"]').forEach(function (el) { el.value = ''; });
    });

    @if($errors->any() && session('modal') === 'create')
        new bootstrap.Modal(document.getElementById('modalCreate')).show();
    @endif
</script>
@endpush
