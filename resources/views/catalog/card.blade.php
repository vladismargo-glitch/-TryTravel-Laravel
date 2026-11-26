<article class="tour-card">
    <img src="{{ asset($tour->image_url) }}" alt="" class="tour-image" loading="lazy" referrerpolicy="no-referrer">
    <div class="tour-content">
        <div class="tour-header">
            <div class="tour-info-left">
                <div class="tour-location">{{ $tour->country }}, {{ $tour->destination }}</div>
                <h2 class="tour-hotel">{{ $tour->title }}</h2>
                @if ($tour->archived_at)
                    <p class="catalog-rating">Тур в архиве</p>
                @endif
                @if ($tour->review_count)
                    <p class="catalog-rating">{{ number_format((float) $tour->average_rating, 1, ',', ' ') }} из 5 · отзывов {{ $tour->review_count }}</p>
                @elseif ($sort === 'rating')
                    <p class="catalog-rating">Пока без отзывов</p>
                @endif
                @if ($sort === 'popular')
                    <p class="catalog-rating">Подтверждённых броней {{ $tour->booking_count }}</p>
                @endif
                <div class="tour-departure">
                    <span><img src="{{ asset('images/Samolet.png') }}" alt="" class="departure-icon">Вылет: {{ $tour->departure_city }}</span>
                    <span><img src="{{ asset('images/Calendar.png') }}" alt="" class="departure-icon"><time datetime="{{ $tour->departure_date }}">{{ \Illuminate\Support\Carbon::parse($tour->departure_date)->format('d.m.Y') }}</time></span>
                    <span><img src="{{ asset('images/image 203.png') }}" alt="" class="departure-icon">Ночей: {{ $tour->nights }}</span>
                    <span><img src="{{ asset('images/Eda.png') }}" alt="" class="departure-icon">{{ $tour->meal_plan }}</span>
                </div>
                @if (! $tour->archived_at)
                    <a class="tour-details-link" href="{{ route('tours.show', $tour->tour_id) }}">Подробнее о туре</a>
                @endif
            </div>
            <div class="tour-pricing">
                <div class="tour-price-info">
                    <div class="price-label">Цена</div>
                    <div class="price-value">{{ number_format((float) $tour->price, fmod((float) $tour->price, 1) ? 2 : 0, ',', ' ') }} ₽</div>
                </div>
                @if (\App\Queries\TourCards::isBookable($tour))
                    <a class="tour-select-btn booking-link" href="{{ route('bookings.create', ['tourId' => $tour->tour_id]) }}">
                        Оформить заявку
                    </a>
                @endif
                @include('catalog.favorite-button')
            </div>
        </div>
    </div>
</article>
