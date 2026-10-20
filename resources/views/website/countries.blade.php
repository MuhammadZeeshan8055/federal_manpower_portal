@extends('layouts.website')

@section('title', 'Countries')

@section('content')
<section class="page-hero"><div class="site-wrap page-hero__inner"><span class="section-kicker">Global opportunities</span><h1>Countries where opportunity awaits.</h1><p>Explore destinations recruiting international talent across construction, healthcare, hospitality, logistics and skilled trades.</p></div></section>
<section class="page-section page-section--soft"><div class="site-wrap"><div class="section-heading section-heading--split"><div><span class="section-kicker">Explore countries</span><h2>Popular recruitment destinations</h2></div><p>Select a country to view relevant overseas opportunities.</p></div><div class="countries-page-grid">@foreach ($destinations as $destination) @include('website.partials.country-card', ['destination' => $destination]) @endforeach</div></div></section>
@endsection
