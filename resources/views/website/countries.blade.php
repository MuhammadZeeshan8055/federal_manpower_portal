@extends('layouts.website')

@section('title', 'Countries')

@section('content')
<section class="page-hero"><div class="site-wrap page-hero__inner"><span class="section-kicker">Global opportunities</span><h1>Destinations we recruit for.</h1><p>Browse popular countries. Live vacancies on this site come from approved employer posts for those destinations.</p></div></section>
<section class="page-section page-section--soft"><div class="site-wrap"><div class="section-heading section-heading--split"><div><span class="section-kicker">Explore countries</span><h2>Popular recruitment destinations</h2></div><p>Open Jobs to filter by country, or apply online with your preferred destination in bio data.</p></div><div class="countries-page-grid">@foreach ($destinations as $destination) @include('website.partials.country-card', ['destination' => $destination]) @endforeach</div></div></section>
@endsection
