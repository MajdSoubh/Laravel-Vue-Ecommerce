<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'title' => 'string|max:1000',
            'images' => 'array',
            'images.*' => 'image',
            'categories' => 'array|required|min:1',
            'categories.*' => 'int|exists:categories,id',
            'price' => 'decimal:0,999999',
            'description' => 'string',
            'published' => 'boolean'

        ];
    }
}
