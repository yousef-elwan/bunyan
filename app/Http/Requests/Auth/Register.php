<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\MyRequest;
use Illuminate\Support\Facades\Password;

class Register extends MyRequest
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
        return trans('validation/auth/register.messages');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $name_validation_rule = 'regex:/^[\p{L}\s\-]+$/u';
        $rules = [
            'first_name' => ['required', 'string', 'max:255', $name_validation_rule],
            'last_name' => ['required', 'string', 'max:255', $name_validation_rule],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'mobile' => ['required', 'string', 'unique:users,mobile'],
            'password' => [
                'required',
                // 'min:8',
                // 'regex:/[a-z]/',
                // 'regex:/[A-Z]/',
                // 'regex:/[0-9]/',
                // 'regex:/[@$!%*?&]/',
            ],
            'password_confirmation' => ['required', 'same:password'],
            'agreeTerms' => ['accepted'],

        ];
        return  $rules;
    }

    public function attributes()
    {
        return trans('validation/auth/register.attributes');
    }
}
