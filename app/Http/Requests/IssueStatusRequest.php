<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueStatusRequest extends FormRequest
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
            'book_id' => 'required',
            'member_id' => 'required',
            'issue_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:issue_date',
        ];
    }
    public function messages()
    {
        return [
            'book_id.required' => 'Book name is required',
            'member_id.required' => 'Member name is required',
        ];
    }
}
