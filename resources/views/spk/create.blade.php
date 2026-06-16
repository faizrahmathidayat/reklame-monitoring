@extends($isMobile ? 'layouts.mobile' : 'layouts.app')

@section('title', 'Tambah SPK')
@section('page-title', 'Tambah SPK')

@section('content')

@if($isMobile)
<div class="d-flex align-items-center justify-content-between mb-3">
    <span style="font-weight:700;font-size:0.95rem;color:var(--text-primary)">
        <i class="fa-solid fa-plus-circle me-1" style="color:var(--accent)"></i>Tambah SPK Baru
    </span>
    <a href="{{ route('spk.index') }}" class="btn btn-outline-secondary btn-sm py-1">
        <i class="fa-solid fa-arrow-left me-1"></i>Kembali
    </a>
</div>
@else
<div class="page-header">
    <div>
        <h2><i class="fa-solid fa-plus-circle me-2" style="color:var(--accent)"></i>Tambah SPK Baru</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-dim)">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('spk.index') }}" style="color:var(--text-dim)">SPK</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>
</div>
@endif

@if($errors->any())
<div class="alert mb-4" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;border-radius:0.625rem;padding:0.75rem 1rem;font-size:0.875rem">
    <i class="fa-solid fa-circle-exclamation me-2"></i><strong>Terdapat kesalahan:</strong>
    <ul class="mb-0 mt-1 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('spk.store') }}">
    @csrf

    @include('spk._form_header')

    <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-save me-1"></i> Simpan SPK
        </button>
        <a href="{{ route('spk.index') }}" class="btn btn-outline-secondary btn-sm">Batal</a>
    </div>
</form>

@endsection

@push('scripts')
<script>
@php
    $cabangsJson = json_encode(
        $cabangs->map(function ($c) {
            return ['id' => $c->id, 'nama' => $c->nama_cabang, 'wilayah_id' => $c->wilayah_id];
        })->values()->toArray()
    );
@endphp
(function () {
    var allCabangs = {!! $cabangsJson !!};

    function populateCabangs(wilayahId, selectedId) {
        var sel = document.getElementById('s_cabang');
        if (!sel) return;
        var cur = selectedId !== undefined ? selectedId : sel.value;
        sel.innerHTML = '<option value="">-- Pilih Cabang --</option>';
        var list = wilayahId
            ? allCabangs.filter(function (c) { return c.wilayah_id == wilayahId; })
            : allCabangs;
        list.forEach(function (c) {
            var opt = new Option(c.nama, c.id);
            if (cur && c.id == cur) opt.selected = true;
            sel.appendChild(opt);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var wilayahSel = document.getElementById('s_wilayah');
        if (!wilayahSel) return;
        var initW = wilayahSel.value;
        var initC = document.getElementById('s_cabang') ? document.getElementById('s_cabang').value : null;
        if (initW) populateCabangs(initW, initC);

        wilayahSel.addEventListener('change', function () {
            populateCabangs(this.value, null);
        });
    });
})();
</script>
@endpush
