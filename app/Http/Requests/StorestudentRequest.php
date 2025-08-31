<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
            'name' => "required|min:3",
            'address' => "required|min:5",
            'gender' => "required|in:male,female",
            'class' => "required|min:2",
            'age' => "required|integer|min:1|max:120",
            'phone' => "required|min:10|max:15",
            'email' => "required|email|unique:students,email",
        ];
    }
}
