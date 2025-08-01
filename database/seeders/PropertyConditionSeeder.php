<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PropertyCondition\PropertyCondition;
use App\Models\PropertyCondition\PropertyConditionTranslation;

class PropertyConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conditions = [
            ['id' => 1, 'title_ar' => 'جيد', 'title_en' => 'Good'],
            ['id' => 2, 'title_ar' => 'بحاجة لتجديد', 'title_en' => 'Needs Renovation'],
            ['id' => 3, 'title_ar' => 'سوبر ديلوكس', 'title_en' => 'Super Deluxe'],
            ['id' => 4, 'title_ar' => 'ديلوكس', 'title_en' => 'Deluxe'],
            ['id' => 5, 'title_ar' => 'جيد جدًا', 'title_en' => 'Very Good'],
            ['id' => 6, 'title_ar' => 'عادي', 'title_en' => 'Normal'],
        ];

        foreach ($conditions as $condition) {
            $conditionModel = PropertyCondition::updateOrCreate(
                ['id' => $condition['id']],
                ['created_by' => 1, 'updated_by' => 1]
            );

            PropertyConditionTranslation::updateOrCreate(
                ['property_condition_id' => $conditionModel->id, 'locale' => 'ar'],
                ['name' => $condition['title_ar']]
            );

            PropertyConditionTranslation::updateOrCreate(
                ['property_condition_id' => $conditionModel->id, 'locale' => 'en'],
                ['name' => $condition['title_en']]
            );
        }
    }
}
