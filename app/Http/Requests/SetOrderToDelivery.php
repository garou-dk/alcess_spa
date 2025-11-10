<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetOrderToDelivery extends FormRequest
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
            'estimated_delivery_date_start' => ['required', 'date', 'after_or_equal:today'],
            'estimated_delivery_date_end'=> ['required','date', 'after_or_equal:estimated_delivery_date_start'],
            'delivery_courier' => ['required', 'string', 'max:255'],
            'tracking_number'=> ['required', 'string', 'max:255'],
        ];
    }
}
