<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class UnitRequest extends FormRequest
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
            'unit_name' => [
                'bail',
                'required',
                'string',
                'max:255',
                Rule::unique('units', 'unit_name')
                    ->when($this->method() == 'PATCH' && $this->id, function (Unique $query) {
                        $query->whereNot('unit_id', $this->id);
                    }),
            ],
        ];
    }
}
