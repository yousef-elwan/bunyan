<?php

namespace App\Http\Requests\Dashboard\Category;

use App\Http\Requests\MyRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends MyRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        // Convert string booleans to actual booleans
        $this->merge([
            'locales' => is_array($this->locales ?? null) ? $this->locales : json_decode($this->locales ?? '[]', true)
        ]);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        $rules = [
            'image' => ['file', 'nullable'],
            'locales' => 'required|array|min:1',
            'locales.*.locale' => 'required|string',
            'locales.*.name' => 'required|string|max:255',
        ];

        foreach ($this->locales ?? [] as $index => $locale) {

            // name
            $rules["locales.$index.name"][] = 'string';
            $rules["locales.$index.name"][] =  Rule::unique('categories_translations', 'name')->where('locale', $locale['locale']);

            // slug
            $rules["locales.$index.slug"][] = 'string';
            $rules["locales.$index.slug"][] =  Rule::unique('categories_translations', 'slug')->where('locale', $locale['locale']);
        }

        return $rules;
    }
}
