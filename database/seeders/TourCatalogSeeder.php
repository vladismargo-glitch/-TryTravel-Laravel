<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TourCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            'Горящие туры' => [
                ['Grand Kaptan Hotel & Spa', 'Турция', 'Аланья (Мана)', '2026-12-10', 7, 'Питание', 58900, 'Grand Kaptan Hotel & Spa.png'],
                ['Jumeirah Beach Hotel', 'ОАЭ', 'Дубай', '2026-12-11', 9, 'Питание', 99800, 'Jumeirah Beach Hotel.png'],
                ['Phuket Marriott Resort & Spa', 'Таиланд', 'Пхукет', '2026-12-12', 10, 'Питание', 84500, 'Phuket Marriott Resort & Spa.png'],
                ['Grand Resort Hurghada', 'Египет', 'Хургада', '2026-12-13', 8, 'Питание', 63200, 'Grand Resort Hurghada.png'],
                ['Rixos Bab Al Bahr', 'ОАЭ', 'Рас-эль-Хайма', '2026-12-14', 11, 'Питание', 114300, 'Rixos Bab Al Bahr.png'],
            ],
            'Новогодние туры' => [
                ['Riviera Hotel & Spa', 'Турция', 'Аланья (Алапул)', '2026-12-25', 7, 'Всё включено', 72500, 'image 216.png'],
                ['JA Palm Tree Court', 'ОАЭ', 'Дубай', '2026-12-28', 7, 'Всё включено', 95000, 'image 216 (1).png'],
                ['Holiday Inn Resort Phuket', 'Таиланд', 'Пхукет', '2026-12-30', 10, 'Завтрак', 85000, 'image 216 (2).png'],
                ['Jaz Aquamarine Resort', 'Египет', 'Хургада', '2027-01-01', 7, 'Всё включено', 68000, 'image 216 (3).png'],
                ['Hilton Ras Al Khaimah Resort', 'ОАЭ', 'Рас-эль-Хайма', '2027-01-02', 8, 'Всё включено', 88000, 'image 216 (4).png'],
            ],
        ];

        DB::transaction(function () use ($catalog): void {
            foreach ($catalog as $category => $offers) {
                $categoryId = DB::table('tour_categories')
                    ->where('name', $category)
                    ->lockForUpdate()
                    ->value('category_id');

                if ($categoryId === null) {
                    throw new \RuntimeException('Сначала импортируйте схему TryTravel с категориями.');
                }

                foreach ($offers as [$title, $country, $destination, $date, $nights, $meal, $price, $image]) {
                    $location = ['country' => $country, 'name' => $destination];
                    $destinationId = DB::table('destinations')->where($location)->value('destination_id')
                        ?? DB::table('destinations')->insertGetId($location, 'destination_id');

                    $identity = [
                        'category_id' => $categoryId,
                        'destination_id' => $destinationId,
                        'title' => $title,
                        'departure_city' => 'Томск',
                        'departure_date' => $date,
                    ];

                    if (DB::table('tours')->where($identity)->doesntExist()) {
                        DB::table('tours')->insert($identity + [
                            'nights' => $nights,
                            'meal_plan' => $meal,
                            'price' => $price,
                            'image_url' => 'images/'.$image,
                            'description' => 'Демонстрационное предложение из макета TryTravel.',
                        ]);
                    }
                }
            }
        });
    }
}
