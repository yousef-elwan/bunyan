<?php

namespace App\Http\Requests\Dashboard\Property;

use App\Http\Requests\MyRequest;
use App\Models\Property\PropertyTranslation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class Destroy  extends MyRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [];

        return $rules;
    }
}
