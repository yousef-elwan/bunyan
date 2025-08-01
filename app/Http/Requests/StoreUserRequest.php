<?php

namespace App\Http\Requests;


class StoreUserRequest extends MyRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages()
    {
        return trans('validation/store_user.messages');
    }

    public function attributes()
    {
        return trans('validation/store_user.attributes');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|phone:INTERNATIONAL',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
        ];
    }
}
