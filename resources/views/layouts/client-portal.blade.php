<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#f4f7f9">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @isset($title)
            {{ $title }}
        @else
            @yield('title', 'Client Portal')
        @endif
        — Federal Manpower
    </title>
    @livewireStyles
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&amp;family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0b4f79;
            --ink: #17324a;
            --muted: #738395;
            --line: rgba(11, 79, 121, .12);
            --card: #ffffff;
            --orange: #f47b20;
            --ok: #1c9a5a;
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100dvh;
            font-family: Manrope, system-ui, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(ellipse 80% 50% at 50% -8%, rgba(28, 122, 180, .10), transparent 55%),
                radial-gradient(ellipse 60% 40% at 100% 100%, rgba(159, 204, 55, .08), transparent 50%),
                #f4f7f9;
            background-attachment: fixed;
            -webkit-font-smoothing: antialiased;
        }

        .cp-shell {
            width: min(430px, 100%);
            margin: 0 auto;
            min-height: 100dvh;
            padding:
                max(20px, var(--safe-top))
                16px
                max(24px, var(--safe-bottom));
            display: flex;
            flex-direction: column;
        }

        .cp-brand {
            text-align: center;
            margin-bottom: 18px;
        }

        .cp-brand img {
            display: block;
            width: auto;
            max-width: min(220px, 70vw);
            max-height: 58px;
            margin: 0 auto 8px;
        }

        .cp-brand p {
            margin: 0;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .cp-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 24px;
            padding: 22px 18px 18px;
            box-shadow: 0 12px 28px rgba(23, 50, 74, .06);
            position: relative;
            overflow: hidden;
        }

        .cp-card::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, #9fcc37, var(--orange), #1c7ab4);
        }

        .cp-title {
            margin: 0 0 6px;
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -.02em;
        }

        .cp-intro {
            margin: 0 0 18px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.45;
        }

        .cp-field {
            display: grid;
            gap: 6px;
            margin-bottom: 14px;
        }

        .cp-field label {
            font-size: 12px;
            font-weight: 700;
            color: var(--muted);
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .cp-field input {
            width: 100%;
            min-height: 48px;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px 14px;
            font: inherit;
            font-size: 15px;
            color: var(--ink);
            background: #f5f7fc;
        }

        .cp-field input:focus {
            outline: 2px solid rgba(28, 122, 180, .35);
            outline-offset: 1px;
            background: #fff;
        }

        .cp-error {
            margin: 0;
            color: #b04452;
            font-size: 12px;
            font-weight: 600;
        }

        .cp-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 16px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 600;
        }

        .cp-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 50px;
            border: 0;
            border-radius: 14px;
            padding: 12px 16px;
            background: linear-gradient(135deg, #0b4f79, #063d5c);
            color: #fff;
            font: inherit;
            font-size: 15px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
        }

        .cp-btn--ghost {
            background: #eef3f8;
            color: var(--navy);
        }

        .cp-btn--soft {
            background: linear-gradient(135deg, rgba(28, 122, 180, .12), rgba(159, 204, 55, .16));
            color: var(--navy);
        }

        .cp-switch {
            margin: 16px 0 0;
            text-align: center;
            color: var(--muted);
            font-size: 13px;
        }

        .cp-switch a {
            color: var(--navy);
            font-weight: 800;
            text-decoration: none;
        }

        .cp-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
            color: var(--ink);
            font-size: 13px;
            font-weight: 700;
        }

        .cp-topbar a {
            color: var(--navy) !important;
        }

        .cp-topbar form { margin: 0; }

        .cp-link-btn {
            border: 0;
            background: #e8eef4;
            color: var(--navy);
            border-radius: 999px;
            padding: 8px 14px;
            font: inherit;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
        }

        .cp-item {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            min-height: 52px;
            align-items: center;
            padding: 12px 14px;
            border-radius: 14px;
            background: #f5f7fc;
            border: 1px solid var(--line);
            margin-bottom: 10px;
        }

        .cp-item span:first-child {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .cp-item span:last-child {
            font-size: 14px;
            font-weight: 700;
            text-align: right;
        }

        .cp-actions {
            display: grid;
            gap: 10px;
            margin-top: 16px;
        }

        .cp-note {
            margin: 14px 0 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.45;
            text-align: center;
        }

        .cp-footer {
            margin-top: auto;
            padding-top: 18px;
            text-align: center;
            color: var(--muted);
            font-size: 11px;
            font-weight: 600;
        }

        .cp-field select,
        .cp-field input[type="file"] {
            width: 100%;
            min-height: 48px;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px 14px;
            font: inherit;
            font-size: 15px;
            color: var(--ink);
            background: #f5f7fc;
        }

        .cp-field input[type="file"] {
            padding: 10px 12px;
            font-size: 13px;
        }

        .cp-hint {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 600;
        }

        .cp-job {
            display: grid;
            gap: 0;
            margin-bottom: 8px;
        }

        .cp-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 10px;
        }

        .cp-section {
            border: 1px solid var(--line);
            border-radius: 14px;
            margin-bottom: 10px;
            overflow: hidden;
            background: #fbfcfe;
        }

        .cp-section.is-locked {
            opacity: .55;
        }

        .cp-section__toggle {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border: 0;
            background: transparent;
            padding: 14px 14px;
            font: inherit;
            font-size: 14px;
            font-weight: 800;
            color: var(--ink);
            cursor: pointer;
            text-align: left;
        }

        .cp-chevron {
            width: 8px;
            height: 8px;
            border-right: 2px solid var(--muted);
            border-bottom: 2px solid var(--muted);
            transform: rotate(45deg);
            transition: transform .2s ease;
        }

        .cp-section.is-open .cp-chevron {
            transform: rotate(-135deg);
        }

        .cp-section__body {
            padding: 0 14px 14px;
        }

        .cp-exp {
            display: grid;
            gap: 6px;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 12px;
            background: #fff;
            border: 1px solid var(--line);
        }

        .cp-exp input {
            width: 100%;
            min-height: 42px;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            font: inherit;
            font-size: 13px;
            background: #f5f7fc;
        }

        .cp-doc {
            margin-bottom: 12px;
            padding: 12px;
            border-radius: 12px;
            background: #fff;
            border: 1px solid var(--line);
        }

        .cp-doc strong {
            display: block;
            font-size: 13px;
            margin-bottom: 2px;
        }

        .cp-app .cp-btn {
            margin-top: 6px;
        }

        .cp-toast {
            position: fixed;
            top: max(16px, var(--safe-top));
            left: 50%;
            transform: translateX(-50%);
            z-index: 80;
            width: min(420px, calc(100% - 24px));
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px 14px 16px;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 16px 36px rgba(8, 12, 48, .28);
            overflow: hidden;
        }

        .cp-toast__copy {
            flex: 1;
            min-width: 0;
        }

        .cp-toast__copy small {
            display: block;
            color: var(--ok);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .cp-toast__copy strong {
            display: block;
            margin-top: 4px;
            color: var(--ink);
            font-size: 13px;
            font-weight: 700;
            line-height: 1.35;
        }

        .cp-toast__close {
            border: 0;
            background: transparent;
            color: var(--muted);
            font-size: 20px;
            line-height: 1;
            cursor: pointer;
            padding: 0 2px;
        }

        .cp-toast__progress {
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 100%;
            background: var(--ok);
            transform-origin: left center;
            animation: cp-toast-progress 4.5s linear forwards;
        }

        @keyframes cp-toast-progress {
            from { transform: scaleX(1); }
            to { transform: scaleX(0); }
        }

        .cp-shell--auth {
            width: 100%;
            max-width: none;
            padding: 0;
            font-family: "Plus Jakarta Sans", Manrope, sans-serif;
        }

        .cp-shell--auth .cp-footer {
            margin: 0;
            padding: 15px 24px;
            color: #7890a0;
            background: #f5f9fb;
            border-top: 1px solid #e1ebf0;
        }

        .cp-auth {
            min-height: calc(100dvh - 44px);
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(500px, .92fr);
            background: #f7fafc;
        }

        .cp-auth__visual {
            position: relative;
            min-height: 720px;
            padding: 48px clamp(36px, 5vw, 82px);
            overflow: hidden;
            display: flex;
            align-items: flex-end;
            color: white;
            background-position: center;
            background-size: cover;
        }

        .cp-auth__visual::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(3,39,62,.12), rgba(3,39,62,.28) 45%, rgba(3,39,62,.96) 100%), linear-gradient(90deg, rgba(3,39,62,.34), transparent 68%);
        }

        .cp-auth__home {
            position: absolute;
            z-index: 2;
            top: 38px;
            left: clamp(36px, 5vw, 82px);
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: white;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
        }

        .cp-auth__home span { font-size: 18px; }

        .cp-auth__visual-copy {
            position: relative;
            z-index: 1;
            max-width: 660px;
            padding-bottom: 48px;
        }

        .cp-auth__eyebrow {
            display: block;
            margin-bottom: 15px;
            color: #dff46b;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .cp-auth__visual h1 {
            max-width: 610px;
            margin: 0;
            font-size: clamp(2.8rem, 4vw, 4.8rem);
            line-height: 1.04;
            letter-spacing: -.055em;
        }

        .cp-auth__visual-copy > p {
            max-width: 570px;
            margin: 22px 0 0;
            color: rgba(255,255,255,.76);
            font-size: 14px;
            line-height: 1.8;
        }

        .cp-auth__benefits {
            margin: 28px 0 0;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 12px 20px;
            list-style: none;
        }

        .cp-auth__benefits li {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,.86);
            font-size: 11px;
            font-weight: 700;
        }

        .cp-auth__benefits li::before {
            content: "✓";
            width: 19px;
            height: 19px;
            display: grid;
            place-items: center;
            color: white;
            background: #159563;
            border-radius: 50%;
            font-size: 11px;
        }

        .cp-auth__panel {
            padding: 54px clamp(36px, 5vw, 84px);
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
        }

        .cp-auth__form-wrap { width: min(100%, 500px); }

        .cp-auth__brand {
            margin-bottom: 38px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .cp-auth__brand img { width: 185px; height: auto; }

        .cp-auth__brand span {
            padding: 7px 11px;
            color: #087fc2;
            background: #eaf7fc;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .cp-auth__form-head { margin-bottom: 28px; }
        .cp-auth__form-head .cp-auth__eyebrow { margin-bottom: 10px; color: #0885c7; }

        .cp-auth__form-head h2 {
            margin: 0;
            color: #052b46;
            font-size: clamp(2rem, 3vw, 2.8rem);
            line-height: 1.1;
            letter-spacing: -.045em;
        }

        .cp-auth__form-head p {
            margin: 12px 0 0;
            color: #637b8d;
            font-size: 13px;
            line-height: 1.7;
        }

        .cp-auth .cp-field { gap: 7px; margin-bottom: 17px; }
        .cp-auth .cp-field label { color: #36566c; font-size: 10px; letter-spacing: .08em; }

        .cp-auth .cp-field input {
            min-height: 54px;
            padding: 13px 15px;
            color: #15354d;
            background: #f8fbfd;
            border: 1px solid #d8e6ee;
            border-radius: 9px;
            transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .cp-auth .cp-field input:focus {
            outline: 0;
            background: white;
            border-color: #0885c7;
            box-shadow: 0 0 0 4px rgba(8,133,199,.1);
        }

        .cp-auth .cp-check { margin: 2px 0 20px; font-size: 12px; }
        .cp-auth .cp-check input { width: 16px; height: 16px; accent-color: #159563; }

        .cp-auth .cp-btn {
            min-height: 54px;
            border-radius: 9px;
            background: linear-gradient(135deg, #0787ca 0%, #24a46d 62%, #78bd3d 100%);
            box-shadow: 0 12px 25px rgba(20,126,125,.2);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .cp-auth .cp-btn:hover { transform: translateY(-2px); box-shadow: 0 16px 30px rgba(20,126,125,.27); }
        .cp-auth .cp-switch { margin-top: 22px; font-size: 12px; }
        .cp-auth .cp-switch a { color: #0885c7; }
        .cp-auth__employer {
            margin-top: 20px;
            padding: 15px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            color: #36566c;
            background: linear-gradient(135deg, #f0f8fb, #f2f9ee);
            border: 1px solid #d8e8e9;
            border-radius: 10px;
            font-size: 11px;
            line-height: 1.5;
        }
        .cp-auth__employer strong { display: block; margin-bottom: 2px; color: #052b46; font-size: 12px; }
        .cp-auth__employer a { flex: 0 0 auto; color: #087fc2; font-weight: 800; text-decoration: none; white-space: nowrap; }
        .cp-auth__employer a:hover { color: #159563; }
        .cp-auth__secure { margin: 24px 0 0; color: #8094a2; text-align: center; font-size: 10px; line-height: 1.6; }

        @media (max-width: 960px) {
            .cp-auth { grid-template-columns: 1fr; }
            .cp-auth__visual { min-height: 370px; padding: 34px 34px; }
            .cp-auth__home { top: 28px; left: 34px; }
            .cp-auth__visual-copy { padding-bottom: 5px; }
            .cp-auth__visual h1 { max-width: 570px; font-size: 3.2rem; }
            .cp-auth__panel { padding: 48px 30px 58px; }
        }

        @media (max-width: 600px) {
            .cp-auth__visual { min-height: 300px; padding: 28px 22px; background-position: 68% center; }
            .cp-auth__home { top: 22px; left: 22px; }
            .cp-auth__visual-copy { padding-top: 60px; }
            .cp-auth__visual h1 { font-size: 2.35rem; }
            .cp-auth__visual-copy > p { margin-top: 14px; font-size: 12px; }
            .cp-auth__benefits { margin-top: 18px; gap: 8px 14px; }
            .cp-auth__benefits li { font-size: 9px; }
            .cp-auth__panel { padding: 34px 20px 46px; align-items: flex-start; }
            .cp-auth__brand { margin-bottom: 28px; }
            .cp-auth__brand img { width: 155px; }
            .cp-auth__brand span { font-size: 7px; }
            .cp-auth__form-head h2 { font-size: 2rem; }
            .cp-auth .cp-field input { min-height: 50px; }
            .cp-auth__employer { align-items: flex-start; flex-direction: column; gap: 8px; }
        }
    </style>
    @stack('head')
</head>
<body>
    <div class="cp-shell @yield('shell-class')">
        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
        <p class="cp-footer">Federal Manpower Promoter · Client Portal</p>
    </div>
    @livewireScripts
</body>
</html>
