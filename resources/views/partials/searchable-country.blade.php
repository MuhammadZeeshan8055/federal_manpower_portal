@php
    $countriesJson = $countries->map(fn ($country) => [
        'id' => (string) $country->id,
        'name' => $country->name,
        'code' => $country->code,
        'flag' => $country->flagUrl(),
        'active' => (bool) $country->is_active,
    ])->values();
@endphp

<div
    class="search-select"
    wire:ignore
    x-data="{
        open: false,
        search: '',
        selectedId: @js((string) $selectedId),
        options: @js($countriesJson),
        labelFor(id) {
            if (! id) {
                return '';
            }
            var i;
            for (i = 0; i < this.options.length; i++) {
                if (this.options[i].id === String(id)) {
                    return this.options[i].name;
                }
            }
            return '';
        },
        selectedLabel() {
            var selected = this.selectedOption();
            return selected ? selected.name : '';
        },
        selectedOption() {
            for (var i = 0; i < this.options.length; i++) {
                if (this.options[i].id === String(this.selectedId)) {
                    return this.options[i];
                }
            }
            return null;
        },
        matches(name) {
            if (this.search === '') {
                return true;
            }
            return name.toLowerCase().indexOf(this.search.toLowerCase()) !== -1;
        },
        hasResults() {
            var i;
            for (i = 0; i < this.options.length; i++) {
                if (this.matches(this.options[i].name + ' ' + this.options[i].code)) {
                    return true;
                }
            }
            return false;
        },
        pick(id) {
            this.selectedId = String(id);
            $wire.set('country_id', String(id));
            this.open = false;
            this.search = '';
        },
        clear() {
            this.selectedId = '';
            $wire.set('country_id', '');
            this.open = false;
            this.search = '';
        }
    }"
    x-init="$wire.$watch('country_id', value => { selectedId = value ? String(value) : ''; })"
    @click.outside="open = false; search = ''"
>
    <button
        type="button"
        class="search-select__button"
        @click="open = !open; if (open) { search = ''; $nextTick(() => $refs.countrySearch.focus()); }"
        :aria-expanded="open.toString()"
    >
        <span class="search-select__leading">
            <span class="search-select__flag-wrap">
                <img
                    x-show="selectedOption()"
                    :src="selectedOption()?.flag"
                    alt=""
                    class="search-select__flag"
                >
                <span x-show="!selectedOption()" class="search-select__globe">
                    @include('partials.icon', ['name' => 'building'])
                </span>
            </span>
            <span class="search-select__value-wrap">
                <span class="search-select__value" x-text="selectedLabel() || 'Select country'" :class="{ 'search-select__value--placeholder': !selectedLabel() }"></span>
                <small x-text="selectedOption() ? selectedOption().code : options.length + ' countries available'"></small>
            </span>
        </span>
        <span class="search-select__chevron" :class="{ 'search-select__chevron--open': open }">&#9662;</span>
    </button>

    <div class="search-select__menu" x-cloak x-show="open" @click.stop>
        <div class="search-select__search">
            <span>@include('partials.icon', ['name' => 'search'])</span>
            <input
                type="search"
                x-ref="countrySearch"
                x-model="search"
                placeholder="Search country..."
                autocomplete="off"
            >
        </div>

        <div class="search-select__list">
            <button
                type="button"
                class="search-select__item search-select__item--muted"
                x-show="selectedId !== ''"
                @click="clear()"
            >
                <span class="search-select__clear-icon">&times;</span>
                <span>Clear selection</span>
            </button>

            <template x-for="country in options" :key="country.id">
                <button
                    type="button"
                    class="search-select__item"
                    x-show="matches(country.name + ' ' + country.code)"
                    :class="{ 'search-select__item--active': selectedId === country.id }"
                    @click="pick(country.id)"
                >
                    <img class="search-select__flag" :src="country.flag" alt="">
                    <span class="search-select__item-copy">
                        <strong x-text="country.name"></strong>
                        <small>
                            <span x-text="country.code"></span>
                            <span x-show="!country.active"> &middot; Inactive</span>
                        </small>
                    </span>
                    <span class="search-select__check" x-show="selectedId === country.id">
                        @include('partials.icon', ['name' => 'check'])
                    </span>
                </button>
            </template>

            <p class="search-select__empty" x-show="search !== '' && !hasResults()" x-cloak>
                No country found
            </p>
        </div>
    </div>
</div>
