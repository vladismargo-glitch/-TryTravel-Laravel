<?php

namespace App\Http\Controllers;

use App\Queries\TourCards;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AdminBookingController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $bookings = $this->bookings()
            ->orderBy('bookings.checking')
            ->orderByDesc('bookings.created_at')
            ->orderByDesc('bookings.booking_id')
            ->paginate(12);

        if ($bookings->currentPage() > $bookings->lastPage()) {
            return redirect()->to($bookings->url($bookings->lastPage()));
        }

        return view('admin.bookings.index', ['bookings' => $bookings]);
    }

    public function show(string $bookingId): View
    {
        $booking = $this->bookings()
            ->addSelect('users.email', 'users.phone', 'tours.departure_city')
            ->where('bookings.booking_id', $bookingId)->first();
        abort_if($booking === null, 404);

        return view('admin.bookings.show', ['booking' => $booking]);
    }

    public function confirm(string $bookingId): RedirectResponse
    {
        $tourId = DB::table('bookings')->where('booking_id', $bookingId)->value('tour_id');
        abort_if($tourId === null, 404);

        $redirect = to_route('admin.bookings.show', ['bookingId' => $bookingId], 303);

        return DB::transaction(function () use ($bookingId, $tourId, $redirect): RedirectResponse {
            $tour = DB::table('tours')->where('tour_id', $tourId)
                ->lockForUpdate()->first(['archived_at', 'departure_date']);
            $record = DB::table('bookings')->where('booking_id', $bookingId)->where('tour_id', $tourId);
            $booking = (clone $record)->lockForUpdate()->first(['checking']);
            abort_if($tour === null || $booking === null, 404);

            if ($booking->checking) {
                return $redirect->with('status', 'Заявка уже подтверждена.');
            }

            if (! TourCards::isBookable($tour)) {
                return $redirect->withErrors(['booking' => 'Тур недоступен для бронирования.']);
            }

            $record->update(['checking' => 1]);

            return $redirect->with('status', 'Заявка подтверждена.');
        }, 3);
    }

    private function bookings(): Builder
    {
        return DB::table('bookings')
            ->join('users', 'users.user_id', '=', 'bookings.user_id')
            ->join('tours', 'tours.tour_id', '=', 'bookings.tour_id')
            ->select('bookings.booking_id', 'bookings.checking', 'bookings.created_at',
                'users.first_name', 'users.last_name', 'tours.title', 'tours.archived_at', 'tours.departure_date');
    }
}
