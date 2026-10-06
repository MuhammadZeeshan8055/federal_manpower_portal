@extends('layouts.portal')

@section('title', 'Operations Overview')

@section('content')

{{-- HOME: module cards --}}
<div class="dashboard-view" x-show="screen === 'home'">
    <section class="hero-card">
        <div class="hero-card__orb hero-card__orb--one"></div>
        <div class="hero-card__orb hero-card__orb--two"></div>
        <div class="hero-card__content">
            <p class="eyebrow"><span></span> MANPOWER OPERATIONS CENTER</p>
            <h2>Welcome back, {{ auth()->user()->name }}</h2>
            <!-- <p>Open a module below to manage clients, care offs, accounts, attendance, and more.</p> -->
        </div>
    </section>

    <div class="module-grid">
        @forelse ($modules as $module)
            <button
                type="button"
                class="module-card"
                @click="openModule('{{ $module['key'] }}')"
            >
                <span class="module-card__top">
                    <span class="module-card__icon">
                        @include('partials.icon', ['name' => $module['icon'] ?? 'grid'])
                    </span>
                </span>
                <span class="module-card__body">
                    <span class="module-card__title">{{ $module['title'] }}</span>
                    <span class="module-card__desc">{{ $module['description'] }}</span>
                </span>
                <span class="module-card__footer">
                    <span class="module-card__arrow" aria-hidden="true">
                        @include('partials.icon', ['name' => 'arrow'])
                    </span>
                </span>
            </button>
        @empty
            <article class="panel empty-card">
                <h3>No modules available</h3>
                <p>Your account has no feature permissions yet. Ask an admin to grant access.</p>
            </article>
        @endforelse
    </div>
</div>

{{-- MODULE: dashboard space reserved for future summaries and charts --}}
<div class="dashboard-view module-workspace" x-show="screen === 'module'" x-cloak>
    <section class="module-workspace__hero">
        <button type="button" class="text-button back-button" @click="goHome()">
            <span aria-hidden="true">&larr;</span> Back to overview
        </button>
        <p class="eyebrow"><span></span> MODULE</p>
        <h2 x-text="moduleTitle"></h2>
        <p x-text="moduleDescription"></p>
    </section>

    <section class="module-summary" aria-label="Module record totals">
        <template x-for="stat in (statsByModule[moduleKey] || [])" :key="stat.key">
            <article class="module-summary__card">
                <span class="module-summary__icon">
                    @include('partials.icon', ['name' => 'chart'])
                </span>
                <span class="module-summary__copy">
                    <small x-text="stat.label"></small>
                    <strong x-text="Number(stat.value).toLocaleString()"></strong>
                    <span>listed</span>
                </span>
            </article>
        </template>
    </section>

</div>

{{-- FEATURE screens --}}
<div class="dashboard-view" x-show="screen === 'feature'" x-cloak>
    <section class="feature-page-head">
        <button type="button" class="text-button back-button" @click="goModule()">
            <span aria-hidden="true">&larr;</span> Back to module
        </button>
        <p class="eyebrow"><span></span> FEATURE</p>
        <h2 x-text="featureTitle"></h2>
    </section>

    <div x-show="moduleKey === 'operations' && featureKey === 'countries'">
        <livewire:operations.manage-countries />
    </div>

    <div
        class="module-placeholder__hero"
        x-show="!(moduleKey === 'operations' && featureKey === 'countries')"
    >
        <p>This screen will hold the real tools for this feature. Next steps will add forms and tables here.</p>
        <p class="muted">
            Module: <strong x-text="moduleKey"></strong>
            · Feature: <strong x-text="featureKey"></strong>
        </p>
    </div>
</div>

@endsection
