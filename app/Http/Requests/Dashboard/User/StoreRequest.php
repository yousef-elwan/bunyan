<?php

namespace App\Http\Requests\Dashboard\User;

use App\Http\Requests\MyRequest;

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
        $this->merge([]);
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
        ];
        return $rules;
    }
}
