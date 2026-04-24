<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAgencyRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $agencyId = $this->route('agency'); // Get ID from URL

        return [
            'agency_name' => [
                'required', 
                'min:8', 
                Rule::unique('agencies', 'agency_name')->ignore($agencyId)
            ],
            'agency_acronym' => [
                'required', 
                Rule::unique('agencies', 'agency_acronym')->ignore($agencyId)
            ],
        ];
    }
}
