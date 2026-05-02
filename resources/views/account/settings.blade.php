@extends('layouts.app')

@section('title', 'Pengaturan Akun')

@section('content')
    <div class="page-header">
        <h1>Pengaturan Akun</h1>
        <p>Kelola informasi login dan data kontak akun Anda.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Profil Akun</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('account.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nama</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="telepon" class="form-label fw-semibold">Nomor HP</label>
                            <input type="text" name="telepon" id="telepon"
                                class="form-control @error('telepon') is-invalid @enderror"
                                value="{{ old('telepon', $user->telepon) }}" placeholder="Contoh: 081234567890">
                            @error('telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Role</label>
                            <input type="text" class="form-control" value="{{ $user->role_label }}" disabled>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2-circle me-2"></i>Simpan Profil
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Password</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('account.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold">Password Saat Ini</label>
                            <input type="password" name="current_password" id="current_password"
                                class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Password Baru</label>
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-key me-2"></i>Ubah Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
