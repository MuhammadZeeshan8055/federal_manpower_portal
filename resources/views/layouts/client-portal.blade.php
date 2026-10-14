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
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
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
    </style>
    @stack('head')
</head>
<body>
    <div class="cp-shell">
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
