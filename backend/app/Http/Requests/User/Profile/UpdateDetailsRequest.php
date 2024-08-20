<?php

namespace App\Http\Requests\User\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDetailsRequest extends FormRequest
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
            'name' => "required|string",
            'phone' => "required|string|min:10",
            'email' => "required|string|unique:users,email," . $this->user()->id,
            'address_1' => "required",
            // 'address_2' => "nullable",
            'country' => "required",
            'city' => "required",
            'state' => "nullable",
            'zip_code' => "required",
        ];
    }
}
