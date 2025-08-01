<?php

namespace App\Http\Requests\Dashboard\Property;

use App\Http\Requests\MyRequest;
use App\Models\Property\PropertyTranslation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class Upload extends MyRequest
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
        $this->merge([
            'price_on_request' => filter_var($this->price_on_request, FILTER_VALIDATE_BOOLEAN),
            'user_id' => Auth::id(),
        ]);
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
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Max 2MB per image
        ];

        return $rules;
    }
}
