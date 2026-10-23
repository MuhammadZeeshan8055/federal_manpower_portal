@extends('layouts.website')

@section('title', 'Our Services')

@section('content')
<section class="page-hero">
    <div class="site-wrap page-hero__inner">
        <span class="section-kicker">Our services</span>
        <h1>Recruitment, selection and overseas placement.</h1>
        <p>Federal Manpower Promoter offers full recruitment and selection support from Rawalpindi / Islamabad — plus online apply and employer job posting.</p>
    </div>
</section>

<section class="page-section">
    <div class="site-wrap">
        <div class="section-heading section-heading--center">
            <span class="section-kicker">Core services</span>
            <h2>How we support candidates and employers</h2>
        </div>
        <div class="services-page-grid">
            <article class="service-detail">
                <span>01</span>
                <h2>Overseas recruitment</h2>
                <p>We work closely with partners who understand our business, culture and values — representing FMP to clients that meet specific manpower needs.</p>
            </article>
            <article class="service-detail">
                <span>02</span>
                <h2>HR &amp; management consultancy</h2>
                <p>As a leading HR and management consultant based in Rawalpindi / Islamabad, we offer end-to-end recruitment and selection support.</p>
            </article>
            <article class="service-detail">
                <span>03</span>
                <h2>Head hunting &amp; selection</h2>
                <p>We comprehend organisational needs and offer customised solutions for the right unskilled, semi-skilled, technical and professional talent.</p>
            </article>
            <article class="service-detail">
                <span>04</span>
                <h2>Candidate portal</h2>
                <p>Register online, complete bio data, upload documents and apply to approved overseas vacancies anytime.</p>
            </article>
            <article class="service-detail">
                <span>05</span>
                <h2>Employer job posting</h2>
                <p>Companies register, get approved, post roles, and go live on this website after our office staff activate the vacancy.</p>
            </article>
            <article class="service-detail">
                <span>06</span>
                <h2>Process follow-up</h2>
                <p>Our office staff update case stages so candidates know where they stand — from applied through documentation and departure preparation.</p>
            </article>
        </div>
    </div>
</section>

<section class="page-section page-section--soft">
    <div class="site-wrap">
        <div class="section-heading section-heading--center">
            <span class="section-kicker">Manpower available</span>
            <h2>Categories we supply</h2>
            <p>We strengthen careers by placing people in proper roles across major trades and professions.</p>
        </div>
        <div class="info-card-grid info-card-grid--four">
            <article class="info-card"><span>01</span><h3>Construction &amp; technical</h3><p>Site and technical manpower for overseas projects.</p></article>
            <article class="info-card"><span>02</span><h3>Engineering staff</h3><p>Qualified engineering talent for industrial and project needs.</p></article>
            <article class="info-card"><span>03</span><h3>Medical &amp; para-medical</h3><p>Healthcare professionals for hospitals and clinics abroad.</p></article>
            <article class="info-card"><span>04</span><h3>Project management</h3><p>Supervisors and managers for project delivery teams.</p></article>
            <article class="info-card"><span>05</span><h3>Agriculture &amp; mechanical</h3><p>Industrial and agricultural manpower for production operations.</p></article>
            <article class="info-card"><span>06</span><h3>IT professionals</h3><p>Technology and support roles for modern employers.</p></article>
            <article class="info-card"><span>07</span><h3>Hotel &amp; hospitality</h3><p>Hotel and service staff for hospitality employers.</p></article>
            <article class="info-card"><span>08</span><h3>And more trades</h3><p>Browse live vacancies or register your profile for matching.</p></article>
        </div>
    </div>
</section>

<section class="page-section">
    <div class="site-wrap">
        <div class="section-heading section-heading--center">
            <span class="section-kicker">Industries we serve</span>
            <h2>Sectors that hire through FMP</h2>
        </div>
        <div class="info-card-grid info-card-grid--four">
            <article class="info-card"><span>01</span><h3>Construction</h3><p>Large-scale building and infrastructure manpower.</p></article>
            <article class="info-card"><span>02</span><h3>Hospitality</h3><p>Hotels, catering and guest service roles.</p></article>
            <article class="info-card"><span>03</span><h3>Healthcare</h3><p>Medical and support staffing abroad.</p></article>
            <article class="info-card"><span>04</span><h3>Food processing</h3><p>Production and processing industry staff.</p></article>
            <article class="info-card"><span>05</span><h3>IT industry</h3><p>Technology and digital workforce needs.</p></article>
            <article class="info-card"><span>06</span><h3>Oil, gas &amp; petroleum</h3><p>Energy-sector skilled and support manpower.</p></article>
            <article class="info-card"><span>07</span><h3>Other industries</h3><p>Additional sectors based on approved employer demand.</p></article>
            <article class="info-card"><span>08</span><h3>Our network</h3><p>Partners who understand our values help represent FMP to the right clients.</p></article>
        </div>
    </div>
</section>

<section class="page-cta">
    <div class="site-wrap page-cta__inner">
        <div>
            <span class="section-kicker">Find your role</span>
            <h2>Find your dream job anytime, anywhere.</h2>
        </div>
        <div class="page-hero__actions">
            <a href="{{ route('website.jobs') }}" class="btn btn-accent">Browse vacancies <span>&rarr;</span></a>
            <a href="{{ route('website.contact') }}" class="btn btn-light">Contact us</a>
        </div>
    </div>
</section>
@endsection
