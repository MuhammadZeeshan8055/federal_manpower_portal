@php
    [$visual, $title, $category, $country, $countryName, $experience, $experienceLevel, $type, $typeSlug, $salary, $badge, $image, $jobId] = $job;
@endphp
<article class="vacancy-card" data-title="{{ strtolower($title) }}" data-category="{{ $category }}" data-country="{{ $country }}" data-experience="{{ $experienceLevel }}" data-type="{{ $typeSlug }}">
    <div class="vacancy-card__visual vacancy-card__visual--{{ $visual }}" style="background-image:url('{{ asset($image) }}')">
        <span class="vacancy-card__badge">{{ $badge }}</span>
        <button type="button" class="vacancy-card__save" aria-label="Save {{ $title }}">&#9825;</button>
    </div>
    <div class="vacancy-card__body">
        <span class="vacancy-card__type">{{ \Illuminate\Support\Str::headline($category) }}</span>
        <h3>{{ $title }}</h3>
        <ul><li>{{ $countryName }}</li><li>{{ $experience }} experience</li></ul>
        <p><span>{{ $type }}</span><strong>{{ $salary }}</strong></p>
        <a href="{{ route('client-portal.apply', $jobId) }}" class="btn btn-accent">Apply now <span>&rarr;</span></a>
    </div>
</article>
