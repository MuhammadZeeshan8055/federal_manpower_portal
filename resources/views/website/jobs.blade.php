@extends('layouts.website')

@section('title', 'Overseas Jobs')

@section('content')
<section class="page-hero"><div class="site-wrap page-hero__inner"><span class="section-kicker">Current opportunities</span><h1>Approved overseas vacancies.</h1><p>These roles are posted by registered employers and activated by our office. Apply online through the candidate portal.</p></div></section>

<section class="page-section page-section--soft agency-section--jobs" id="jobs">
    <div class="site-wrap">
        <div class="section-heading section-heading--split">
            <div>
                <span class="section-kicker">Featured jobs</span>
                <h2>Latest overseas vacancies</h2>
            </div>
            <a href="{{ route('client-portal.register') }}" class="text-link">Register to apply <span>&rarr;</span></a>
        </div>

        <div class="vacancy-layout">
            <aside class="vacancy-filter">
                <strong>Filter Jobs</strong>
                <label>Country
                    <select id="filter-country">
                        <option value="">All countries</option>
                        @foreach ($jobFilterCountries as $slug => $label)
                            <option value="{{ $slug }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Category
                    <select id="filter-category">
                        <option value="">All categories</option>
                        @foreach ($jobFilterCategories as $slug => $label)
                            <option value="{{ $slug }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Experience
                    <select id="filter-experience">
                        <option value="">All experience</option>
                        @foreach ($jobFilterExperiences as $slug => $label)
                            <option value="{{ $slug }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Employment Type
                    <select id="filter-type">
                        <option value="">All types</option>
                        @foreach ($jobFilterTypes as $slug => $label)
                            <option value="{{ $slug }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <button type="button" class="btn btn-navy" id="apply-filters">Apply filters</button>
                <button type="button" class="filter-reset" id="reset-filters">Clear selection</button>
            </aside>

            <div class="vacancy-grid" id="jobs-grid">
                @forelse ($jobs as $job)
                    @include('website.partials.job-card', ['job' => $job])
                @empty
                    <p class="jobs-empty">No approved vacancies yet. Register your candidate profile so you are ready when new jobs go live.</p>
                @endforelse
                <p class="jobs-empty" id="jobs-empty" hidden>No vacancies match your selection.</p>
            </div>
        </div>
    </div>
</section>

<section class="page-cta"><div class="site-wrap page-cta__inner"><div><span class="section-kicker">Don’t see your role?</span><h2>Create your profile for future vacancies.</h2></div><a href="{{ route('client-portal.register') }}" class="btn btn-accent">Register now <span>&rarr;</span></a></div></section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const cards = [...document.querySelectorAll('.vacancy-card')];
    const empty = document.getElementById('jobs-empty');
    const filterJobs = (criteria = {}) => {
        let shown = 0;
        cards.forEach(card => {
            const match = (!criteria.country || card.dataset.country === criteria.country)
                && (!criteria.category || card.dataset.category === criteria.category)
                && (!criteria.experience || card.dataset.experience === criteria.experience)
                && (!criteria.type || card.dataset.type === criteria.type);
            card.hidden = !match;
            if (match) shown++;
        });
        if (empty) empty.hidden = shown !== 0 || cards.length === 0;
    };
    document.getElementById('apply-filters')?.addEventListener('click', () => filterJobs({
        country: document.getElementById('filter-country').value,
        category: document.getElementById('filter-category').value,
        experience: document.getElementById('filter-experience').value,
        type: document.getElementById('filter-type').value,
    }));
    document.getElementById('reset-filters')?.addEventListener('click', () => {
        document.getElementById('filter-country').value = '';
        document.getElementById('filter-category').value = '';
        document.getElementById('filter-experience').value = '';
        document.getElementById('filter-type').value = '';
        filterJobs();
    });
});
</script>
@endpush
