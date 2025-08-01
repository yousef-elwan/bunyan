<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Floor\Floor;
use App\Models\Floor\FloorTranslation;

class FloorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $floors = [
            ['id' => 1, 'value' => 1, 'title_ar' => 'طابق أول', 'title_en' => 'First Floor'],
            ['id' => 2, 'value' => 2, 'title_ar' => 'طابق ثاني', 'title_en' => 'Second Floor'],
            ['id' => 3, 'value' => 3, 'title_ar' => 'طابق ثالث', 'title_en' => 'Third Floor'],
            ['id' => 4, 'value' => 4, 'title_ar' => 'طابق رابع', 'title_en' => 'Fourth Floor'],
            ['id' => 5, 'value' => 5, 'title_ar' => 'طابق خامس', 'title_en' => 'Fifth Floor'],
        ];

        foreach ($floors as $floor) {
            $floorModel = Floor::updateOrCreate(
                ['id' => $floor['id']],
                ['value' => $floor['value'], 'created_by' => 1, 'updated_by' => 1]
            );

            FloorTranslation::updateOrCreate(
                ['floor_id' => $floorModel->id, 'locale' => 'ar'],
                ['name' => $floor['title_ar']]
            );

            FloorTranslation::updateOrCreate(
                ['floor_id' => $floorModel->id, 'locale' => 'en'],
                ['name' => $floor['title_en']]
            );
        }
    }
}
