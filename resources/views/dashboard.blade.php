@extends('layouts.portal')

@section('title', 'Operations Overview')

@section('content')
<div x-show="activeSection === 'overview'" x-transition.opacity.duration.200ms>
    <section class="hero-card">
        <div class="hero-card__orb hero-card__orb--one"></div><div class="hero-card__orb hero-card__orb--two"></div>
        <div class="hero-card__content">
            <p class="eyebrow"><span></span> MANPOWER OPERATIONS CENTER</p>
            <h2>Good morning, Ahmed.</h2>
            <p>Everything you need to manage recruitment, workforce deployment, and client operations—beautifully organized in one place.</p>
            <div class="hero-card__actions">
                <button class="button button--light" @click="setSection('candidates')">@include('partials.icon', ['name' => 'plus']) Add candidate</button>
                <button class="button button--glass" @click="setSection('reports')">View reports @include('partials.icon', ['name' => 'arrow'])</button>
            </div>
        </div>
        <div class="hero-card__pulse">
            <div class="pulse-ring"><span>92<small>%</small></span></div>
            <div><strong>Deployment target</strong><small>On track this month</small></div>
        </div>
    </section>

    <section class="stats-grid">
        @php
            $stats = [
                ['Active candidates', '1,284', '+12.5%', 'users', 'blue'],
                ['Open positions', '48', '+6 this week', 'briefcase', 'green'],
                ['Deployed workforce', '856', '+8.2%', 'badge', 'yellow'],
                ['Active clients', '32', '3 new partners', 'building', 'mixed'],
            ];
        @endphp
        @foreach ($stats as [$label, $value, $trend, $icon, $tone])
        <article class="stat-card stat-card--{{ $tone }}">
            <div class="stat-card__top"><span class="stat-card__icon">@include('partials.icon', ['name' => $icon])</span><span class="trend-pill">@include('partials.icon', ['name' => 'trend']) {{ $trend }}</span></div>
            <strong class="stat-card__value">{{ $value }}</strong><span class="stat-card__label">{{ $label }}</span>
            <div class="stat-card__line"><span></span></div>
        </article>
        @endforeach
    </section>

    <section class="content-grid">
        <article class="panel panel--wide">
            <div class="panel__header"><div><p class="panel__eyebrow">RECRUITMENT ACTIVITY</p><h3>Hiring overview</h3></div><select><option>Last 6 months</option><option>This year</option></select></div>
            <div class="chart-area">
                <div class="chart-y"><span>120</span><span>90</span><span>60</span><span>30</span><span>0</span></div>
                <div class="chart-canvas">
                    <div class="chart-gridlines"></div>
                    <svg viewBox="0 0 620 210" preserveAspectRatio="none">
                        <defs><linearGradient id="areaBlue" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#1c7ab4" stop-opacity=".3"/><stop offset="1" stop-color="#1c7ab4" stop-opacity="0"/></linearGradient></defs>
                        <path class="chart-area-fill" d="M0,170 C70,155 105,105 165,120 S260,68 320,92 S415,50 475,70 S555,35 620,42 L620,210 L0,210Z"/>
                        <path class="chart-line" d="M0,170 C70,155 105,105 165,120 S260,68 320,92 S415,50 475,70 S555,35 620,42"/>
                        <circle cx="475" cy="70" r="5"/><circle cx="620" cy="42" r="5"/>
                    </svg>
                    <div class="chart-x"><span>May</span><span>Jun</span><span>Jul</span><span>Aug</span><span>Sep</span><span>Oct</span></div>
                </div>
            </div>
            <div class="chart-legend"><span><i class="dot dot--blue"></i> Applications</span><span><i class="dot dot--green"></i> Placements</span><strong>+18.4% <small>vs previous period</small></strong></div>
        </article>

        <article class="panel">
            <div class="panel__header"><div><p class="panel__eyebrow">PIPELINE</p><h3>Candidate stages</h3></div><button class="text-button" @click="setSection('candidates')">View all</button></div>
            <div class="pipeline">
                @foreach ([['New applications', 486, 78, 'blue'], ['Screening', 312, 57, 'green'], ['Interview', 184, 38, 'yellow'], ['Documentation', 96, 25, 'lime'], ['Ready to deploy', 72, 18, 'deep']] as [$name, $count, $width, $tone])
                <div class="pipeline-row"><div><span>{{ $name }}</span><strong>{{ $count }}</strong></div><div class="progress"><span class="progress--{{ $tone }}" style="width:{{ $width }}%"></span></div></div>
                @endforeach
            </div>
        </article>
    </section>

    <section class="content-grid content-grid--bottom">
        <article class="panel panel--wide">
            <div class="panel__header"><div><p class="panel__eyebrow">RECENT ACTIVITY</p><h3>Latest candidates</h3></div><button class="button button--soft" @click="setSection('candidates')">View all candidates</button></div>
            <div class="table-wrap"><table class="data-table"><thead><tr><th>Candidate</th><th>Position</th><th>Destination</th><th>Stage</th><th>Updated</th><th></th></tr></thead><tbody>
                @foreach ([
                    ['MU','Muhammad Usman','Electrician','Saudi Arabia','Screening','2 min ago','screening'],
                    ['AK','Ali Khan','HVAC Technician','UAE','Interview','18 min ago','interview'],
                    ['RS','Rashid Saleem','Welder','Qatar','Documentation','1 hr ago','documentation'],
                    ['HB','Hamza Bilal','Site Supervisor','Serbia','Ready','3 hrs ago','ready'],
                ] as [$initials,$name,$role,$country,$stage,$time,$status])
                <tr><td><div class="person"><span class="person__avatar">{{ $initials }}</span><div><strong>{{ $name }}</strong><small>ID #FM-{{ 2400 + $loop->index * 17 }}</small></div></div></td><td>{{ $role }}</td><td><span class="country-dot"></span>{{ $country }}</td><td><span class="status status--{{ $status }}">{{ $stage }}</span></td><td class="muted">{{ $time }}</td><td><button class="more-button">•••</button></td></tr>
                @endforeach
            </tbody></table></div>
        </article>
        <article class="panel quick-panel">
            <div class="panel__header"><div><p class="panel__eyebrow">QUICK ACTIONS</p><h3>Shortcuts</h3></div></div>
            @foreach ([['plus','Add new candidate','Create candidate profile','candidates'],['briefcase','Create job opening','Add a client requirement','recruitment'],['document','Document tracking','Review pending documents','documents'],['chart','Generate report','Export operational data','reports']] as [$icon,$title,$desc,$section])
                <button class="quick-action" @click="setSection('{{ $section }}')"><span>@include('partials.icon', ['name' => $icon])</span><div><strong>{{ $title }}</strong><small>{{ $desc }}</small></div><b>›</b></button>
            @endforeach
        </article>
    </section>
</div>

@foreach (['recruitment','candidates','workforce','clients','documents','reports','settings'] as $section)
<section class="module-placeholder" x-cloak x-show="activeSection === '{{ $section }}'" x-transition.opacity.duration.200ms>
    <div class="module-placeholder__hero">
        <p class="eyebrow"><span></span> FEDERAL MANPOWER PORTAL</p>
        <h2 x-text="sectionTitles['{{ $section }}'][0]"></h2>
        <p x-text="sectionTitles['{{ $section }}'][1]"></p>
        <button class="button button--light">@include('partials.icon', ['name' => 'plus']) Create new</button>
    </div>
    <div class="module-placeholder__grid">
        <article class="panel empty-card"><span>@include('partials.icon', ['name' => $section === 'reports' ? 'chart' : ($section === 'settings' ? 'settings' : 'document')])</span><h3>Design-ready workspace</h3><p>This screen is prepared for the {{ $section }} workflow. Backend functionality can be connected in the next phase.</p></article>
        <article class="panel"><div class="panel__header"><div><p class="panel__eyebrow">OVERVIEW</p><h3>Activity summary</h3></div></div><div class="skeleton-list"><i></i><i></i><i></i><i></i></div></article>
    </div>
</section>
@endforeach
@endsection

