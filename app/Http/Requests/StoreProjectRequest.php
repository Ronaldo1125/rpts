<?php

namespace App\Http\Requests;

//use App\Rules\FileArraySize;
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
            'project_title' => ['required', Rule::unique('projects', 'project_title')],
            'sector_id' => ['required'],
            'sub_sector_id' => ['required'],
            'agency_id' => ['required'],
            'status' => ['required'],
            'indicators' => ['required', 'array', 'min:1'],
            'funding_category' => ['required'],
            'fund_source' => ['required'],
            'other_fund_source' => ['nullable', 'required_if:fund_source,others'],
            //'rdp_chapters' => ['required'],
            'location' => ['required'],
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
            //'attachments.*' => ['sometimes','nullable','max:2048', 'mimes:png,jpg,pdf,csv,xls,xlsx,doc,docx'],
            //'attachments' => [new FileArraySize],
            // 'attachments' => new ArraySize(2048), //Total size limit in KB
        ];
    }
}
