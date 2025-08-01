<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContractType\ContractType;
use App\Models\ContractType\ContractTypeTranslation;

class ContractTypeSeeder extends Seeder
{
    /*
        php artisan db:seed --class=ContractTypeSeeder
    */

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contractTypes = [
            ['id' => 1, 'title_ar' => 'طابو اخضر', 'title_en' => 'Title Deed'],
            ['id' => 2, 'title_ar' => 'حكم محكمة', 'title_en' => 'Court Ruling'],
            ['id' => 3, 'title_ar' => 'إقرار وشطب', 'title_en' => 'Acknowledgment'],
        ];

        foreach ($contractTypes as $contractType) {
            $contractTypeModel = ContractType::updateOrCreate(
                ['id' => $contractType['id']],
                ['created_by' => 1, 'updated_by' => 1]
            );

            ContractTypeTranslation::updateOrCreate(
                ['contract_type_id' => $contractTypeModel->id, 'locale' => 'ar'],
                ['name' => $contractType['title_ar']]
            );

            ContractTypeTranslation::updateOrCreate(
                ['contract_type_id' => $contractTypeModel->id, 'locale' => 'en'],
                ['name' => $contractType['title_en']]
            );
        }
    }
}
