<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#052b46">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Application status &mdash; {{ $client->full_name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy-950: #052b46;
            --navy-800: #0a507e;
            --blue: #0885c7;
            --green: #24a46d;
            --lime: #8ac43f;
            --orange: #f47b20;
            --ink: #15354d;
            --muted: #71869a;
            --line: #dce8ee;
            --ok: #168858;
            --off: #b04452;
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
        }

        * { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; }

        body {
            position: relative;
            min-height: 100dvh;
            overflow-x: hidden;
            display: flex;
            justify-content: center;
            padding: max(28px, var(--safe-top)) 18px max(28px, var(--safe-bottom));
            color: var(--ink);
            background:
                radial-gradient(circle at 12% 14%, rgba(8, 133, 199, .13), transparent 28%),
                radial-gradient(circle at 90% 88%, rgba(138, 196, 63, .13), transparent 26%),
                linear-gradient(145deg, #f2f7f9 0%, #f8fafb 50%, #edf4f5 100%);
            font-family: Manrope, system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        body::before,
        body::after {
            position: fixed;
            z-index: 0;
            border: 1px solid rgba(8, 133, 199, .08);
            border-radius: 50%;
            content: "";
            pointer-events: none;
        }

        body::before { top: -220px; right: -180px; width: 540px; height: 540px; }
        body::after { bottom: -270px; left: -210px; width: 620px; height: 620px; }

        .wrap {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 540px;
            margin: auto;
            animation: rise .6s cubic-bezier(.2,.7,.2,1) both;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-bottom: 20px;
        }

        .brand-logo {
            padding: 13px 18px;
            background: rgba(255, 255, 255, .82);
            border: 1px solid rgba(255, 255, 255, .95);
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(5, 43, 70, .08);
            backdrop-filter: blur(12px);
        }

        .logo {
            display: block;
            width: auto;
            max-width: min(210px, 58vw);
            height: auto;
            max-height: 54px;
        }

        .brand-copy { text-align: left; }

        .brand-kicker {
            display: block;
            margin-bottom: 4px;
            color: var(--blue);
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .brand-label {
            margin: 0;
            color: var(--navy-950);
            font-size: 12px;
            font-weight: 800;
            line-height: 1.35;
        }

        .sheet {
            position: relative;
            overflow: hidden;
            background: rgba(255, 255, 255, .96);
            border: 1px solid rgba(255, 255, 255, .92);
            border-radius: 30px;
            box-shadow: 0 28px 70px rgba(5, 43, 70, .14), 0 2px 0 #fff inset;
        }

        .sheet::before {
            position: absolute;
            z-index: 3;
            top: 0;
            right: 42px;
            left: 42px;
            height: 3px;
            border-radius: 0 0 999px 999px;
            background: linear-gradient(90deg, var(--lime), var(--orange) 50%, var(--blue));
            content: "";
        }

        .hero {
            position: relative;
            overflow: hidden;
            padding: 32px 28px 30px;
            text-align: center;
            background: linear-gradient(135deg, var(--navy-950) 0%, #075678 58%, #08778a 100%);
        }

        .hero::before,
        .hero::after {
            position: absolute;
            border: 1px solid rgba(255, 255, 255, .09);
            border-radius: 50%;
            content: "";
        }

        .hero::before { top: -100px; right: -70px; width: 260px; height: 260px; }
        .hero::after { bottom: -135px; left: -85px; width: 250px; height: 250px; }

        .eyebrow,
        .name,
        .process { position: relative; z-index: 1; }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin: 0 0 10px;
            color: rgba(255, 255, 255, .68);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .eyebrow::before {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #9ad75a;
            box-shadow: 0 0 0 4px rgba(154, 215, 90, .13);
            content: "";
        }

        .name {
            margin: 0 0 20px;
            color: #fff;
            font-size: clamp(1.65rem, 6vw, 2.05rem);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -.035em;
        }

        .process {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            max-width: 100%;
            padding: 11px 17px;
            color: #fff;
            background: rgba(255, 255, 255, .11);
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 999px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, .10);
            font-size: 14px;
            font-weight: 800;
            line-height: 1.25;
            backdrop-filter: blur(10px);
        }

        .process-dot {
            width: 9px;
            height: 9px;
            flex-shrink: 0;
            background: var(--orange);
            border: 2px solid rgba(255, 255, 255, .65);
            border-radius: 50%;
            box-shadow: 0 0 0 5px rgba(244, 123, 32, .16);
            animation: softPulse 2.4s ease-in-out infinite;
        }

        @keyframes softPulse {
            0%, 100% { box-shadow: 0 0 0 4px rgba(244, 123, 32, .13); }
            50% { box-shadow: 0 0 0 8px rgba(244, 123, 32, .05); }
        }

        .details { padding: 24px; }

        .list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .item {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            min-height: 82px;
            padding: 15px;
            background: linear-gradient(145deg, #f8fafb, #f3f7f9);
            border: 1px solid var(--line);
            border-radius: 17px;
        }

        .item--overall { grid-column: 1 / -1; min-height: 68px; }

        .item-icon {
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            color: var(--navy-800);
            background: #fff;
            border: 1px solid rgba(8, 133, 199, .10);
            border-radius: 12px;
            box-shadow: 0 5px 14px rgba(5, 43, 70, .06);
        }

        .item-icon svg { width: 18px; height: 18px; fill: currentColor; }

        .item-copy {
            display: flex;
            min-width: 0;
            flex: 1;
            flex-direction: column;
            gap: 3px;
        }

        .item-label {
            color: var(--muted);
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .11em;
            text-transform: uppercase;
        }

        .item-value {
            overflow-wrap: anywhere;
            color: var(--ink);
            font-size: 13px;
            font-weight: 800;
            line-height: 1.4;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
        }

        .pill::before {
            width: 6px;
            height: 6px;
            background: currentColor;
            border-radius: 50%;
            content: "";
        }

        .pill--active { color: var(--ok); background: rgba(22, 136, 88, .11); }
        .pill--inactive { color: var(--off); background: rgba(176, 68, 82, .11); }

        .note {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 16px 0 0;
            padding: 14px 15px;
            color: var(--muted);
            background: rgba(8, 133, 199, .055);
            border-radius: 14px;
            font-size: 11px;
            line-height: 1.6;
        }

        .note svg {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            margin-top: 1px;
            fill: var(--blue);
        }

        .footer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            margin: 17px 0 0;
            color: var(--muted);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .05em;
        }

        .footer svg { width: 12px; height: 12px; fill: var(--green); }

        @media (max-width: 520px) {
            body { padding-inline: 12px; }
            .wrap { max-width: 430px; }
            .brand { flex-direction: column; gap: 10px; margin-bottom: 16px; }
            .brand-copy { text-align: center; }
            .brand-logo { padding: 10px 16px; border-radius: 16px; }
            .logo { max-height: 48px; }
            .sheet { border-radius: 24px; }
            .hero { padding: 27px 18px 25px; }
            .details { padding: 16px; }
            .list { grid-template-columns: 1fr; gap: 10px; }
            .item--overall { grid-column: auto; }
            .item { min-height: 66px; padding: 12px; }
        }

        @media (prefers-reduced-motion: reduce) {
            .wrap, .process-dot { animation: none; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <header class="brand">
            <div class="brand-logo">
                <img
                    class="logo"
                    src="{{ asset('images/federal-group-logo.png') }}"
                    alt="Federal Group of Companies"
                >
            </div>
            <div class="brand-copy">
                <span class="brand-kicker">Official record</span>
                <p class="brand-label">Federal Manpower<br>Promoter</p>
            </div>
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

            <div class="details">
                <div class="list">
                    <div class="item item--overall">
                        <span class="item-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M12 2 4 5v6c0 5.1 3.4 9.9 8 11 4.6-1.1 8-5.9 8-11V5l-8-3Zm-1 14-4-4 1.4-1.4 2.6 2.6 4.6-4.6L17 10l-6 6Z"/></svg>
                        </span>
                        <span class="item-copy">
                            <span class="item-label">Overall application</span>
                            <span class="item-value">
                                <span class="pill pill--{{ $client->overall_status === 'active' ? 'active' : 'inactive' }}">
                                    {{ ucfirst($client->overall_status ?: '—') }}
                                </span>
                            </span>
                        </span>
                    </div>

                    <div class="item">
                        <span class="item-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M20 6h-4V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2H4a2 2 0 0 0-2 2v12h20V8a2 2 0 0 0-2-2ZM10 4h4v2h-4v12h-4v-7h4v1h4v-1h4v7h-4V6h-4Z"/></svg>
                        </span>
                        <span class="item-copy">
                            <span class="item-label">Job title</span>
                            <span class="item-value">{{ $client->trade?->name ?: '—' }}</span>
                        </span>
                    </div>

                    <div class="item">
                        <span class="item-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z"/></svg>
                        </span>
                        <span class="item-copy">
                            <span class="item-label">Destination</span>
                            <span class="item-value">{{ $client->country?->name ?: '—' }}</span>
                        </span>
                    </div>
                </div>

                <p class="note">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17 8h-1V6a4 4 0 0 0-8 0v2H7a2 2 0 0 0-2 2v10h14V10a2 2 0 0 0-2-2Zm-7-2a2 2 0 0 1 4 0v2h-4V6Zm3 9.7V18h-2v-2.3a2 2 0 1 1 2 0Z"/></svg>
                    <span>This secure page updates whenever our office changes your process status. For documents or account details, please contact the office.</span>
                </p>
            </div>
        </main>

        <p class="footer">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9.2 16.6-4.3-4.3 1.5-1.5 2.8 2.8 8.4-8.4 1.5 1.5-9.9 9.9Z"/></svg>
            Verified secure status link &middot; Federal Group
        </p>
    </div>
</body>
</html>
