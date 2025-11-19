<?php

namespace App\Http\Requests;

use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApproveDeclineRequest extends FormRequest
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
            'status' => ['required', 'string', 'max:255', Rule::in([OrderStatusEnum::CONFIRMED->value, OrderStatusEnum::REJECTED->value])],
            'shipping_fee' => [
                Rule::requiredIf($this->input('status') === OrderStatusEnum::CONFIRMED->value), 
                Rule::excludeIf($this->input('status') === OrderStatusEnum::REJECTED->value),
                'numeric', 
                'min:0'
            ],
            'remarks' => [
                Rule::requiredIf($this->input('status') === OrderStatusEnum::REJECTED->value), 
                Rule::excludeIf($this->input('status') === OrderStatusEnum::CONFIRMED->value),
                'string', 
                'max:255'
            ],
        ];
    }
}
