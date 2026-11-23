<?php

namespace App\Http\Controllers;

use App\Queries\TourCards;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $bookings = DB::table('bookings')
            ->join('tours', 'tours.tour_id', '=', 'bookings.tour_id')
            ->join('destinations', 'destinations.destination_id', '=', 'tours.destination_id')
            ->where('bookings.user_id', $request->user()->getKey())
            ->select('bookings.booking_id', 'bookings.checking', 'bookings.created_at',
                'tours.title', 'tours.departure_city', 'tours.departure_date', 'tours.nights',
                'tours.meal_plan', 'destinations.country', 'destinations.name as destination')
            ->orderByDesc('bookings.created_at')
            ->orderByDesc('bookings.booking_id')
            ->paginate(12);

        if ($bookings->currentPage() > $bookings->lastPage()) {
            return redirect()->to($bookings->url($bookings->lastPage()));
        }

        return view('account', ['bookings' => $bookings]);
    }

    public function create(Request $request, string $tourId): View
    {
        $tour = TourCards::query()->where('tours.tour_id', $tourId)->first();
        abort_if($tour === null, 404);

        return view('booking', [
            'tour' => $tour,
            'bookable' => TourCards::isBookable($tour),
            'alreadyRequested' => DB::table('bookings')->where([
                'user_id' => $request->user()->getKey(),
                'tour_id' => $tour->tour_id,
            ])->exists(),
        ]);
    }

    public function store(Request $request, string $tourId): RedirectResponse
    {
        $userId = $request->user()->getKey();

        return DB::transaction(function () use ($tourId, $userId): RedirectResponse {
            // Lock the tour so simultaneous submissions cannot create duplicate requests.
            $tour = DB::table('tours')->where('tour_id', $tourId)
                ->lockForUpdate()->first(['tour_id', 'archived_at', 'departure_date']);
            abort_if($tour === null, 404);

            $booking = ['user_id' => $userId, 'tour_id' => $tour->tour_id];

            if (DB::table('bookings')->where($booking)->exists()) {
                return to_route('account', [], 303)->with('booking_status', 'Ты уже подал заявку на этот тур.');
            }

            if (! TourCards::isBookable($tour)) {
                return to_route('bookings.create', ['tourId' => $tour->tour_id], 303);
            }

            DB::table('bookings')->insert($booking);

            return to_route('account', [], 303)->with('booking_status', 'Заявка отправлена.');
        }, 3);
    }
}
