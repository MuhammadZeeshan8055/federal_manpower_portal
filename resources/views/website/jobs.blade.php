@extends('layouts.website')

@section('title', 'Overseas Jobs')

@section('content')
<section class="page-hero"><div class="site-wrap page-hero__inner"><span class="section-kicker">Current opportunities</span><h1>Find your next overseas role.</h1><p>Explore sample opportunities across high-demand industries and register your profile for suitable openings.</p></div></section>
<section class="page-section page-section--soft"><div class="site-wrap"><div class="section-heading section-heading--split"><div><span class="section-kicker">Featured jobs</span><h2>Latest Overseas Vacancies</h2></div><a href="{{ route('client-portal.register') }}" class="text-link">Register for job alerts <span>&rarr;</span></a></div><div class="vacancy-grid page-vacancy-grid">@foreach ($jobs as $job) @include('website.partials.job-card', ['job' => $job]) @endforeach</div></div></section>
<section class="page-cta"><div class="site-wrap page-cta__inner"><div><span class="section-kicker">Don’t see your role?</span><h2>Create your profile for future vacancies.</h2></div><a href="{{ route('client-portal.register') }}" class="btn btn-accent">Register now <span>&rarr;</span></a></div></section>
@endsection

@push('scripts')
<script>document.querySelectorAll('.vacancy-card__save').forEach(button => button.addEventListener('click', () => { button.classList.toggle('is-saved'); button.textContent = button.classList.contains('is-saved') ? '♥' : '♡'; }));</script>
@endpush
