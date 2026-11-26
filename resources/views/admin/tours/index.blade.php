@extends('layouts.admin')

@section('heading', 'Управление турами')

@section('content')
    <div class="admin-toolbar">
        <a class="admin-button admin-button-primary" href="{{ route('admin.tours.create') }}">Добавить тур</a>
        <a class="admin-button" href="{{ route('tours.index') }}">Каталог на сайте</a>
    </div>

    <ul class="admin-list">
        @forelse ($tours as $tour)
            <li class="admin-row">
                <div class="admin-row-info">
                    <h2>{{ $tour->title }}</h2>
                    <p>{{ $tour->country }}, {{ $tour->destination }} · {{ $tour->category }} ·
                        {{ \Illuminate\Support\Carbon::parse($tour->departure_date)->format('d.m.Y') }}</p>
                </div>
                <div class="admin-row-actions">
                    @if ($tour->archived_at)
                        <span class="admin-badge">В архиве</span>
                    @else
                        <a class="admin-button" href="{{ route('admin.tours.edit', $tour->tour_id) }}">Редактировать</a>
                        <a class="admin-button admin-button-danger" href="{{ route('admin.tours.confirm-archive', $tour->tour_id) }}">Убрать из каталога</a>
                    @endif
                </div>
            </li>
        @empty
            <li class="admin-empty">Туров пока нет.</li>
        @endforelse
    </ul>

    @include('catalog.pagination')
@endsection
