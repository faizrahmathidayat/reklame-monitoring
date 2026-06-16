@extends('layouts.app')

@section('title', 'Tambah Data Reklame')
@section('page-title', 'Tambah Data Reklame')

@section('content')

@php
    $backUrl = $defaultSpkId
        ? route('spk.show', $defaultSpkId)
        : route('reklame.index');
    $backLabel = $defaultSpkId ? 'Kembali ke SPK' : 'Kembali';
@endphp

<div class="page-header">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h2><i class="fa-solid fa-plus-circle me-2" style="color:var(--accent)"></i>Tambah Data Reklame</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-dim)">Dashboard</a></li>
                    @if($defaultSpkId)
                    <li class="breadcrumb-item"><a href="{{ route('spk.index') }}" style="color:var(--text-dim)">SPK</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('spk.show', $defaultSpkId) }}" style="color:var(--text-dim)">Detail SPK</a></li>
                    @else
                    <li class="breadcrumb-item"><a href="{{ route('reklame.index') }}" style="color:var(--text-dim)">Data Reklame</a></li>
                    @endif
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
        <a href="{{ $backUrl }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i>{{ $backLabel }}
        </a>
    </div>
</div>

<form method="POST" action="{{ route('reklame.store') }}">
    @csrf
    @if($defaultSpkId)
    <input type="hidden" name="_from_spk_id" value="{{ $defaultSpkId }}">
    @endif

    @include('reklame._form', ['reklame' => null, 'defaultSpkId' => $defaultSpkId])

    <div class="d-flex justify-content-end gap-2 mt-1 mb-4">
        <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-xmark me-1"></i>Batal
        </a>
        <button type="submit" class="btn btn-primary px-4">
            <i class="fa-solid fa-save me-1"></i>Simpan Data
        </button>
    </div>
</form>

@endsection
