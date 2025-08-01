<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Orientation\Orientation;
use App\Models\Orientation\OrientationTranslation;

class OrientationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orientations = [
            ['id' => 1, 'title_ar' => 'شمالي', 'title_en' => 'North'],
            ['id' => 2, 'title_ar' => 'شرقي', 'title_en' => 'East'],
            ['id' => 3, 'title_ar' => 'غربي', 'title_en' => 'West'],
            ['id' => 4, 'title_ar' => 'جنوبي', 'title_en' => 'South'],
            ['id' => 5, 'title_ar' => 'شمالي شرقي', 'title_en' => 'Northeast'],
            ['id' => 6, 'title_ar' => 'شمالي غربي', 'title_en' => 'Northwest'],
            ['id' => 7, 'title_ar' => 'جنوبي شرقي', 'title_en' => 'Southeast'],
            ['id' => 8, 'title_ar' => 'جنوبي غربي', 'title_en' => 'Southwest'],
        ];

        foreach ($orientations as $orientation) {
            $orientationModel = Orientation::updateOrCreate(
                ['id' => $orientation['id']],
                ['created_by' => 1, 'updated_by' => 1]
            );

            OrientationTranslation::updateOrCreate(
                ['orientation_id' => $orientationModel->id, 'locale' => 'ar'],
                ['name' => $orientation['title_ar']]
            );

            OrientationTranslation::updateOrCreate(
                ['orientation_id' => $orientationModel->id, 'locale' => 'en'],
                ['name' => $orientation['title_en']]
            );
        }
    }
}
