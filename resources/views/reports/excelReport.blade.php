<style>

table, thead, th, tr, td {
  border: 1px solid black;
  border-collapse: collapse;
}

</style>

<table>
    <thead>
        <tr>
        
        <th rowspan="2">Project Title</th>
        <th rowspan="2">Description</th>
        <th rowspan="2">Indicator</th>
        <th rowspan="2">Location</th>
        <th rowspan="2">Duty Bearer</th>
        <th rowspan="2">Funding Category</th>
        <th rowspan="2">Funding Requirement (PhP M)</th>
        <th colspan="7">Physical Target</th>
        
        
        <th colspan="7">Project Cost (PM)</th>
        
        <th rowspan="2">Remarks</th>
        </tr>
        <tr>
        <th>2023</th>
        <th>2024</th>
        <th>2025</th>
        <th>2026</th>
        <th>2027</th>
        <th>2028</th>
        <th>Succeeding Years</th>
        <th>2023</th>
        <th>2024</th>
        <th>2025</th>
        <th>2026</th>
        <th>2027</th>
        <th>2028</th>
        <th>Succeeding Years</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($projects as $project)
            <tr>
            <td>{{ $project->project_title }}</td>
            <td style="word-wrap: normal;">{{ $project->description }}</td>
            <td>{{ $project->project_indicator->indicator->indicator_name }}</td>
             <td>
                    @if($project->location == 'regionwide')
                            {{ ucfirst($project->location) }}
                    @elseif($project->location == 'interprovince')
                        @php
                            $provinces = '';
                        @endphp
                        @foreach($project->project_location as $location)
                        @php
                            $provinces .= $location->province->province_name . ", ";
                        @endphp
                        @endforeach
                        {{ substr($provinces, 0, -2) }}
                    @else
                        {{ $project->project_location_specific->municipality->municipality_name . ', ' . $project->project_location_specific->province->province_name }}
                    @endif
             </td>
            <td>{{ $project->agency->agency_acronym }}</td>
            <td style="text-align: center;">{{ $project->funding_category->category_name }}</td>
            <td style="text-align: right;">{{ number_format($project->funding_requirement, 2) }}</td>
            
            <td style="text-align: right;">{{ ($project->project_cost_target->target_year_2023 > 0) ? number_format($project->project_cost_target->target_year_2023, 2) : "" }}</td>
            <td style="text-align: right;">{{ ($project->project_cost_target->target_year_2024 > 0) ? number_format($project->project_cost_target->target_year_2024, 2) : "" }}</td>
            <td style="text-align: right;">{{ ($project->project_cost_target->target_year_2025 > 0) ? number_format($project->project_cost_target->target_year_2025, 2) : "" }}</td>
            <td style="text-align: right;">{{ ($project->project_cost_target->target_year_2026 > 0) ? number_format($project->project_cost_target->target_year_2026, 2) : "" }}</td>
            <td style="text-align: right;">{{ ($project->project_cost_target->target_year_2027 > 0) ? number_format($project->project_cost_target->target_year_2027, 2) : "" }}</td>
            <td style="text-align: right;">{{ ($project->project_cost_target->target_year_2028 > 0) ? number_format($project->project_cost_target->target_year_2028, 2) : "" }}</td>
            <td style="text-align: right;">{{ ($project->project_cost_target->target_succeeding_years > 0) ? number_format($project->project_cost_target->target_succeeding_years, 2) : "" }}</td>
            <td style="text-align: right;">{{ ($project->project_cost_target->cost_year_2023 > 0) ? number_format($project->project_cost_target->cost_year_2023, 2) : "" }}</td>
            <td style="text-align: right;">{{ ($project->project_cost_target->cost_year_2024 > 0) ? number_format($project->project_cost_target->cost_year_2024, 2) : "" }}</td>
            <td style="text-align: right;">{{ ($project->project_cost_target->cost_year_2025 > 0) ? number_format($project->project_cost_target->cost_year_2025, 2) : "" }}</td>
            <td style="text-align: right;">{{ ($project->project_cost_target->cost_year_2026 > 0) ? number_format($project->project_cost_target->cost_year_2026, 2) : "" }}</td>
            <td style="text-align: right;">{{ ($project->project_cost_target->cost_year_2027 > 0) ? number_format($project->project_cost_target->cost_year_2027, 2) : "" }}</td>
            <td style="text-align: right;">{{ ($project->project_cost_target->cost_year_2028 > 0) ? number_format($project->project_cost_target->cost_year_2028, 2) : "" }}</td>
            <td style="text-align: right;">{{ ($project->project_cost_target->cost_succeeding_years > 0) ? number_format($project->project_cost_target->cost_succeeding_years, 2) : "" }}</td>
            <td style="text-align: right;">{{ $project->remarks }}</td>
        </tr>  
        @endforeach
    </tbody>
</table>
       