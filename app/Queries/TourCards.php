<?php

namespace App\Queries;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class TourCards
{
    public static function isBookable(object $tour): bool
    {
        return $tour->archived_at === null && $tour->departure_date >= today('Asia/Tomsk')->toDateString();
    }

    public static function query(): Builder
    {
        $ratings = DB::table('tour_reviews')
            ->where('status', 'published')
            ->groupBy('tour_id')
            ->selectRaw('tour_id, AVG(rating) AS average_rating, COUNT(*) AS review_count');

        return DB::table('tours')
            ->join('destinations', 'destinations.destination_id', '=', 'tours.destination_id')
            ->leftJoinSub($ratings, 'ratings', 'ratings.tour_id', '=', 'tours.tour_id')
            ->select('tours.*', 'destinations.country', 'destinations.name as destination',
                'ratings.average_rating', 'ratings.review_count');
    }
}
