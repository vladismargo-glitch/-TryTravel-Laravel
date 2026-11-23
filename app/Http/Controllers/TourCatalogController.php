<?php

namespace App\Http\Controllers;

use App\Queries\TourCards;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TourCatalogController extends Controller
{
    private const SORT_OPTIONS = [
        'price_asc' => 'Сначала дешевле',
        'price_desc' => 'Сначала дороже',
        'popular' => 'По популярности',
        'rating' => 'По отзывам',
    ];

    public function index(Request $request, string $section = 'all'): View
    {
        $search = $request->query('q', '');
        abort_unless(is_string($search) && mb_strlen($search) <= 100, 422);
        $search = trim(preg_replace('/\s+/u', ' ', $search) ?? '');
        $sort = $request->query('sort', '');
        abort_unless(is_string($sort) && ($sort === '' || isset(self::SORT_OPTIONS[$sort])), 422);

        $heading = match ($section) {
            'hot' => 'Горящие туры',
            'newyear' => 'Новогодние туры',
            default => 'Каталог туров',
        };

        $catalog = TourCards::query()
            ->join('tour_categories', 'tour_categories.category_id', '=', 'tours.category_id')
            ->whereNull('tours.archived_at');

        if ($request->user()) {
            $catalog->selectSub(DB::table('favorites')
                ->selectRaw('1')
                ->whereColumn('favorites.tour_id', 'tours.tour_id')
                ->where('favorites.user_id', $request->user()->getKey()), 'is_favorite');
        } else {
            $catalog->selectRaw('0 AS is_favorite');
        }

        if ($section !== 'all') {
            $catalog->where('tour_categories.name', $heading);
        }

        foreach (explode(' ', $search) as $term) {
            if ($term === '') {
                continue;
            }

            // Match parts of words while treating percent and underscore literally.
            $pattern = '%'.strtr($term, ['!' => '!!', '%' => '!%', '_' => '!_']).'%';
            $catalog->where(function (Builder $query) use ($pattern): void {
                $query->whereRaw("tours.title LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("destinations.name LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("destinations.country LIKE ? ESCAPE '!'", [$pattern]);
            });
        }

        if ($sort === 'popular') {
            $confirmed = DB::table('bookings')
                ->where('checking', 1)
                ->groupBy('tour_id')
                ->selectRaw('tour_id, COUNT(*) AS booking_count');
            $catalog->leftJoinSub($confirmed, 'confirmed', 'confirmed.tour_id', '=', 'tours.tour_id')
                ->selectRaw('COALESCE(confirmed.booking_count, 0) AS booking_count')
                ->orderByDesc('booking_count');
        }

        match ($sort) {
            'price_asc' => $catalog->orderBy('tours.price'),
            'price_desc' => $catalog->orderByDesc('tours.price'),
            'rating' => $catalog->orderByDesc('ratings.average_rating')->orderByDesc('ratings.review_count'),
            default => null,
        };

        $tours = $catalog->orderBy('tours.tour_id')->paginate(12)
            ->appends(array_filter(['q' => $search, 'sort' => $sort], fn ($value) => $value !== ''));

        return view('catalog.index', [
            'heading' => $heading,
            'section' => $section,
            'search' => $search,
            'searchUrl' => $request->url(),
            'sort' => $sort,
            'sortOptions' => self::SORT_OPTIONS,
            'tours' => $tours,
        ]);
    }
}
