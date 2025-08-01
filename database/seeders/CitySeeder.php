<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City\City;
use App\Models\City\CityTranslation;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['id' => 1, 'title_ar' => 'دمشق', 'title_en' => 'Damascus'],
            ['id' => 2, 'title_ar' => 'ريف دمشق', 'title_en' => 'Rif Dimashq'],
            ['id' => 3, 'title_ar' => 'حلب', 'title_en' => 'Aleppo'],
            ['id' => 4, 'title_ar' => 'حمص', 'title_en' => 'Homs'],
            ['id' => 5, 'title_ar' => 'حماة', 'title_en' => 'Hama'],
            ['id' => 6, 'title_ar' => 'اللاذقية', 'title_en' => 'Latakia'],
            ['id' => 7, 'title_ar' => 'طرطوس', 'title_en' => 'Tartus'],
            ['id' => 8, 'title_ar' => 'إدلب', 'title_en' => 'Idlib'],
            ['id' => 9, 'title_ar' => 'دير الزور', 'title_en' => 'Deir ez-Zor'],
            ['id' => 10, 'title_ar' => 'الحسكة', 'title_en' => 'Al-Hasakah'],
            ['id' => 11, 'title_ar' => 'الرقة', 'title_en' => 'Raqqa'],
            ['id' => 12, 'title_ar' => 'درعا', 'title_en' => 'Daraa'],
            ['id' => 13, 'title_ar' => 'السويداء', 'title_en' => 'As-Suwayda'],
            ['id' => 14, 'title_ar' => 'القنيطرة', 'title_en' => 'Quneitra'],
        ];

        foreach ($cities as $city) {
            $cityModel = City::updateOrCreate(
                ['id' => $city['id']],
                ['created_by' => 1, 'updated_by' => 1]
            );

            CityTranslation::updateOrCreate(
                ['city_id' => $cityModel->id, 'locale' => 'ar'],
                ['name' => $city['title_ar']]
            );

            CityTranslation::updateOrCreate(
                ['city_id' => $cityModel->id, 'locale' => 'en'],
                ['name' => $city['title_en']]
            );
        }
    }
}
