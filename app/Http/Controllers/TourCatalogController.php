<?php

namespace App\Http\Controllers;

use App\Queries\TourCards;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TourCatalogController extends Controller
{
    private const SORT_OPTIONS = [
        'price_asc' => 'Сначала дешевле',
        'price_desc' => 'Сначала дороже',
        'popular' => 'По популярности',
        'rating' => 'По отзывам',
    ];

    private const SEARCH_SCOPES = [
        'hotel' => 'Отели',
        'destination' => 'Путешествия',
        'all' => 'Туры',
    ];

    private const GUEST_OPTIONS = [
        '1-0' => '1 взрослый',
        '2-0' => '2 взрослых',
        '2-1' => '2 взрослых и 1 ребёнок',
        '2-2' => '2 взрослых и 2 ребёнка',
    ];

    public function show(Request $request, string $tourId): View
    {
        $tour = TourCards::query()->where('tours.tour_id', $tourId)->whereNull('tours.archived_at')->first();
        abort_if($tour === null, 404);

        $userId = $request->user()?->getKey();
        $tour->is_favorite = $userId !== null && DB::table('favorites')
            ->where('user_id', $userId)->where('tour_id', $tour->tour_id)->exists();

        $reviews = DB::table('tour_reviews')->where('tour_id', $tour->tour_id)->where('status', 'published')
            ->orderByDesc('created_at')->orderByDesc('review_id')->limit(5)->get(['rating', 'content', 'created_at']);

        $today = today('Asia/Tomsk')->toDateString();
        $promotions = DB::table('promotions')
            ->join('promotion_tours', 'promotion_tours.promotion_id', '=', 'promotions.promotion_id')
            ->where('promotion_tours.tour_id', $tour->tour_id)
            ->where('promotions.starts_at', '<=', $today)->where('promotions.ends_at', '>=', $today)
            ->orderBy('promotions.ends_at')->get(['promotions.title', 'promotions.ends_at']);

        return view('catalog.show', [
            'tour' => $tour,
            'bookable' => TourCards::isBookable($tour),
            'reviews' => $reviews,
            'promotions' => $promotions,
        ]);
    }

    public function index(Request $request, string $section = 'all'): View
    {
        $search = $request->query('q', '');
        abort_unless(is_string($search) && mb_strlen($search) <= 100, 422);
        $search = trim(preg_replace('/\s+/u', ' ', $search) ?? '');
        $sort = $request->query('sort', '');
        abort_unless(is_string($sort) && ($sort === '' || isset(self::SORT_OPTIONS[$sort])), 422);
        $filters = Validator::make($request->only('scope', 'departure_from', 'departure_to', 'guests'), [
            'scope' => ['nullable', 'string', Rule::in(array_keys(self::SEARCH_SCOPES))],
            'departure_from' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:1000-01-01'],
            'departure_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:1000-01-01',
                Rule::when($request->filled('departure_from'), 'after_or_equal:departure_from')],
            'guests' => ['nullable', 'string', Rule::in(array_keys(self::GUEST_OPTIONS))],
        ]);
        abort_if($filters->fails(), 422, 'Проверь параметры поиска и период вылета.');
        $filters = $filters->validated();
        $scope = $filters['scope'] ?? 'all';

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

        if (isset($filters['departure_from'])) {
            $catalog->where('tours.departure_date', '>=', $filters['departure_from']);
        }

        if (isset($filters['departure_to'])) {
            $catalog->where('tours.departure_date', '<=', $filters['departure_to']);
        }

        if (isset($filters['guests'])) {
            [$adults, $children] = explode('-', $filters['guests']);
            $catalog->join('tour_guest_limits', 'tour_guest_limits.tour_id', '=', 'tours.tour_id')
                ->where('tour_guest_limits.max_adults', '>=', (int) $adults)
                ->where('tour_guest_limits.max_children', '>=', (int) $children);
        }

        foreach (explode(' ', $search) as $term) {
            if ($term === '') {
                continue;
            }

            // Match parts of words while treating percent and underscore literally.
            $pattern = '%'.strtr($term, ['!' => '!!', '%' => '!%', '_' => '!_']).'%';
            $catalog->where(function (Builder $query) use ($pattern, $scope): void {
                $query->whereRaw("destinations.name LIKE ? ESCAPE '!'", [$pattern]);
                if ($scope !== 'destination') {
                    $query->orWhereRaw("tours.title LIKE ? ESCAPE '!'", [$pattern]);
                }
                if ($scope !== 'hotel') {
                    $query->orWhereRaw("destinations.country LIKE ? ESCAPE '!'", [$pattern]);
                }
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
            ->appends(array_filter(['q' => $search, 'sort' => $sort] + $filters));

        return view('catalog.index', [
            'heading' => $heading,
            'section' => $section,
            'search' => $search,
            'searchUrl' => $request->url(),
            'sort' => $sort,
            'sortOptions' => self::SORT_OPTIONS,
            'filters' => $filters,
            'scope' => $scope,
            'scopeOptions' => self::SEARCH_SCOPES,
            'guestOptions' => self::GUEST_OPTIONS,
            'hasFilters' => $search !== '' || isset($filters['departure_from'])
                || isset($filters['departure_to']) || isset($filters['guests']),
            'tours' => $tours,
        ]);
    }
}
