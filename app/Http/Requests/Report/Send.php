<?php

namespace App\Http\Requests\Report;

use App\Http\Requests\MyRequest;
use Illuminate\Support\Facades\Auth;

class Send extends MyRequest
{
    public function prepareForValidation()
    {
        // Convert string booleans to actual booleans
        $this->merge([
            'property_id' => $this->route('property'),
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        $rules = [

            'property_id' => ['required'],
            'type_id' => 'required|string|max:255',
            'message' => 'nullable|string|max:2000',
        ];

        if (!Auth::check()) {
            $rules['email'] = ['required', 'email', 'max:255'];
            $rules['mobile'] = ['required', 'string', 'max:20'];
            $rules['name'] = ['required', 'string', 'max:255'];
        }

        return  $rules;
    }

    public function messages()
    {
        return trans('validation/report_send.messages');
    }

    public function attributes()
    {
        return trans('validation/report_send.attributes');
    }
}
