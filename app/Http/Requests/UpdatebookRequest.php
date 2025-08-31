<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatebookRequest extends FormRequest
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
        $bookId = $this->route('book') ?? $this->route()->parameter('id');
        
        return [
            'name' => 'required|min:3',
            'category_id' => 'required|exists:categories,id',
            'author_id' => 'required|exists:authors,id',
            'publisher_id' => 'required|exists:publishers,id',
            'isbn' => 'nullable|string|unique:books,isbn,' . $bookId,
            'description' => 'nullable|string|max:1000',
            'total_copies' => 'required|integer|min:1|max:999',
        ];
    }
}
