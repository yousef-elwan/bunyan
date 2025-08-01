<?php

namespace App\Http\Requests\Dashboard\Category;

use App\Http\Requests\MyRequest;
use App\Models\Category\CategoryTranslation;
use Illuminate\Validation\Rule;

class EditeRequest extends MyRequest
{

    public function prepareForValidation()
    {
        // Convert string booleans to actual booleans
        $this->merge([
            'id' =>  $this->route()?->originalParameter('category'),
            'locales' => is_array($this->locales ?? null) ? $this->locales : json_decode($this->locales ?? '[]', true)
        ]);
    }

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
            'id' => [Rule::exists('categories', 'id')],
            'image' => ['file', 'nullable'],
            'locales' => 'required|array|min:1',
            'locales.*.locale' => 'required|string',
            'locales.*.name' => 'required|string|max:255',
        ];
        foreach (($this->locales ?? []) as $index => $locale) {
            // check if there are  old translation record in same locale
            $translate = CategoryTranslation::where('category_id', $this->id)->where('locale', $locale['locale'])->first();

            $rules["locales.$index.name"] = [];
            $rules["locales.$index.name"][] = 'string';

            if ($translate) {
                // there are  old record..
                // check if name  is unique except this  record
                $rules["locales.$index.name"][] =  Rule::unique('categories_translations', 'name')->where('locale', $locale['locale'])->ignore($translate->id);
            } else {
                // first recored  in this locale..
                // check if name  is unique
                $rules["locales.$index.name"][] =  Rule::unique('categories_translations', 'name')->where('locale', $locale['locale']);
            }
        }
        return  $rules;
    }
}
