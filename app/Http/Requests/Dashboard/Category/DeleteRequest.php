<?php

namespace App\Http\Requests\Dashboard\Category;

use App\Http\Requests\MyRequest;
use Illuminate\Validation\Rule;

class DeleteRequest extends MyRequest
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
            'id' =>  $this->route()?->originalParameter('category'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => [Rule::exists('categories', 'id')],
        ];
    }
}
