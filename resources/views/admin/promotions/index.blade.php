@extends('layouts.admin')

@section('heading', 'Управление акциями')

@section('content')
    <div class="admin-toolbar">
        <a class="admin-button admin-button-primary" href="{{ route('admin.promotions.create') }}">Добавить акцию</a>
        <a class="admin-button" href="{{ route('promotions.index') }}">Акции на сайте</a>
    </div>

    <ul class="admin-list">
        @forelse ($promotions as $promotion)
            <li class="admin-row">
                <div class="admin-row-info">
                    <h2>{{ $promotion->title }}</h2>
                    <p>{{ \Illuminate\Support\Carbon::parse($promotion->starts_at)->format('d.m.Y') }}
                        — {{ \Illuminate\Support\Carbon::parse($promotion->ends_at)->format('d.m.Y') }}</p>
                </div>
                @if ($promotion->starts_at > $today)
                    <span class="admin-badge admin-badge-waiting">Запланирована</span>
                @elseif ($promotion->ends_at < $today)
                    <span class="admin-badge">Завершена</span>
                @else
                    <span class="admin-badge admin-badge-active">Действует</span>
                @endif
                <div class="admin-row-actions">
                    <a class="admin-button" href="{{ route('admin.promotions.edit', $promotion->promotion_id) }}">Редактировать</a>
                    <a class="admin-button admin-button-danger" href="{{ route('admin.promotions.confirm-delete', $promotion->promotion_id) }}">Удалить</a>
                </div>
            </li>
        @empty
            <li class="admin-empty">Акций пока нет.</li>
        @endforelse
    </ul>

    @include('catalog.pagination', ['tours' => $promotions, 'paginationLabel' => 'Страницы списка акций'])
@endsection
