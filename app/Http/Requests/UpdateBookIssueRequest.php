<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookIssueRequest extends FormRequest
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
            'student_id' => 'required|exists:students,id',
            'book_id' => 'required|exists:books,id',
            'issue_date' => 'required|date',
            'return_date' => 'required|date|after:issue_date',
            'issue_status' => 'in:Y,N'
        ];
    }
}
