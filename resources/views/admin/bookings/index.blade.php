@extends('layouts.admin')

@section('heading', 'Заявки на бронирование')

@section('content')
    <ul class="admin-list">
        @forelse ($bookings as $booking)
            <li class="admin-row">
                <div class="admin-row-info">
                    <h2>№{{ $booking->booking_id }} · {{ $booking->title }}</h2>
                    <p>{{ $booking->first_name }} {{ $booking->last_name }} ·
                        {{ \Illuminate\Support\Carbon::parse($booking->created_at)->format('d.m.Y') }}</p>
                </div>
                @include('bookings.status', ['asBadge' => true])
                <a class="admin-button" href="{{ route('admin.bookings.show', $booking->booking_id) }}">Открыть заявку</a>
            </li>
        @empty
            <li class="admin-empty">Заявок пока нет.</li>
        @endforelse
    </ul>

    @include('catalog.pagination', ['tours' => $bookings, 'paginationLabel' => 'Страницы списка заявок'])
@endsection
