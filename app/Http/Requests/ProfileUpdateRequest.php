<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            'mobile_number' => ['required', 'numeric', 'digits:11'],
            'address' => ['required'],
            'avatar' => ['required', // Ensures the image is present
                'image',    // Ensures the uploaded file is an image
                'mimes:jpeg,png,jpg,gif', // Specifies allowed MIME types
                'max:1024', // Limits file size to 2MB (in kilobytes)
                Rule::dimensions()->minWidth(100)->minHeight(100)->maxWidth(1000)->maxHeight(1000)], // Enforces dimensions
            'current_password' => ['required', 'min:8', 'max:30'],
            'password' => ['required', 'min:8', 'max:30'],
            'confirm_password' =>  ['required', 'min:8', 'max:30', 'same:password'],
        ];
    }
}
