<article class="tour-card">
    <img src="{{ asset($tour->image_url) }}" alt="{{ $tour->title }}" class="tour-image" loading="lazy">
    <div class="tour-content">
        <div class="tour-header">
            <div class="tour-info-left">
                <div class="tour-location">{{ $tour->country }}, {{ $tour->destination }}</div>
                <h2 class="tour-hotel">{{ $tour->title }}</h2>
                <div class="tour-departure">
                    <span><img src="{{ asset('images/Samolet.png') }}" alt="" class="departure-icon">Вылет: {{ $tour->departure_city }}</span>
                    <span><img src="{{ asset('images/Calendar.png') }}" alt="" class="departure-icon"><time datetime="{{ $tour->departure_date }}">{{ \Illuminate\Support\Carbon::parse($tour->departure_date)->format('d.m.Y') }}</time></span>
                    <span><img src="{{ asset('images/image 203.png') }}" alt="" class="departure-icon">Ночей: {{ $tour->nights }}</span>
                    <span><img src="{{ asset('images/Eda.png') }}" alt="" class="departure-icon">{{ $tour->meal_plan }}</span>
                </div>
                @if ($tour->description !== '')
                    <details class="catalog-description">
                        <summary>Подробнее о туре</summary>
                        <p>{{ $tour->description }}</p>
                    </details>
                @endif
            </div>
            <div class="tour-pricing">
                <div class="tour-price-info">
                    <div class="price-label">Цена</div>
                    <div class="price-value">{{ number_format((float) $tour->price, fmod((float) $tour->price, 1) ? 2 : 0, ',', ' ') }} ₽</div>
                </div>
            </div>
        </div>
    </div>
</article>
