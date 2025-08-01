<?php

namespace App\Http\Requests\Dashboard\Property;

use App\Http\Requests\MyRequest;
use App\Models\Property\PropertyTranslation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class Update extends MyRequest
{


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $property = $this->route('property');
        $user = Auth::user();

        return $user->is_admin || $property->user_id == $user->id;
    }

    public function prepareForValidation()
    {
        // Convert string booleans to actual booleans

        $user = Auth::user();

        $property = $this->route('property');

        $this->merge([
            'old_data' => $property,
            'price_on_request' => filter_var($this->price_on_request, FILTER_VALIDATE_BOOLEAN),
            'price' => $this->price ?? 0,
            'locales' => is_array($this->locales ?? null) ? $this->locales : json_decode($this->locales ?? '[]', true),
        ]);

        if (isset($this->status_id)) {
            $this->merge([
                'status_id' => ($this->status_id != $property->status_id && $property->status_id == 'rejected' && !$user->isAdmin()) ? $property->status_id : $this->status_id,
            ]);
        }
    }

    public function messages()
    {
        return trans('validation/dashboard_property_update.messages');
    }

    public function attributes()
    {
        return trans('validation/dashboard_property_update.attributes');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            // Basic property information
            'old_data' => [],

            'id' => [Rule::exists('property', 'id')],
            'price' => ['required_if:price_on_request,false', 'nullable', 'numeric', 'min:0'],
            'size' => ['nullable', 'numeric', 'min:1'],
            'year_built' => ['nullable', 'numeric', 'min:0', 'max:' . (date('Y') + 1)],
            'category_id' => ['sometimes', 'required', Rule::exists('categories', 'id')],
            'type_id' => ['sometimes','required', Rule::exists('types', 'id')],
            'floor_id' => ['nullable', Rule::exists('floors', 'id')],
            'rooms_count' => ['nullable', 'integer', 'min:1'],
            'currency_id' => ['sometimes', 'required', Rule::exists('currencies', 'id')],
            'status_id' => [
                'sometimes',
                Rule::exists('property_status', 'id')
            ],
            'price_on_request' => ['boolean'],
            'orientation_id' => ['nullable', Rule::exists('orientations', 'id')],
            'city_id' => ['sometimes', Rule::exists('cities', 'id')],
            'latitude' => ['sometimes','required', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes','required', 'numeric', 'between:-180,180'],
            'video_url' => ['nullable', 'url'],
            'user_id' => [],
            'available_from' => ['nullable'],
            'deleted_images' => [],

            // Amenities
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['integer', Rule::exists('amenities', 'id')],

            // Custom attributes
            'attributes' => ['nullable', 'array'],
            'attributes.*.custom_attribute_id' => ['required_with:attributes', 'integer', Rule::exists('attributes', 'id')],
            'attributes.*.value' => ['required_with:attributes.*.custom_attribute_id'],

            // Localized content
            // 'locales' => ['array', 'min:1'],
            'locales' => ['array'],
            'locales.*.locale' => ['string'],
            'locales.*.content' => ['string'],
            'locales.*.location' => ['string', 'max:500'],
        ];

        foreach (($this->locales ?? []) as $index => $locale) {
            $translate = PropertyTranslation::where('property_id', $this->id)->where('locale', $locale['locale'])->first();

            // locale
            $rules["locales.$index.locale"] = [];
            // $rules["locales.$index.locale"][] =  Rule::exists('langs', 'locale');

            // slug
            $rules["locales.$index.slug"] = [];
            $rules["locales.$index.slug"][] = 'string';
            if ($translate) {
                $rules["locales.$index.slug"][] =  Rule::unique('ec_item_translations', 'slug')->where('locale', $locale['locale'])->ignore($translate->id);
            } else {
                $rules["locales.$index.slug"][] =  Rule::unique('ec_item_translations', 'slug')->where('locale', $locale['locale']);
            }


            // location
            $rules["locales.$index.location"] = [];
            $rules["locales.$index.location"][] = 'nullable';
            $rules["locales.$index.location"][] = 'string';

            // content
            $rules["locales.$index.content"] = [];
            $rules["locales.$index.content"][] = 'string';
        }

        // Add unique validation for title per locale
        // foreach ($this->locales ?? [] as $index => $locale) {
        //     $rules["locales.$index.title"][] = Rule::unique('property_translations', 'title')
        //         ->where('locale', $locale['locale'])
        //         ->ignore($this->property?->id);
        // }

        return $rules;
    }
}
