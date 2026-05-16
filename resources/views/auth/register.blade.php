@extends('layouts.app')

@section('title', 'Register')

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
        max-width: 560px;
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
        const setupToggle = (inputId, buttonId) => {
            const input = document.getElementById(inputId);
            const button = document.getElementById(buttonId);
            if (!input || !button) return;

            const setState = (isVisible) => {
                const label = isVisible ? 'Sembunyikan Password' : 'Lihat Password';
                button.setAttribute('aria-label', label);
                button.setAttribute('title', label);

                const icon = button.querySelector('i');
                if (icon) {
                    icon.classList.toggle('bi-eye', !isVisible);
                    icon.classList.toggle('bi-eye-slash', isVisible);
                }
            };

            button.addEventListener('click', function () {
                const willShow = input.type === 'password';
                input.type = willShow ? 'text' : 'password';
                setState(willShow);
            });

            setState(false);
        };

        setupToggle('password', 'toggle_password');
        setupToggle('password-confirm', 'toggle_password_confirm');
    });
</script>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="card auth-card shadow-sm">
        <div class="card-body">
            <h1 class="auth-title text-center">Buat Akun</h1>
            <p class="auth-subtitle text-center">Daftar untuk mulai layanan pemesanan percetakan</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input id="name" type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                    @error('name')
                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                        @error('email')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="telepon" class="form-label">Nomor WhatsApp</label>
                        <input id="telepon" type="text" class="form-control form-control-lg @error('telepon') is-invalid @enderror" name="telepon" value="{{ old('telepon') }}" required>
                        @error('telepon')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-wrap">
                            <input id="password" type="password" class="form-control form-control-lg password-input @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                            <button class="toggle-password" type="button" id="toggle_password" aria-label="Lihat Password" title="Lihat Password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="password-confirm" class="form-label">Konfirmasi Password</label>
                        <div class="password-wrap">
                            <input id="password-confirm" type="password" class="form-control form-control-lg password-input" name="password_confirmation" required autocomplete="new-password">
                            <button class="toggle-password" type="button" id="toggle_password_confirm" aria-label="Lihat Password" title="Lihat Password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" value="" id="agreement" required>
                    <label class="form-check-label" for="agreement">
                        Saya setuju dengan syarat dan ketentuan
                    </label>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-lg">
                        Daftar
                    </button>
                </div>

                <div class="text-center">
                    <span class="text-muted">Sudah punya akun?</span>
                    <a class="auth-meta-link ms-1" href="{{ route('login') }}">Login sekarang</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
