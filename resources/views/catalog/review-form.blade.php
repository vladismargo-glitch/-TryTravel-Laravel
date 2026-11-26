@if (session('review_status'))
    <p class="tour-review-feedback" role="status">{{ session('review_status') }}</p>
@endif

@auth
    @if ($ownReview)
        <div class="tour-review-form">
            <h3>Твой отзыв</h3>
            <p>{{ $ownReview->rating }} из 5 · {{ match ($ownReview->status) {
                'published' => 'Опубликован',
                'rejected' => 'Отклонён',
                default => 'На модерации',
            } }}</p>
            @if ($ownReview->status !== 'published')
                <p class="tour-detail-copy">{{ $ownReview->content }}</p>
            @endif
        </div>
    @else
        @php($oldRating = old('rating'))
        @php($oldContent = old('content'))
        <form class="auth-form tour-review-form" method="post" action="{{ route('reviews.store', $tour->tour_id) }}">
            @csrf
            <h3>Оставить отзыв</h3>
            <fieldset class="tour-review-ratings">
                <legend class="auth-label">Оценка</legend>
                @foreach (range(1, 5) as $rating)
                    <label>
                        <input type="radio" name="rating" value="{{ $rating }}" required
                            @checked(is_scalar($oldRating) && (string) $oldRating === (string) $rating)>
                        <span>{{ $rating }} <span aria-hidden="true">★</span></span>
                    </label>
                @endforeach
            </fieldset>
            @error('rating', 'review')
                <p class="auth-error" role="alert">{{ $message }}</p>
            @enderror
            <div class="auth-group">
                <label class="auth-label" for="tour-review-content">Текст отзыва</label>
                <textarea class="auth-input" id="tour-review-content" name="content" rows="4" maxlength="2000"
                    @if ($errors->review->has('content')) aria-invalid="true" @endif required>{{ is_string($oldContent) ? $oldContent : '' }}</textarea>
                @error('content', 'review')
                    <p class="auth-error" role="alert">{{ $message }}</p>
                @enderror
            </div>
            <button class="auth-submit" type="submit">Отправить отзыв</button>
        </form>
    @endif
@else
    <p class="tour-review-feedback">
        <a class="auth-link" href="{{ route('reviews.create', $tour->tour_id) }}">Войти, чтобы оставить отзыв</a>
    </p>
@endauth
