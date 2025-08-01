<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property\Property;
use App\Models\Property\PropertyTranslation;
use App\Models\Amenity\Amenity;
use App\Models\Category\Category;
use App\Models\City\City;
use App\Models\Type\Type;
use App\Models\Orientation\Orientation;
use App\Models\PropertyCondition\PropertyCondition;
use App\Models\Floor\Floor;
use Illuminate\Support\Facades\DB;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['id' => 1, 'name_ar' => 'دمشق', 'name_en' => 'Damascus'],
            ['id' => 2, 'name_ar' => 'ريف دمشق', 'name_en' => 'Rif Dimashq'],
            ['id' => 3, 'name_ar' => 'حلب', 'name_en' => 'Aleppo'],
            ['id' => 4, 'name_ar' => 'حمص', 'name_en' => 'Homs'],
            ['id' => 5, 'name_ar' => 'حماة', 'name_en' => 'Hama'],
        ];
        $categories = Category::all();
        $types = Type::all();
        $orientations = Orientation::all();
        $conditions = PropertyCondition::all();
        $floors = Floor::all();
        $amenities = Amenity::all();

        $propertyCount = 25;

        for ($i = 1; $i <= $propertyCount; $i++) {
            DB::beginTransaction();

            $city = $cities[array_rand($cities)];
            $category = $categories->random();
            $type = $types->random();
            $orientation = $orientations->random();
            $condition = $conditions->random();
            $floor = $floors->random();

            $property = Property::updateOrCreate(
                ['id' => $i],
                [
                    'city_id' => $city['id'],
                    'category_id' => $category->id,
                    'type_id' => $type->id,
                    'orientation_id' => $orientation->id,
                    'condition_id' => $condition->id,
                    'floor_id' => $floor->id,
                    'rooms_count' => rand(1, 5),
                    'baths' => rand(1, 3),
                    'area' => rand(50, 300),
                    'price' => rand(50000, 500000),
                    'created_by' => 1,
                    'updated_by' => 1,
                ]
            );

            $titleAr = "عقار رقم $i في " . $city['name_ar'];
            $titleEn = "Property #$i in " . $city['name_en'];

            $descriptionAr = "وصف تفصيلي للعقار رقم $i في " . $city['name_ar'] . " مع جميع المميزات.";
            $descriptionEn = "Detailed description for property #$i in " . $city['name_en'] . " with all features.";

            $keywordsAr = "عقار, $city[name_ar], $category->name, $type->name";
            $keywordsEn = "property, $city[name_en], $category->name, $type->name";

            PropertyTranslation::updateOrCreate(
                ['property_id' => $property->id, 'locale' => 'ar'],
                [
                    'title' => $titleAr,
                    'description' => $descriptionAr,
                    'keywords' => $keywordsAr,
                ]
            );

            PropertyTranslation::updateOrCreate(
                ['property_id' => $property->id, 'locale' => 'en'],
                [
                    'title' => $titleEn,
                    'description' => $descriptionEn,
                    'keywords' => $keywordsEn,
                ]
            );

            // Attach random amenities (1 to 5)
            $amenityIds = $amenities->random(rand(1, 5))->pluck('id')->toArray();
            $property->amenities()->sync($amenityIds);

            DB::commit();
        }
    }
}
