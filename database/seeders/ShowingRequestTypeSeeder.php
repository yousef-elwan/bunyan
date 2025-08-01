<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShowingRequestType\ShowingRequestType;
use App\Models\ShowingRequestType\ShowingRequestTypeTranslation;

class ShowingRequestTypeSeeder extends Seeder
{
    /*
        php artisan db:seed --class=ShowingRequestTypeSeeder
    */

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $showingRequestTypes = [
            ['id' => 1, 'title_ar' => 'جولة', 'title_en' => 'Tour'],
            ['id' => 2, 'title_ar' => 'محادثة فيديو', 'title_en' => 'Video Conversation'],
        ];

        foreach ($showingRequestTypes as $showingRequestType) {
            $showingRequestTypeModel = ShowingRequestType::updateOrCreate(
                ['id' => $showingRequestType['id']],
                ['created_by' => 1, 'updated_by' => 1]
            );

            ShowingRequestTypeTranslation::updateOrCreate(
                ['showing_request_type_id' => $showingRequestTypeModel->id, 'locale' => 'ar'],
                ['name' => $showingRequestType['title_ar']]
            );

            ShowingRequestTypeTranslation::updateOrCreate(
                ['showing_request_type_id' => $showingRequestTypeModel->id, 'locale' => 'en'],
                ['name' => $showingRequestType['title_en']]
            );
        }
    }
}
