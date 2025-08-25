<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAgencyRequest extends FormRequest
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
            'agency_name' => ['required', 'min:8', Rule::unique('agencies', 'agency_name')->ignore($this->route('agency'))],
            'agency_acronym' => ['required', Rule::unique('agencies', 'agency_acronym')->ignore($this->route('agency'))],
            'sector_id'=> ['required'],
        ];
    }
}
