@extends('layouts.website')

@section('title', 'About Us')

@section('content')
<section class="page-hero">
    <div class="site-wrap page-hero__inner">
        <span class="section-kicker">About Federal Manpower</span>
        <h1>Federal Manpower Promoter.</h1>
        <p>Overseas Employment Promoters · Licence No. OP&amp;HRD/4749/RWP — working under the Federal Group of Companies.</p>
    </div>
</section>

<section class="page-section">
    <div class="site-wrap split-content">
        <div>
            <span class="section-kicker">Who we are</span>
            <h2>A foundation stone in overseas employment promotion</h2>
        </div>
        <div class="prose">
            <p>Federal Manpower Promoter (FMP) works under the umbrella of the Federal Group of Companies. We are a manpower exporting company specialised in supplying unskilled, semi-skilled, highly technical and professional manpower overseas.</p>
            <p>For more than seventeen years we have served Pakistan through overseas employment promotion. A large number of people have trusted our active, disciplined service — not only for profit, but with a passion for serving our nation.</p>
            <p>Today candidates can also apply online and employers can post approved vacancies, while our office staff continue to guide selection, documentation and process stages.</p>
        </div>
    </div>
</section>

<section class="page-section page-section--soft">
    <div class="site-wrap">
        <div class="section-heading section-heading--center">
            <span class="section-kicker">Trust &amp; licence</span>
            <h2>Licensed overseas employment promoters</h2>
            <p>We operate as a trusted licence holder and strengthen people by placing them in proper overseas job roles.</p>
        </div>
        <div class="info-card-grid">
            <article class="info-card">
                <span>01</span>
                <h3>Licence details</h3>
                <p>Overseas Employment Promoters · Licence No. OP&amp;HRD/4749/RWP.</p>
            </article>
            <article class="info-card">
                <span>02</span>
                <h3>Federal Group</h3>
                <p>FMP works under the Federal Group of Companies with a long record in overseas placement.</p>
            </article>
            <article class="info-card">
                <span>03</span>
                <h3>Serving the nation</h3>
                <p>We focus on trustable, disciplined service and proper roles that strengthen careers abroad.</p>
            </article>
        </div>
    </div>
</section>

<section class="page-section">
    <div class="site-wrap split-content">
        <div>
            <span class="section-kicker">Mission</span>
            <h2>Mission statement</h2>
        </div>
        <div class="prose">
            <p>Extending unique services in manpower selection, head hunting and consultancy by understanding organisational needs and offering customised solutions.</p>
            <p>We deal across major destinations including the Middle East — UAE, Kuwait, Saudi Arabia — as well as markets such as Canada, Korea, Malaysia and more.</p>
        </div>
    </div>
</section>

<section class="page-section page-section--soft">
    <div class="site-wrap">
        <div class="section-heading section-heading--center">
            <span class="section-kicker">What guides us</span>
            <h2>Benefits of working with FMP</h2>
        </div>
        <div class="info-card-grid">
            <article class="info-card">
                <span>01</span>
                <h3>Proper job roles</h3>
                <p>We strengthen candidates by matching skills and experience to suitable overseas positions.</p>
            </article>
            <article class="info-card">
                <span>02</span>
                <h3>Trusted process</h3>
                <p>Licensed promoters with office staff guiding applications, documents and case stages.</p>
            </article>
            <article class="info-card">
                <span>03</span>
                <h3>Employer partnership</h3>
                <p>Close working relationships with recruitment partners who understand culture, values and business needs.</p>
            </article>
        </div>
    </div>
</section>

<section class="page-cta">
    <div class="site-wrap page-cta__inner">
        <div>
            <span class="section-kicker">Start your journey</span>
            <h2>Ready to apply or hire through FMP?</h2>
        </div>
        <div class="page-hero__actions">
            <a href="{{ route('client-portal.register') }}" class="btn btn-accent">Candidate register <span>&rarr;</span></a>
            <a href="{{ route('employer.register') }}" class="btn btn-light">Employer register</a>
        </div>
    </div>
</section>
@endsection
