@if ($booking->checking)
    Подтверждена
@elseif (\App\Queries\TourCards::isBookable($booking))
    Ожидает подтверждения
@else
    Тур недоступен
@endif
