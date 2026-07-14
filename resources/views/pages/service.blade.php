@extends('layouts.catalog')

@section('title', 'Service | ' . ($navSettings['nama_toko'] ?? 'Barokah Computer'))

@section('styles')
    <style>
        .page-section {
            padding: 40px 0 60px;
        }

        .service-banner {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 36px;
            align-items: center;
        }

        .service-banner h1 {
            font-size: 26px;
            margin-bottom: 10px;
        }

        .service-banner p {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 16px;
        }

        .service-points {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 13.5px;
            font-weight: 600;
        }

        .service-photo {
            border-radius: 16px;
            min-height: 220px;
            background: linear-gradient(135deg, var(--primary-soft), #EEF2FF);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 64px;
            color: var(--primary);
        }

        .service-info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-top: 32px;
        }

        .service-info-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 22px;
        }

        .service-info-card i {
            font-size: 22px;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .service-info-card h3 {
            font-size: 14.5px;
            margin-bottom: 6px;
        }

        .service-info-card p {
            font-size: 12.5px;
            color: var(--muted);
        }

        @media(max-width:860px) {
            .service-banner {
                grid-template-columns: 1fr;
            }

            .service-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <section class="page-section">
        <div class="container">
            <div class="service-banner">
                <div>
                    <h1>Service Laptop & Komputer Terpercaya</h1>
                    <p>Perbaikan laptop, komputer, upgrade perangkat, instalasi sistem, dan pengecekan kerusakan oleh
                        teknisi berpengalaman di {{ $namaToko }}.</p>
                    <div class="service-points">
                        <span>✓ Diagnosa awal gratis</span>
                        <span>✓ Pengerjaan transparan</span>
                        <span>✓ Garansi service</span>
                    </div>
                    @if($contacts->count())
                        <a class="btn btn-primary"
                            href="https://wa.me/{{ $contacts->first()->phone }}?text={{ urlencode('Halo, saya ingin booking service laptop/komputer.') }}"
                            target="_blank">
                            <i class="fa-brands fa-whatsapp"></i> Booking Service
                        </a>
                    @endif
                </div>
                <div class="service-photo"><i class="fa-solid fa-screwdriver-wrench"></i></div>
            </div>

            <div class="service-info-grid">
                <div class="service-info-card">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <h3>Diagnosa Awal</h3>
                    <p>Pengecekan kerusakan dilakukan sebelum pengerjaan agar biaya dan waktu perbaikan jelas sejak
                        awal.</p>
                </div>
                <div class="service-info-card">
                    <i class="fa-solid fa-clock"></i>
                    <h3>Pengerjaan Cepat & Transparan</h3>
                    <p>Update progres perbaikan disampaikan langsung, tidak ada biaya tersembunyi.</p>
                </div>
                <div class="service-info-card">
                    <i class="fa-solid fa-shield-halved"></i>
                    <h3>Garansi Service</h3>
                    <p>Setiap layanan service dilengkapi garansi pengerjaan untuk ketenangan Anda.</p>
                </div>
            </div>

            <div class="service-info-grid" style="margin-top:18px;grid-template-columns:1fr;">
                <div class="service-info-card">
                    <i class="fa-solid fa-location-dot"></i>
                    <h3>Kunjungi Toko Kami</h3>
                    <p>{{ $alamat }} &middot; Buka {{ $jamBuka }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
