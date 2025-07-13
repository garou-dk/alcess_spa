<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchProductRequest extends FormRequest
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
            'search' => ['nullable', 'string'],
            'limit' => ['nullable', 'integer', 'min:1'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'category_id')],
            'status' => ['nullable', 'boolean'],
            'low_stock' => ['nullable', 'boolean'],
            'available_online' => ['nullable', 'boolean'],
        ];
    }
}
