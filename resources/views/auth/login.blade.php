@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="auth-card">
    <div class="brand-icon">
        <i class="fa-solid fa-billboard"></i>
    </div>
    <h1 class="auth-title">Reklame Monitoring</h1>
    <p class="auth-subtitle">Masukkan kredensial Anda untuk melanjutkan</p>

    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" autocomplete="off">
        @csrf

        <div class="mb-3">
            <label for="login" class="form-label">Username / Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-user fa-sm"></i></span>
                <input
                    type="text"
                    class="form-control @error('login') is-invalid @enderror"
                    id="login"
                    name="login"
                    value="{{ old('login') }}"
                    placeholder="Username atau email"
                    autofocus
                    required
                >
            </div>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock fa-sm"></i></span>
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    required
                >
                <button class="input-group-text border-start-0" type="button" id="togglePassword" style="cursor:pointer;">
                    <i class="fa-solid fa-eye fa-sm" id="eyeIcon"></i>
                </button>
            </div>
        </div>

        <div class="mb-4 d-flex align-items-center justify-content-between">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>
        </div>

        <button type="submit" class="btn btn-login w-100">
            <i class="fa-solid fa-right-to-bracket me-2"></i>Masuk
        </button>
    </form>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
</script>
@endsection
