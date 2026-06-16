@extends('layouts.app')

@section('title', 'Edit Data Reklame')
@section('page-title', 'Edit Data Reklame')

@section('content')

<div class="page-header">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h2><i class="fa-solid fa-pen me-2" style="color:var(--accent)"></i>Edit Data Reklame</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-dim)">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reklame.index') }}" style="color:var(--text-dim)">Data Reklame</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reklame.show', $reklame) }}" style="color:var(--text-dim)">{{ $reklame->no_spk }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reklame.show', $reklame) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-eye me-1"></i>Detail
            </a>
            <a href="{{ route('reklame.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('reklame.update', $reklame) }}">
    @csrf
    @method('PUT')

    @include('reklame._form', ['reklame' => $reklame])

    <div class="d-flex justify-content-end gap-2 mt-1 mb-4">
        <a href="{{ route('reklame.show', $reklame) }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-xmark me-1"></i>Batal
        </a>
        <button type="submit" class="btn btn-primary px-4">
            <i class="fa-solid fa-save me-1"></i>Simpan Perubahan
        </button>
    </div>
</form>

@endsection
