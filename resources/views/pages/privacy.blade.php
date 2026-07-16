@extends('layouts.catalog')

@section('title', 'Privacy | ' . ($navSettings['nama_toko'] ?? 'Barokah Computer'))
@section('meta_description', 'Kebijakan privasi pelanggan di ' . ($navSettings['nama_toko'] ?? 'Barokah Computer') . '. Informasi mengenai pengumpulan, penggunaan, dan perlindungan data pribadi.')

@section('styles')
    <style>
        .page-section {
            padding: 40px 0 60px;
        }

        .policy-hero {
            background: linear-gradient(135deg, #ffffff, #f8fbff);
            border: 1px solid var(--line);
            border-radius: 24px;
            padding: 36px;
            margin-bottom: 24px;
        }

        .policy-hero h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .policy-hero p {
            max-width: 760px;
            color: var(--muted);
            font-size: 14px;
        }

        .policy-grid {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 20px;
            align-items: start;
        }

        .policy-card,
        .policy-side-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 24px;
        }

        .policy-card {
            display: grid;
            gap: 22px;
        }

        .policy-block h3,
        .policy-side-card h3 {
            font-size: 15px;
            margin-bottom: 8px;
        }

        .policy-block p,
        .policy-block li,
        .policy-side-card p,
        .policy-side-card li {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.7;
        }

        .policy-block ul,
        .policy-side-card ul {
            padding-left: 18px;
        }

        .policy-side-card {
            position: sticky;
            top: 110px;
        }

        .policy-side-card ul {
            display: grid;
            gap: 6px;
            margin-top: 8px;
        }

        @media(max-width:860px) {
            .policy-grid {
                grid-template-columns: 1fr;
            }

            .policy-side-card {
                position: static;
            }
        }
    </style>
@endsection

@section('content')
    <section class="page-section">
        <div class="container">
            <div class="policy-hero">
                <h1>Privacy</h1>
                <p>
                    Kami menghargai privasi setiap pelanggan. Halaman ini menjelaskan bagaimana {{ $namaToko }}
                    mengumpulkan, menggunakan, menyimpan, dan melindungi informasi yang diberikan saat Anda
                    berbelanja, melakukan checkout, atau menghubungi kami.
                </p>
            </div>

            <div class="policy-grid">
                <div class="policy-card">
                    <div class="policy-block">
                        <h3>Informasi yang Kami Kumpulkan</h3>
                        <p>
                            Kami dapat mengumpulkan data seperti nama, nomor telepon, alamat email, alamat pengiriman,
                            serta informasi transaksi yang diperlukan untuk memproses pesanan dan memberikan layanan
                            kepada pelanggan.
                        </p>
                    </div>

                    <div class="policy-block">
                        <h3>Penggunaan Informasi</h3>
                        <ul>
                            <li>Memproses pesanan, pembayaran, dan pengiriman.</li>
                            <li>Memberikan pembaruan status pesanan dan layanan pelanggan.</li>
                            <li>Meningkatkan kualitas layanan, keamanan, dan pengalaman penggunaan website.</li>
                            <li>Menangani keluhan, garansi, dan komunikasi terkait transaksi.</li>
                        </ul>
                    </div>

                    <div class="policy-block">
                        <h3>Penyimpanan dan Perlindungan Data</h3>
                        <p>
                            Data pelanggan disimpan seperlunya untuk keperluan operasional, administrasi, dan
                            pencatatan transaksi. Kami berupaya menjaga akses data agar hanya digunakan oleh pihak yang
                            berwenang sesuai kebutuhan layanan.
                        </p>
                    </div>

                    <div class="policy-block">
                        <h3>Pembagian Informasi</h3>
                        <p>
                            Kami tidak menjual data pribadi pelanggan. Informasi hanya dapat dibagikan kepada pihak
                            pendukung layanan, seperti penyedia pembayaran atau pengiriman, sejauh diperlukan untuk
                            menyelesaikan transaksi Anda.
                        </p>
                    </div>

                    <div class="policy-block">
                        <h3>Pembaruan Kebijakan</h3>
                        <p>
                            Kebijakan ini dapat diperbarui sewaktu-waktu untuk menyesuaikan proses bisnis atau
                            kebutuhan layanan. Perubahan terbaru akan ditampilkan pada halaman ini.
                        </p>
                    </div>
                </div>

                <aside class="policy-side-card">
                    <h3>Ringkasan</h3>
                    <ul>
                        <li>Data dipakai untuk memproses transaksi dan pelayanan pelanggan.</li>
                        <li>Informasi pelanggan tidak diperjualbelikan.</li>
                        <li>Akses data dibatasi sesuai kebutuhan operasional.</li>
                        <li>Layanan pihak ketiga hanya digunakan untuk mendukung proses transaksi.</li>
                    </ul>
                </aside>
            </div>
        </div>
    </section>
@endsection
