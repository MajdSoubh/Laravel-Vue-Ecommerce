<?php

namespace App\Http\Requests\User\Cart;

use App\Rules\BulkProductExistenceChecker;
use App\Rules\BulkProductQuantityChecker;
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

    protected function prepareForValidation()
    {
        $this->merge([
            'items' => [['product_id' => $this->input('product_id'), 'quantity' => $this->input('quantity')]],
        ]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'bail', 'integer', 'min:1'],
            'product_id' => ['required', 'bail', 'integer', 'min:1'],
            'items' => ["bail",  new BulkProductExistenceChecker, new BulkProductQuantityChecker],
        ];
    }
}
