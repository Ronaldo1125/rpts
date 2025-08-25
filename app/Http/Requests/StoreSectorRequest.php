<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSectorRequest extends FormRequest
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
            'sector_name' => ['required', 'min:4', Rule::unique('sectors', 'sector_name')->ignore($this->route('sector'))],
            'sector_acronym' => ['required', Rule::unique('sectors', 'sector_acronym')->ignore($this->route('sector'))],
        ];
    }
}
