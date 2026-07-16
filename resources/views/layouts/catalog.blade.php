<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="m9aNRYIi7-DHrJnPfbOawMfsSRFxJE8BqNZ8i30Jrzo">
    @php
        $siteName = $navSettings['nama_toko'] ?? 'Barokah Computer';
        $siteUrl = url('/');
        $staticLogo = url('/barokah-logo.png');
        $seoDescription = trim($__env->yieldContent('meta_description')) ?: ($siteName . ' - Toko komputer Subang terpercaya. Jual beli dan service laptop, komputer, aksesoris, serta perangkat elektronik berkualitas di Subang.');
        $seoKeywords = $navSettings['meta_keywords'] ?? 'Barokah Computer Subang, toko komputer Subang, jual laptop Subang, service laptop Subang, komputer Subang, aksesoris komputer Subang';
        $seoImage = $navSettings['og_image'] ?? $staticLogo;
        $navigationSchema = [
            ['name' => 'Beranda', 'url' => url('/')],
            ['name' => 'Produk', 'url' => route('catalog.listing')],
            ['name' => 'Service', 'url' => route('pages.service')],
            ['name' => 'Artikel', 'url' => route('pages.articles')],
            ['name' => 'Tentang Kami', 'url' => route('pages.about')],
            ['name' => 'Privacy', 'url' => route('pages.privacy')],
            ['name' => 'Security', 'url' => route('pages.security')],
        ];
        $structuredData = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id' => $siteUrl . '#organization',
                    'name' => $siteName,
                    'alternateName' => [
                        'Barokah Computer Subang',
                        'Barokah Computer',
                        'Toko Komputer Subang',
                        'Service Laptop Subang',
                    ],
                    'url' => $siteUrl,
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => $staticLogo,
                        'width' => 512,
                        'height' => 512,
                    ],
                    'image' => $staticLogo,
                    'description' => $seoDescription,
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => $alamat ?? ($navSettings['alamat'] ?? 'Subang'),
                        'addressLocality' => 'Subang',
                        'addressRegion' => 'Jawa Barat',
                        'addressCountry' => 'ID',
                    ],
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => $siteUrl . '#website',
                    'url' => $siteUrl,
                    'name' => $siteName,
                    'alternateName' => 'Barokah Computer Subang',
                    'publisher' => ['@id' => $siteUrl . '#organization'],
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => url('/') . '?search={search_term_string}',
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
                [
                    '@type' => 'ItemList',
                    '@id' => $siteUrl . '#site-navigation',
                    'name' => 'Menu utama ' . $siteName,
                    'itemListElement' => collect($navigationSchema)->map(fn ($item, $index) => [
                        '@type' => 'SiteNavigationElement',
                        'position' => $index + 1,
                        'name' => $item['name'],
                        'url' => $item['url'],
                    ])->values()->all(),
                ],
            ],
        ];
    @endphp
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="{{ $seoKeywords }}">
    <meta name="robots" content="index, follow">
    <meta name="author" content="{{ $siteName }}">
    <meta name="application-name" content="{{ $siteName }}">
    <meta name="apple-mobile-web-app-title" content="{{ $siteName }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <title>@yield('title', $siteName . ' | Toko Komputer Subang - Laptop, Aksesoris & Service')</title>

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="@yield('title', $siteName . ' | Toko Komputer Subang')">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', $seoImage)">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', $siteName)">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="@yield('og_image', $seoImage)">

    <link rel="icon" href="{{ url('/favicon.ico') }}" sizes="any">
    <link rel="shortcut icon" href="{{ url('/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ url('/favicon-512.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ url('/favicon-192.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ url('/favicon-96.png') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ url('/favicon-48.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('/favicon-32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ url('/site.webmanifest') }}">
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

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

        html {
            overflow-x: hidden;
        }

        body {
            font-family: Inter, system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
            overflow-x: hidden;
            max-width: 100vw;
            padding-top: 98px;
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

        /* ============ HEADER ============ */
        .header {
            background: rgba(255, 255, 255, .92);
            backdrop-filter: blur(18px) saturate(180%);
            -webkit-backdrop-filter: blur(18px) saturate(180%);
            border-bottom: 1px solid var(--line);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
        }

        .header-main {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) minmax(260px, 360px) auto;
            gap: 18px;
            align-items: center;
            min-height: 84px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 10px;
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

        .desktop-nav {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-width: 0;
        }

        .desktop-nav a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            color: #475467;
            white-space: nowrap;
            transition: background .2s ease, color .2s ease, transform .2s ease;
        }

        .desktop-nav a.active,
        .desktop-nav a:hover {
            background: rgba(23, 92, 211, .08);
            color: var(--primary);
            transform: translateY(-1px);
        }

        .mobile-brand-title {
            display: none;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-action-btn {
            position: relative;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: #fff;
            display: grid;
            place-items: center;
            color: var(--text);
            font-size: 16px;
            flex-shrink: 0;
        }

        .header-action-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .cart-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: var(--danger);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            padding: 0 4px;
        }

        .header-account {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px 8px 8px;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: #fff;
            font-size: 12.5px;
            font-weight: 600;
            white-space: nowrap;
        }

        .header-account-avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--primary-soft);
            color: var(--primary);
            display: grid;
            place-items: center;
            font-size: 12px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .header-account-menu {
            position: relative;
        }

        .header-account-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: 0 20px 44px rgba(16, 24, 40, .16);
            width: 190px;
            overflow: hidden;
            opacity: 0;
            transform: translateY(-6px);
            pointer-events: none;
            transition: opacity .18s ease, transform .18s ease;
            z-index: 80;
        }

        .header-account-dropdown.open {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .header-account-dropdown a,
        .header-account-dropdown button {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 11px 14px;
            font-size: 13px;
            width: 100%;
            text-align: left;
            border: 0;
            background: none;
            cursor: pointer;
            color: var(--text);
        }

        .header-account-dropdown a:hover,
        .header-account-dropdown button:hover {
            background: var(--bg);
        }

        @media(max-width:640px) {
            .header-account span.acc-name {
                display: none;
            }

            .search-wrap {
                display: none;
            }
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

        /* ============ SEARCH DROPDOWN (history + suggestions) ============ */
        .search-wrap {
            position: relative;
            min-width: 0;
        }

        .search-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 20px 44px rgba(16, 24, 40, .16);
            z-index: 80;
            overflow: hidden;
            opacity: 0;
            transform: translateY(-6px);
            pointer-events: none;
            transition: opacity .18s ease, transform .18s ease;
            max-height: 420px;
            overflow-y: auto;
        }

        .search-dropdown.open {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .search-dd-section {
            padding: 12px 14px 6px;
        }

        .search-dd-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .search-dd-clear {
            font-size: 11px;
            font-weight: 600;
            color: var(--primary);
            cursor: pointer;
            background: none;
            border: 0;
        }

        .search-dd-history {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding-bottom: 10px;
        }

        .search-dd-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--bg);
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 6px 8px 6px 12px;
            font-size: 12px;
            color: var(--text);
            cursor: pointer;
        }

        .search-dd-chip:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .search-dd-chip button {
            border: 0;
            background: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 10px;
            padding: 3px;
            display: grid;
            place-items: center;
        }

        .search-dd-chip button:hover {
            color: var(--danger);
        }

        .search-dd-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            cursor: pointer;
            transition: background .15s ease;
        }

        .search-dd-item:hover {
            background: var(--bg);
        }

        .search-dd-thumb {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            background: #F6F8FB;
            overflow: hidden;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            color: #CBD5E1;
        }

        .search-dd-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .search-dd-info {
            flex: 1;
            min-width: 0;
        }

        .search-dd-info strong {
            display: block;
            font-size: 12.5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .search-dd-info span {
            font-size: 11.5px;
            color: var(--primary);
            font-weight: 700;
        }

        .search-dd-empty {
            padding: 18px 14px;
            text-align: center;
            font-size: 12.5px;
            color: var(--muted);
        }

        .search-dd-footer {
            border-top: 1px solid var(--line);
            padding: 10px 14px;
        }

        /* ============ WHATSAPP FLOATING WIDGET ============ */
        .wa-float {
            position: fixed;
            right: 22px;
            bottom: 24px;
            z-index: 70;
        }

        .wa-fab {
            position: relative;
            width: 58px;
            height: 58px;
            border-radius: 50%;
            border: 0;
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: #fff;
            font-size: 26px;
            display: grid;
            place-items: center;
            cursor: pointer;
            box-shadow: 0 12px 28px rgba(18, 140, 126, .4);
            transition: transform .25s cubic-bezier(.34, 1.56, .64, 1);
        }

        .wa-fab:hover {
            transform: scale(1.08);
        }

        .wa-fab:active {
            transform: scale(.92);
        }

        .wa-fab-ping {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: #25D366;
            opacity: .55;
            animation: waPing 2.2s cubic-bezier(0, 0, .2, 1) infinite;
            z-index: -1;
        }

        @keyframes waPing {

            75%,
            100% {
                transform: scale(1.9);
                opacity: 0;
            }
        }

        .wa-popup {
            position: absolute;
            right: 0;
            bottom: 74px;
            width: 320px;
            max-width: calc(100vw - 40px);
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 20px 50px rgba(16, 24, 40, .24);
            overflow: hidden;
            transform: translateY(16px) scale(.96);
            opacity: 0;
            pointer-events: none;
            transition: transform .25s cubic-bezier(.34, 1.56, .64, 1), opacity .2s ease;
            border: 1px solid var(--line);
        }

        .wa-popup.open {
            transform: translateY(0) scale(1);
            opacity: 1;
            pointer-events: auto;
        }

        .wa-popup-header {
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: #fff;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .wa-popup-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            overflow: hidden;
            background: #fff;
            flex-shrink: 0;
        }

        .wa-popup-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .wa-popup-title {
            flex: 1;
            line-height: 1.3;
            min-width: 0;
        }

        .wa-popup-title strong {
            display: block;
            font-size: 13px;
        }

        .wa-popup-title span {
            font-size: 10.5px;
            color: rgba(255, 255, 255, .85);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .wa-popup-close {
            background: rgba(255, 255, 255, .18);
            border: 0;
            color: #fff;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            cursor: pointer;
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .wa-popup-body {
            padding: 14px;
            background: #ECE5DD;
            max-height: 360px;
            overflow-y: auto;
        }

        .wa-bubble {
            background: #fff;
            border-radius: 12px;
            border-top-left-radius: 2px;
            padding: 10px 12px;
            font-size: 12.5px;
            color: var(--text);
            box-shadow: 0 2px 6px rgba(16, 24, 40, .06);
            margin-bottom: 12px;
            max-width: 92%;
        }

        .wa-contact-row {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            border-radius: 12px;
            padding: 10px 12px;
            margin-bottom: 8px;
            box-shadow: 0 2px 6px rgba(16, 24, 40, .06);
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .wa-contact-row:last-child {
            margin-bottom: 0;
        }

        .wa-contact-row:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(16, 24, 40, .12);
        }

        .wa-contact-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-soft);
            color: var(--primary);
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .wa-contact-info {
            flex: 1;
            min-width: 0;
        }

        .wa-contact-info strong {
            display: block;
            font-size: 12.5px;
        }

        .wa-contact-info span {
            font-size: 10.5px;
            color: var(--muted);
        }

        .wa-contact-icon {
            color: #25D366;
            font-size: 18px;
            flex-shrink: 0;
        }

        .wa-empty {
            font-size: 12px;
            color: var(--muted);
            text-align: center;
            padding: 12px 0;
        }

        @media(max-width:640px) {
            .wa-float {
                right: 14px;
                bottom: 100px;
            }

            .wa-fab {
                width: 52px;
                height: 52px;
                font-size: 22px;
            }

            .wa-popup {
                bottom: 66px;
                width: 280px;
            }
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
                grid-template-columns: auto 1fr auto;
                min-height: 74px;
            }

            .desktop-nav,
            .search-wrap {
                display: none;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width:640px) {
            body {
                padding-top: 72px;
                padding-bottom: 78px;
                background: #fff;
            }

            .header {
                padding-top: env(safe-area-inset-top);
            }

            .header-main {
                position: relative;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                padding: 12px 0;
            }

            .brand {
                flex: 0 0 auto;
                justify-content: flex-start;
                gap: 8px;
            }

            .brand-mark {
                width: 32px;
                height: 32px;
                border-radius: 9px;
            }

            .brand-text {
                display: none;
            }

            .mobile-brand-title {
                display: block;
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                width: calc(100% - 112px);
                text-align: center;
                pointer-events: none;
                font-size: 13px;
                font-weight: 800;
                color: var(--text);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .header-right {
                flex-shrink: 0;
            }

            .header-account-menu {
                display: none;
            }

            .header-actions {
                gap: 0;
            }

            .header-actions .header-action-btn:not(:first-child) {
                display: none;
            }

            section {
                padding: 14px 0;
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

            /* Equal-width left/right groups so the search FAB always sits dead-center,
               regardless of how many items are in either group. */
            .bottom-nav-side {
                display: flex;
                align-items: center;
                flex: 1;
                min-width: 0;
                justify-content: space-around;
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

        /* ---- Mobile full-screen search popup ---- */
        .mobile-search-overlay {
            position: fixed;
            inset: 0;
            background: #fff;
            z-index: 200;
            display: none;
            flex-direction: column;
        }

        .mobile-search-overlay.open {
            display: flex;
        }

        .mobile-search-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
        }

        .mobile-search-input-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--bg);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            color: var(--muted);
        }

        .mobile-search-input-wrap input {
            border: 0;
            background: none;
            outline: none;
            flex: 1;
            font-size: 13.5px;
            color: var(--text);
        }

        .mobile-search-header button {
            border: 0;
            background: none;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--primary);
            flex-shrink: 0;
        }

        .mobile-search-body {
            flex: 1;
            overflow-y: auto;
        }

        /* ---- Profile bottom sheet (mobile) ---- */
        .profile-sheet-overlay {
            position: fixed;
            inset: 0;
            background: rgba(16, 24, 40, .5);
            z-index: 200;
            display: none;
            align-items: flex-end;
            justify-content: center;
        }

        .profile-sheet-overlay.open {
            display: flex;
        }

        .profile-sheet {
            background: #fff;
            width: 100%;
            border-radius: 20px 20px 0 0;
            padding: 10px 8px calc(14px + env(safe-area-inset-bottom));
            animation: sheetSlideUp .22s ease-out;
        }

        @keyframes sheetSlideUp {
            from {
                transform: translateY(24px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .profile-sheet-handle {
            width: 42px;
            height: 4px;
            background: var(--line);
            border-radius: 999px;
            margin: 6px auto 14px;
        }

        .profile-sheet-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px 14px;
            border-bottom: 1px solid var(--line);
            margin-bottom: 8px;
        }

        .profile-sheet a,
        .profile-sheet button {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 14px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            border-radius: 12px;
            width: 100%;
            text-align: left;
            border: 0;
            background: none;
        }

        .profile-sheet a i,
        .profile-sheet button i {
            width: 18px;
            color: var(--primary);
        }

        .profile-sheet a:hover,
        .profile-sheet button:hover {
            background: var(--bg);
        }

        /* ---- Shared AJAX loading overlay ---- */
        .app-loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, .72);
            backdrop-filter: blur(2px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .app-loading-overlay.open {
            display: flex;
        }

        .app-loading-spinner {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            border: 4px solid var(--primary-soft);
            border-top-color: var(--primary);
            animation: appLoadingSpin .7s linear infinite;
        }

        @keyframes appLoadingSpin {
            to {
                transform: rotate(360deg);
            }
        }

        .scroll-reveal {
            opacity: 0;
            transform: translateY(28px) scale(.985);
            transition: opacity .7s cubic-bezier(.22, 1, .36, 1), transform .7s cubic-bezier(.22, 1, .36, 1);
            transition-delay: var(--reveal-delay, 0ms);
            will-change: transform, opacity;
        }

        .scroll-reveal.is-visible {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        @media (prefers-reduced-motion: reduce) {
            .scroll-reveal {
                opacity: 1;
                transform: none;
                transition: none;
            }
        }
    </style>

    @yield('styles')
</head>

<body>
    <header class="header">
        <div class="container header-main">
            <a href="{{ url('/') }}" class="brand">
                <div class="brand-mark"><img src="{{ asset('storage/' . $logo) }}" alt="{{ $namaToko }}"></div>
                <div class="brand-text">
                    <strong>{{ $namaToko }}</strong>
                </div>
            </a>

            <div class="mobile-brand-title">Barokah Computer</div>

            <nav class="desktop-nav" aria-label="Navigasi utama">
                <a class="{{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Beranda</a>
                <a class="{{ request()->is('produk*') ? 'active' : '' }}" href="{{ route('catalog.listing') }}">Produk</a>
                <a class="{{ request()->is('service') ? 'active' : '' }}" href="{{ route('pages.service') }}">Service</a>
                <a class="{{ request()->is('artikel*') ? 'active' : '' }}" href="{{ route('pages.articles') }}">Artikel</a>
                <a class="{{ request()->is('tentang-kami') ? 'active' : '' }}" href="{{ route('pages.about') }}">Tentang Kami</a>
            </nav>

            <div class="search-wrap">
                <form class="search-bar" action="{{ url('/') }}" method="GET" id="searchForm" autocomplete="off">
                    <input id="global-search" name="search" type="search" placeholder="Cari nama produk atau kode..." />
                    <button type="submit" aria-label="Cari"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>

                <div class="search-dropdown" id="searchDropdown">
                    <div class="search-dd-section" id="searchDdHistory"></div>
                    <div id="searchDdSuggestions"></div>
                </div>
            </div>

            <div class="header-right">
                <div class="header-actions">
                    <a href="{{ route('cart.index') }}" class="header-action-btn" title="Keranjang">
                        <i class="fa-solid fa-cart-shopping"></i>
                        @auth('customers')
                            @php $cartCount = \App\Models\CartItem::where('customer_id', Auth::guard('customers')->id())->sum('qty'); @endphp
                            @if ($cartCount > 0)
                                <span class="cart-badge">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                            @endif
                        @endauth
                    </a>

                    @auth('customers')
                        <div class="header-account-menu" id="accountMenu">
                            <button type="button" class="header-account" id="accountMenuBtn">
                                <span class="header-account-avatar">{{ strtoupper(substr(Auth::guard('customers')->user()->name, 0, 1)) }}</span>
                                <span class="acc-name">{{ \Illuminate\Support\Str::before(Auth::guard('customers')->user()->name, ' ') }}</span>
                                <i class="fa-solid fa-chevron-down" style="font-size:10px;"></i>
                            </button>

                            <div class="header-account-dropdown" id="accountDropdown">
                                <a href="{{ route('customer.profile.edit') }}">
                                    <i class="fa-solid fa-user"></i> Profil Saya
                                </a>
                                <a href="{{ route('customer.orders.index') }}">
                                    <i class="fa-solid fa-box"></i> Pesanan Saya
                                </a>
                                <a href="{{ route('customer.addresses.index') }}">
                                    <i class="fa-solid fa-location-dot"></i> Alamat Saya
                                </a>
                                <a href="{{ route('customer.favorites.index') }}">
                                    <i class="fa-solid fa-heart"></i> Favorit Saya
                                </a>
                                <form method="POST" action="{{ route('customer.logout') }}">
                                    @csrf
                                    <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('customer.login') }}" class="header-action-btn" title="Masuk / Daftar">
                            <i class="fa-solid fa-user"></i>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    @yield('content')

    <nav class="bottom-nav" id="bottomNav">
        <div class="bottom-nav-side">
            <a href="{{ url('/') }}" class="bottom-nav-item {{ request()->is('/') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i>
                <span>Beranda</span>
            </a>
            <a href="{{ route('catalog.listing') }}" class="bottom-nav-item {{ request()->is('produk*') ? 'active' : '' }}">
                <i class="fa-solid fa-grip"></i>
                <span>Produk</span>
            </a>
        </div>

        <a href="#" class="bottom-nav-item bottom-nav-fab-wrap" id="bottomNavSearch">
            <span class="bottom-nav-fab"><i class="fa-solid fa-magnifying-glass"></i></span>
            <span>Cari</span>
        </a>

        <div class="bottom-nav-side">
            <a href="{{ route('pages.articles') }}" class="bottom-nav-item {{ request()->is('artikel*') ? 'active' : '' }}">
                <i class="fa-solid fa-newspaper"></i>
                <span>Artikel</span>
            </a>
            <button type="button" class="bottom-nav-item" id="bottomNavProfile" style="background:none;border:0;">
                <i class="fa-solid fa-user"></i>
                <span>Profile</span>
            </button>
        </div>
    </nav>

    <div class="mobile-search-overlay" id="mobileSearchOverlay">
        <div class="mobile-search-header">
            <div class="mobile-search-input-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" id="mobile-search-input" placeholder="Cari nama produk atau kode..." autocomplete="off">
            </div>
            <button type="button" id="mobileSearchClose" aria-label="Tutup">Batal</button>
        </div>
        <div class="mobile-search-body">
            <div id="mobileSearchDdHistory"></div>
            <div id="mobileSearchDdSuggestions"></div>
        </div>
    </div>

    <div class="profile-sheet-overlay" id="profileSheetOverlay">
        <div class="profile-sheet">
            <div class="profile-sheet-handle"></div>
            @auth('customers')
                <div class="profile-sheet-user">
                    <span class="header-account-avatar">{{ strtoupper(substr(Auth::guard('customers')->user()->name, 0, 1)) }}</span>
                    <strong>{{ Auth::guard('customers')->user()->name }}</strong>
                </div>
                <a href="{{ route('customer.profile.edit') }}"><i class="fa-solid fa-user"></i> Profil Saya</a>
                <a href="{{ route('customer.orders.index') }}"><i class="fa-solid fa-box"></i> Pesanan Saya</a>
                <a href="{{ route('customer.addresses.index') }}"><i class="fa-solid fa-location-dot"></i> Alamat Saya</a>
                <a href="{{ route('customer.favorites.index') }}"><i class="fa-solid fa-heart"></i> Favorit Saya</a>
                <a href="{{ route('pages.service') }}"><i class="fa-solid fa-screwdriver-wrench"></i> Service</a>
                <form method="POST" action="{{ route('customer.logout') }}">
                    @csrf
                    <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button>
                </form>
            @else
                <a href="{{ route('pages.service') }}"><i class="fa-solid fa-screwdriver-wrench"></i> Service</a>
                <a href="{{ route('customer.login') }}"><i class="fa-solid fa-right-to-bracket"></i> Masuk / Daftar</a>
            @endauth
        </div>
    </div>

    <div class="app-loading-overlay" id="appLoadingOverlay">
        <div class="app-loading-spinner"></div>
    </div>

    <div class="wa-float" id="waFloat">
        <div class="wa-popup" id="waPopup">
            <div class="wa-popup-header">
                <div class="wa-popup-avatar"><img src="{{ asset('storage/' . $logo) }}" alt="{{ $namaToko }}"></div>
                <div class="wa-popup-title">
                    <strong>{{ $namaToko }}</strong>
                    <span><i class="fa-solid fa-circle" style="color:#B9F6CA;font-size:6px;"></i> Biasanya balas
                        cepat</span>
                </div>
                <button type="button" class="wa-popup-close" id="waPopupClose" aria-label="Tutup">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="wa-popup-body">
                <div class="wa-bubble">
                    Halo! 👋 Ada yang bisa kami bantu? Pilih admin di bawah untuk mulai chat via WhatsApp.
                </div>

                @forelse($navContacts as $contact)
                    <a class="wa-contact-row"
                        href="https://wa.me/{{ $contact->phone }}?text={{ urlencode($contact->whatsapp_text ?? 'Halo, saya ingin bertanya.') }}"
                        target="_blank">
                        <div class="wa-contact-avatar"><i class="fa-solid fa-headset"></i></div>
                        <div class="wa-contact-info">
                            <strong>{{ $contact->label }}</strong>
                            <span>Klik untuk chat via WhatsApp</span>
                        </div>
                        <i class="fa-brands fa-whatsapp wa-contact-icon"></i>
                    </a>
                @empty
                    <p class="wa-empty">Kontak belum tersedia.</p>
                @endforelse
            </div>
        </div>

        <button type="button" class="wa-fab" id="waFabBtn" aria-label="Chat WhatsApp">
            <i class="fa-brands fa-whatsapp"></i>
            <span class="wa-fab-ping"></span>
        </button>
    </div>

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
                    <h4>Informasi</h4>
                    <a href="{{ route('pages.about') }}"><i class="fa-solid fa-circle-info"></i> Tentang Kami</a>
                    <a href="{{ route('pages.privacy') }}"><i class="fa-solid fa-user-shield"></i> Privacy</a>
                    <a href="{{ route('pages.security') }}"><i class="fa-solid fa-shield-halved"></i> Security</a>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.applyScrollReveal = function (root = document) {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }

            const selectors = [
                'main section .hero-card',
                'main section .benefits',
                'main section .section-head',
                'main section .category-card',
                'main section .product-card',
                'main section .service-banner',
                'main section .why-card',
                'main section .brand-chip',
                'main section .testimonial',
                'main section .contact-bar',
                '.checkout-card',
                '.addr-card',
                '.page-card',
                '.article-card',
                '.service-card'
            ];

            const nodes = root.querySelectorAll(selectors.join(','));
            nodes.forEach((node, index) => {
                if (node.dataset.revealBound === '1') {
                    return;
                }

                node.dataset.revealBound = '1';
                node.classList.add('scroll-reveal');
                node.style.setProperty('--reveal-delay', `${Math.min(index % 6, 5) * 70}ms`);
                revealObserver.observe(node);
            });
        };

        const revealObserver = window.matchMedia('(prefers-reduced-motion: reduce)').matches
            ? { observe() {} }
            : new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                });
            }, {
                threshold: 0.14,
                rootMargin: '0px 0px -48px 0px',
            });

        window.applyScrollReveal();

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
        const mobileSearchOverlay = document.getElementById('mobileSearchOverlay');
        const mobileSearchInput = document.getElementById('mobile-search-input');
        const mobileSearchDdHistory = document.getElementById('mobileSearchDdHistory');
        const mobileSearchDdSuggestions = document.getElementById('mobileSearchDdSuggestions');
        const mobileSearchClose = document.getElementById('mobileSearchClose');

        function openMobileSearch() {
            if (!mobileSearchOverlay) return;
            mobileSearchOverlay.classList.add('open');
            renderHistory(mobileSearchDdHistory, mobileSearchInput);
            fetchSuggestions(mobileSearchInput.value.trim(), mobileSearchDdSuggestions);
            // Focus must happen synchronously in the click handler (no setTimeout/await
            // before it) or iOS Safari won't pop the keyboard up.
            mobileSearchInput.focus();
        }

        function closeMobileSearch() {
            if (mobileSearchOverlay) mobileSearchOverlay.classList.remove('open');
        }

        if (bottomNavSearch) {
            bottomNavSearch.addEventListener('click', function (e) {
                e.preventDefault();
                this.classList.remove('tap');
                void this.offsetWidth;
                this.classList.add('tap');
                openMobileSearch();
            });
        }

        if (mobileSearchClose) mobileSearchClose.addEventListener('click', closeMobileSearch);

        if (mobileSearchInput) {
            let mobileSuggestTimeout = null;
            mobileSearchInput.addEventListener('input', function () {
                clearTimeout(mobileSuggestTimeout);
                const val = this.value.trim();

                if (!val) {
                    renderHistory(mobileSearchDdHistory, mobileSearchInput);
                    fetchSuggestions('', mobileSearchDdSuggestions);
                    return;
                }

                mobileSearchDdHistory.innerHTML = '';
                mobileSuggestTimeout = setTimeout(() => fetchSuggestions(val, mobileSearchDdSuggestions), 300);
            });

            mobileSearchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submitSearch(this.value.trim());
                }
            });
        }

        // ===== Profile bottom sheet (mobile) =====
        const bottomNavProfile = document.getElementById('bottomNavProfile');
        const profileSheetOverlay = document.getElementById('profileSheetOverlay');

        if (bottomNavProfile && profileSheetOverlay) {
            bottomNavProfile.addEventListener('click', () => profileSheetOverlay.classList.add('open'));

            profileSheetOverlay.addEventListener('click', (e) => {
                if (e.target === profileSheetOverlay) profileSheetOverlay.classList.remove('open');
            });
        }

        // ===== Account dropdown =====
        const accountMenuBtn = document.getElementById('accountMenuBtn');
        const accountDropdown = document.getElementById('accountDropdown');

        if (accountMenuBtn && accountDropdown) {
            accountMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                accountDropdown.classList.toggle('open');
            });

            document.addEventListener('click', (e) => {
                if (!e.target.closest('#accountMenu')) accountDropdown.classList.remove('open');
            });
        }

        // ===== WhatsApp floating widget =====
        const waFabBtn = document.getElementById('waFabBtn');
        const waPopup = document.getElementById('waPopup');
        const waPopupClose = document.getElementById('waPopupClose');
        const bottomNavChat = document.getElementById('bottomNavChat');

        function toggleWaPopup(forceOpen) {
            if (!waPopup) return;
            if (forceOpen === true) waPopup.classList.add('open');
            else if (forceOpen === false) waPopup.classList.remove('open');
            else waPopup.classList.toggle('open');
        }

        if (waFabBtn) waFabBtn.addEventListener('click', () => toggleWaPopup());
        if (waPopupClose) waPopupClose.addEventListener('click', () => toggleWaPopup(false));
        if (bottomNavChat) bottomNavChat.addEventListener('click', () => toggleWaPopup(true));

        document.addEventListener('click', (e) => {
            if (waPopup && waPopup.classList.contains('open') && !e.target.closest('.wa-float') && !e.target.closest('#bottomNavChat')) {
                toggleWaPopup(false);
            }
        });

        // ===== Search: history + live suggestions (e-commerce style) =====
        const SEARCH_HISTORY_KEY = 'catalog_search_history';
        const searchInput = document.getElementById('global-search');
        const searchDropdown = document.getElementById('searchDropdown');
        const searchDdHistory = document.getElementById('searchDdHistory');
        const searchDdSuggestions = document.getElementById('searchDdSuggestions');
        const searchForm = document.getElementById('searchForm');

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function getSearchHistory() {
            try {
                return JSON.parse(localStorage.getItem(SEARCH_HISTORY_KEY)) || [];
            } catch (e) {
                return [];
            }
        }

        function saveSearchHistory(term) {
            term = (term || '').trim();
            if (!term) return;
            let history = getSearchHistory().filter(t => t.toLowerCase() !== term.toLowerCase());
            history.unshift(term);
            localStorage.setItem(SEARCH_HISTORY_KEY, JSON.stringify(history.slice(0, 8)));
        }

        function removeSearchHistoryItem(term) {
            localStorage.setItem(SEARCH_HISTORY_KEY, JSON.stringify(getSearchHistory().filter(t => t !== term)));
            renderHistory();
        }

        function clearSearchHistory() {
            localStorage.removeItem(SEARCH_HISTORY_KEY);
            renderHistory();
        }

        function renderHistory(historyEl = searchDdHistory, inputEl = searchInput) {
            if (!historyEl) return;
            const history = getSearchHistory();

            if (!history.length) {
                historyEl.innerHTML = '';
                return;
            }

            historyEl.innerHTML = `
                <div class="search-dd-heading">
                    <span><i class="fa-solid fa-clock-rotate-left"></i> Pencarian Terakhir</span>
                    <button type="button" class="search-dd-clear" data-clear-history>Hapus Semua</button>
                </div>
                <div class="search-dd-history">
                    ${history.map(term => `
                        <span class="search-dd-chip" data-term="${escapeHtml(term)}">
                            ${escapeHtml(term)}
                            <button type="button" data-remove="${escapeHtml(term)}" aria-label="Hapus"><i class="fa-solid fa-xmark"></i></button>
                        </span>
                    `).join('')}
                </div>
            `;

            historyEl.querySelectorAll('.search-dd-chip').forEach(chip => {
                chip.addEventListener('click', (e) => {
                    if (e.target.closest('button[data-remove]')) return;
                    inputEl.value = chip.dataset.term;
                    submitSearch(chip.dataset.term);
                });
            });

            historyEl.querySelectorAll('button[data-remove]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    removeSearchHistoryItem(btn.dataset.remove);
                    renderHistory(historyEl, inputEl);
                });
            });

            const clearBtn = historyEl.querySelector('[data-clear-history]');
            if (clearBtn) clearBtn.addEventListener('click', () => { clearSearchHistory(); renderHistory(historyEl, inputEl); });
        }

        function submitSearch(term) {
            saveSearchHistory(term);
            window.location.href = `{{ url('/') }}?search=${encodeURIComponent(term)}`;
        }

        function formatSearchPrice(v) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0
            }).format(v);
        }

        let suggestTimeout = null;
        async function fetchSuggestions(term, suggestEl = searchDdSuggestions) {
            if (!suggestEl) return;

            suggestEl.innerHTML = `<div class="search-dd-empty">${term ? 'Mencari...' : 'Memuat rekomendasi...'}</div>`;

            try {
                const res = await fetch(`/data/catalog?search=${encodeURIComponent(term)}&page=1${term ? '' : '&per_page=all&in_stock_only=1'}`);
                const data = await res.json();
                let items = data.data || [];

                if (!term) {
                    items = items
                        .map(p => ({ p, sort: Math.random() }))
                        .sort((a, b) => a.sort - b.sort)
                        .map(({ p }) => p);
                }

                items = items.slice(0, 5);

                if (!items.length) {
                    suggestEl.innerHTML = term
                        ? `<div class="search-dd-empty">Produk "${escapeHtml(term)}" tidak ditemukan.</div>`
                        : `<div class="search-dd-empty">Belum ada produk untuk direkomendasikan.</div>`;
                    return;
                }

                const heading = term ? 'Rekomendasi Produk' : 'Produk Populer';

                suggestEl.innerHTML = `
                    <div class="search-dd-heading" style="padding:6px 14px 0;">
                        <span><i class="fa-solid fa-fire"></i> ${heading}</span>
                    </div>
                    ${items.map(p => `
                        <a class="search-dd-item" href="${p.url}">
                            <div class="search-dd-thumb">
                                ${p.image ? `<img src="/storage/${p.image}" alt="">` : `<i class="fa-solid fa-image"></i>`}
                            </div>
                            <div class="search-dd-info">
                                <strong>${escapeHtml(p.name)}</strong>
                                <span>${formatSearchPrice(p.price)}${p.is_sold ? ' · Sold' : ''}</span>
                            </div>
                        </a>
                    `).join('')}
                    ${term ? `
                        <div class="search-dd-footer">
                            <a href="{{ url('/') }}?search=${encodeURIComponent(term)}" class="search-dd-viewall"
                                data-term="${escapeHtml(term)}" style="font-size:12px;font-weight:700;color:var(--primary);">
                                Lihat semua hasil untuk "${escapeHtml(term)}" →
                            </a>
                        </div>
                    ` : ''}
                `;

                const viewAllLink = suggestEl.querySelector('.search-dd-viewall');
                if (viewAllLink) viewAllLink.addEventListener('click', () => saveSearchHistory(viewAllLink.dataset.term));
            } catch (e) {
                suggestEl.innerHTML = '';
            }
        }

        function openSearchDropdown() {
            if (!searchDropdown) return;
            searchDropdown.classList.add('open');
            if (!searchInput.value.trim()) {
                renderHistory();
                fetchSuggestions('');
            }
        }

        function closeSearchDropdown() {
            if (searchDropdown) searchDropdown.classList.remove('open');
        }

        if (searchInput) {
            searchInput.addEventListener('focus', openSearchDropdown);

            searchInput.addEventListener('input', function () {
                clearTimeout(suggestTimeout);
                const val = this.value.trim();

                if (!val) {
                    renderHistory();
                    fetchSuggestions('');
                    return;
                }

                searchDdHistory.innerHTML = '';
                suggestTimeout = setTimeout(() => fetchSuggestions(val), 300);
            });

            document.addEventListener('click', (e) => {
                if (!e.target.closest('.search-wrap') && !e.target.closest('#bottomNavSearch')) {
                    closeSearchDropdown();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeSearchDropdown();
            });
        }

        if (searchForm) {
            searchForm.addEventListener('submit', function () {
                saveSearchHistory(searchInput.value);
            });
        }

        @if (session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('success')), confirmButtonColor: '#2563eb' });
        @endif
        @if (session('error'))
            Swal.fire({ icon: 'error', title: 'Gagal', text: @json(session('error')), confirmButtonColor: '#2563eb' });
        @endif
        @if (session('info'))
            Swal.fire({ icon: 'info', title: 'Info', text: @json(session('info')), confirmButtonColor: '#2563eb' });
        @endif

        function showLoading() {
            document.getElementById('appLoadingOverlay').classList.add('open');
        }

        function hideLoading() {
            document.getElementById('appLoadingOverlay').classList.remove('open');
        }
    </script>

    @stack('scripts')

</body>

</html>
