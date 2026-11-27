<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReviewController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $reviews = DB::table('tour_reviews')
            ->join('tours', 'tours.tour_id', '=', 'tour_reviews.tour_id')
            ->join('users', 'users.user_id', '=', 'tour_reviews.user_id')
            ->orderByRaw("CASE WHEN tour_reviews.status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('tour_reviews.created_at')->orderByDesc('tour_reviews.review_id')
            ->paginate(12, ['tour_reviews.review_id', 'tour_reviews.rating', 'tour_reviews.content',
                'tour_reviews.status', 'tour_reviews.created_at', 'tours.tour_id', 'tours.title', 'tours.archived_at',
                'users.first_name', 'users.last_name']);

        if ($reviews->currentPage() > $reviews->lastPage()) {
            return redirect()->to($reviews->url($reviews->lastPage()));
        }

        return view('admin.reviews.index', ['reviews' => $reviews]);
    }

    public function update(Request $request, string $reviewId): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:published,rejected'],
        ], [
            'status.required' => 'Выбери, что сделать с отзывом.',
            'status.string' => 'Выбери, что сделать с отзывом.',
            'status.in' => 'Выбери, что сделать с отзывом.',
        ]);

        $review = DB::table('tour_reviews')->where('review_id', $reviewId);
        $updated = (clone $review)->where('status', 'pending')->update(['status' => $data['status']]);
        abort_if($updated === 0 && ! $review->exists(), 404);

        $message = match (true) {
            $updated === 0 => 'Этот отзыв уже обработан.',
            $data['status'] === 'published' => 'Отзыв опубликован.',
            default => 'Отзыв отклонён.',
        };

        return to_route('admin.reviews.index', [], 303)->with('status', $message);
    }
}
