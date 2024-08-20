<?php

namespace App\Http\Requests\Admin\Category;

use App\Rules\ParentCategoryChecker;
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
            'name' => 'string|max:255|unique:categories,name,' . $this->category,
            'parent' => ['nullable', 'exists:categories,id', new ParentCategoryChecker($this->category)],
            'active' => 'boolean'
        ];
    }
}
