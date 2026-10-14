<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#063d5c">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Application status — {{ $client->full_name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0b4f79;
            --orange: #f47b20;
            --orange-soft: #ffb06a;
            --ink: #17324a;
            --muted: #738395;
            --line: rgba(11, 79, 121, .12);
            --card: #ffffff;
            --ok: #1c9a5a;
            --off: #b04452;
            --federal-gradient-dark:
                linear-gradient(
                    315deg,
                    rgba(249, 233, 58, 0.38) 0%,
                    rgba(249, 233, 58, 0) 32%
                ),
                linear-gradient(
                    225deg,
                    rgba(159, 204, 55, 0.46) 0%,
                    rgba(159, 204, 55, 0) 46%
                ),
                linear-gradient(135deg, #063d5c, #000000);
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            min-height: 100%;
        }

        body {
            min-height: 100dvh;
            font-family: Manrope, system-ui, sans-serif;
            color: var(--ink);
            background:
                linear-gradient(rgba(7, 54, 80, 0.2), rgba(7, 54, 80, 0.2)),
                var(--federal-gradient-dark);
            background-attachment: fixed;
            display: flex;
            align-items: stretch;
            justify-content: center;
            padding:
                max(20px, var(--safe-top))
                16px
                max(24px, var(--safe-bottom));
            -webkit-font-smoothing: antialiased;
        }

        .wrap {
            width: 100%;
            max-width: 430px;
            margin: auto;
            animation: rise .55s ease both;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand {
            text-align: center;
            margin-bottom: 18px;
        }

        .logo {
            display: block;
            width: auto;
            max-width: min(240px, 72vw);
            height: auto;
            max-height: 64px;
            margin: 0 auto 10px;
            filter: drop-shadow(0 8px 18px rgba(0, 0, 0, .25));
        }

        .brand-label {
            margin: 0;
            color: rgba(255, 255, 255, .78);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .sheet {
            background: var(--card);
            border-radius: 24px;
            padding: 22px 18px 18px;
            box-shadow:
                0 24px 48px rgba(8, 12, 48, .28),
                0 1px 0 rgba(255, 255, 255, .55) inset;
            overflow: hidden;
            position: relative;
        }

        .sheet::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #9fcc37, var(--orange), #1c7ab4);
        }

        .hero {
            text-align: center;
            padding: 8px 4px 18px;
        }

        .eyebrow {
            margin: 0 0 8px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .name {
            margin: 0 0 16px;
            font-size: clamp(1.35rem, 5.4vw, 1.65rem);
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -.02em;
        }

        .process {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            max-width: 100%;
            padding: 12px 18px;
            border-radius: 999px;
            background: linear-gradient(135deg, rgba(28, 122, 180, .12), rgba(159, 204, 55, .16));
            color: var(--navy);
            font-size: 15px;
            font-weight: 800;
            line-height: 1.25;
            animation: softPulse 2.8s ease-in-out infinite;
        }

        .process-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--orange);
            flex-shrink: 0;
            box-shadow: 0 0 0 4px rgba(244, 123, 32, .18);
        }

        @keyframes softPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(28, 122, 180, 0); }
            50% { box-shadow: 0 0 0 6px rgba(28, 122, 180, .08); }
        }

        .list {
            display: grid;
            gap: 10px;
        }

        .item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            min-height: 52px;
            padding: 12px 14px;
            border-radius: 14px;
            background: #f5f7fc;
            border: 1px solid var(--line);
        }

        .item-label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .item-value {
            font-size: 14px;
            font-weight: 700;
            text-align: right;
            word-break: break-word;
        }

        .pill {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            background: rgba(28, 122, 180, .12);
            color: var(--navy);
        }

        .pill--active {
            background: rgba(28, 154, 90, .12);
            color: var(--ok);
        }

        .pill--inactive {
            background: rgba(176, 68, 82, .12);
            color: var(--off);
        }

        .note {
            margin: 16px 2px 2px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.5;
            text-align: center;
        }

        .footer {
            margin-top: 16px;
            text-align: center;
            color: rgba(255, 255, 255, .62);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .04em;
        }

        @media (min-width: 480px) {
            .sheet {
                padding: 26px 24px 22px;
                border-radius: 28px;
            }

            .item {
                min-height: 56px;
                padding: 14px 16px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .wrap, .process { animation: none; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <header class="brand">
            <img
                class="logo"
                src="{{ asset('images/federal-group-logo.png') }}"
                alt="Federal Group of Companies"
            >
            <p class="brand-label">Federal Manpower Promoter</p>
        </header>

        <main class="sheet">
            <section class="hero">
                <p class="eyebrow">Live application status</p>
                <h1 class="name">{{ $client->full_name }}</h1>
                <div class="process">
                    <span class="process-dot" aria-hidden="true"></span>
                    <span>{{ $client->processStatus?->name ?: 'Status pending' }}</span>
                </div>
            </section>

            <div class="list">
                <div class="item">
                    <span class="item-label">Overall</span>
                    <span class="item-value">
                        <span class="pill pill--{{ $client->overall_status === 'active' ? 'active' : 'inactive' }}">
                            {{ ucfirst($client->overall_status ?: '—') }}
                        </span>
                    </span>
                </div>

                <div class="item">
                    <span class="item-label">Job title</span>
                    <span class="item-value">{{ $client->trade?->name ?: '—' }}</span>
                </div>

                <div class="item">
                    <span class="item-label">Country</span>
                    <span class="item-value">{{ $client->country?->name ?: '—' }}</span>
                </div>
            </div>

            <p class="note">
                This page updates when our office changes your process status.
                For documents or account details, please contact the office.
            </p>
        </main>

        <p class="footer">Secure status link · Federal Group</p>
    </div>
</body>
</html>
