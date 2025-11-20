<?php

namespace App\Http\Requests;

use App\Enums\UserRoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRegistrationRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
            'role' => ['nullable', Rule::in(UserRoleEnum::values())],
            'phone' => 'required|string|max:20'
        ];
    }

    /**
     * Set default role if not provided
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'role' => $this->input('role', UserRoleEnum::CUSTOMER->value),
        ]);
    }
}
