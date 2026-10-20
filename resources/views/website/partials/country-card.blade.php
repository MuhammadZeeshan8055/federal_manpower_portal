@php
    [$slug, $name, $flag, $sectors] = $destination;
@endphp
<a href="{{ route('website.jobs') }}" class="destination-card destination-card--{{ $slug }}">
    <span class="destination-card__photo" style="background-image:url('{{ asset('images/website/destination-cards-strip.png') }}')" role="img" aria-label="{{ $name }} skyline"></span>
    <span class="destination-card__flag"><img src="{{ asset('images/flags/'.$flag) }}" alt="" width="34" height="34"></span>
    <span class="destination-card__content"><strong>{{ $name }}</strong><small>{{ $sectors }}</small><span>View opportunities &rarr;</span></span>
</a>
