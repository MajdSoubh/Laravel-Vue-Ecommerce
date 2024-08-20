<?php

namespace App\Http\Requests\User\Cart;

use App\Rules\ProductQuantityChecker;
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
            'product_id' => 'required|exists:products,id',
            'quantity' => ['required', 'integer', 'min:1', new ProductQuantityChecker($this->product_id, $this->quantity)]
        ];
    }

    public function messages()
    {
        return [
            'product_id.exists' => "This product is no longer available",
        ];
    }
}
