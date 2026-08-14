<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => [
                'required',
                'digits:13',
                Rule::unique('books', 'isbn')->ignore($this->book),
            ],
            'published_date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'genres' => ['required', 'array', 'min:1'],
            'genres.*' => ['integer', 'distinct', 'exists:genres,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'isbn.digits' => 'ISBNは13桁の数字で入力してください。',
            'isbn.unique' => 'このISBNは既に登録されています。',
            'genres.*.exists' => '選択されたジャンルは存在しません。',
        ];
    }
}
