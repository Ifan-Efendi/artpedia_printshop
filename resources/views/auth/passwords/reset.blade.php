@extends('layouts.app')

@section('title', 'Reset Password')

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
            <h1 class="auth-title">Reset Password</h1>
            <p class="auth-subtitle">Buat password baru untuk akun Artpedia Anda.</p>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email"
                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                        name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                    @error('email')
                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru</label>
                    <div class="password-wrap">
                        <input id="password" type="password"
                            class="form-control form-control-lg password-input @error('password') is-invalid @enderror"
                            name="password" required autocomplete="new-password">
                        <button class="toggle-password" type="button" id="toggle_password" aria-label="Lihat Password" title="Lihat Password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    @error('password')
                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password-confirm" class="form-label">Konfirmasi Password Baru</label>
                    <div class="password-wrap">
                        <input id="password-confirm" type="password"
                            class="form-control form-control-lg password-input"
                            name="password_confirmation" required autocomplete="new-password">
                        <button class="toggle-password" type="button" id="toggle_password_confirm" aria-label="Lihat Password" title="Lihat Password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        Simpan Password Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
