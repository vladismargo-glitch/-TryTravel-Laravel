@if (session('favorite_status'))
    <p class="favorite-status" role="status">{{ session('favorite_status') }}</p>
@endif
