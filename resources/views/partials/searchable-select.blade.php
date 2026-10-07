@php
    $wireModel = $wireModel ?? 'id';
    $selectedId = (string) ($selectedId ?? '');
    $placeholder = $placeholder ?? 'Select';
    $searchPlaceholder = $searchPlaceholder ?? 'Search...';
    $emptyText = $emptyText ?? 'No results';
    $optionsJson = collect($options)->map(fn ($item) => [
        'id' => (string) (is_array($item) ? $item['id'] : $item->id),
        'name' => (string) (is_array($item) ? $item['name'] : $item->name),
    ])->values();
@endphp

<div
    class="search-select search-select--simple"
    wire:ignore
    x-data="{
        open: false,
        search: '',
        selectedId: @js($selectedId),
        options: @js($optionsJson),
        wireModel: @js($wireModel),
        labelFor(id) {
            if (! id) {
                return '';
            }
            for (var i = 0; i < this.options.length; i++) {
                if (this.options[i].id === String(id)) {
                    return this.options[i].name;
                }
            }
            return '';
        },
        selectedLabel() {
            return this.labelFor(this.selectedId);
        },
        matches(name) {
            if (this.search === '') {
                return true;
            }
            return name.toLowerCase().indexOf(this.search.toLowerCase()) !== -1;
        },
        hasResults() {
            for (var i = 0; i < this.options.length; i++) {
                if (this.matches(this.options[i].name)) {
                    return true;
                }
            }
            return false;
        },
        pick(id) {
            this.selectedId = String(id);
            $wire.set(this.wireModel, String(id));
            this.open = false;
            this.search = '';
        },
        clear() {
            this.selectedId = '';
            $wire.set(this.wireModel, '');
            this.open = false;
            this.search = '';
        }
    }"
    x-init="$wire.$watch(@js($wireModel), value => { selectedId = value ? String(value) : ''; })"
    @click.outside="open = false; search = ''"
>
    <button
        type="button"
        class="search-select__button"
        @click="open = !open; if (open) { search = ''; $nextTick(() => $refs.selectSearch.focus()); }"
        :aria-expanded="open.toString()"
    >
        <span
            class="search-select__value"
            x-text="selectedLabel() || @js($placeholder)"
            :class="{ 'search-select__value--placeholder': !selectedLabel() }"
        ></span>
        <span class="search-select__chevron" :class="{ 'search-select__chevron--open': open }">&#9662;</span>
    </button>

    <div class="search-select__menu" x-cloak x-show="open" @click.stop>
        <div class="search-select__search">
            <span>@include('partials.icon', ['name' => 'search'])</span>
            <input
                type="search"
                x-ref="selectSearch"
                x-model="search"
                placeholder="{{ $searchPlaceholder }}"
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
                Clear selection
            </button>

            <template x-for="option in options" :key="option.id">
                <button
                    type="button"
                    class="search-select__item"
                    x-show="matches(option.name)"
                    :class="{ 'search-select__item--active': selectedId === option.id }"
                    @click="pick(option.id)"
                    x-text="option.name"
                ></button>
            </template>

            <p class="search-select__empty" x-show="options.length === 0 || (search !== '' && !hasResults())" x-cloak>
                {{ $emptyText }}
            </p>
        </div>
    </div>
</div>
