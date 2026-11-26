@extends('layouts.auth')

@section('heading', 'Управление турами')
@section('container-class', 'admin-container')

@section('content')
    <div class="admin-links">
        <a class="auth-link" href="{{ route('admin.tours.create') }}">Добавить тур</a>
        <a class="auth-link" href="{{ route('account') }}">Личный кабинет</a>
    </div>

    <ul class="admin-list">
        @forelse ($tours as $tour)
            <li>
                <h2>{{ $tour->title }}</h2>
                <p>{{ $tour->country }}, {{ $tour->destination }} · {{ $tour->category }} ·
                    {{ \Illuminate\Support\Carbon::parse($tour->departure_date)->format('d.m.Y') }}</p>
                @if ($tour->archived_at)
                    <p>В архиве</p>
                @else
                    <div class="admin-links">
                        <a class="auth-link" href="{{ route('admin.tours.edit', $tour->tour_id) }}">Редактировать</a>
                        <a class="auth-link" href="{{ route('admin.tours.confirm-archive', $tour->tour_id) }}">Убрать из каталога</a>
                    </div>
                @endif
            </li>
        @empty
            <li>Туров пока нет.</li>
        @endforelse
    </ul>

    @include('catalog.pagination')
@endsection
