@extends('layouts.auth')

@section('heading', 'Заявки на бронирование')
@section('container-class', 'admin-container')

@section('content')
    <div class="admin-links">
        <a class="auth-link" href="{{ route('account') }}">Личный кабинет</a>
        <a class="auth-link" href="{{ route('admin.tours.index') }}">Управление турами</a>
    </div>
    <ul class="admin-list">
        @forelse ($bookings as $booking)
            <li>
                <h2>
                    <a class="auth-link" href="{{ route('admin.bookings.show', $booking->booking_id) }}">
                        №{{ $booking->booking_id }} · {{ $booking->title }}
                    </a>
                </h2>
                <p>{{ $booking->first_name }} {{ $booking->last_name }} ·
                    {{ \Illuminate\Support\Carbon::parse($booking->created_at)->format('d.m.Y') }}</p>
                <p>@include('bookings.status')</p>
            </li>
        @empty
            <li>Заявок пока нет.</li>
        @endforelse
    </ul>

    @include('catalog.pagination', ['tours' => $bookings, 'paginationLabel' => 'Страницы списка заявок'])
@endsection
