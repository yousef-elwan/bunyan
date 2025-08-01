<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\MyRequest;

class Login extends MyRequest
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
            'login_identifier' => 'required|string',
            'password' => 'required|string',

        ];
        return  $rules;
    }

    public function messages()
    {
        return trans('validation/auth/login.messages');
    }

    public function attributes()
    {
        return trans('validation/auth/login.attributes');
    }
}
