<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|between:2,100',
            'address' => 'required|max:510',
            'issue_date' => 'required|date',
            'expiary_date' => 'required|date|after_or_equal:issue_date',
        ];
    }
    public function messages()
    {
        return [
            'issue_date.required' => 'Issue Date is required',
            'expiary_date.required' => 'Expiary Date is required',
        ];
    }
}
