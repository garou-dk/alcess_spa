<?php

namespace App\Http\Requests;

use App\Enums\OrderTypeEnum;
use App\Enums\PaymentMethodEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
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
        $paymentMethodAvailable = match ($this->input('order_type')) {
            OrderTypeEnum::PICKUP->value => PaymentMethodEnum::pickupOrderTypeAvailable(),
            OrderTypeEnum::DELIVERY->value => PaymentMethodEnum::deliveryOrderTypeAvailable(),
            default => [],
        };
        return [
            'order_type' => ['required', 'string', 'max:255', Rule::in(OrderTypeEnum::all())],
            'payment_method' => ['required', 'string', 'max:255', Rule::in($paymentMethodAvailable)],
            'products' => ['required', 'array'],
            'products.*.product_id' => ['required', 'integer', 'exists:products,product_id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
