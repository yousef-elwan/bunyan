<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\MyRequest;
use Illuminate\Support\Facades\Password;

class ForgotPassword extends MyRequest
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
        return trans('validation/auth/forgot_password.messages');
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

        ];
        return  $rules;
    }

    public function attributes()
    {
        return trans('validation/auth/forgot_password.attributes');
    }
}
