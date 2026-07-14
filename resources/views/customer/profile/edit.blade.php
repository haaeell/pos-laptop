@extends('layouts.catalog')

@section('title', 'Profil Saya | ' . ($navSettings['nama_toko'] ?? 'Barokah Computer'))

@section('styles')
    <style>
        .profile-section {
            padding: 40px 0 60px;
        }

        .profile-section h1 {
            font-size: 24px;
            margin-bottom: 22px;
        }

        .profile-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 24px;
            max-width: 520px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid var(--line);
            border-radius: 10px;
            font-size: 13.5px;
            outline: none;
        }

        .form-group input:focus {
            border-color: var(--primary);
        }

        .form-hint {
            font-size: 11px;
            color: var(--muted);
            margin-top: 5px;
        }

        .form-divider {
            border-top: 1px solid var(--line);
            margin: 22px 0;
            padding-top: 6px;
        }
    </style>
@endsection

@section('content')
    <section class="profile-section">
        <div class="container">
            <h1><i class="fa-solid fa-user"></i> Profil Saya</h1>

            <div class="profile-card">
                <form action="{{ route('customer.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $customer->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email', $customer->email) }}" required>
                    </div>

                    <div class="form-group">
                        <label>No. HP</label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" required>
                    </div>

                    <div class="form-divider">
                        <p style="font-size:12.5px;font-weight:700;">Ubah Password (opsional)</p>
                    </div>

                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                    </div>

                    <div class="form-group">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation">
                        <p class="form-hint">Minimal 6 karakter.</p>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;margin-top:6px;">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
