@extends('layouts.catalog')

@section('title', 'Security | ' . ($navSettings['nama_toko'] ?? 'Barokah Computer'))
@section('meta_description', 'Informasi keamanan transaksi dan perlindungan akun pelanggan di ' . ($navSettings['nama_toko'] ?? 'Barokah Computer') . '.')

@section('styles')
    <style>
        .page-section {
            padding: 40px 0 60px;
        }

        .security-hero {
            background: linear-gradient(135deg, #0f172a, #175CD3);
            border-radius: 24px;
            padding: 36px;
            color: #fff;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }

        .security-hero::after {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            right: -60px;
            top: -60px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
        }

        .security-hero h1 {
            font-size: 28px;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .security-hero p {
            max-width: 760px;
            font-size: 14px;
            color: rgba(255, 255, 255, .88);
            position: relative;
            z-index: 1;
        }

        .security-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .security-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 24px;
        }

        .security-card i {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: var(--primary-soft);
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .security-card h3 {
            font-size: 15px;
            margin-bottom: 8px;
        }

        .security-card p {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.7;
        }

        @media(max-width:860px) {
            .security-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <section class="page-section">
        <div class="container">
            <div class="security-hero">
                <h1>Security</h1>
                <p>
                    Keamanan transaksi dan informasi pelanggan menjadi perhatian penting bagi {{ $namaToko }}.
                    Kami menerapkan langkah-langkah yang wajar untuk membantu menjaga proses checkout, pembayaran,
                    dan pengelolaan data tetap aman.
                </p>
            </div>

            <div class="security-grid">
                <div class="security-card">
                    <i class="fa-solid fa-lock"></i>
                    <h3>Keamanan Transaksi</h3>
                    <p>
                        Proses pembayaran online didukung oleh penyedia pembayaran yang relevan agar alur transaksi
                        pelanggan dapat diproses secara lebih aman dan terverifikasi.
                    </p>
                </div>

                <div class="security-card">
                    <i class="fa-solid fa-user-shield"></i>
                    <h3>Perlindungan Akses</h3>
                    <p>
                        Akses ke data operasional dibatasi sesuai peran dan kebutuhan kerja, sehingga informasi
                        pelanggan tidak digunakan secara sembarangan.
                    </p>
                </div>

                <div class="security-card">
                    <i class="fa-solid fa-shield-halved"></i>
                    <h3>Pemantauan dan Validasi</h3>
                    <p>
                        Kami melakukan validasi data penting seperti pesanan, pembayaran, pengiriman, dan kode
                        referral untuk membantu mencegah kesalahan maupun penyalahgunaan sistem.
                    </p>
                </div>

                <div class="security-card">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <h3>Saran untuk Pelanggan</h3>
                    <p>
                        Jaga kerahasiaan akun dan data pribadi Anda, gunakan informasi yang benar saat checkout,
                        dan segera hubungi kami jika menemukan aktivitas atau transaksi yang mencurigakan.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
