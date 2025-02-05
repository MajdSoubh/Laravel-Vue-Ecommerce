<?php

namespace App\Http\Requests\User\Checkout;

use App\Rules\BulkProductExistenceChecker;
use App\Rules\BulkProductQuantityChecker;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
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
            'items.*.quantity' => ['required', 'bail', 'integer', 'min:1'],
            'items.*.product_id' => ['required', 'bail', 'integer', 'min:1'],
            'items' => ["required", "bail", "array",  new BulkProductExistenceChecker, new BulkProductQuantityChecker],
            'success_url' => 'required|url',
            'cancel_url' => 'required|url',
        ];
    }

    public function messages()
    {
        return [
            'items.*.product_id.exists' => "The requested product is not available",
        ];
    }
}
