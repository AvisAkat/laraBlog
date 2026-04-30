<div style="width: 100%;">
    @if ($paginator->hasPages())
        <nav class="pagination">

            {{-- Previous (Newer Posts) --}}
            @if ($paginator->onFirstPage())
                <span class="nav-btn disabled">←</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="nav-btn" rel="prev">
                    ←
                </a>
            @endif

            @php
                $current = $paginator->currentPage();
                $last = $paginator->lastPage();
            @endphp

            <div class="pages">

                {{-- First page --}}
                @if ($current > 3)
                    <a href="{{ $paginator->url(1) }}">1</a>
                @endif

                {{-- Left ellipsis --}}
                @if ($current > 4)
                    <span class="dots">...</span>
                @endif

                {{-- Middle pages --}}
                @for ($i = max(1, $current - 2); $i <= min($last, $current + 2); $i++)
                    @if ($i == $current)
                        <a class="active">{{ $i }}</a>
                    @else
                        <a href="{{ $paginator->url($i) }}">{{ $i }}</a>
                    @endif
                @endfor

                {{-- Right ellipsis --}}
                @if ($current < $last - 3)
                    <span class="dots">...</span>
                @endif

                {{-- Last page --}}
                @if ($current < $last - 2)
                    <a href="{{ $paginator->url($last) }}">{{ $last }}</a>
                @endif

            </div>

            {{-- Next (Older Posts) --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="nav-btn" rel="next">
                    →
                </a>
            @else
                <span class="nav-btn disabled">→</span>
            @endif

        </nav>
    @endif
</div>