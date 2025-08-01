<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\MyRequest;
use App\Rules\MatchOldPassword;

class UpdatePasswordRequest extends MyRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'old_password' => ['required', new MatchOldPassword()],
            'new_password' => [
                'required',
                'different:old_password',
                // 'confirmed',
                // 'different:old_password',
                // 'min:8',
                // 'regex:/[a-z]/',      // يجب أن تحتوي على حروف صغيرة على الأقل
                // 'regex:/[A-Z]/',      // يجب أن تحتوي على حروف كبيرة على الأقل
                // 'regex:/[0-9]/',      // يجب أن تحتوي على أرقام على الأقل
                // 'regex:/[@$!%*?&]/',  // يجب أن تحتوي على رمز واحد على الأقل
            ]
        ];
    }

    public function messages()
    {
        return trans('validation/auth/update_password.messages');
    }

    public function attributes()
    {
        return trans('validation/auth/update_password.attributes');
    }
}
