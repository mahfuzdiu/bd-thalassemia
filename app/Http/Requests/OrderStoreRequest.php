<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\ShippingMethodEnum;

class OrderStoreRequest extends FormRequest
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
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id'
            ],
            'items.*.variant_sku' => ['required', 'string'],
            'items.*.quantity' => ['required', 'integer', 'min:1'], // positive quantity

            'payment' => ['required', 'array'],
            'payment.method' => ['required', 'string', Rule::in(PaymentMethodEnum::values())],
            'payment.status' => ['required', 'string', Rule::in(PaymentStatusEnum::values())],

            'shipping' => ['required', 'array'],
            'shipping.method' => ['required', 'string', Rule::in(ShippingMethodEnum::values())],
            'shipping.address' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages()
    {
        return [
            'items.required' => 'You must provide at least one order item.',
            'items.*.product_variant_id.exists' => 'One or more variants are invalid.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'payment.method.in' => 'Invalid payment method.',
            'payment.status.in' => 'Invalid payment status.',
            'shipping.method.in' => 'Invalid shipping method.',
        ];
    }
}
