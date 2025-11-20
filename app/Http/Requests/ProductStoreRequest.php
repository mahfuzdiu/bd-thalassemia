<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'product_sku' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            // variants array
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.variant_sku' => ['required', 'string', 'max:255'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.stock' => ['required', 'integer', 'min:0'],

            // nested attributes inside each variant
            'variants.*.attributes' => ['required', 'array', 'min:1'],
            'variants.*.attributes.*' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'variants.required' => 'At least one product variant is required.',
            'variants.*.sku.required' => 'Each variant must contain an SKU.',
            'variants.*.attributes.required' => 'Each variant must contain attributes.',
        ];
    }
}
