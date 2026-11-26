<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $today = today('Asia/Tomsk')->toDateString();
        $promotions = DB::table('promotions')
            ->where('starts_at', '<=', $today)->where('ends_at', '>=', $today)
            ->orderBy('ends_at')->orderByDesc('promotion_id')->paginate(12);

        if ($promotions->currentPage() > $promotions->lastPage()) {
            return redirect()->to($promotions->url($promotions->lastPage()));
        }

        $promotionTours = DB::table('promotion_tours')
            ->join('tours', 'tours.tour_id', '=', 'promotion_tours.tour_id')
            ->whereIn('promotion_tours.promotion_id', $promotions->pluck('promotion_id'))
            ->whereNull('tours.archived_at')->where('tours.departure_date', '>=', $today)
            ->orderBy('tours.departure_date')->orderBy('tours.tour_id')
            ->get(['promotion_tours.promotion_id', 'tours.tour_id', 'tours.title', 'tours.image_url'])
            ->groupBy('promotion_id');

        return view('promotions', compact('promotions', 'promotionTours'));
    }
}
