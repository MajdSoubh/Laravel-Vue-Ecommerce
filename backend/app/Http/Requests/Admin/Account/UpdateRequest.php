<?php

namespace App\Http\Requests\Admin\Account;

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
            'name' => 'string|min:3',
            'email' => 'email|unique:users,email,' . $this->input('id'),
            'type' => 'in:client,admin',
            'password' => 'confirmed|min:8',
            'password_confirmation' => 'nullable',
            'phone' => 'nullable|string',
            'active' => 'boolean'
        ];
    }
}
