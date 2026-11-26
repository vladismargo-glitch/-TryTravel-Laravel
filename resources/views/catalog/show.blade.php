@extends('layouts.auth')

@section('heading', $tour->title)
@section('container-class', 'tour-detail-container')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/tour-search.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tour-detail.css') }}">
@endpush

@section('content')
    @include('catalog.favorite-status')
    <div class="tour-detail">
        <img class="tour-detail-photo" src="{{ asset($tour->image_url) }}" alt=""
            referrerpolicy="no-referrer">

        <div class="tour-detail-info">
            <p class="tour-detail-location">{{ $tour->country }}, {{ $tour->destination }}</p>
            <dl class="tour-detail-facts">
                <div>
                    <dt>Город вылета</dt>
                    <dd>{{ $tour->departure_city }}</dd>
                </div>
                <div>
                    <dt>Дата вылета</dt>
                    <dd><time datetime="{{ $tour->departure_date }}">{{ \Illuminate\Support\Carbon::parse($tour->departure_date)->format('d.m.Y') }}</time></dd>
                </div>
                <div>
                    <dt>Ночей</dt>
                    <dd>{{ $tour->nights }}</dd>
                </div>
                <div>
                    <dt>Питание</dt>
                    <dd>{{ $tour->meal_plan }}</dd>
                </div>
            </dl>
            <p class="tour-detail-price">{{ number_format((float) $tour->price, fmod((float) $tour->price, 1) ? 2 : 0, ',', ' ') }} ₽</p>
            <div class="tour-detail-actions">
                @if ($bookable)
                    <a class="tour-detail-book" href="{{ route('bookings.create', $tour->tour_id) }}">Оформить заявку</a>
                @else
                    <p>Дата вылета прошла.</p>
                @endif
                @include('catalog.favorite-button')
            </div>
        </div>
    </div>

    @if ($tour->description !== '')
        <section class="tour-detail-section">
            <h2>О туре</h2>
            <p class="tour-detail-copy">{{ $tour->description }}</p>
        </section>
    @endif

    @if ($bookable && $promotions->isNotEmpty())
        <section class="tour-detail-section">
            <h2>Акции на этот тур</h2>
            <ul class="tour-detail-list">
                @foreach ($promotions as $promotion)
                    <li>{{ $promotion->title }} · до
                        <time datetime="{{ $promotion->ends_at }}">{{ \Illuminate\Support\Carbon::parse($promotion->ends_at)->format('d.m.Y') }}</time>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="tour-detail-section">
        <h2>Отзывы</h2>
        @if ($tour->review_count)
            <p><strong>{{ number_format((float) $tour->average_rating, 1, ',', ' ') }} из 5</strong> · отзывов {{ $tour->review_count }}</p>
            @if ($tour->review_count > $reviews->count())
                <p>Последние {{ $reviews->count() }} отзывов</p>
            @endif
            <ul class="tour-detail-list">
                @foreach ($reviews as $review)
                    <li>
                        <strong>{{ $review->rating }} из 5</strong> · {{ \Illuminate\Support\Carbon::parse($review->created_at)->format('d.m.Y') }}
                        <p class="tour-detail-copy">{{ $review->content }}</p>
                    </li>
                @endforeach
            </ul>
        @else
            <p>У этого тура пока нет опубликованных отзывов.</p>
        @endif
    </section>

    <p class="auth-alt-text"><a class="auth-link" href="{{ route('tours.index') }}">В каталог туров</a></p>
@endsection
