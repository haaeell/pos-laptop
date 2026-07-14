<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="@yield('meta_description', ($navSettings['nama_toko'] ?? 'Barokah Computer') . ' - Jual beli dan service laptop, komputer, aksesoris, serta perangkat elektronik berkualitas.')">
    <title>@yield('title', ($navSettings['nama_toko'] ?? 'Barokah Computer') . ' | Laptop, Aksesoris & Service')</title>

    <link rel="icon" type="image/jpeg" href="{{ asset('storage/' . ($navSettings['logo'] ?? 'logo.jpeg')) }}">
    <link rel="apple-touch-icon" href="{{ asset('storage/' . ($navSettings['logo'] ?? 'logo.jpeg')) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        :root {
            --primary: #175CD3;
            --primary-dark: #0F4CB8;
            --primary-soft: #EAF2FF;
            --text: #101828;
            --muted: #667085;
            --line: #EAECF0;
            --surface: #FFFFFF;
            --bg: #F8FAFC;
            --success: #12B76A;
            --warning: #F79009;
            --danger: #F04438;
            --shadow: 0 12px 32px rgba(16, 24, 40, .08);
            --radius: 18px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: Inter, system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
        }

        img {
            max-width: 100%;
            display: block;
        }

        button,
        input,
        select {
            font: inherit;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .container {
            width: min(1180px, calc(100% - 32px));
            margin: auto;
        }

        /* ============ TOPBAR ============ */
        .topbar {
            background: var(--primary);
            color: #fff;
            font-size: 12px;
        }

        .topbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 9px 0;
            gap: 16px;
            flex-wrap: wrap;
        }

        .top-links {
            display: flex;
            gap: 18px;
            flex-wrap: wrap;
        }

        /* ============ HEADER ============ */
        .header {
            background: #fff;
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .header-main {
            display: grid;
            grid-template-columns: 220px 1fr auto;
            gap: 24px;
            align-items: center;
            padding: 18px 0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 13px;
            overflow: hidden;
            background: linear-gradient(135deg, var(--primary), #4F8DFD);
            display: grid;
            place-items: center;
            color: #fff;
            font-weight: 800;
            box-shadow: 0 8px 20px rgba(23, 92, 211, .25);
            flex-shrink: 0;
        }

        .brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .brand-text strong {
            display: block;
            color: var(--primary);
            font-size: 16px;
            letter-spacing: .3px;
            line-height: 1.2;
        }

        .brand-text span {
            font-size: 11px;
            color: var(--muted);
        }

        .search-bar {
            display: grid;
            grid-template-columns: 1fr 46px;
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }

        .search-bar input {
            border: 0;
            outline: 0;
            padding: 12px 14px;
            background: #fff;
            color: var(--text);
            min-width: 0;
        }

        .search-bar button {
            border: 0;
            background: var(--primary);
            color: #fff;
            cursor: pointer;
            font-size: 16px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .action {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 11px;
            color: var(--muted);
            gap: 4px;
        }

        .action-icon {
            width: 38px;
            height: 38px;
            border: 1px solid var(--line);
            border-radius: 10px;
            display: grid;
            place-items: center;
            color: #fff;
            background: var(--success);
        }

        .mobile-toggle {
            display: none;
            border: 0;
            background: transparent;
            font-size: 20px;
            color: var(--text);
            cursor: pointer;
        }

        .nav {
            border-top: 1px solid var(--line);
        }

        .nav .container {
            display: flex;
            gap: 28px;
            align-items: center;
            padding: 12px 0;
            overflow: auto;
        }

        .nav a {
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            color: #475467;
        }

        .nav a.active,
        .nav a:hover {
            color: var(--primary);
        }

        /* ============ BUTTONS ============ */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 18px;
            border-radius: 11px;
            font-weight: 700;
            font-size: 13px;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 8px 18px rgba(23, 92, 211, .22);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-light {
            background: #fff;
            border-color: var(--line);
            color: var(--text);
        }

        .select2-container .select2-selection--single {
            height: 42px;
            border-radius: 12px;
            border: 1px solid var(--line);
            padding: 6px 12px;
            display: flex;
            align-items: center;
        }

        .select2-selection__rendered {
            padding-left: 0 !important;
        }

        .select2-selection__arrow {
            height: 100%;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: var(--primary);
            outline: none;
        }

        section {
            padding: 54px 0;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 18px;
            margin-bottom: 22px;
            flex-wrap: wrap;
        }

        .section-head h2 {
            font-size: 27px;
            letter-spacing: -.6px;
        }

        .section-head p {
            color: var(--muted);
            font-size: 14px;
            margin-top: 6px;
        }

        /* ============ FOOTER ============ */
        footer {
            background: #101828;
            color: #cbd5e1;
            margin-top: 24px;
            padding: 48px 0 24px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1fr;
            gap: 40px;
        }

        .footer-about p {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 14px;
            max-width: 380px;
        }

        .footer-col h4 {
            color: #fff;
            font-size: 13px;
            margin-bottom: 14px;
        }

        .footer-col a,
        .footer-col p {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 12px;
        }

        .footer-col a:hover {
            color: #fff;
        }

        .footer-map {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #1f2937;
        }

        .footer-map iframe {
            width: 100%;
            height: 160px;
            border: 0;
        }

        .copyright {
            border-top: 1px solid #1f2937;
            margin-top: 30px;
            padding-top: 20px;
            text-align: center;
            color: #64748b;
            font-size: 11px;
        }

        /* ============ BOTTOM NAV (mobile app style) ============ */
        .bottom-nav {
            display: none;
        }

        @media(max-width:960px) {
            .header-main {
                grid-template-columns: 1fr auto;
            }

            .search-bar {
                grid-column: 1/-1;
                grid-row: 2;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }

            .mobile-toggle {
                display: block;
            }

            .nav {
                display: none;
            }

            .nav.open {
                display: block;
            }

            .nav.open .container {
                flex-direction: column;
                align-items: flex-start;
                padding: 16px;
            }
        }

        @media(max-width:640px) {
            body {
                padding-bottom: 78px;
                background: #fff;
            }

            .topbar {
                display: none;
            }

            .header {
                padding-top: env(safe-area-inset-top);
            }

            .header-main {
                grid-template-columns: 40px 1fr auto;
                gap: 10px;
                padding: 12px 0;
            }

            .brand-text {
                display: none;
            }

            .search-bar {
                grid-column: 1/-1;
                grid-row: 2;
                grid-template-columns: 1fr 44px;
                border-radius: 999px;
                border-color: var(--line);
            }

            .search-bar input {
                padding: 10px 4px 10px 14px;
                font-size: 13px;
            }

            .search-bar button {
                border-radius: 0 999px 999px 0;
            }

            .header-actions {
                display: none;
            }

            .mobile-toggle {
                display: none;
            }

            .nav {
                display: none;
            }

            section {
                padding: 22px 0;
            }

            .section-head {
                margin-bottom: 14px;
            }

            .section-head h2 {
                font-size: 18px;
            }

            .section-head p {
                font-size: 12px;
                margin-top: 2px;
            }

            .footer-grid {
                gap: 24px;
            }

            /* ---- Modern animated bottom tab bar ---- */
            .bottom-nav {
                display: flex;
                align-items: center;
                justify-content: space-around;
                position: fixed;
                left: 10px;
                right: 10px;
                bottom: 10px;
                z-index: 60;
                background: rgba(255, 255, 255, .88);
                backdrop-filter: blur(16px) saturate(180%);
                -webkit-backdrop-filter: blur(16px) saturate(180%);
                border: 1px solid rgba(234, 236, 240, .8);
                border-radius: 22px;
                padding: 8px 6px calc(6px + env(safe-area-inset-bottom));
                box-shadow: 0 12px 32px rgba(16, 24, 40, .14);
                animation: navRise .45s cubic-bezier(.22, 1, .36, 1);
            }

            @keyframes navRise {
                from {
                    transform: translateY(24px);
                    opacity: 0;
                }

                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            .bottom-nav-item {
                position: relative;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 2px;
                font-size: 10px;
                font-weight: 600;
                color: var(--muted);
                flex: 1;
                padding: 7px 4px;
                border-radius: 14px;
                overflow: hidden;
                -webkit-tap-highlight-color: transparent;
                transition: color .25s ease;
            }

            .bottom-nav-item i {
                font-size: 18px;
                transition: transform .35s cubic-bezier(.34, 1.56, .64, 1);
            }

            .bottom-nav-item span {
                transition: opacity .25s ease, transform .25s ease;
                transform: translateY(0);
            }

            .bottom-nav-item::before {
                content: "";
                position: absolute;
                inset: 0;
                background: var(--primary-soft);
                border-radius: 14px;
                transform: scale(.4);
                opacity: 0;
                transition: transform .35s cubic-bezier(.34, 1.56, .64, 1), opacity .25s ease;
                z-index: -1;
            }

            .bottom-nav-item.active {
                color: var(--primary);
            }

            .bottom-nav-item.active::before {
                transform: scale(1);
                opacity: 1;
            }

            .bottom-nav-item.active i {
                transform: translateY(-2px) scale(1.08);
            }

            .bottom-nav-item:active i {
                transform: scale(.82);
            }

            .bottom-nav-item.tap i {
                animation: navBounce .45s cubic-bezier(.34, 1.56, .64, 1);
            }

            @keyframes navBounce {
                0% {
                    transform: scale(1);
                }

                35% {
                    transform: scale(.72);
                }

                65% {
                    transform: scale(1.22);
                }

                100% {
                    transform: scale(1);
                }
            }

            .bottom-nav-fab-wrap {
                margin-top: -30px;
                flex: 0 0 auto;
                width: 58px;
            }

            .bottom-nav-fab-wrap::before {
                content: none;
            }

            .bottom-nav-fab {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background: linear-gradient(135deg, var(--primary), #3B82F6);
                color: #fff;
                display: grid;
                place-items: center;
                box-shadow: 0 10px 22px rgba(23, 92, 211, .4), 0 0 0 5px #fff;
                font-size: 17px;
                margin-bottom: 3px;
                transition: transform .3s cubic-bezier(.34, 1.56, .64, 1), box-shadow .3s ease;
            }

            .bottom-nav-fab-wrap:active .bottom-nav-fab {
                transform: scale(.88);
            }

            .bottom-nav-fab-wrap.tap .bottom-nav-fab {
                animation: fabPop .5s cubic-bezier(.34, 1.56, .64, 1);
            }

            @keyframes fabPop {
                0% {
                    transform: scale(1);
                }

                40% {
                    transform: scale(.8) rotate(-8deg);
                }

                70% {
                    transform: scale(1.15) rotate(4deg);
                }

                100% {
                    transform: scale(1) rotate(0);
                }
            }
        }

        @yield('styles')
    </style>
</head>

<body>

    <div class="topbar">
        <div class="container">
            <span>Gratis konsultasi untuk pembelian dan service perangkat</span>
            <div class="top-links"><span>Garansi Resmi</span><span>Produk Original</span><span>Pelayanan
                    Terpercaya</span></div>
        </div>
    </div>

    <header class="header">
        <div class="container header-main">
            <a href="{{ url('/') }}" class="brand">
                <div class="brand-mark"><img src="{{ asset('storage/' . $logo) }}" alt="{{ $namaToko }}"></div>
                <div class="brand-text">
                    <strong>{{ $namaToko }}</strong>
                    <span><i class="fa-solid fa-location-dot"></i> {{ $alamat }}</span>
                </div>
            </a>

            <form class="search-bar" action="{{ url('/') }}" method="GET">
                <input id="global-search" name="search" type="search" placeholder="Cari nama produk atau kode..." />
                <button type="submit" aria-label="Cari"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>

            <div class="header-actions">
                @foreach($navContacts as $contact)
                    <a href="https://wa.me/{{ $contact->phone }}?text={{ urlencode($contact->whatsapp_text) }}"
                        target="_blank" class="action" title="{{ $contact->label }}">
                        <div class="action-icon"><i class="fa-brands fa-whatsapp"></i></div>
                        <span>{{ $contact->label }}</span>
                    </a>
                @endforeach
            </div>
            <button class="mobile-toggle" id="mobileToggle" aria-label="Buka menu"><i
                    class="fa-solid fa-bars"></i></button>
        </div>
        <nav class="nav" id="nav">
            <div class="container">
                <a class="{{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Beranda</a>
                <a href="{{ url('/#products') }}">Produk</a>
                <a href="{{ url('/#service') }}">Service</a>
                <a href="{{ url('/#about') }}">Tentang Kami</a>
                <a href="{{ url('/#contact') }}">Kontak</a>
            </div>
        </nav>
    </header>

    @yield('content')

    <nav class="bottom-nav" id="bottomNav">
        <a href="{{ url('/') }}#home" class="bottom-nav-item {{ request()->is('/') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ url('/') }}#products" class="bottom-nav-item">
            <i class="fa-solid fa-grip"></i>
            <span>Kategori</span>
        </a>
        <a href="#" class="bottom-nav-item bottom-nav-fab-wrap" id="bottomNavSearch">
            <span class="bottom-nav-fab"><i class="fa-solid fa-magnifying-glass"></i></span>
            <span>Cari</span>
        </a>
        <a href="{{ url('/') }}#service" class="bottom-nav-item">
            <i class="fa-solid fa-screwdriver-wrench"></i>
            <span>Service</span>
        </a>
        @if($navContacts->count())
            <a href="https://wa.me/{{ $navContacts->first()->phone }}?text={{ urlencode($navContacts->first()->whatsapp_text ?? 'Halo, saya ingin bertanya.') }}"
                target="_blank" class="bottom-nav-item">
                <i class="fa-brands fa-whatsapp"></i>
                <span>Chat</span>
            </a>
        @endif
    </nav>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <a href="{{ url('/') }}" class="brand">
                        <div class="brand-mark"><img src="{{ asset('storage/' . $logo) }}" alt="{{ $namaToko }}">
                        </div>
                        <div class="brand-text"><strong style="color:#fff">{{ $namaToko }}</strong></div>
                    </a>
                    <p>{{ $deskripsi }}</p>
                </div>

                <div class="footer-col">
                    <h4>Hubungi Kami</h4>
                    @foreach($navContacts as $contact)
                        <a href="https://wa.me/{{ $contact->phone }}?text={{ urlencode($contact->whatsapp_text) }}"
                            target="_blank"><i class="fa-brands fa-whatsapp"></i> {{ $contact->label }}</a>
                    @endforeach
                    <p><i class="fa-solid fa-location-dot"></i> <span>{{ $alamat }}</span></p>
                    <p><i class="fa-solid fa-clock"></i> <span>Buka {{ $jamBuka }}</span></p>
                </div>

                <div class="footer-col">
                    <h4>Lokasi Toko</h4>
                    <div class="footer-map">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d247.82443395730712!2d107.79983202293691!3d-6.369330654093672!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69470029254c6d%3A0x8b2d8f31b3c65028!2sBarokah%20Computer!5e0!3m2!1sid!2sid!4v1769418867952!5m2!1sid!2sid"
                            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>

            <div class="copyright">© {{ date('Y') }} {{ $namaToko }}. All rights reserved.</div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        const navEl = document.getElementById('nav');
        const mobileToggleEl = document.getElementById('mobileToggle');
        if (mobileToggleEl) {
            mobileToggleEl.addEventListener('click', () => navEl.classList.toggle('open'));
            navEl.querySelectorAll('a').forEach(a => a.addEventListener('click', () => navEl.classList.remove('open')));
        }

        // Bottom nav: active state + tap animation
        const bottomNavItems = document.querySelectorAll('.bottom-nav-item');
        bottomNavItems.forEach(item => {
            item.addEventListener('click', function (e) {
                if (this.id === 'bottomNavSearch') return;
                if (this.target === '_blank') return;

                bottomNavItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');

                this.classList.remove('tap');
                void this.offsetWidth;
                this.classList.add('tap');
            });
        });

        const bottomNavSearch = document.getElementById('bottomNavSearch');
        if (bottomNavSearch) {
            bottomNavSearch.addEventListener('click', function (e) {
                e.preventDefault();
                this.classList.remove('tap');
                void this.offsetWidth;
                this.classList.add('tap');

                window.scrollTo({ top: 0, behavior: 'smooth' });
                const globalSearch = document.getElementById('global-search');
                setTimeout(() => globalSearch && globalSearch.focus(), 300);
            });
        }
    </script>

    @stack('scripts')

</body>

</html>
