<?php

namespace App\Http\Requests;

use App\Rules\FileArraySize;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
            'project_title' => ['required', Rule::unique('projects', 'project_title')->ignore($this->route('project'))],
            'status_id' => ['required'],
            'endorsement_id' => ['required'],
            'funding_requirement' => ['required' , 'numeric'],
            'rdp_chapters' => ['required'],
            'province_id' => ['required'],
            'district_id' => ['required'],
            'municipality_id' => ['required'],
            'target_year_2023' => ['nullable', 'numeric','max:120'],
            'target_year_2024' => ['nullable', 'numeric','max:120'],
            'target_year_2025' => ['nullable', 'numeric','max:120'],
            'target_year_2026' => ['nullable', 'numeric','max:120'],
            'target_year_2027' => ['nullable', 'numeric','max:120'],
            'target_year_2028' => ['nullable', 'numeric','max:120'],
            'target_succeeding_years' => ['nullable', 'numeric','max:120'],
            'cost_year_2023' => ['nullable', 'numeric'],
            'cost_year_2024' => ['nullable', 'numeric'],
            'cost_year_2025' => ['nullable', 'numeric'],
            'cost_year_2026' => ['nullable', 'numeric'],
            'cost_year_2027' => ['nullable', 'numeric'],
            'cost_year_2028' => ['nullable', 'numeric'],
            'cost_succeeding_years' => ['nullable', 'numeric'],
            //'attachments.*' => ['sometimes','nullable','max:2048', 'mimes:png,jpg,pdf,csv,xls,xlsx,doc,docx'],
            //'attachments' => [new FileArraySize],
            // 'attachments' => new ArraySize(2048), //Total size limit in KB
        ];
    }
}
