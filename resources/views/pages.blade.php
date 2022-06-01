@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="d-flex justify-content-between gap-3">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="flex-average bg-gray text-center py-2 text-white-50 rounded-3">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
               class="flex-average bg-linear-gray text-center py-2 text-white-50 rounded-3">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
               class="flex-average bg-linear-gray text-center py-2 text-white-50 rounded-3">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="flex-average bg-gray text-center py-2 text-white-50 rounded-3">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </nav>
@endif
