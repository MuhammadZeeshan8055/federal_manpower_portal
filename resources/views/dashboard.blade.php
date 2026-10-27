@extends('layouts.portal')

@section('title', 'Operations Overview')

@section('content')

{{-- HOME: module cards --}}
<div class="dashboard-view" x-show="screen === 'home'">
    <section class="hero-card">
        <div class="hero-card__orb hero-card__orb--one"></div>
        <div class="hero-card__orb hero-card__orb--two"></div>
        <div class="hero-card__content">
            <p class="eyebrow"><span></span> FEDERAL MANPOWER OPERATIONS CENTER</p>
            <h2>Welcome back, {{ auth()->user()->name }}</h2>
        </div>
        <div class="hero-card__workday">
            <livewire:attendance.workday-card />
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

    {{-- Numbers for the open module (Alpine tells Livewire which module via refresh-module-stats) --}}
    <livewire:module-summary-stats :module-key="$startModule" />

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

    <div x-show="moduleKey === 'operations' && featureKey === 'companies'">
        <livewire:operations.manage-companies />
    </div>

    <div x-show="moduleKey === 'operations' && featureKey === 'universities'">
        <livewire:operations.manage-universities />
    </div>

    <div x-show="moduleKey === 'operations' && featureKey === 'care_offs'">
        <livewire:operations.manage-care-offs />
    </div>

    <div x-show="moduleKey === 'operations' && featureKey === 'trades'">
        <livewire:operations.manage-trades />
    </div>

    <div x-show="moduleKey === 'operations' && featureKey === 'process_statuses'">
        <livewire:operations.manage-process-statuses />
    </div>

    <div x-show="moduleKey === 'clients' && featureKey === 'create'">
        <livewire:clients.client-bio-data-form />
    </div>

    <div x-show="moduleKey === 'clients' && featureKey === 'list'">
        <livewire:clients.list-clients />
    </div>

    <div x-show="moduleKey === 'clients' && featureKey === 'batches'">
        <livewire:clients.manage-batches />
    </div>

    <div x-show="moduleKey === 'clients' && featureKey === 'documents'">
        <livewire:clients.list-client-documents />
    </div>

    <div x-show="moduleKey === 'visitors' && featureKey === 'list'">
        <livewire:visitors.manage-visitors />
    </div>

    <div x-show="moduleKey === 'visitors' && featureKey === 'reminders'">
        <livewire:visitors.manage-reminders />
    </div>

    <div x-show="moduleKey === 'attendance' && featureKey === 'office-settings'">
        <livewire:attendance.office-settings />
    </div>

    <div x-show="moduleKey === 'attendance' && featureKey === 'my-daily-attendance'">
        <livewire:attendance.my-daily-attendance />
    </div>

    <div x-show="moduleKey === 'attendance' && featureKey === 'my-loans'">
        <livewire:attendance.my-staff-loans />
    </div>

    <div x-show="moduleKey === 'attendance' && featureKey === 'staff-daily-attendance'">
        <livewire:attendance.staff-daily-attendance />
    </div>

    <div x-show="moduleKey === 'attendance' && featureKey === 'apply-leave'">
        <livewire:attendance.apply-leave />
    </div>

    <div x-show="moduleKey === 'attendance' && featureKey === 'leave-approvals'">
        <livewire:attendance.leave-approvals />
    </div>

    <div x-show="moduleKey === 'attendance' && featureKey === 'holidays'">
        <livewire:attendance.manage-holidays />
    </div>

    <div x-show="moduleKey === 'attendance' && featureKey === 'salary-slips'">
        <livewire:attendance.manage-salary-slips />
    </div>

    <div x-show="moduleKey === 'attendance' && featureKey === 'staff-loans'">
        <livewire:attendance.manage-staff-loans />
    </div>

    <div x-show="moduleKey === 'accounts' && featureKey === 'payment_methods'">
        <livewire:accounts.manage-payment-methods />
    </div>

    <div x-show="moduleKey === 'accounts' && featureKey === 'payments'">
        <livewire:accounts.manage-payments wire:key="accounts-payments-screen" />
    </div>

    <div x-show="moduleKey === 'accounts' && featureKey === 'protector_payments'">
        <livewire:accounts.manage-protector-payments wire:key="accounts-protector-payments-screen" />
    </div>

    <div x-show="moduleKey === 'accounts' && featureKey === 'reports'">
        <livewire:accounts.accounts-reports />
    </div>

    <div x-show="moduleKey === 'employers' && featureKey === 'approvals'">
        <livewire:employers.manage-employer-approvals />
    </div>

    <div x-show="moduleKey === 'employers' && featureKey === 'job-approvals'">
        <livewire:employers.manage-job-approvals />
    </div>

    <div x-show="moduleKey === 'settings' && featureKey === 'users'">
        <livewire:settings.manage-users />
    </div>

    <div
        class="module-placeholder__hero"
        x-show="!(moduleKey === 'operations' && ['countries', 'companies', 'universities', 'care_offs', 'trades', 'process_statuses'].includes(featureKey)) && !(moduleKey === 'clients' && ['create', 'list', 'batches', 'documents'].includes(featureKey)) && !(moduleKey === 'visitors' && ['list', 'reminders'].includes(featureKey)) && !(moduleKey === 'attendance' && ['office-settings', 'my-daily-attendance', 'my-loans', 'staff-daily-attendance', 'apply-leave', 'leave-approvals', 'holidays', 'salary-slips', 'staff-loans'].includes(featureKey)) && !(moduleKey === 'accounts' && ['payments', 'protector_payments', 'payment_methods', 'reports'].includes(featureKey)) && !(moduleKey === 'employers' && ['approvals', 'job-approvals'].includes(featureKey)) && !(moduleKey === 'settings' && featureKey === 'users')"
    >
        <p>This screen will hold the real tools for this feature. Next steps will add forms and tables here.</p>
        <p class="muted">
            Module: <strong x-text="moduleKey"></strong>
            · Feature: <strong x-text="featureKey"></strong>
        </p>
    </div>
</div>

@endsection
