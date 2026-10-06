@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination Navigation" style="display:flex;align-items:center;justify-content:center;gap:5px;flex-wrap:wrap;margin-top:4px;">
    @if ($paginator->onFirstPage())
    <span class="btn btn-o btn-sm" style="opacity:.4;cursor:default;" aria-label="Previous page"><i class="fas fa-chevron-left"></i></span>
    @else
    <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-o btn-sm" rel="prev" aria-label="Previous page"><i class="fas fa-chevron-left"></i></a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
        <span class="tm" style="font-size:12px;padding:0 3px;">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                <span class="btn btn-p btn-sm" style="min-width:32px;justify-content:center;" aria-current="page">{{ $page }}</span>
                @else
                <a href="{{ $url }}" class="btn btn-o btn-sm" style="min-width:32px;justify-content:center;">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-o btn-sm" rel="next" aria-label="Next page"><i class="fas fa-chevron-right"></i></a>
    @else
    <span class="btn btn-o btn-sm" style="opacity:.4;cursor:default;" aria-label="Next page"><i class="fas fa-chevron-right"></i></span>
    @endif
</nav>
@endif
