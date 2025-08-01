<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category\Category;
use App\Models\Category\CategoryTranslation;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['id' => 1, 'title_ar' => 'بيوت', 'title_en' => 'Houses'],
            ['id' => 2, 'title_ar' => 'شقق', 'title_en' => 'Apartments'],
            ['id' => 3, 'title_ar' => 'مزارع', 'title_en' => 'Farms'],
            ['id' => 4, 'title_ar' => 'محلات تجارية', 'title_en' => 'Commercial Shops'],
            ['id' => 5, 'title_ar' => 'فلل', 'title_en' => 'Villas'],
            ['id' => 6, 'title_ar' => 'استوديوهات', 'title_en' => 'Studios'],
            ['id' => 7, 'title_ar' => 'تاون هاوس', 'title_en' => 'Townhouses'],
            ['id' => 8, 'title_ar' => 'مكاتب', 'title_en' => 'Offices'],
        ];

        foreach ($categories as $category) {
            $categoryModel = Category::updateOrCreate(
                ['id' => $category['id']],
                ['created_by' => 1, 'updated_by' => 1]
            );

            CategoryTranslation::updateOrCreate(
                ['category_id' => $categoryModel->id, 'locale' => 'ar'],
                ['name' => $category['title_ar']]
            );

            CategoryTranslation::updateOrCreate(
                ['category_id' => $categoryModel->id, 'locale' => 'en'],
                ['name' => $category['title_en']]
            );
        }
    }
}
