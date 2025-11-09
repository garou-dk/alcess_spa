<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetPaymentRequest extends FormRequest
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
            'payment_proof' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/jpg', 'max:10048'],
            'bank_name' => ['required', 'string', 'max:255'],
            'transaction_number' => ['required', 'string', 'max:255'],
        ];
    }
}
