<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#075676">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Federal Manpower Promoter (Licence OP&HRD/4749/RWP) — trusted overseas employment promoters under Federal Group of Companies. Apply online for overseas jobs.">
    <title>@yield('title', 'Home') &mdash; Federal Manpower</title>
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&amp;family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/website.css') }}?v={{ filemtime(public_path('css/website.css')) }}">
    @stack('head')
</head>
<body class="site-body">
    <div class="site-topbar">
        <div class="site-wrap site-topbar__inner">
            <div class="site-topbar__contact">
                <a href="tel:+923004340666"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1.1-.3 1.2.4 2.5.6 3.8.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.4 21 3 13.6 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.6.6 3.8.1.4 0 .8-.3 1.1l-2.2 1.9Z"/></svg>+92 300 434 0666</a>
                <a href="mailto:info@federalmanpower.com"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4H4a2 2 0 0 0-2 2v12h20V6a2 2 0 0 0-2-2Zm0 4-8 5-8-5V6l8 5 8-5v2Z"/></svg>info@federalmanpower.com</a>
                <span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z"/></svg>Office No. 12, Blue Area, Islamabad</span>
            </div>
            <div class="site-topbar__social"><a href="#" aria-label="Facebook"><svg viewBox="0 0 24 24"><path d="M14 9h3V6h-3c-1.7 0-3 1.3-3 3v2H8v3h3v7h3v-7h3l1-3h-4V9Z"/></svg></a><span>EN</span></div>
        </div>
    </div>

    <header class="site-header">
        <div class="site-wrap site-header__inner">
            <a href="{{ route('website.home') }}" class="site-logo site-logo--group" aria-label="Federal Group of Companies home">
                <img src="{{ asset('images/federal-group-logo.png') }}" alt="Federal Group of Companies" width="846" height="295">
            </a>
            <button type="button" class="site-nav-toggle" id="site-nav-toggle" aria-expanded="false" aria-controls="site-nav" aria-label="Open menu"><span></span><span></span><span></span></button>
            <nav class="site-nav" id="site-nav" aria-label="Main navigation">
                <a href="{{ route('website.home') }}" class="{{ request()->routeIs('website.home') ? 'is-active' : '' }}">Home</a>
                <a href="{{ route('website.about') }}" class="{{ request()->routeIs('website.about') ? 'is-active' : '' }}">About</a>
                <a href="{{ route('website.jobs') }}" class="{{ request()->routeIs('website.jobs') ? 'is-active' : '' }}">Jobs</a>
                <a href="{{ route('website.countries') }}" class="{{ request()->routeIs('website.countries') ? 'is-active' : '' }}">Countries</a>
                <a href="{{ route('website.services') }}" class="{{ request()->routeIs('website.services') ? 'is-active' : '' }}">Services</a>
                <a href="{{ route('website.employers') }}" class="{{ request()->routeIs('website.employers') || request()->routeIs('employer.*') ? 'is-active' : '' }}">Employers</a>
                <a href="{{ route('website.contact') }}" class="{{ request()->routeIs('website.contact') ? 'is-active' : '' }}">Contact</a>
            </nav>
            <div class="site-header__actions">
                <a href="{{ route('client-portal.login') }}" class="btn btn-ghost"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0 2c-4.4 0-8 2-8 4.5V21h16v-2.5C20 16 16.4 14 12 14Z"/></svg>Login</a>
                <a href="{{ route('client-portal.register') }}" class="btn btn-accent"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM7 10V7H5v3H2v2h3v3h2v-3h3v-2H7Zm8 4c-3.3 0-6 1.5-6 3.5V20h12v-2.5c0-2-2.7-3.5-6-3.5Z"/></svg>Register</a>
            </div>
        </div>
    </header>

    <main>@yield('content')</main>

    <footer class="site-footer" id="contact">
        <section class="footer-feature" aria-labelledby="footer-feature-title">
            <img src="{{ asset('images/website/recruitment-team-banner.png') }}" alt="Skilled professionals ready for overseas opportunities" width="2048" height="816">
            <div class="footer-feature__wash"></div>
            <div class="site-wrap footer-feature__inner">
                <div class="footer-feature__copy">
                    <span class="footer-eyebrow">Federal Manpower Promoter</span>
                    <h2 id="footer-feature-title">Licensed overseas employment promoters</h2>
                    <p>Licence No. OP&amp;HRD/4749/RWP · Under Federal Group of Companies. For 17+ years we have strengthened people through proper overseas roles across the Middle East and beyond — now with online apply and office staff follow-up.</p>
                    <div class="footer-feature__services">
                        <div><span>01</span><p><strong>Online application</strong><small>Register and submit bio data securely</small></p></div>
                        <div><span>02</span><p><strong>Approved vacancies</strong><small>Jobs go live after office review</small></p></div>
                        <div><span>03</span><p><strong>Document uploads</strong><small>Submit required files with your application</small></p></div>
                        <div><span>04</span><p><strong>Status tracking</strong><small>Know your stage with a shareable link</small></p></div>
                        <div><span>05</span><p><strong>Employer posting</strong><small>Companies register and submit roles</small></p></div>
                        <div><span>06</span><p><strong>Office coordination</strong><small>Our team manages matching and process</small></p></div>
                    </div>
                    <a href="{{ route('website.services') }}" class="btn btn-navy">Explore our services <span>&rarr;</span></a>
                </div>
                <div class="footer-feature__promise">
                    <div><strong>Licensed</strong><span>Trusted employment company</span></div>
                    <div><strong>Proper roles</strong><span>Skills matched to jobs</span></div>
                    <div><strong>Supported</strong><span>Office staff follow-up</span></div>
                </div>
            </div>
        </section>

        <div class="footer-journey">
            <div class="site-wrap footer-journey__inner">
                <div class="footer-journey__copy"><span class="footer-journey__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm6.9 6h-3.1a15.7 15.7 0 0 0-1.4-3.5A8.1 8.1 0 0 1 18.9 8ZM12 4c.8 1 1.5 2.3 1.9 4h-3.8c.4-1.7 1.1-3 1.9-4ZM4.3 14a7 7 0 0 1 0-4h3.4a16 16 0 0 0 0 4H4.3Zm.8 2h3.1c.3 1.3.8 2.5 1.4 3.5A8.1 8.1 0 0 1 5.1 16Zm3.1-8H5.1a8.1 8.1 0 0 1 4.5-3.5A15.7 15.7 0 0 0 8.2 8Zm3.8 12c-.8-1-1.5-2.3-1.9-4h3.8c-.4 1.7-1.1 3-1.9 4Zm2.3-6H9.7a14 14 0 0 1 0-4h4.6a14 14 0 0 1 0 4Zm.1 5.5c.6-1 1.1-2.2 1.4-3.5h3.1a8.1 8.1 0 0 1-4.5 3.5ZM16.3 14a16 16 0 0 0 0-4h3.4a7 7 0 0 1 0 4h-3.4Z"/></svg></span><span><strong>Ready to apply for an overseas role?</strong><small>Create your applicant account, fill bio data, and apply to approved vacancies.</small></span></div>
                <a href="{{ route('client-portal.register') }}" class="btn btn-accent">Apply online <span>&rarr;</span></a>
            </div>
        </div>

        <div class="site-wrap site-footer__inner">
            <div class="site-footer__brand"><a href="{{ route('website.home') }}" class="site-logo site-logo--group"><img src="{{ asset('images/federal-group-logo.png') }}" alt="Federal Group of Companies" width="846" height="295"></a><p>Federal Manpower Promoter · Overseas Employment Promoters · Licence No. OP&amp;HRD/4749/RWP. Serving Pakistan’s overseas employment needs with trusted placement and proper job roles.</p></div>
            <div><h4>Quick links</h4><a href="{{ route('website.home') }}">Home</a><a href="{{ route('website.about') }}">About us</a><a href="{{ route('website.jobs') }}">Vacancies</a><a href="{{ route('website.countries') }}">Countries</a></div>
            <div><h4>Our services</h4><a href="{{ route('client-portal.register') }}">Candidate portal</a><a href="{{ route('website.jobs') }}">Browse jobs</a><a href="{{ route('website.services') }}">How we work</a><a href="{{ route('employer.register') }}">Register as employer</a></div>
            <div><h4>Contact us</h4><p>+92 300 434 0666</p><p>info@federalmanpower.com</p><p>Office No. 12, Blue Area,<br>Islamabad, Pakistan</p><div class="site-footer__social"><a href="#" aria-label="Facebook">f</a><a href="#" aria-label="LinkedIn">in</a><a href="#" aria-label="Instagram">ig</a></div></div>
        </div>
        <div class="site-footer__bar"><div class="site-wrap"><span>&copy; {{ date('Y') }} Federal Manpower. All rights reserved.</span><span><a href="#">Privacy policy</a><a href="#">Terms &amp; conditions</a></span></div></div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggle = document.getElementById('site-nav-toggle');
        const nav = document.getElementById('site-nav');
        toggle?.addEventListener('click', () => {
            const open = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', String(open));
            toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        });
        nav?.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
            nav.classList.remove('is-open');
            toggle?.setAttribute('aria-expanded', 'false');
        }));

        const revealElements = [...document.querySelectorAll([
            '.section-heading',
            '.impact-stats__heading',
            '.service-card',
            '.destination-card',
            '.impact-stat',
            '.vacancy-card',
            '.process-steps li',
            '.info-card',
            '.service-detail',
            '.contact-card',
            '.team-card',
            '.split-content > *'
        ].join(','))];
        const counters = [...document.querySelectorAll('[data-count]')];
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        revealElements.forEach((element, index) => {
            element.classList.add('reveal-on-scroll');
            element.style.setProperty('--reveal-delay', `${(index % 5) * 70}ms`);
        });

        const setCounterValue = counter => {
            counter.textContent = Number(counter.dataset.count).toLocaleString('en-US');
        };
        const animateCounter = counter => {
            if (counter.dataset.animated === 'true') return;
            counter.dataset.animated = 'true';
            const target = Number(counter.dataset.count);
            const duration = 1600;
            const startedAt = performance.now();
            const step = now => {
                const progress = Math.min((now - startedAt) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                counter.textContent = Math.round(target * eased).toLocaleString('en-US');
                if (progress < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        };

        if (reduceMotion || !('IntersectionObserver' in window)) {
            revealElements.forEach(element => element.classList.add('is-visible'));
            counters.forEach(setCounterValue);
        } else {
            counters.forEach(counter => { counter.textContent = '0'; });
            const revealObserver = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('is-visible');
                    entry.target.querySelectorAll('[data-count]').forEach(animateCounter);
                    if (entry.target.matches('[data-count]')) animateCounter(entry.target);
                    revealObserver.unobserve(entry.target);
                });
            }, {threshold: 0.14, rootMargin: '0px 0px -35px'});
            revealElements.forEach(element => revealObserver.observe(element));
            counters.forEach(counter => {
                if (!counter.closest('.reveal-on-scroll')) revealObserver.observe(counter);
            });
        }
    });
    </script>
    @stack('scripts')
</body>
</html>
