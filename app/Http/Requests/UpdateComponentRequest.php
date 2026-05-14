<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateComponentRequest extends FormRequest
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
        $rules = [
            'project_title' => ['required', Rule::unique('projects', 'project_title')->ignore($this->id)],
            'sector_id' => ['required'],
            'sub_sector_id' => ['required'],
            'agency_id' => ['required'],
            'status' => ['required'],
            'indicators' => ['required', 'array', 'min:1'],
            'rdp_chapters' => ['nullable', 'array'],
            'funding_requirement' => ['nullable', 'string'],
            'funding_category' => ['required'],
             'fund_source' => ['required'],
            'other_fund_source' => ['nullable','required_if:fund_source,others'],
            //'rdp_chapters' => ['required'],
            'location' => ['required'],
            'remarks' => ['nullable', 'string'],
            'component_project_id' => ['required', 'integer'],
            'component_project_title' => ['required_if:component_project_id,0', Rule::unique('component_projects', 'component_project_title')->ignore($this->input('component_project_id'))],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longtitude'],
            'longtitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'provinces' => ['required_if:location,inter-province', 'array', 'min:2'],
            'province' => ['required_if:location,provincewide'],
            'province_id' => ['required_if:location,locationspecific'],
            'district_id' => ['required_if:location,locationspecific'],
            'municipality_id' => ['required_if:location,locationspecific'],
            'target_year_2023' => ['nullable', 'string'],
            'target_year_2024' => ['nullable', 'string'],
            'target_year_2025' => ['nullable', 'string'],
            'target_year_2026' => ['nullable', 'string'],
            'target_year_2027' => ['nullable', 'string'],
            'target_year_2028' => ['nullable', 'string'],
            'target_succeeding_years' => ['nullable', 'string'],
            'cost_year_2023' => ['nullable', 'string'],
            'cost_year_2024' => ['nullable', 'string'],
            'cost_year_2025' => ['nullable', 'string'],
            'cost_year_2026' => ['nullable', 'string'],
            'cost_year_2027' => ['nullable', 'string'],
            'cost_year_2028' => ['nullable', 'string'],
            'cost_succeeding_years' => ['nullable', 'string'],
        ];

        return $rules;
    }
}
