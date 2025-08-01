<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\MyRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateInfoRequest extends MyRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = Auth::user()->id;
        $name_validation_rule = 'regex:/^[\p{L}\s\-]+$/u';

        return [
            'first_name' => ['string', 'max:255', $name_validation_rule],
            'last_name' =>  ['string', 'max:255', $name_validation_rule],
            'mobile' => [
                Rule::unique('users', 'mobile')->ignore($userId, 'id'),
                'phone:INTERNATIONAL'
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => [
                'uncompromised'
                // 'min:8',
                // 'regex:/[a-z]/',
                // 'regex:/[A-Z]/',
                // 'regex:/[0-9]/',
                // 'regex:/[@$!%*?&]/'
            ],
            'password_confirmation' => ['same:password']
        ];
    }

    public function messages()
    {
        return trans('validation/auth/update_info.messages');
    }

    public function attributes()
    {
        return trans('validation/auth/update_info.attributes');
    }
}
