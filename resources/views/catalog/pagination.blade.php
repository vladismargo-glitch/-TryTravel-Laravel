@if ($tours->hasPages())
    <nav class="tours-pagination" aria-label="Страницы списка туров">
        @if ($tours->onFirstPage())
            <span>Назад</span>
        @else
            <a href="{{ $tours->previousPageUrl() }}" rel="prev">Назад</a>
        @endif
        <span>Страница {{ $tours->currentPage() }} из {{ $tours->lastPage() }}</span>
        @if ($tours->hasMorePages())
            <a href="{{ $tours->nextPageUrl() }}" rel="next">Далее</a>
        @endif
    </nav>
@endif
