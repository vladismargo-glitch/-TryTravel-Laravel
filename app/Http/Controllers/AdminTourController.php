<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminTourController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $tours = DB::table('tours')
            ->join('tour_categories', 'tour_categories.category_id', '=', 'tours.category_id')
            ->join('destinations', 'destinations.destination_id', '=', 'tours.destination_id')
            ->select('tours.tour_id', 'tours.title', 'tours.departure_date', 'tours.archived_at',
                'tour_categories.name as category', 'destinations.country', 'destinations.name as destination')
            ->orderByRaw('tours.archived_at IS NOT NULL')
            ->orderByDesc('tours.tour_id')
            ->paginate(12);

        if ($tours->currentPage() > $tours->lastPage()) {
            return redirect()->to($tours->url($tours->lastPage()));
        }

        return view('admin.tours.index', ['tours' => $tours]);
    }

    public function create(): View
    {
        return $this->form();
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateTour($request);

        DB::transaction(function () use ($data): void {
            DB::table('tours')->insert($this->tourAttributes($data));
        }, 3);

        return to_route('admin.tours.index', [], 303)->with('status', 'Тур добавлен.');
    }

    public function edit(string $tourId): View
    {
        return $this->form($this->findActiveTour($tourId));
    }

    public function update(Request $request, string $tourId): RedirectResponse
    {
        $tour = $this->findActiveTour($tourId);
        $data = $this->validateTour($request, $tour->image_url);

        DB::transaction(function () use ($tourId, $data): void {
            $tour = DB::table('tours')->where('tour_id', $tourId)
                ->whereNull('archived_at')->lockForUpdate()->first(['tour_id']);
            abort_if($tour === null, 404);

            DB::table('tours')->where('tour_id', $tour->tour_id)->update($this->tourAttributes($data));
        }, 3);

        return to_route('admin.tours.index', [], 303)->with('status', 'Изменения сохранены.');
    }

    public function confirmArchive(string $tourId): View
    {
        return view('admin.tours.archive', ['tour' => $this->findActiveTour($tourId)]);
    }

    public function archive(string $tourId): RedirectResponse
    {
        $tour = DB::table('tours')->where('tour_id', $tourId);
        $archived = (clone $tour)->whereNull('archived_at')->update(['archived_at' => now()]);
        abort_if($archived === 0 && ! $tour->exists(), 404);

        return to_route('admin.tours.index', [], 303)->with('status', 'Тур убран из каталога.');
    }

    private function findActiveTour(string $tourId): object
    {
        $tour = DB::table('tours')
            ->join('destinations', 'destinations.destination_id', '=', 'tours.destination_id')
            ->where('tours.tour_id', $tourId)->whereNull('tours.archived_at')
            ->first(['tours.*', 'destinations.country', 'destinations.name as destination']);
        abort_if($tour === null, 404);

        return $tour;
    }

    private function form(?object $tour = null): View
    {
        return view('admin.tours.form', [
            'tour' => $tour,
            'categories' => DB::table('tour_categories')->orderBy('name')->get(),
            'images' => DB::table('tours')->where('image_url', 'like', 'images/%')
                ->distinct()->orderBy('image_url')->pluck('image_url'),
        ]);
    }

    private function validateTour(Request $request, ?string $existingImage = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'category_id' => ['required', 'integer', 'exists:tour_categories,category_id'],
            'country' => ['required', 'string', 'max:100'],
            'destination' => ['required', 'string', 'max:150'],
            'departure_city' => ['required', 'string', 'max:100'],
            'departure_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:1000-01-01'],
            'nights' => ['required', 'integer', 'between:1,65535'],
            'meal_plan' => ['required', 'string', 'max:80'],
            'price' => ['required', 'string', 'regex:/\A[0-9]{1,10}(?:[.,][0-9]{1,2})?\z/'],
            'image_url' => ['bail', 'required', 'string', 'max:500', function (string $attribute, string $value, \Closure $fail) use ($existingImage): void {
                if ($value !== $existingImage && ! $this->isValidImage($value)) {
                    $fail('Выбери изображение из списка или введи ссылку, которая начинается с https://.');
                }
            }],
            'description' => ['nullable', 'string', 'max:10000'],
        ], [
            'required' => 'Заполни поле «:attribute».',
            'string' => 'Проверь поле «:attribute».',
            'max' => 'В поле «:attribute» допустимо не более :max символов.',
            'category_id.integer' => 'Выбери категорию из списка.',
            'category_id.exists' => 'Выбери категорию из списка.',
            'departure_date.date_format' => 'Проверь дату вылета.',
            'departure_date.after_or_equal' => 'Проверь год вылета.',
            'nights.integer' => 'Укажи количество ночей целым числом.',
            'nights.between' => 'Количество ночей должно быть от :min до :max.',
            'price.regex' => 'Укажи цену числом от 0 до 9 999 999 999,99, с точностью до копеек.',
        ], [
            'title' => 'Название тура',
            'category_id' => 'Категория',
            'country' => 'Страна',
            'destination' => 'Курорт или город',
            'departure_city' => 'Город вылета',
            'departure_date' => 'Дата вылета',
            'nights' => 'Количество ночей',
            'meal_plan' => 'Питание',
            'price' => 'Цена',
            'image_url' => 'Изображение',
            'description' => 'Описание',
        ]);
    }

    private function isValidImage(string $value): bool
    {
        if (filter_var($value, FILTER_VALIDATE_URL) !== false) {
            $url = parse_url($value);

            return strtolower($url['scheme'] ?? '') === 'https' && ! isset($url['user']);
        }

        return preg_match('~\Aimages/[^/\\\\]+\.(?:png|jpe?g|webp|gif|avif)\z~i', $value) === 1
            && is_file(public_path($value));
    }

    private function tourAttributes(array $data): array
    {
        $location = ['country' => $data['country'], 'name' => $data['destination']];
        $destinations = DB::table('destinations');
        $destinationId = (clone $destinations)->where($location)->lockForUpdate()->value('destination_id')
            ?? $destinations->insertGetId($location, 'destination_id');

        unset($data['country'], $data['destination']);
        $data['destination_id'] = $destinationId;
        $data['price'] = str_replace(',', '.', $data['price']);
        $data['description'] ??= '';

        return $data;
    }
}
