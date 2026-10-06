<button
    type="button"
    class="panel__header master-form-toggle"
    @click="formOpen = !formOpen"
    :aria-expanded="formOpen.toString()"
    aria-controls="{{ $targetId }}"
>
    <span>
        <span class="panel__eyebrow">{{ $eyebrow }}</span>
        <strong class="master-form-toggle__title">{{ $title }}</strong>
    </span>
    <span class="master-form-toggle__action">
        <span>
            <span x-show="!formOpen" x-cloak>{{ $actionLabel }}</span>
            <span x-show="formOpen" x-cloak>Close form</span>
        </span>
        <span class="master-form-toggle__icon" :class="{ 'master-form-toggle__icon--open': formOpen }">
            @include('partials.icon', ['name' => 'plus'])
        </span>
    </span>
</button>
