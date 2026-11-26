@if ($tours->hasPages())
    <nav class="site-pagination" aria-label="{{ $paginationLabel ?? 'Страницы списка туров' }}">
        @if ($tours->onFirstPage())
            <span class="site-pagination-control" aria-label="Предыдущая страница" aria-disabled="true">‹</span>
        @else
            <a class="site-pagination-control" href="{{ $tours->previousPageUrl() }}" rel="prev" aria-label="Предыдущая страница">‹</a>
        @endif
        @foreach (array_filter(\Illuminate\Pagination\UrlWindow::make($tours->onEachSide(1))) as $pages)
            @unless ($loop->first)
                <span class="site-pagination-gap" aria-hidden="true">…</span>
            @endunless
            @foreach ($pages as $page => $url)
                @if ($page === $tours->currentPage())
                    <span class="site-pagination-current" aria-current="page" aria-label="Страница {{ $page }}">{{ $page }}</span>
                @else
                    <a class="site-pagination-number" href="{{ $url }}" aria-label="Страница {{ $page }}">{{ $page }}</a>
                @endif
            @endforeach
        @endforeach
        @if ($tours->hasMorePages())
            <a class="site-pagination-control" href="{{ $tours->nextPageUrl() }}" rel="next" aria-label="Следующая страница">›</a>
        @else
            <span class="site-pagination-control" aria-label="Следующая страница" aria-disabled="true">›</span>
        @endif
    </nav>
@endif
