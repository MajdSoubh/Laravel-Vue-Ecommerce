<?php

namespace App\Http\Requests\User\Cart;

use App\Rules\ProductQuantityChecker;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'items' => "required|array",
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*' =>  Rule::forEach(function ($value)
            {
                return [
                    new ProductQuantityChecker($value['product_id'], $value['quantity'])
                ];
            }),
        ];
    }

    public function messages()
    {
        return [
            'items.*.product_id.exists' => "The requested product is not available",
        ];
    }
}
