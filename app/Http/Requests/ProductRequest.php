<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
            'product_name' => ['required', 'string', 'max:255', Rule::unique('products', 'product_name')
                ->when($this->method() === 'PATCH', function ($query) {
                    $query->ignore($this->id, 'product_id');
                }),
            ],
            'description' => ['required', 'string', 'max:4294967295'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'category_id')],
            'unit_id' => ['required', 'integer', Rule::exists('units', 'unit_id')],
            'product_price' => ['required', 'numeric', 'min:.1'],
            'product_quantity' => ['required', 'integer', 'min:1'],
            'low_stock_threshold' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
            'sku' => [
                'string',
                'nullable',
                Rule::unique('products', 'sku')
                    ->when($this->method() === 'PATCH', function ($query) {
                        $query->whereNot('product_id', $this->id);
                    }),
            ],
            'available_online' => ['required', 'boolean'],
        ];
    }
}
