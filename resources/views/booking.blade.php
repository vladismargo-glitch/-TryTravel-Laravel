@extends('layouts.auth')

@section('heading', 'Заявка на бронирование')

@section('content')
    <h2>{{ $tour->title }}</h2>
    <dl class="booking-summary">
        <dt>Направление</dt>
        <dd>{{ $tour->country }}, {{ $tour->destination }}</dd>
        <dt>Вылет</dt>
        <dd>{{ $tour->departure_city }}, {{ \Illuminate\Support\Carbon::parse($tour->departure_date)->format('d.m.Y') }}</dd>
        <dt>Ночей</dt>
        <dd>{{ $tour->nights }}</dd>
        <dt>Питание</dt>
        <dd>{{ $tour->meal_plan }}</dd>
        <dt>Цена</dt>
        <dd>{{ number_format((float) $tour->price, fmod((float) $tour->price, 1) ? 2 : 0, ',', ' ') }} ₽</dd>
    </dl>

    @if ($alreadyRequested)
        <p class="auth-feedback">Ты уже подал заявку на этот тур.</p>
        <p class="auth-alt-text"><a class="auth-link" href="{{ route('account') }}">Мои туры</a></p>
    @elseif (! $bookable)
        <p class="auth-feedback" role="alert">Тур больше недоступен для бронирования.</p>
    @else
        <form class="auth-form" method="post" action="{{ route('bookings.store', ['tourId' => $tour->tour_id]) }}">
            @csrf
            <button type="submit" class="auth-submit">Отправить заявку</button>
        </form>
    @endif
    <p class="auth-alt-text"><a class="auth-link" href="{{ route('tours.index') }}">Вернуться в каталог</a></p>
@endsection
