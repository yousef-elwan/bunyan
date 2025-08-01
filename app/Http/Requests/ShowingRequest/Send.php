<?php

namespace App\Http\Requests\ShowingRequest;

use App\Http\Requests\MyRequest;

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
        return [
            'property_id' => [
                'required'
            ],
            // 'name' => [
            //     'required',
            //     'string',
            //     'max:255'
            // ],
            // 'mobile' => [
            //     'required',
            //     'string',
            //     'max:50'
            // ],
            // 'email' => [
            //     'nullable',
            //     'email',
            //     'max:255'
            // ],
            // 'time_id' => [
            //     'exists:property_available_times,id'
            // ],
            // 'showing_request_type_id' => [
            //     'exists:showing_request_types,id'
            // ],
            'message' => [
                'required',
                'string'
            ],
        ];
    }

    public function messages()
    {
        return trans('validation/showing_request_send.messages');
    }

    public function attributes()
    {
        return trans('validation/showing_request_send.attributes');
    }
}
