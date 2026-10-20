@extends('layouts.website')

@section('title', 'Overseas Employment & Recruitment')

@section('content')
<section class="agency-hero" aria-roledescription="carousel" aria-label="Featured opportunities" data-hero-slider>
    <div class="agency-hero__slide is-active" role="group" aria-roledescription="slide" aria-label="1 of 4" aria-hidden="false">
        <img class="agency-hero__image" src="{{ asset('images/website/hero-business-traveler.png') }}" alt="Business traveler with luggage overlooking a modern Gulf skyline" width="2048" height="864">
        <div class="agency-hero__overlay"></div>
        <div class="site-wrap agency-hero__inner">
            <div class="agency-hero__copy">
                <div class="agency-hero__badge"><span></span> Careers without borders</div>
                <h1>Your next chapter starts <span>overseas.</span></h1>
                <p>Turn your experience into an international career with clear guidance, trusted opportunities and support at every step.</p>
                <div class="agency-hero__actions">
                    <a href="#jobs" class="btn btn-accent">Explore Vacancies <span>&rarr;</span></a>
                    <a href="{{ route('client-portal.register') }}" class="btn btn-light">Register Your Profile</a>
                </div>
                <ul class="agency-hero__assurance" aria-label="Candidate assurances">
                    <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9.2 16.6-4.3-4.3 1.5-1.5 2.8 2.8 8.4-8.4 1.5 1.5-9.9 9.9Z"/></svg>Transparent process</li>
                    <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9.2 16.6-4.3-4.3 1.5-1.5 2.8 2.8 8.4-8.4 1.5 1.5-9.9 9.9Z"/></svg>Document guidance</li>
                    <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9.2 16.6-4.3-4.3 1.5-1.5 2.8 2.8 8.4-8.4 1.5 1.5-9.9 9.9Z"/></svg>Candidate support</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="agency-hero__slide" role="group" aria-roledescription="slide" aria-label="2 of 4" aria-hidden="true">
        <img class="agency-hero__image" src="{{ asset('images/website/hero-passport-traveler.png') }}" alt="Traveler holding a passport and looking toward a city skyline" width="2048" height="864">
        <div class="agency-hero__overlay"></div>
        <div class="site-wrap agency-hero__inner">
            <div class="agency-hero__copy">
                <div class="agency-hero__badge"><span></span> Opportunity is calling</div>
                <h1>Your ambition can take you <span>further.</span></h1>
                <p>Explore career possibilities across leading destinations and prepare confidently for the opportunity ahead.</p>
                <div class="agency-hero__actions">
                    <a href="{{ route('website.countries') }}" class="btn btn-accent">Explore Countries <span>&rarr;</span></a>
                    <a href="{{ route('website.services') }}" class="btn btn-light">How We Support You</a>
                </div>
                <ul class="agency-hero__assurance" aria-label="Candidate support">
                    <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9.2 16.6-4.3-4.3 1.5-1.5 2.8 2.8 8.4-8.4 1.5 1.5-9.9 9.9Z"/></svg>Career matching</li>
                    <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9.2 16.6-4.3-4.3 1.5-1.5 2.8 2.8 8.4-8.4 1.5 1.5-9.9 9.9Z"/></svg>Country guidance</li>
                    <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9.2 16.6-4.3-4.3 1.5-1.5 2.8 2.8 8.4-8.4 1.5 1.5-9.9 9.9Z"/></svg>Travel preparation</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="agency-hero__slide" role="group" aria-roledescription="slide" aria-label="3 of 4" aria-hidden="true">
        <img class="agency-hero__image" src="{{ asset('images/website/hero-airplane-city.png') }}" alt="Airplane flying above a modern city at sunrise" width="2048" height="864">
        <div class="agency-hero__overlay"></div>
        <div class="site-wrap agency-hero__inner">
            <div class="agency-hero__copy">
                <div class="agency-hero__badge"><span></span> A clear path forward</div>
                <h1>From application to <span>takeoff.</span></h1>
                <p>Move through documentation, selection and departure preparation with a team that keeps every step clear.</p>
                <div class="agency-hero__actions">
                    <a href="{{ route('client-portal.register') }}" class="btn btn-accent">Start Your Application <span>&rarr;</span></a>
                    <a href="{{ route('website.contact') }}" class="btn btn-light">Talk to Our Team</a>
                </div>
                <ul class="agency-hero__assurance" aria-label="Application support">
                    <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9.2 16.6-4.3-4.3 1.5-1.5 2.8 2.8 8.4-8.4 1.5 1.5-9.9 9.9Z"/></svg>Simple registration</li>
                    <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9.2 16.6-4.3-4.3 1.5-1.5 2.8 2.8 8.4-8.4 1.5 1.5-9.9 9.9Z"/></svg>Document assistance</li>
                    <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9.2 16.6-4.3-4.3 1.5-1.5 2.8 2.8 8.4-8.4 1.5 1.5-9.9 9.9Z"/></svg>Departure guidance</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="agency-hero__slide" role="group" aria-roledescription="slide" aria-label="4 of 4" aria-hidden="true">
        <img class="agency-hero__image" src="{{ asset('images/website/hero-airport-professional.png') }}" alt="Professional traveler with passport and luggage outside an airport" width="2048" height="864">
        <div class="agency-hero__overlay"></div>
        <div class="site-wrap agency-hero__inner">
            <div class="agency-hero__copy">
                <div class="agency-hero__badge"><span></span> Ready for what comes next</div>
                <h1>Prepared for takeoff. <span>Ready for more.</span></h1>
                <p>Create your profile today and be ready when the right overseas opportunity matches your skills.</p>
                <div class="agency-hero__actions">
                    <a href="{{ route('client-portal.register') }}" class="btn btn-accent">Register Your Profile <span>&rarr;</span></a>
                    <a href="{{ route('website.jobs') }}" class="btn btn-light">Browse Jobs</a>
                </div>
                <ul class="agency-hero__assurance" aria-label="Candidate readiness">
                    <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9.2 16.6-4.3-4.3 1.5-1.5 2.8 2.8 8.4-8.4 1.5 1.5-9.9 9.9Z"/></svg>Profile ready</li>
                    <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9.2 16.6-4.3-4.3 1.5-1.5 2.8 2.8 8.4-8.4 1.5 1.5-9.9 9.9Z"/></svg>Opportunity alerts</li>
                    <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9.2 16.6-4.3-4.3 1.5-1.5 2.8 2.8 8.4-8.4 1.5 1.5-9.9 9.9Z"/></svg>Ongoing support</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="agency-hero__controls site-wrap" aria-label="Slider controls">
        <div class="agency-hero__dots">
            <button type="button" class="is-active" aria-label="Show slide 1" aria-current="true" data-slide-to="0"></button>
            <button type="button" aria-label="Show slide 2" aria-current="false" data-slide-to="1"></button>
            <button type="button" aria-label="Show slide 3" aria-current="false" data-slide-to="2"></button>
            <button type="button" aria-label="Show slide 4" aria-current="false" data-slide-to="3"></button>
        </div>
        <div class="agency-hero__arrows">
            <button type="button" aria-label="Previous slide" data-slide-prev><span aria-hidden="true">&larr;</span></button>
            <button type="button" aria-label="Next slide" data-slide-next><span aria-hidden="true">&rarr;</span></button>
        </div>
    </div>
</section>

<div class="site-wrap search-panel-wrap">
    <form class="search-panel" id="job-search" action="#jobs" method="get">
        <div class="search-panel__intro"><span>Find your next role</span><strong>Search overseas jobs</strong></div>
        <div class="search-control"><span>Job category</span><div class="custom-select" data-custom-select><input type="hidden" id="job-category" value=""><button type="button" class="custom-select__trigger" aria-haspopup="listbox" aria-expanded="false"><span>All categories</span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 9.5 5 5 5-5 1.5 1.5-6.5 6.5L5.5 11 7 9.5Z"/></svg></button><div class="custom-select__menu" role="listbox"><button type="button" role="option" aria-selected="true" data-value="">All categories</button><button type="button" role="option" data-value="construction">Construction</button><button type="button" role="option" data-value="healthcare">Healthcare</button><button type="button" role="option" data-value="driving">Driving &amp; Logistics</button><button type="button" role="option" data-value="engineering">Engineering</button><button type="button" role="option" data-value="hospitality">Hospitality</button></div></div></div>
        <div class="search-control"><span>Destination</span><div class="custom-select" data-custom-select><input type="hidden" id="job-country" value=""><button type="button" class="custom-select__trigger" aria-haspopup="listbox" aria-expanded="false"><span>All destinations</span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 9.5 5 5 5-5 1.5 1.5-6.5 6.5L5.5 11 7 9.5Z"/></svg></button><div class="custom-select__menu" role="listbox"><button type="button" role="option" aria-selected="true" data-value="">All destinations</button><button type="button" role="option" data-value="uae">United Arab Emirates</button><button type="button" role="option" data-value="saudi-arabia">Saudi Arabia</button><button type="button" role="option" data-value="qatar">Qatar</button><button type="button" role="option" data-value="kuwait">Kuwait</button></div></div></div>
        <label class="search-control"><span>Keyword</span><input id="job-keyword" type="search" placeholder="Trade, role or skill"></label>
        <button type="submit" class="btn btn-accent"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15.5 14h-.8l-.3-.3A6.5 6.5 0 1 0 14 15.5l.3.3v.8l5 5 1.5-1.5-5-5Zm-6 0A4.5 4.5 0 1 1 14 9.5 4.5 4.5 0 0 1 9.5 14Z"/></svg>Search Jobs</button>
    </form>
</div>

<section class="confidence-bar" aria-label="Our recruitment approach">
    <div class="site-wrap confidence-bar__grid">
        <div><span class="confidence-bar__icon"><svg viewBox="0 0 24 24"><path d="M12 2 4 5v6c0 5.1 3.4 9.9 8 11 4.6-1.1 8-5.9 8-11V5l-8-3Zm-1 14-4-4 1.4-1.4 2.6 2.6 4.6-4.6L17 10l-6 6Z"/></svg></span><span><strong>Responsible recruitment</strong><small>Clear steps and honest communication</small></span></div>
        <div><span class="confidence-bar__icon"><svg viewBox="0 0 24 24"><path d="M20 6h-4V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2H4a2 2 0 0 0-2 2v11h20V8a2 2 0 0 0-2-2ZM10 4h4v2h-4V4Zm10 13H4v-5h6v1h4v-1h6v5Z"/></svg></span><span><strong>Employer-led vacancies</strong><small>Roles based on genuine manpower needs</small></span></div>
        <div><span class="confidence-bar__icon"><svg viewBox="0 0 24 24"><path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z"/></svg></span><span><strong>Destination guidance</strong><small>Support before your departure</small></span></div>
    </div>
</section>

<section class="impact-stats" aria-labelledby="impact-stats-title">
    <div class="site-wrap">
        <div class="impact-stats__heading">
            <div><span class="section-kicker">Our growing impact</span><h2 id="impact-stats-title">Progress you can measure</h2></div>
            <p>Supporting candidates, employers and skilled professionals through every stage of overseas recruitment.</p>
        </div>
        @php
            $impactStats = [
                ['3,500', '+', 'Visas processed', 'Candidates supported through successful visa processing.'],
                ['1,250', '+', 'Active registered cases', 'Candidate applications currently moving through the process.'],
                ['180', '+', 'Companies registered', 'Employers connected with our recruitment network.'],
                ['45', '+', 'Skilled trades', 'Professional and technical trade categories supported.'],
                ['25', '+', 'Universities on board', 'Education partners connected with our talent network.'],
            ];
        @endphp
        <div class="impact-stats__grid">
            @foreach ($impactStats as [$number, $suffix, $label, $description])
                <article class="impact-stat">
                    <strong><span data-count="{{ str_replace(',', '', $number) }}">{{ $number }}</span>{{ $suffix }}</strong>
                    <h3>{{ $label }}</h3>
                    <p>{{ $description }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="agency-section" id="services">
    <div class="site-wrap">
        <div class="section-heading section-heading--center">
            <span class="section-kicker">Candidate services</span>
            <h2>Everything you need to move forward</h2>
            <p>From your first application to departure preparation, our team helps you understand what comes next.</p>
        </div>
        <div class="service-grid">
            @php
                $services = [
                    ['profile', 'Profile Registration', 'Build one complete candidate profile for current and future opportunities.', route('client-portal.register')],
                    ['match', 'Job Matching', 'Find roles aligned with your trade, experience and preferred destination.', '#jobs'],
                    ['docs', 'Document Support', 'Prepare employment documents with clear guidance at every stage.', '#process'],
                    ['travel', 'Pre-Departure Guidance', 'Understand your travel steps and arrive prepared for your new role.', '#contact'],
                ];
            @endphp
            @foreach ($services as [$icon, $title, $text, $link])
                <a class="service-card" href="{{ $link }}">
                    <span class="service-card__icon service-card__icon--{{ $icon }}">
                        @if ($icon === 'profile')<svg viewBox="0 0 24 24"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0 2c-4.4 0-8 2-8 4.5V21h16v-2.5C20 16 16.4 14 12 14Z"/></svg>@endif
                        @if ($icon === 'match')<svg viewBox="0 0 24 24"><path d="M20 6h-4V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2H4a2 2 0 0 0-2 2v11h20V8a2 2 0 0 0-2-2ZM10 4h4v2h-4V4Zm10 13H4v-5h6v1h4v-1h6v5Z"/></svg>@endif
                        @if ($icon === 'docs')<svg viewBox="0 0 24 24"><path d="M14 2H5a2 2 0 0 0-2 2v16h18V9l-7-7Zm1 4.4L18.6 10H15V6.4ZM7 13h10v2H7v-2Zm0 4h10v2H7v-2Z"/></svg>@endif
                        @if ($icon === 'travel')<svg viewBox="0 0 24 24"><path d="m21 16-8-5V5.5a1.5 1.5 0 0 0-3 0V11l-8 5v2l8-2.5V20l-2 1.5V23l3.5-1 3.5 1v-1.5L13 20v-4.5l8 2.5v-2Z"/></svg>@endif
                    </span>
                    <span class="service-card__number">0{{ $loop->iteration }}</span>
                    <h3>{{ $title }}</h3><p>{{ $text }}</p><span class="service-card__link">Learn more &rarr;</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="agency-section agency-section--soft" id="countries">
    <div class="site-wrap">
        <div class="section-heading section-heading--split"><div><span class="section-kicker">Global opportunities</span><h2>Popular countries</h2></div><p>Explore countries recruiting international talent across the Gulf region and Europe.</p></div>
        <div class="destination-grid" data-destination-slider aria-label="Popular destination cards">
            @foreach ($destinations as $destination)
                @include('website.partials.country-card', ['destination' => $destination])
            @endforeach
        </div>
    </div>
</section>

<section class="agency-section process-section" id="process">
    <div class="site-wrap process-layout">
        <div class="process-copy"><span class="section-kicker">How it works</span><h2>A straightforward path to overseas employment</h2><p>We keep candidates informed through every stage, so you know what is required and when.</p><a href="{{ route('client-portal.register') }}" class="btn btn-navy">Start your application</a></div>
        <ol class="process-steps">
            <li><span>01</span><div><strong>Create your profile</strong><p>Share your skills, experience and documents in one secure application.</p></div></li>
            <li><span>02</span><div><strong>Get matched to a role</strong><p>Our team reviews suitable opportunities against employer requirements.</p></div></li>
            <li><span>03</span><div><strong>Complete the process</strong><p>Follow the required screening, documentation and employment steps.</p></div></li>
            <li><span>04</span><div><strong>Prepare for departure</strong><p>Receive practical guidance before beginning your overseas assignment.</p></div></li>
        </ol>
    </div>
</section>

<section class="agency-section agency-section--jobs" id="jobs">
    <div class="site-wrap">
        <div class="section-heading section-heading--split"><div><span class="section-kicker">Featured jobs</span><h2>Latest Overseas Vacancies</h2></div><a href="{{ route('client-portal.register') }}" class="text-link">View all jobs <span>&rarr;</span></a></div>
        <div class="vacancy-layout">
            <aside class="vacancy-filter">
                <strong>Filter Jobs</strong>
                <label>Country<select id="filter-country"><option value="">All countries</option><option value="uae">United Arab Emirates</option><option value="saudi-arabia">Saudi Arabia</option><option value="qatar">Qatar</option><option value="kuwait">Kuwait</option><option value="oman">Oman</option></select></label>
                <label>Category<select id="filter-category"><option value="">All categories</option><option value="construction">Construction</option><option value="healthcare">Healthcare</option><option value="driving">Driving</option><option value="engineering">Engineering</option><option value="hospitality">Hospitality</option></select></label>
                <label>Experience<select id="filter-experience"><option value="">All experience</option><option value="junior">1–2 years</option><option value="mid">3 years</option><option value="senior">4+ years</option></select></label>
                <label>Employment Type<select id="filter-type"><option value="">All types</option><option value="full-time">Full time</option><option value="contract">Contract</option></select></label>
                <button type="button" class="btn btn-navy" id="apply-filters">Apply filters</button><button type="button" class="filter-reset" id="reset-filters">Clear selection</button>
            </aside>
            <div class="vacancy-grid" id="jobs-grid">
                @foreach ($jobs as $job)
                    @include('website.partials.job-card', ['job' => $job])
                @endforeach
                <p class="jobs-empty" id="jobs-empty" hidden>No sample vacancies match your selection.</p>
            </div>
        </div>
    </div>
</section>

<section class="agency-section team-section" aria-labelledby="team-title">
    <div class="site-wrap">
        <div class="section-heading section-heading--center">
            <span class="section-kicker">Our Team</span>
            <h2 id="team-title">Meet our team of professionals</h2>
            <p>Experienced people working together to guide candidates and employers through every stage of overseas recruitment.</p>
        </div>
        @php
            $teamMembers = [
                ['team-director.webp', 'Recruitment Director', 'Leading responsible recruitment strategies and international workforce partnerships.'],
                ['team-talent-lead.webp', 'Talent Acquisition Lead', 'Connecting skilled professionals with roles that match their experience and ambitions.'],
                ['team-employer-relations.webp', 'Employer Relations Manager', 'Building trusted relationships and translating workforce needs into clear hiring plans.'],
                ['team-candidate-success.webp', 'Candidate Success Manager', 'Supporting candidates with clear guidance from registration through deployment.'],
            ];
        @endphp
        <div class="team-grid">
            @foreach ($teamMembers as [$image, $role, $description])
                <article class="team-card">
                    <div class="team-card__photo">
                        <img src="{{ asset('images/website/' . $image) }}" alt="{{ $role }}" width="900" height="1125" loading="lazy">
                        <span aria-hidden="true">0{{ $loop->iteration }}</span>
                    </div>
                    <div class="team-card__content">
                        <span>Federal Manpower</span>
                        <h3>{{ $role }}</h3>
                        <p>{{ $description }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="employer-cta" id="employers">
    <div class="site-wrap employer-cta__inner"><div><span class="section-kicker">For overseas employers</span><h2>Build the workforce your business needs.</h2><p>Share your manpower requirements with our recruitment team and connect with suitable skilled candidates.</p></div><a href="#contact" class="btn btn-light">Discuss Your Requirements <span>&rarr;</span></a></div>
</section>

<section class="agency-about" id="about"><div class="site-wrap agency-about__inner"><div><span class="section-kicker">About Federal Manpower</span><h2>People, preparation and responsible placement.</h2></div><p>We support professionals seeking overseas careers and employers looking for capable manpower. Our approach focuses on clear communication, proper documentation and practical guidance throughout the recruitment journey.</p></div></section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const hero = document.querySelector('[data-hero-slider]');
    if (hero) {
        const slides = [...hero.querySelectorAll('.agency-hero__slide')];
        const dots = [...hero.querySelectorAll('[data-slide-to]')];
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        let currentSlide = 0;
        let autoplay;

        const showSlide = index => {
            currentSlide = (index + slides.length) % slides.length;
            slides.forEach((slide, slideIndex) => {
                const active = slideIndex === currentSlide;
                slide.classList.toggle('is-active', active);
                slide.setAttribute('aria-hidden', String(!active));
            });
            dots.forEach((dot, dotIndex) => {
                const active = dotIndex === currentSlide;
                dot.classList.toggle('is-active', active);
                dot.setAttribute('aria-current', String(active));
            });
        };
        const stopAutoplay = () => window.clearInterval(autoplay);
        const startAutoplay = () => {
            stopAutoplay();
            if (!reduceMotion) autoplay = window.setInterval(() => showSlide(currentSlide + 1), 6500);
        };

        hero.querySelector('[data-slide-prev]').addEventListener('click', () => { showSlide(currentSlide - 1); startAutoplay(); });
        hero.querySelector('[data-slide-next]').addEventListener('click', () => { showSlide(currentSlide + 1); startAutoplay(); });
        dots.forEach(dot => dot.addEventListener('click', () => { showSlide(Number(dot.dataset.slideTo)); startAutoplay(); }));
        hero.addEventListener('mouseenter', stopAutoplay);
        hero.addEventListener('mouseleave', startAutoplay);
        hero.addEventListener('focusin', stopAutoplay);
        hero.addEventListener('focusout', event => { if (!hero.contains(event.relatedTarget)) startAutoplay(); });
        startAutoplay();
    }

    const destinationSlider = document.querySelector('[data-destination-slider]');
    if (destinationSlider) {
        let dragging = false;
        let dragged = false;
        let startX = 0;
        let startScroll = 0;

        destinationSlider.addEventListener('pointerdown', event => {
            if (event.pointerType === 'mouse' && event.button !== 0) return;
            dragging = true;
            dragged = false;
            startX = event.clientX;
            startScroll = destinationSlider.scrollLeft;
            destinationSlider.classList.add('is-dragging');
            destinationSlider.setPointerCapture(event.pointerId);
        });
        destinationSlider.addEventListener('pointermove', event => {
            if (!dragging) return;
            const distance = event.clientX - startX;
            if (Math.abs(distance) > 5) dragged = true;
            destinationSlider.scrollLeft = startScroll - distance;
        });
        const endDrag = event => {
            if (!dragging) return;
            dragging = false;
            destinationSlider.classList.remove('is-dragging');
            if (destinationSlider.hasPointerCapture(event.pointerId)) destinationSlider.releasePointerCapture(event.pointerId);
        };
        destinationSlider.addEventListener('pointerup', endDrag);
        destinationSlider.addEventListener('pointercancel', endDrag);
        destinationSlider.addEventListener('click', event => {
            if (dragged) {
                event.preventDefault();
                event.stopPropagation();
                dragged = false;
            }
        }, true);
    }

    const closeSelects = except => document.querySelectorAll('[data-custom-select].is-open').forEach(select => {
        if (select !== except) {
            select.classList.remove('is-open');
            select.querySelector('.custom-select__trigger').setAttribute('aria-expanded', 'false');
        }
    });
    document.querySelectorAll('[data-custom-select]').forEach(select => {
        const input = select.querySelector('input[type="hidden"]');
        const trigger = select.querySelector('.custom-select__trigger');
        const triggerText = trigger.querySelector('span');
        const options = [...select.querySelectorAll('[role="option"]')];
        trigger.addEventListener('click', event => {
            event.stopPropagation();
            const willOpen = !select.classList.contains('is-open');
            closeSelects(select);
            select.classList.toggle('is-open', willOpen);
            trigger.setAttribute('aria-expanded', String(willOpen));
        });
        options.forEach(option => option.addEventListener('click', () => {
            input.value = option.dataset.value;
            triggerText.textContent = option.textContent;
            options.forEach(item => item.setAttribute('aria-selected', String(item === option)));
            select.classList.remove('is-open');
            trigger.setAttribute('aria-expanded', 'false');
            trigger.focus();
        }));
    });
    document.addEventListener('click', () => closeSelects());
    document.addEventListener('keydown', event => { if (event.key === 'Escape') closeSelects(); });

    const cards = [...document.querySelectorAll('.vacancy-card')];
    const empty = document.getElementById('jobs-empty');
    const filterJobs = (criteria = {}) => {
        let shown = 0;
        cards.forEach(card => {
            const match = (!criteria.country || card.dataset.country === criteria.country)
                && (!criteria.category || card.dataset.category === criteria.category)
                && (!criteria.experience || card.dataset.experience === criteria.experience)
                && (!criteria.type || card.dataset.type === criteria.type)
                && (!criteria.keyword || card.dataset.title.includes(criteria.keyword));
            card.hidden = !match;
            if (match) shown++;
        });
        empty.hidden = shown !== 0;
    };
    document.getElementById('apply-filters')?.addEventListener('click', () => filterJobs({country: document.getElementById('filter-country').value, category: document.getElementById('filter-category').value, experience: document.getElementById('filter-experience').value, type: document.getElementById('filter-type').value}));
    document.getElementById('reset-filters')?.addEventListener('click', () => { document.getElementById('filter-country').value = ''; document.getElementById('filter-category').value = ''; document.getElementById('filter-experience').value = ''; document.getElementById('filter-type').value = ''; document.getElementById('job-keyword').value = ''; filterJobs(); });
    document.getElementById('job-search')?.addEventListener('submit', event => {
        event.preventDefault();
        const criteria = {country: document.getElementById('job-country').value, category: document.getElementById('job-category').value, keyword: document.getElementById('job-keyword').value.trim().toLowerCase()};
        document.getElementById('filter-country').value = criteria.country;
        document.getElementById('filter-category').value = criteria.category;
        filterJobs(criteria);
        document.getElementById('jobs').scrollIntoView({behavior: 'smooth'});
    });
    document.querySelectorAll('.vacancy-card__save').forEach(button => button.addEventListener('click', () => { button.classList.toggle('is-saved'); button.textContent = button.classList.contains('is-saved') ? '♥' : '♡'; }));
});
</script>
@endpush
