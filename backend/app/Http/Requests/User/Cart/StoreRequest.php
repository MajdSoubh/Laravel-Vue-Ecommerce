<?php

namespace App\Http\Requests\User\Cart;

use App\Rules\BulkProductExistenceChecker;
use App\Rules\BulkProductQuantityChecker;
use Illuminate\Validation\Rule;
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
            'items.*.quantity' => ['required', 'bail', 'integer', 'min:1'],
            'items.*.product_id' => ['required', 'bail', 'integer', 'min:1'],
            'items' => ["required", "array", "bail",  new BulkProductExistenceChecker, new BulkProductQuantityChecker],
        ];
    }
}
