@extends('layouts.admin')

@section('heading', 'Заявка №'.$booking->booking_id)

@section('content')
    <dl class="admin-booking-details">
        <div>
            <dt>Тур</dt>
            <dd>{{ $booking->title }}</dd>
        </div>
        <div>
            <dt>Вылет</dt>
            <dd>{{ $booking->departure_city }}, {{ \Illuminate\Support\Carbon::parse($booking->departure_date)->format('d.m.Y') }}</dd>
        </div>
        <div>
            <dt>Клиент</dt>
            <dd>{{ $booking->first_name }} {{ $booking->last_name }}</dd>
        </div>
        <div>
            <dt>Электронная почта</dt>
            <dd>{{ $booking->email }}</dd>
        </div>
        <div>
            <dt>Телефон</dt>
            <dd>{{ $booking->phone }}</dd>
        </div>
        <div>
            <dt>Дата подачи</dt>
            <dd>{{ \Illuminate\Support\Carbon::parse($booking->created_at)->format('d.m.Y') }}</dd>
        </div>
        <div>
            <dt>Состояние</dt>
            <dd>@include('bookings.status', ['asBadge' => true])</dd>
        </div>
    </dl>

    @if (! $booking->checking && \App\Queries\TourCards::isBookable($booking))
        <form class="auth-form" method="post" action="{{ route('admin.bookings.confirm', $booking->booking_id) }}">
            @csrf
            @method('PATCH')
            <button class="auth-submit" type="submit">Подтвердить заявку</button>
        </form>
    @endif

    <p class="auth-alt-text"><a class="auth-link" href="{{ route('admin.bookings.index') }}">К списку заявок</a></p>
@endsection
