<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
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
            'project_title' => ['required', Rule::unique('projects', 'project_title')->ignore($this->id)],
            'sector_id' => ['required'],
            'sub_sector_id' => ['required'],
             'agency_id' => ['required'],
            'status' => ['required'],
            'indicator_id' => ['required'],
            'funding_category' => ['required'],
             'fund_source' => ['required'],
            'other_fund_source' => ['nullable','required_if:fund_source,others'],
            //'rdp_chapters' => ['required'],
            'location' => ['required'],
            'latitude' => ['nullable', 'required_with:longtitude'],
            'longtitude' => ['nullable', 'required_with:latitude'],
            'provinces' => ['required_if:location,inter-province', 'array', 'min:2'],
            'province' => ['required_if:location,provincewide'],
            'province_id' => ['required_if:location,locationspecific'],
            'district_id' => ['required_if:location,locationspecific'],
            'municipality_id' => ['required_if:location,locationspecific'],
            'target_year_2023' => ['nullable', 'numeric'],
            'target_year_2024' => ['nullable', 'numeric'],
            'target_year_2025' => ['nullable', 'numeric'],
            'target_year_2026' => ['nullable', 'numeric'],
            'target_year_2027' => ['nullable', 'numeric'],
            'target_year_2028' => ['nullable', 'numeric'],
            'target_succeeding_years' => ['nullable', 'numeric'],
            'cost_year_2023' => ['nullable', 'numeric'],
            'cost_year_2024' => ['nullable', 'numeric'],
            'cost_year_2025' => ['nullable', 'numeric'],
            'cost_year_2026' => ['nullable', 'numeric'],
            'cost_year_2027' => ['nullable', 'numeric'],
            'cost_year_2028' => ['nullable', 'numeric'],
            'cost_succeeding_years' => ['nullable', 'numeric'],
        ];
    }
}
