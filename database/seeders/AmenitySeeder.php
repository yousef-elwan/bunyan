<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Amenity\Amenity;
use App\Models\Amenity\AmenityTranslation;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            ['id' => 1, 'title_ar' => 'حديقة', 'title_en' => 'Garden'],
            ['id' => 2, 'title_ar' => 'بلكون', 'title_en' => 'Balcony'],
            ['id' => 3, 'title_ar' => 'خزان', 'title_en' => 'Tank'],
            ['id' => 4, 'title_ar' => 'خزن مطبخ', 'title_en' => 'Kitchen Storage'],
            ['id' => 5, 'title_ar' => 'طاقة شمسية', 'title_en' => 'Solar Energy'],
            ['id' => 6, 'title_ar' => 'مكيف هواء', 'title_en' => 'Air Conditioning'],
            ['id' => 7, 'title_ar' => 'موقف سيارات', 'title_en' => 'Parking'],
            ['id' => 8, 'title_ar' => 'مسبح', 'title_en' => 'Swimming Pool'],
            ['id' => 9, 'title_ar' => 'مفروش', 'title_en' => 'Furnished'],
            ['id' => 10, 'title_ar' => 'خدمة إنترنت', 'title_en' => 'Internet Service'],
            ['id' => 11, 'title_ar' => 'هاتف أرضي', 'title_en' => 'Landline Phone'],
            ['id' => 12, 'title_ar' => 'تغطية شبكة الإتصالات', 'title_en' => 'Network Coverage'],
        ];

        foreach ($amenities as $amenity) {
            $amenityModel = Amenity::updateOrCreate(
                ['id' => $amenity['id']],
                ['created_by' => 1, 'updated_by' => 1]
            );

            AmenityTranslation::updateOrCreate(
                ['amenity_id' => $amenityModel->id, 'locale' => 'ar'],
                ['name' => $amenity['title_ar']]
            );

            AmenityTranslation::updateOrCreate(
                ['amenity_id' => $amenityModel->id, 'locale' => 'en'],
                ['name' => $amenity['title_en']]
            );
        }
    }
}
