@extends('layouts.admin')

@section('heading', 'Отзывы')

@section('content')
    <ul class="admin-list">
        @forelse ($reviews as $review)
            <li class="admin-row admin-review">
                <div class="admin-row-info">
                    <h2>
                        @if ($review->archived_at === null)
                            <a class="admin-review-tour" href="{{ route('tours.show', $review->tour_id) }}#reviews">{{ $review->title }}</a>
                        @else
                            {{ $review->title }}
                        @endif
                    </h2>
                    <p>{{ $review->first_name }} {{ $review->last_name }} · {{ $review->rating }} из 5 ·
                        {{ \Illuminate\Support\Carbon::parse($review->created_at)->format('d.m.Y') }}</p>
                </div>

                @switch($review->status)
                    @case('pending')
                        <span class="admin-badge admin-badge-waiting">На модерации</span>
                        @break
                    @case('published')
                        <span class="admin-badge admin-badge-active">Опубликован</span>
                        @break
                    @default
                        <span class="admin-badge">Отклонён</span>
                @endswitch

                <p class="admin-review-copy">{{ $review->content }}</p>

                @if ($review->status === 'pending')
                    <form class="admin-row-actions" method="post" action="{{ route('admin.reviews.update', $review->review_id) }}">
                        @csrf
                        @method('PATCH')
                        <button class="admin-button admin-button-primary" type="submit" name="status" value="published">Опубликовать</button>
                        <button class="admin-button admin-button-danger" type="submit" name="status" value="rejected">Отклонить</button>
                    </form>
                @endif
            </li>
        @empty
            <li class="admin-empty">Отзывов пока нет.</li>
        @endforelse
    </ul>

    @include('catalog.pagination', ['tours' => $reviews, 'paginationLabel' => 'Страницы списка отзывов'])
@endsection
