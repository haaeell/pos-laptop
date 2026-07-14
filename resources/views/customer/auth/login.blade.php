@extends('layouts.catalog')

@section('title', 'Masuk Akun | ' . ($navSettings['nama_toko'] ?? 'Barokah Computer'))

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
            width: min(420px, 100%);
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

        .form-error {
            color: var(--danger);
            font-size: 12px;
            margin-top: 5px;
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
            .auth-section {
                padding: 24px 16px;
            }

            .auth-card {
                padding: 26px 22px;
                border-radius: 16px;
            }
        }
    </style>
@endsection

@section('content')
    <section class="auth-section">
        <div class="auth-card">
            <h1>Masuk ke Akun Anda</h1>
            <p class="sub">Masuk untuk berbelanja dan memantau pesanan Anda.</p>

            @if ($errors->any())
                <div class="auth-alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('customer.login') }}">
                @csrf
                <input type="hidden" name="redirect" value="{{ request('redirect') }}">

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary auth-submit">Masuk</button>
            </form>

            <p class="auth-switch">
                Belum punya akun?
                <a href="{{ route('customer.register', ['redirect' => request('redirect')]) }}">Daftar sekarang</a>
            </p>
        </div>
    </section>
@endsection
