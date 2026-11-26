<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPromotionController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $promotions = DB::table('promotions')->orderByDesc('promotion_id')->paginate(12);

        if ($promotions->currentPage() > $promotions->lastPage()) {
            return redirect()->to($promotions->url($promotions->lastPage()));
        }

        return view('admin.promotions.index', [
            'promotions' => $promotions,
            'today' => today('Asia/Tomsk')->toDateString(),
        ]);
    }

    public function create(): View
    {
        return $this->form();
    }

    public function store(Request $request): RedirectResponse
    {
        $this->save($request);

        return to_route('admin.promotions.index', [], 303)->with('status', 'Акция добавлена.');
    }

    public function edit(string $promotionId): View
    {
        return $this->form($this->findPromotion($promotionId));
    }

    public function update(Request $request, string $promotionId): RedirectResponse
    {
        $this->save($request, $promotionId);

        return to_route('admin.promotions.index', [], 303)->with('status', 'Изменения сохранены.');
    }

    public function confirmDelete(string $promotionId): View
    {
        return view('admin.promotions.delete', ['promotion' => $this->findPromotion($promotionId)]);
    }

    public function destroy(string $promotionId): RedirectResponse
    {
        abort_if(DB::table('promotions')->where('promotion_id', $promotionId)->delete() === 0, 404);

        return to_route('admin.promotions.index', [], 303)->with('status', 'Акция удалена.');
    }

    private function findPromotion(string $promotionId): object
    {
        $promotion = DB::table('promotions')->where('promotion_id', $promotionId)->first();
        abort_if($promotion === null, 404);

        return $promotion;
    }

    private function form(?object $promotion = null): View
    {
        $selectedTours = $promotion
            ? DB::table('promotion_tours')->where('promotion_id', $promotion->promotion_id)->pluck('tour_id')->all()
            : [];

        $tours = DB::table('tours')
            ->where(fn ($query) => $query->whereNull('archived_at')->orWhereIn('tour_id', $selectedTours))
            ->orderBy('title')->orderBy('departure_date')->get(['tour_id', 'title', 'departure_date', 'archived_at']);

        return view('admin.promotions.form', compact('promotion', 'tours', 'selectedTours'));
    }

    private function save(Request $request, ?string $promotionId = null): void
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:10000'],
            'starts_at' => ['bail', 'required', 'date_format:Y-m-d', 'after_or_equal:1000-01-01'],
            'ends_at' => ['bail', 'required', 'date_format:Y-m-d', 'after_or_equal:starts_at'],
            'tour_ids' => ['sometimes', 'array', 'list'],
            'tour_ids.*' => ['bail', 'required', 'integer', 'distinct', 'exists:tours,tour_id'],
        ], [
            'required' => 'Заполни поле «:attribute».',
            'string' => 'Проверь поле «:attribute».',
            'max' => 'В поле «:attribute» допустимо не более :max символов.',
            'date_format' => 'Проверь дату в поле «:attribute».',
            'starts_at.after_or_equal' => 'Проверь год начала акции.',
            'ends_at.after_or_equal' => 'Дата окончания акции не может быть раньше даты начала.',
            'tour_ids.*.exists' => 'Один из выбранных туров больше не существует.',
        ], [
            'title' => 'Название акции',
            'description' => 'Описание',
            'starts_at' => 'Дата начала',
            'ends_at' => 'Дата окончания',
        ]);

        $tourIds = $data['tour_ids'] ?? [];
        unset($data['tour_ids']);

        DB::transaction(function () use ($promotionId, $data, $tourIds): void {
            if ($promotionId === null) {
                $promotionId = DB::table('promotions')->insertGetId($data, 'promotion_id');
            } else {
                $query = DB::table('promotions')->where('promotion_id', $promotionId);
                abort_if((clone $query)->lockForUpdate()->first(['promotion_id']) === null, 404);
                $query->update($data);
                DB::table('promotion_tours')->where('promotion_id', $promotionId)->delete();
            }

            if ($tourIds !== []) {
                DB::table('promotion_tours')->insert(array_map(fn ($tourId) => [
                    'promotion_id' => $promotionId,
                    'tour_id' => $tourId,
                ], $tourIds));
            }
        }, 3);
    }
}
