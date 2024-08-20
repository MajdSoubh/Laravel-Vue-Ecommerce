<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'images' => 'required|array',
            'images.*' => 'image',
            'categories' => 'required|array',
            'categories.*' => 'int|exists:categories,id',
            'price' => 'required|decimal:0,999999',
            'quantity' => 'required|integer',
            'description' => 'required|string',
            'published' => 'required|boolean'

        ];
    }
}
