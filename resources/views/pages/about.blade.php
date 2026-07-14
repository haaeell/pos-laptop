@extends('layouts.catalog')

@section('title', 'Tentang Kami | ' . ($navSettings['nama_toko'] ?? 'Barokah Computer'))

@section('styles')
    <style>
        .page-section {
            padding: 40px 0 60px;
        }

        .about-hero {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 36px;
            margin-bottom: 30px;
        }

        .about-hero h1 {
            font-size: 26px;
            margin-bottom: 10px;
        }

        .about-hero p {
            color: var(--muted);
            font-size: 14px;
            max-width: 640px;
        }

        .why-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 30px;
        }

        .why-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 22px;
        }

        .why-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: var(--primary-soft);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .why-card h3 {
            font-size: 14px;
            margin-bottom: 6px;
        }

        .why-card p {
            font-size: 12.5px;
            color: var(--muted);
        }

        .about-info-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 24px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        .about-info-item span {
            display: block;
            font-size: 11px;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .about-info-item strong {
            font-size: 14px;
        }

        @media(max-width:860px) {
            .why-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .about-info-card {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <section class="page-section">
        <div class="container">
            <div class="about-hero">
                <h1>Tentang {{ $namaToko }}</h1>
                <p>{{ $deskripsi }}</p>
            </div>

            <div class="why-grid">
                <div class="why-card">
                    <div class="why-icon"><i class="fa-solid fa-store"></i></div>
                    <h3>Toko Terpercaya</h3>
                    <p>Pelayanan langsung, jelas, dan berorientasi pada kebutuhan pelanggan.</p>
                </div>
                <div class="why-card">
                    <div class="why-icon"><i class="fa-solid fa-sack-dollar"></i></div>
                    <h3>Harga Kompetitif</h3>
                    <p>Harga terbaik dengan pilihan produk sesuai kebutuhan dan anggaran.</p>
                </div>
                <div class="why-card">
                    <div class="why-icon"><i class="fa-solid fa-users"></i></div>
                    <h3>Pilihan Lengkap</h3>
                    <p>Laptop, aksesoris, komponen, perangkat elektronik, dan jasa service.</p>
                </div>
                <div class="why-card">
                    <div class="why-icon"><i class="fa-solid fa-comments"></i></div>
                    <h3>Respons Cepat</h3>
                    <p>Konsultasi pembelian dan service dengan respons yang cepat.</p>
                </div>
            </div>

            <div class="about-info-card">
                <div class="about-info-item">
                    <span>Alamat</span>
                    <strong>{{ $alamat }}</strong>
                </div>
                <div class="about-info-item">
                    <span>Jam Operasional</span>
                    <strong>{{ $jamBuka }}</strong>
                </div>
                <div class="about-info-item">
                    <span>Kontak</span>
                    @forelse($contacts as $contact)
                        <strong>{{ $contact->label }}: {{ $contact->phone }}</strong>
                    @empty
                        <strong>-</strong>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
