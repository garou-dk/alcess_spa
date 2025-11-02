<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'user_id' => ['required', 'integer', 'exists:users,user_id'],
            'barangay_id' => ['required', 'integer', 'exists:barangays,barangay_id'],
            'other_details' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'regex:/^09\d{9}$/'],
            'postal_code' => ['required', 'string', 'max:255'],
        ];
    }
}
