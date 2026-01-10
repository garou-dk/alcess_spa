<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchUserRequest extends FormRequest
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
            'status' => ['nullable', 'integer', 'in:0,1'],
            'role_id' => ['nullable', 'integer', 'exists:roles,role_id'],
            'limit' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
