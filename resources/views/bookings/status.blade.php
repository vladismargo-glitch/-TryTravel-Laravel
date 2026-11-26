@php
    [$status, $tone] = match (true) {
        (bool) $booking->checking => ['Подтверждена', 'active'],
        \App\Queries\TourCards::isBookable($booking) => ['Ожидает подтверждения', 'waiting'],
        default => ['Тур недоступен', 'muted'],
    };
@endphp
@if ($asBadge ?? false)
    <span class="admin-badge admin-badge-{{ $tone }}">{{ $status }}</span>
@else
    {{ $status }}
@endif
