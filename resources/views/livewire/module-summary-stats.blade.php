<section class="module-summary" aria-label="Module record totals">
    @foreach ($cards as $card)
        @php
            $canClick = ! empty($card['clickable']);
            $tone = $card['tone'] ?? null;
            $statusId = $card['process_status_id'] ?? '';
        @endphp

        <button
            type="button"
            class="module-summary__card {{ $canClick ? 'module-summary__card--button' : '' }}"
            @if ($tone) style="--summary-tone: {{ $tone }}" @endif
            @if ($canClick)
                @click="openClientStat({
                    label: @js($card['label']),
                    process_status_id: @js($statusId),
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
