<?php

namespace App\Http\Requests;


class SendMessageRequest extends MyRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'required|string|max:20',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:2000',
        ];
    }

    public function messages()
    {
        return trans('validation/send_message.messages');
    }

    public function attributes()
    {
        return trans('validation/send_message.attributes');
    }
}
