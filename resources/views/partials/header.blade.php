@php
    $selectedCountry = null;
    foreach ($countries as $country) {
        if ((int) $workspaceCountryId === (int) $country->id) {
            $selectedCountry = $country;
            break;
        }
    }
@endphp

<header class="topbar">
    <div class="topbar__title-wrap">
        <button
            class="menu-button"
            type="button"
            @click="toggleSidebar()"
            :aria-label="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
            :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
            :aria-pressed="sidebarCollapsed.toString()"
        >
            @include('partials.icon', ['name' => 'menu'])
        </button>
        <div class="topbar__title">
            <p class="breadcrumb">
                Federal Manpower
                <span>/</span>
                <span x-text="screen === 'home' ? 'Workspace' : 'Module'"></span>
                <span>/</span>
                <span x-text="pageTitle()"></span>
            </p>
            <h1 class="page-title" x-text="pageTitle()"></h1>
        </div>
    </div>

    <div class="topbar__actions">
        <button
            type="button"
            class="theme-toggle"
            @click="toggleTheme()"
            :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
            :title="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
            :aria-pressed="darkMode.toString()"
        >
            <span class="theme-toggle__icon" x-show="!darkMode" x-cloak>
                @include('partials.icon', ['name' => 'moon'])
            </span>
            <span class="theme-toggle__icon" x-show="darkMode" x-cloak>
                @include('partials.icon', ['name' => 'sun'])
            </span>
            <span class="theme-toggle__label" x-text="darkMode ? 'Light' : 'Dark'"></span>
        </button>

        <div
            class="country-dd"
            x-data="{
                open: false,
                search: '',
                countryNames: @js($countries->pluck('name')->values()),
                showCountry(name) {
                    if (this.search === '') {
                        return true;
                    }
                    return name.toLowerCase().indexOf(this.search.toLowerCase()) !== -1;
                },
                hasResults() {
                    if (this.showCountry('All countries')) {
                        return true;
                    }
                    var i;
                    for (i = 0; i < this.countryNames.length; i++) {
                        if (this.showCountry(this.countryNames[i])) {
                            return true;
                        }
                    }
                    return false;
                }
            }"
            @click.outside="open = false; search = ''"
        >
            <button
                type="button"
                class="country-dd__button"
                @click="open = !open; if (open) { search = ''; $nextTick(() => { $refs.countrySearch.focus(); }); } else { search = ''; }"
                :aria-expanded="open.toString()"
            >
                <span class="country-dd__meta">
                    <small>Country</small>
                    <strong>
                        <img
                            class="country-dd__flag"
                            src="{{ $selectedCountry ? $selectedCountry->flagUrl() : asset('images/flags/world.svg') }}"
                            alt=""
                        >
                        <span class="country-dd__name">
                            {{ $selectedCountry ? $selectedCountry->name : 'All countries' }}
                        </span>
                    </strong>
                </span>
                <span class="country-dd__chevron" :class="{ 'country-dd__chevron--open': open }">▾</span>
            </button>

            <div class="country-dd__menu" x-cloak x-show="open" @click.stop>
                <div class="country-dd__search">
                    <input
                        type="search"
                        x-ref="countrySearch"
                        x-model="search"
                        placeholder="Search country..."
                        autocomplete="off"
                    >
                </div>

                <div class="country-dd__list">
                    <form
                        method="POST"
                        action="{{ route('workspace.country') }}"
                        x-show="showCountry('All countries')"
                    >
                        @csrf
                        <input type="hidden" name="country_id" value="">
                        <button type="submit" class="country-dd__item {{ empty($workspaceCountryId) ? 'country-dd__item--active' : '' }}">
                            <img class="country-dd__flag" src="{{ asset('images/flags/world.svg') }}" alt="">
                            <span>All countries</span>
                        </button>
                    </form>

                    @foreach ($countries as $country)
                        <form
                            method="POST"
                            action="{{ route('workspace.country') }}"
                            x-show="showCountry(@js($country->name))"
                        >
                            @csrf
                            <input type="hidden" name="country_id" value="{{ $country->id }}">
                            <button
                                type="submit"
                                class="country-dd__item {{ (int) $workspaceCountryId === (int) $country->id ? 'country-dd__item--active' : '' }}"
                            >
                                <img class="country-dd__flag" src="{{ $country->flagUrl() }}" alt="">
                                <span>{{ $country->name }}</span>
                            </button>
                        </form>
                    @endforeach

                    <p class="country-dd__empty" x-show="search !== '' && !hasResults()" x-cloak>
                        No country found
                    </p>
                </div>
            </div>
        </div>

        <div class="profile" @click.outside="profileOpen = false">
            <button class="profile__button" type="button" @click="profileOpen = !profileOpen">
                <span class="avatar avatar--header">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                <span class="profile__copy">
                    <strong>{{ auth()->user()->name }}</strong>
                    <small>{{ auth()->user()->roleLabel() }}</small>
                </span>
                <span class="profile__chevron">⌄</span>
            </button>
            <div class="profile-menu" x-cloak x-show="profileOpen">
                <a href="{{ route('profile.edit') }}">My profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Sign out</button>
                </form>
            </div>
        </div>
    </div>
</header>
