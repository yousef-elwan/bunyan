<?php

namespace Database\Seeders;

use App\Models\CustomAttribute\CustomAttribute;
use App\Models\CustomAttribute\CustomAttributeTranslation;
use App\Models\CustomAttribute\CustomAttributeValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomAttributesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = DB::table('categories')->get();

        $attributesData = [
            [
                'name_ar' => 'عدد الغرف',
                'name_en' => 'Number of rooms',
                'values' => [
                    ['value_ar' => '1', 'value_en' => '1'],
                    ['value_ar' => '2', 'value_en' => '2'],
                    ['value_ar' => '3', 'value_en' => '3'],
                    ['value_ar' => '4', 'value_en' => '4'],
                    ['value_ar' => '5 أو أكثر', 'value_en' => '5 or more'],
                ]
            ],
            [
                'name_ar' => 'عمر البناء',
                'name_en' => 'Building age',
                'values' => [
                    ['value_ar' => 'جديد', 'value_en' => 'New'],
                    ['value_ar' => '1-5 سنوات', 'value_en' => '1-5 years'],
                    ['value_ar' => '6-10 سنوات', 'value_en' => '6-10 years'],
                    ['value_ar' => '11-20 سنة', 'value_en' => '11-20 years'],
                    ['value_ar' => 'أكثر من 20 سنة', 'value_en' => 'Over 20 years'],
                ]
            ],
            [
                'name_ar' => 'الموقع',
                'name_en' => 'Location',
                'values' => [
                    ['value_ar' => 'منطقة حيوية', 'value_en' => 'Prime area'],
                    ['value_ar' => 'شارع رئيسي', 'value_en' => 'Main street'],
                    ['value_ar' => 'شارع فرعي', 'value_en' => 'Side street'],
                ]
            ],
            [
                'name_ar' => 'الطابق',
                'name_en' => 'Floor',
                'values' => [
                    ['value_ar' => 'طابق أرضي', 'value_en' => 'Ground floor'],
                    ['value_ar' => 'طابق علوي', 'value_en' => 'Upper floor'],
                    ['value_ar' => 'طابق سفلي', 'value_en' => 'Basement'],
                ]
            ],
        ];

        DB::beginTransaction();
        foreach ($categories as $category) {
            foreach ($attributesData as $attribute) {

                $createdModel = CustomAttribute::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'type' => 'select',
                    ],
                    ['created_by' => 1, 'updated_by' => 1]
                );

                $createdAr = CustomAttributeTranslation::updateOrCreate(
                    ['attribute_id' => $createdModel->id, 'locale' => 'ar'],
                    ['name' => $attribute['name_ar']]
                );

                $createdEn =   CustomAttributeTranslation::updateOrCreate(
                    ['attribute_id' => $createdModel->id, 'locale' => 'en'],
                    ['name' => $attribute['name_en']]
                );
                foreach ($attribute['values'] as $value) {
                    CustomAttributeValue::updateOrCreate(
                        ['attribute_translation_id' => $createdEn->id],
                        ['value' => $value['value_en']]
                    );
                    CustomAttributeValue::updateOrCreate(
                        ['attribute_translation_id' => $createdAr->id],
                        ['value' => $value['value_ar']]
                    );
                }
            }
        }
        DB::commit();
    }
}
