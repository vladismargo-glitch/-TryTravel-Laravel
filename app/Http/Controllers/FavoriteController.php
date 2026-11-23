<?php

namespace App\Http\Controllers;

use App\Queries\TourCards;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $tours = TourCards::query()
            ->join('favorites', 'favorites.tour_id', '=', 'tours.tour_id')
            ->where('favorites.user_id', $request->user()->getKey())
            ->selectRaw('1 AS is_favorite')
            ->orderByDesc('favorites.added_at')
            ->orderByDesc('tours.tour_id')
            ->paginate(12);

        if ($tours->currentPage() > $tours->lastPage()) {
            return redirect()->to($tours->url($tours->lastPage()));
        }

        return view('favorites', ['tours' => $tours]);
    }

    public function store(Request $request, string $tourId): RedirectResponse
    {
        DB::transaction(function () use ($request, $tourId): void {
            $tour = DB::table('tours')->where('tour_id', $tourId)
                ->whereNull('archived_at')->sharedLock()->first(['tour_id']);

            abort_if($tour === null, 404);

            DB::table('favorites')->upsert([
                'user_id' => $request->user()->getKey(),
                'tour_id' => $tour->tour_id,
            ], ['user_id', 'tour_id'], ['tour_id']);
        }, 3);

        return $this->backToList('Тур добавлен в избранное.');
    }

    public function destroy(Request $request, string $tourId): RedirectResponse
    {
        DB::table('favorites')
            ->where('user_id', $request->user()->getKey())
            ->where('tour_id', $tourId)
            ->delete();

        return $this->backToList('Тур удалён из избранного.');
    }

    private function backToList(string $message): RedirectResponse
    {
        $previous = url()->previous();
        $target = str_starts_with($previous, rtrim(url('/'), '/').'/')
            ? $previous
            : route('favorites.index');

        return redirect()->to($target, 303)->with('favorite_status', $message);
    }
}
