<form class="favorite-form" method="post"
    action="{{ route($tour->is_favorite ? 'favorites.destroy' : 'favorites.store', ['tourId' => $tour->tour_id]) }}">
    @csrf
    @if ($tour->is_favorite)
        @method('DELETE')
    @endif
    <button type="submit" class="favorite-button{{ $tour->is_favorite ? ' is-saved' : '' }}">
        <span aria-hidden="true">{{ $tour->is_favorite ? '♥' : '♡' }}</span>
        {{ $tour->is_favorite ? 'Убрать из избранного' : 'В избранное' }}
    </button>
</form>
