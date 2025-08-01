<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property\Property;
use App\Models\Property\PropertyKeyword;
use App\Models\PropertyFAQ\PropertyFAQ;
use App\Models\PropertyFAQ\PropertyFAQTranslation;
use App\Models\Property\PropertyAttributeValue;
use App\Models\Property\PropertyAvailableTime;
use App\Models\CustomAttribute\CustomAttribute;
use Illuminate\Support\Facades\DB;

class PropertyDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $properties = Property::all();
        $locales = ['ar', 'en'];
        $customAttributes = CustomAttribute::all();

        foreach ($properties as $property) {
            DB::beginTransaction();

            // Seed property_keywords
            foreach ($locales as $locale) {
                PropertyKeyword::updateOrCreate(
                    ['property_id' => $property->id, 'locale' => $locale],
                    ['keyword' => "Sample keyword for property {$property->id} in {$locale}", 'created_by' => 1, 'updated_by' => 1]
                );
            }

            // Seed property_faqs and property_faq_translations
            $faq = PropertyFAQ::updateOrCreate(
                ['property_id' => $property->id],
                ['created_by' => 1, 'updated_by' => 1]
            );

            foreach ($locales as $locale) {
                PropertyFAQTranslation::updateOrCreate(
                    ['property_faq_id' => $faq->id, 'locale' => $locale],
                    [
                        'title' => "FAQ Title for property {$property->id} in {$locale}",
                        'content' => "FAQ content for property {$property->id} in {$locale}",
                    ]
                );
            }

            // Seed property_attributes
            foreach ($customAttributes as $attribute) {
                PropertyAttributeValue::updateOrCreate(
                    ['property_id' => $property->id, 'attribute_id' => $attribute->id],
                    ['value' => "Sample value for attribute {$attribute->id}", 'created_by' => 1, 'updated_by' => 1]
                );
            }

            // Seed property_available_times
            PropertyAvailableTime::updateOrCreate(
                ['property_id' => $property->id, 'time' => now()->addDays(rand(1, 30))],
                ['created_by' => 1, 'updated_by' => 1]
            );

            DB::commit();
        }
    }
}
