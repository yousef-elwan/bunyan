<?php

namespace App\Http\Requests\Dashboard\Report;

use App\Http\Requests\MyRequest;
use Illuminate\Validation\Rule;

class UpdateReportStatusRequest extends MyRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'report_status_id' => [
                'required',
                'string',
                Rule::exists('report_status', 'id')
            ],
        ];
    }
}
