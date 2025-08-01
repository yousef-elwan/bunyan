<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property\Property;
use App\Models\Property\PropertyTranslation;
use App\Models\Property\PropertyKeyword;
use App\Models\PropertyFAQ\PropertyFAQ;
use App\Models\PropertyFAQ\PropertyFAQTranslation;
use App\Models\Property\PropertyAttributeValue;
use App\Models\Property\PropertyAvailableTime;
use App\Models\Amenity\Amenity;
use App\Models\CustomAttribute\CustomAttribute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Property\PropertyGallery;

class PropertyFullSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $cities = \App\Models\City\City::all();
        $categories = \App\Models\Category\Category::all();
        $types = \App\Models\Type\Type::all();
        $orientations = \App\Models\Orientation\Orientation::all();
        $conditions = \App\Models\PropertyCondition\PropertyCondition::all();
        $floors = \App\Models\Floor\Floor::all();
        $contractTypes = \App\Models\ContractType\ContractType::all();
        $amenities = Amenity::all();
        $customAttributes = CustomAttribute::all();

        $locales = ['ar', 'en'];
        $propertyCount = 25;

        for ($i = 1; $i <= $propertyCount; $i++) {
            DB::beginTransaction();

            $city = $cities->random();
            $category = $categories->random();
            $type = $types->random();
            $orientation = $orientations->random();
            $condition = $conditions->random();
            $floor = $floors->random();
            $contractType = $contractTypes->random();

            $property = Property::create(
                [
                    'city_id' => $city->id,
                    'image' => '',
                    'user_id' => 1,
                    'latitude' => rand(-90000000, 90000000) / 1000000,
                    'longitude' => rand(-180000000, 180000000) / 1000000,
                    'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'year_built' => rand(1900, 2024),
                    'is_new' => rand(0, 1),
                    'is_featured' => rand(0, 1),
                    'price' => rand(50000, 500000),
                    'size' => rand(50, 300),
                    'rooms_count' => rand(1, 5),
                    'contract_type_id' => $contractType->id,
                    'floor_id' => $floor->id,
                    'status_id' => '',
                    'currency_id' => '',
                    'category_id' => $category->id,
                    'orientation_id' => $orientation->id,
                    'type_id' => $type->id,
                    'created_by' => 1,
                    'condition_id' => $condition->id,
                    'updated_by' => 1,
                ]
            );

            // Copy images from temp folder to new property folder and insert into property_gallery
            $tempPath = storage_path('app/public/property/temp');
            $propertyPath = storage_path("app/public/property/{$property->id}/images");

            if (!file_exists($propertyPath)) {
                mkdir($propertyPath, 0755, true);
            }

            $tempImagesPath = $tempPath . DIRECTORY_SEPARATOR . 'images';
            $files = scandir($tempImagesPath);
            $imageFiles = [];
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $sourceFile = $tempImagesPath . DIRECTORY_SEPARATOR . $file;
                $destFile = $propertyPath . DIRECTORY_SEPARATOR . $file;
                copy($sourceFile, $destFile);

                PropertyGallery::create([
                    'property_id' => $property->id,
                    'name' => $file,
                    'created_by' => 1,
                    'updated_by' => 1,
                ]);
                $imageFiles[] = $file;
            }

            // Update property image with a random image from the copied images
            if (count($imageFiles) > 0) {
                $randomImage = $imageFiles[array_rand($imageFiles)];
                $property->image = $randomImage;
                $property->save();
            }


            $data = [
                'property_id' => $property['id'],
                'keywords' => [],
                'locale' => [],
            ];

            foreach ($locales as $locale) {
                $data['locale'][] =  [
                    'locale' => $locale,
                    'location' => $locale === 'ar' ? "موقع فريد وطويل للعقار رقم $i في " . $city['name_ar'] . " " . uniqid() : "Unique location for property #$i in " . $city['name_en'] . " " . uniqid(),
                    'content' => $locale === 'ar' ? str_repeat("وصف تفصيلي فريد وطويل جدا للعقار رقم $i في " . $city['name_ar'] . " مع جميع المميزات والتفاصيل الدقيقة. ", 50) . uniqid() : str_repeat("Very long unique detailed description for property #$i in " . $city['name_en'] . " with all features. ", 50) . uniqid(),
                    'slug' => $locale === 'ar' ? "slug-ar-$i-" . uniqid() : "slug-en-$i-" . uniqid(),
                    'meta_title' => $locale === 'ar' ? "عنوان ميتا فريد وطويل للعقار رقم $i " . uniqid() : "Unique long meta title for property #$i " . uniqid(),
                    'meta_description' => $locale === 'ar' ? "وصف ميتا فريد وطويل للعقار رقم $i " . uniqid() : "Unique long meta description for property #$i " . uniqid(),
                    'created_by' => 1,
                    'updated_by' => 1,
                ];
            }

            foreach ($data['locale'] as $key => $localeData) {
                PropertyTranslation::create(
                    [
                        'property_id' => $property->id,
                        'locale' => $localeData['locale'],
                        'location' => $localeData['location'],
                        'content' => $localeData['content'],
                        'slug' => $localeData['slug'],
                        'meta_title' => $localeData['meta_title'],
                        'meta_description' => $localeData['meta_description'],
                        'created_by' => 1,
                        'updated_by' => 1,
                    ]
                );
            }

            foreach ($data['keywords'] as $key => $keyword) {
                PropertyKeyword::updateOrCreate(
                    [
                        'property_id' => $property->id,
                        'keyword' => $keyword,
                        'created_by' => 1,
                        'updated_by' => 1,
                    ]
                );
            }



            // Attach random amenities (1 to 5)
            $amenityIds = $amenities->random(rand(1, 5))->pluck('id')->toArray();
            $property->amenities()->sync($amenityIds);

            // Property FAQs and translations
            $faq = PropertyFAQ::create(
                array_merge(['property_id' => $property->id], ['created_by' => 1, 'updated_by' => 1])
            );

            foreach ($locales as $locale) {
                $faqTitle = $locale === 'ar' ? "سؤال شائع للعقار رقم {$property->id} في {$locale} " . uniqid() : "FAQ Title for property {$property->id} in {$locale} " . uniqid();
                $faqContent = $locale === 'ar' ? str_repeat("محتوى سؤال شائع طويل للعقار رقم {$property->id} في {$locale}. ", 20) : str_repeat("Long FAQ content for property {$property->id} in {$locale}. ", 20);

                PropertyFAQTranslation::create(
                    [
                        'property_faq_id' => $faq->id,
                        'locale' => $locale,
                        'title' => $faqTitle,
                        'content' => $faqContent,
                        'created_by' => 1,
                        'updated_by' => 1,
                    ]
                );
            }


            // Property attributes
            foreach ($customAttributes as $attribute) {
                $attributeValues = $attribute->customAttributeValues;
                if ($attributeValues->isNotEmpty()) {
                    $attrValue = $attributeValues->random();
                    PropertyAttributeValue::updateOrCreate(
                        ['property_id' => $property->id, 'attribute_id' => $attribute->id],
                        [
                            'value' => $attrValue->value,
                            'created_by' => 1,
                            'updated_by' => 1,
                        ]
                    );
                }
            }

            // Property available times - insert 4 rows with different times
            for ($j = 1; $j <= 4; $j++) {
                PropertyAvailableTime::updateOrCreate(
                    ['property_id' => $property->id, 'time' => now()->addDays(rand(1, 30))],
                    ['created_by' => 1, 'updated_by' => 1]
                );
            }

            DB::commit();
        }
    }
}
