@if ($paginator->hasPages())
    @php
        $pageName = $paginator->getPageName();
    @endphp

    <nav class="master-pagination" role="navigation" aria-label="Pagination">
        <p class="master-pagination__meta">
            Showing
            <strong>{{ $paginator->firstItem() }}</strong>
            –
            <strong>{{ $paginator->lastItem() }}</strong>
            of
            <strong>{{ $paginator->total() }}</strong>
        </p>

        <div class="master-pagination__links">
            @if ($paginator->onFirstPage())
                <span class="master-page-btn master-page-btn--disabled" aria-disabled="true">Prev</span>
            @else
                <button
                    type="button"
                    class="master-page-btn"
                    wire:click="previousPage('{{ $pageName }}')"
                    wire:loading.attr="disabled"
                >
                    Prev
                </button>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="master-page-btn master-page-btn--disabled">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="master-page-btn master-page-btn--active" aria-current="page">{{ $page }}</span>
                        @else
                            <button
                                type="button"
                                class="master-page-btn"
                                wire:click="gotoPage({{ $page }}, '{{ $pageName }}')"
                                wire:loading.attr="disabled"
                            >
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <button
                    type="button"
                    class="master-page-btn"
                    wire:click="nextPage('{{ $pageName }}')"
                    wire:loading.attr="disabled"
                >
                    Next
                </button>
            @else
                <span class="master-page-btn master-page-btn--disabled" aria-disabled="true">Next</span>
            @endif
        </div>
    </nav>
@endif
