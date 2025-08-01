<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Type\Type;
use App\Models\Type\TypeTranslation;

class TypeSeeder extends Seeder
{
    /*
        php artisan db:seed --class=TypeSeeder
        php artisan db:seed --class=CitySeeder
        php artisan db:seed --class=FloorSeeder
        php artisan db:seed --class=OrientationSeeder
        php artisan db:seed --class=PropertyConditionSeeder
        php artisan db:seed --class=CategorySeeder
        php artisan db:seed --class=AmenitySeeder 
        php artisan db:seed --class=CustomAttributesSeeder
        php artisan db:seed --class=ContractTypeSeeder
        php artisan db:seed --class=ShowingRequestTypeSeeder
     */

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['id' => 1, 'title_ar' => 'شراء', 'title_en' => 'Purchase'],
            ['id' => 2, 'title_ar' => 'رهن', 'title_en' => 'Mortgage'],
            ['id' => 3, 'title_ar' => 'آجار', 'title_en' => 'Rent'],
        ];

        foreach ($types as $type) {
            $typeModel = Type::updateOrCreate(
                ['id' => $type['id']],
                ['created_by' => 1, 'updated_by' => 1]
            );

            TypeTranslation::updateOrCreate(
                ['type_id' => $typeModel->id, 'locale' => 'ar'],
                ['name' => $type['title_ar']]
            );

            TypeTranslation::updateOrCreate(
                ['type_id' => $typeModel->id, 'locale' => 'en'],
                ['name' => $type['title_en']]
            );
        }
    }
}
