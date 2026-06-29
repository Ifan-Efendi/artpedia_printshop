@extends('layouts.app')

@section('title', 'Login')

@push('styles')
<style>
    .auth-wrap {
        min-height: calc(100vh - 56px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
    }

    .auth-card {
        width: 100%;
        max-width: 460px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
    }

    .auth-card .card-body {
        padding: 2rem;
    }

    .auth-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }

    .auth-subtitle {
        color: #64748b;
        margin-bottom: 1.5rem;
    }

    .auth-meta-link {
        font-size: 0.92rem;
        text-decoration: none;
    }

    .auth-card .form-check-label {
        font-size: 0.92rem;
    }

    .auth-card .form-control.form-control-lg {
        font-size: 1rem;
    }

    .password-wrap {
        position: relative;
    }

    .password-input {
        font-size: 0.95rem;
        padding-right: 2.75rem;
    }

    .toggle-password {
        position: absolute;
        top: 50%;
        right: 0.75rem;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        color: #495057;
        padding: 0;
        line-height: 1;
        cursor: pointer;
    }

    .toggle-password:hover {
        color: #343a40;
    }

    .toggle-password:focus,
    .toggle-password:focus-visible,
    .toggle-password:active {
        box-shadow: none !important;
        outline: none;
        color: #495057;
    }

    .toggle-password i {
        font-size: 1.05rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('toggle_password');
        const password = document.getElementById('password');

        if (!toggle || !password) return;

        const setState = (isVisible) => {
            const icon = toggle.querySelector('i');
            if (icon) {
                icon.classList.toggle('bi-eye', !isVisible);
                icon.classList.toggle('bi-eye-slash', isVisible);
            }
            const label = isVisible ? 'Sembunyikan Password' : 'Lihat Password';
            toggle.setAttribute('aria-label', label);
            toggle.setAttribute('title', label);
        };

        toggle.addEventListener('click', function () {
            const willShow = password.type === 'password';
            password.type = willShow ? 'text' : 'password';
            setState(willShow);
        });

        setState(false);
    });
</script>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="card auth-card shadow-sm">
        <div class="card-body">
            <h1 class="auth-title text-center">Login</h1>
            <p class="auth-subtitle text-center">Masuk untuk melanjutkan pemesanan</p>

            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-wrap">
                        <input id="password" type="password" class="form-control form-control-lg password-input @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                        <button class="toggle-password" type="button" id="toggle_password" aria-label="Lihat Password" title="Lihat Password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">Ingat Saya</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a class="auth-meta-link text-primary" href="{{ route('password.request') }}">Lupa Password?</a>
                    @endif
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-lg">
                        Login
                    </button>
                </div>

                <div class="text-center">
                    <span class="text-muted">Belum punya akun?</span>
                    <a class="auth-meta-link ms-1" href="{{ route('register') }}">Daftar sekarang</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
