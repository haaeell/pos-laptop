@extends('layouts.catalog')

@section('title', 'Daftar Akun | ' . ($navSettings['nama_toko'] ?? 'Barokah Computer'))

@section('styles')
    <style>
        .auth-section {
            padding: 54px 0;
            min-height: calc(100vh - 400px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            width: min(460px, 100%);
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 36px 32px;
        }

        .auth-card h1 {
            font-size: 22px;
            margin-bottom: 6px;
        }

        .auth-card p.sub {
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 26px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
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
            padding: 12px 14px;
            border: 1px solid var(--line);
            border-radius: 10px;
            font-size: 14px;
            outline: none;
        }

        .form-group input:focus {
            border-color: var(--primary);
        }

        .auth-alert {
            background: #FEF3F2;
            border: 1px solid #FECDCA;
            color: #B42318;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 12.5px;
            margin-bottom: 18px;
        }

        .auth-alert ul {
            margin: 0;
            padding-left: 18px;
        }

        .auth-submit {
            width: 100%;
            margin-top: 8px;
        }

        .auth-switch {
            text-align: center;
            font-size: 13px;
            color: var(--muted);
            margin-top: 20px;
        }

        .auth-switch a {
            color: var(--primary);
            font-weight: 700;
        }

        @media(max-width:480px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <section class="auth-section">
        <div class="auth-card">
            <h1>Buat Akun Baru</h1>
            <p class="sub">Daftar untuk mulai belanja online.</p>

            @if ($errors->any())
                <div class="auth-alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('customer.register') }}">
                @csrf
                <input type="hidden" name="redirect" value="{{ request('redirect') }}">

                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">No. HP</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Kata Sandi</label>
                        <input type="password" id="password" name="password" required minlength="8">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Ulangi Kata Sandi</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            minlength="8">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary auth-submit">Daftar</button>
            </form>

            <p class="auth-switch">
                Sudah punya akun?
                <a href="{{ route('customer.login', ['redirect' => request('redirect')]) }}">Masuk di sini</a>
            </p>
        </div>
    </section>
@endsection
