<section class="module-summary" aria-label="Module record totals">
    @foreach ($cards as $card)
        <button
            type="button"
            class="module-summary__card {{ ! empty($card['clickable']) ? 'module-summary__card--button' : '' }}"
            @if (! empty($card['tone']))
                style="--summary-tone: {{ $card['tone'] }}"
            @endif
            @if (! empty($card['clickable']))
                @click="openClientStat({
                    label: @js($card['label']),
                    process_status_id: @js($card['process_status_id'] ?? ''),
                    clickable: true
                })"
            @else
                disabled
            @endif
        >
            <span class="module-summary__icon">
                @include('partials.icon', ['name' => 'chart'])
            </span>
            <span class="module-summary__copy">
                <small>{{ $card['label'] }}</small>
                <strong>{{ number_format((int) $card['value']) }}</strong>
                <span>listed</span>
            </span>
        </button>
    @endforeach
</section>
