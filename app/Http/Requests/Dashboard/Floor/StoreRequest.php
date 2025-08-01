<?php

namespace App\Http\Requests\Dashboard\Floor;

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


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        $rules = [
            'value' => 'required|numeric|min:0',
            'locales' => 'required|array|min:1',
            'locales.*.locale' => 'required|string',
            'locales.*.name' => 'required|string|max:255',
        ];

        foreach ($this->locales ?? [] as $index => $locale) {

            // name
            $rules["locales.$index.name"][] = 'string';
            $rules["locales.$index.name"][] =  Rule::unique('floors_translations', 'name')->where('locale', $locale['locale']);

            // slug
            $rules["locales.$index.slug"][] = 'string';
            $rules["locales.$index.slug"][] =  Rule::unique('floors_translations', 'slug')->where('locale', $locale['locale']);
        }

        return $rules;
    }
}
