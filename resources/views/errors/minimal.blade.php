<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Terjadi Kendala')</title>
    <style>
        :root {
            --bg: linear-gradient(180deg, #eef7f5 0%, #ffffff 100%);
            --panel: rgba(255, 255, 255, .92);
            --text: #0f172a;
            --muted: #64748b;
            --line: #dbe4ef;
            --primary: #2563eb;
            --accent: #14b8a6;
            --danger: #ef4444;
            --shadow: 0 30px 60px rgba(15, 23, 42, .12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--text);
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .error-shell {
            width: min(100%, 1080px);
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(280px, .8fr);
            background: var(--panel);
            border: 1px solid rgba(219, 228, 239, .8);
            border-radius: 32px;
            overflow: hidden;
            box-shadow: var(--shadow);
            backdrop-filter: blur(12px);
        }

        .error-copy {
            padding: 56px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
            padding: 8px 14px;
            border-radius: 999px;
            background: #eff6ff;
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .status-code {
            margin: 0 0 10px;
            font-size: clamp(52px, 10vw, 104px);
            line-height: .95;
            letter-spacing: -.04em;
        }

        .headline {
            margin: 0;
            font-size: clamp(26px, 4vw, 42px);
            line-height: 1.08;
        }

        .message {
            margin: 18px 0 0;
            max-width: 600px;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.7;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 20px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
            transition: .2s ease;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 16px 34px rgba(37, 99, 235, .18);
        }

        .btn-secondary {
            border: 1px solid var(--line);
            background: #fff;
            color: var(--text);
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .error-visual {
            position: relative;
            min-height: 100%;
            padding: 32px;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, .18), transparent 44%),
                radial-gradient(circle at bottom right, rgba(20, 184, 166, .18), transparent 40%),
                #f8fbff;
            border-left: 1px solid rgba(219, 228, 239, .9);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .orb {
            width: min(100%, 290px);
            aspect-ratio: 1;
            border-radius: 32px;
            background: linear-gradient(135deg, rgba(37, 99, 235, .94), rgba(20, 184, 166, .94));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: clamp(38px, 7vw, 72px);
            font-weight: 800;
            letter-spacing: -.05em;
            box-shadow: 0 28px 40px rgba(37, 99, 235, .18);
        }

        .note {
            margin-top: 18px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.7;
        }

        @media (max-width: 860px) {
            .error-shell {
                grid-template-columns: 1fr;
            }

            .error-copy {
                padding: 34px 24px 28px;
            }

            .error-visual {
                border-left: 0;
                border-top: 1px solid rgba(219, 228, 239, .9);
                min-height: 220px;
            }
        }
    </style>
</head>
<body>
    <main class="error-shell">
        <section class="error-copy">
            <span class="eyebrow">@yield('eyebrow', 'Halaman Sistem')</span>
            <h1 class="status-code">@yield('code', '500')</h1>
            <h2 class="headline">@yield('headline', 'Terjadi kendala pada halaman ini')</h2>
            <p class="message">@yield('message', 'Silakan coba beberapa saat lagi atau kembali ke halaman utama untuk melanjutkan aktivitas Anda.')</p>

            <div class="actions">
                <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Beranda</a>
                <a href="{{ url()->previous() ?: url('/') }}" class="btn btn-secondary">Halaman Sebelumnya</a>
            </div>

            <p class="note">@yield('note', 'Jika kendala terus berulang, silakan hubungi admin toko agar dapat segera ditindaklanjuti.')</p>
        </section>

        <aside class="error-visual">
            <div class="orb">@yield('orb', 'Oops')</div>
        </aside>
    </main>
</body>
</html>
