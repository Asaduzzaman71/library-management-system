<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
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
            'book_name' => 'required |max:255',
            'category_id' => 'required',
            // 'isbn' => request()->route('book')
            //     ? 'required|max:255|unique:books' . request()->route('book')
            //     : 'required|max:255|unique:books',
            'book_name' => 'required',
            'author_name' => 'required',
            'publisher' => 'required',
            'rack_no' => 'required',
            'no_of_copies' => 'required',
            'edition' => 'required',
            'image'    => 'required|sometimes|mimetypes:image/jpeg,image/png,image/bmp|max:2000',
        ];
    }
}
