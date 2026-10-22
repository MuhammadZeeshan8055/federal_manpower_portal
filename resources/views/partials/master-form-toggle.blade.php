@php
    $toggleVar = $openVar ?? 'formOpen';
@endphp
<button
    type="button"
    class="panel__header master-form-toggle"
    @click="{{ $toggleVar }} = !{{ $toggleVar }}"
    :aria-expanded="{{ $toggleVar }}.toString()"
    aria-controls="{{ $targetId }}"
>
    <span>
        <span class="panel__eyebrow">{{ $eyebrow }}</span>
        <strong class="master-form-toggle__title">{{ $title }}</strong>
    </span>
    <span class="master-form-toggle__action">
        <span>
            <span x-show="!{{ $toggleVar }}" x-cloak>{{ $actionLabel }}</span>
            <span x-show="{{ $toggleVar }}" x-cloak>Close form</span>
        </span>
        <span class="master-form-toggle__icon" :class="{ 'master-form-toggle__icon--open': {{ $toggleVar }} }">
            @include('partials.icon', ['name' => 'plus'])
        </span>
    </span>
</button>
