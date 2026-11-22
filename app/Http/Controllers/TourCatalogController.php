<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TourCatalogController extends Controller
{
    public function index(Request $request, string $section = 'all'): View
    {
        $search = $request->query('q', '');
        abort_unless(is_string($search) && mb_strlen($search) <= 100, 422);
        $search = trim(preg_replace('/\s+/u', ' ', $search) ?? '');

        $heading = match ($section) {
            'hot' => 'Горящие туры',
            'newyear' => 'Новогодние туры',
            default => 'Каталог туров',
        };

        $catalog = DB::table('tours')
            ->join('destinations', 'destinations.destination_id', '=', 'tours.destination_id')
            ->join('tour_categories', 'tour_categories.category_id', '=', 'tours.category_id')
            ->whereNull('tours.archived_at')
            ->select('tours.*', 'destinations.country', 'destinations.name as destination');

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

        $tours = $catalog->orderBy('tours.tour_id')->paginate(12);

        if ($search !== '') {
            $tours->appends(['q' => $search]);
        }

        return view('catalog.index', [
            'heading' => $heading,
            'section' => $section,
            'search' => $search,
            'searchUrl' => $request->url(),
            'tours' => $tours,
        ]);
    }
}
