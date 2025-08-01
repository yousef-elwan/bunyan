<?php

namespace App\Http\Requests\Dashboard\Property;

use App\Http\Requests\MyRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class Store extends MyRequest
{


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
        // Convert string booleans to actual booleans
        $this->merge([
            'price_on_request' => filter_var($this->price_on_request, FILTER_VALIDATE_BOOLEAN),
            'user_id' => Auth::id(),
            'locales' => is_array($this->locales ?? null) ? $this->locales : json_decode($this->locales ?? '[]', true)
        ]);
    }

    public function messages()
    {
        return trans('validation/dashboard_property_store.messages');
    }

    public function attributes()
    {
        return trans('validation/dashboard_property_store.attributes');
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
            'price' => ['required_if:price_on_request,false', 'nullable', 'numeric', 'min:0'],
            'size' => ['nullable', 'numeric', 'min:1'],
            'year_built' => ['nullable', 'numeric', 'min:0', 'max:' . (date('Y') + 1)],
            'category_id' => ['required', Rule::exists('categories', 'id')],
            'type_id' => ['required', Rule::exists('types', 'id')],
            'floor_id' => ['nullable', Rule::exists('floors', 'id')],
            'rooms_count' => ['nullable', 'integer', 'min:1'],
            'currency_id' => [Rule::exists('currencies', 'id')],
            'price_on_request' => ['boolean'],
            'orientation_id' => ['nullable', Rule::exists('orientations', 'id')],
            'city_id' => ['nullable', Rule::exists('cities', 'id')],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'video_url' => ['nullable', 'url'],
            'available_from' => [],
            'user_id' => [],

            // Amenities
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['integer', Rule::exists('amenities', 'id')],

            // Custom attributes
            'attributes' => ['nullable', 'array'],
            'attributes.*.custom_attribute_id' => ['required_with:attributes', 'integer', Rule::exists('attributes', 'id')],
            'attributes.*.value' => ['required_with:attributes.*.custom_attribute_id'],

            // Localized content
            'locales' => ['required', 'array', 'min:1'],
            'locales.*.locale' => ['required', 'string'],
            'locales.*.content' => ['required', 'string'],
            'locales.*.location' => ['nullable', 'string', 'max:500'],
        ];

        // Add unique validation for title per locale
        // foreach ($this->locales ?? [] as $index => $locale) {
        //     $rules["locales.$index.title"][] = Rule::unique('property_translations', 'title')
        //         ->where('locale', $locale['locale'])
        //         ->ignore($this->property?->id);
        // }

        return $rules;
    }
}
