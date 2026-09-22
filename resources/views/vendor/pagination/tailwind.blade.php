@php
    $summary = $paginator->total() > 0
        ? __('Showing :from to :to of :total results', [
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
        ])
        : __('No results');
@endphp

<nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="admin-pagination-nav">

    <p class="admin-pagination-summary">{{ $summary }}</p>

    <div class="admin-pagination-links">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span aria-disabled="true">&lsaquo;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">&lsaquo;</a>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)

            @if (is_string($element))
                <span aria-disabled="true">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif

        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">&rsaquo;</a>
        @else
            <span aria-disabled="true">&rsaquo;</span>
        @endif

    </div>

</nav>
