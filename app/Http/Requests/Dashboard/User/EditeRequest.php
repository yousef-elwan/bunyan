<?php

namespace App\Http\Requests\Dashboard\User;

use App\Http\Requests\MyRequest;
use Illuminate\Validation\Rule;

class EditeRequest extends MyRequest
{

    public function prepareForValidation()
    {
        // Convert string booleans to actual booleans
        $this->merge([
            'id' =>  $this->route()?->originalParameter('user'),
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
            'id' => [Rule::exists('users', 'id')],
            'image' => ['file', 'nullable'],
        ];
        return  $rules;
    }
}
