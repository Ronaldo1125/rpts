<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name' => ['required','string','max:255'],
            'email' => ['required', 'string','email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'password' => ['min:8','max:30','same:confirm-password'],
            'role' => ['required'],
            'agency_id' => ['required'],
        ];
    }
}
