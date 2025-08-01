<?php

namespace App\Http\Requests\Chat;

use App\Http\Requests\MyRequest;

class StoreMessageRequest extends MyRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        $rules = [
            'property_id' => [],
            // 'name' => ['required', 'string'],
            // 'mobile' => ['required', 'string'],
            // 'email' => ['nullable', 'email'],
            'message' => ['required', 'string']

        ];
        return  $rules;
    }

    public function messages()
    {
        return trans('validation/comment_send.messages');
    }

    public function attributes()
    {
        return trans('validation/comment_send.attributes');
    }
}
