<?php

namespace App\Http\Controllers;

use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TourReviewController extends Controller
{
    public function create(string $tourId): RedirectResponse
    {
        return to_route('tours.show', $tourId)->withFragment('reviews');
    }

    public function store(Request $request, string $tourId): RedirectResponse
    {
        $input = $request->only('rating', 'content');
        $validator = Validator::make($input, [
            'rating' => ['bail', 'required', 'integer', 'between:1,5'],
            'content' => ['bail', 'required', 'string', 'max:2000'],
        ], [
            'rating.required' => 'Выбери оценку.',
            'rating.integer' => 'Выбери оценку от 1 до 5.',
            'rating.between' => 'Выбери оценку от 1 до 5.',
            'content.required' => 'Напиши текст отзыва.',
            'content.string' => 'Проверь текст отзыва.',
            'content.max' => 'Максимальная длина отзыва — 2000 символов.',
        ]);

        $redirect = to_route('tours.show', $tourId, 303)->withFragment('reviews');
        if ($validator->fails()) {
            return $redirect->withErrors($validator, 'review')->withInput($input);
        }

        $userId = $request->user()->getKey();
        $data = $validator->validated();

        try {
            DB::transaction(function () use ($tourId, $userId, $data): void {
                $tour = DB::table('tours')->where('tour_id', $tourId)->whereNull('archived_at')
                    ->sharedLock()->first(['tour_id']);
                abort_if($tour === null, 404);

                DB::table('tour_reviews')->insert([
                    'tour_id' => $tour->tour_id,
                    'user_id' => $userId,
                    'rating' => $data['rating'],
                    'content' => $data['content'],
                    'status' => 'pending',
                ]);
            }, 3);
        } catch (UniqueConstraintViolationException) {
            return $redirect->with('review_status', 'Ты уже оставил отзыв об этом туре.');
        }

        return $redirect->with('review_status', 'Отзыв отправлен на модерацию.');
    }
}
