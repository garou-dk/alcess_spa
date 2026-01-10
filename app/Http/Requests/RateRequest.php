<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RateRequest extends FormRequest
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
            'rate' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:255'],
            'media' => ['nullable', 'array', 'max:4'],
            'media.*' => ['nullable', 'file', 'mimetypes:image/*,video/*', 'max:51200'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->hasFile('media')) {
                foreach ($this->file('media') as $file) {
                    $mimeType = $file->getMimeType();
                    
                    // Only allow image and video mime types
                    if (!str_starts_with($mimeType, 'image/') && !str_starts_with($mimeType, 'video/')) {
                        $validator->errors()->add('media', 'Only image and video files are allowed.');
                        break;
                    }
                }
            }
        });
    }


}
