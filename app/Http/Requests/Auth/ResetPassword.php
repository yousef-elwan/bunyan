<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\MyRequest;

class ResetPassword extends MyRequest
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

    public function messages()
    {
        return trans('validation/auth/reset_password.messages');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'email' => ['required', 'string'],
            'token' => ['required'],
            'password' => ['required'],
            'password_confirmation' => ['required', 'same:password'],

        ];
        return  $rules;
    }

    public function attributes()
    {
        return trans('validation/auth/reset_password.attributes');
    }
}
