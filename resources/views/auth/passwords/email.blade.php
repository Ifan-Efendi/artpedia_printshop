@extends('layouts.app')

@section('title', 'Lupa Password')

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

    .auth-card .form-control.form-control-lg {
        font-size: 1rem;
    }
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="card auth-card shadow-sm">
        <div class="card-body">
            <h1 class="auth-title">Lupa Password</h1>
            <p class="auth-subtitle">Masukkan email akun Anda untuk menerima tautan reset password.</p>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email"
                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                    @error('email')
                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-lg">
                        Kirim Link Reset
                    </button>
                </div>

                <div class="text-center">
                    <a class="auth-meta-link" href="{{ route('login') }}">Kembali ke login</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
