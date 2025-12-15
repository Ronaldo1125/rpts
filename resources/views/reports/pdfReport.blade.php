<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="_token" content="{{csrf_token()}}" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>View Pre Travel Order Application</title>

  <link rel="stylesheet" href="{{ url("/assets/vendor/css/core.css") }}" />
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ url("/assets/vendor/fonts/fontawesome.css") }}">
  <!-- IonIcons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  

<style>

body{margin-top:20px;
/* background:#eee; */
font-size: 12px;
font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif

}

table, thead, th, tr, td {
  border: 1px solid black;
  border-collapse: collapse;
}

/**    17. Panel
 *************************************************** **/
/* pannel */
.panel {
	position:relative;

	background:transparent;

	-webkit-border-radius: 0;
	   -moz-border-radius: 0;
			border-radius: 0;

	-webkit-box-shadow: none;
	   -moz-box-shadow: none;
			box-shadow: none;
}
.panel.fullscreen .accordion .panel-body,
.panel.fullscreen .panel-group .panel-body {
	position:relative !important;
	top:auto !important;
	left:auto !important;
	right:auto !important;
	bottom:auto !important;
}
	
.panel.fullscreen .panel-footer {
	position:absolute;
	bottom:0;
	left:0;
	right:0;
}


.panel>.panel-heading {
	text-transform: uppercase;

	-webkit-border-radius: 0;
	   -moz-border-radius: 0;
			border-radius: 0;
}
.panel>.panel-heading small {
	text-transform:none;
}
.panel>.panel-heading strong {
	font-family:Arial,Helvetica,Sans-Serif;
}
.panel>.panel-heading .buttons {
	display:inline-block;
	margin-top:-3px;
	margin-right:-8px;
}
.panel-default>.panel-heading {
	padding: 15px 15px;
	background:#fff;
}
.panel-default>.panel-heading small {
	color:#9E9E9E;
	font-size:12px;
	font-weight:300;
}
.panel-clean {
	border: 1px solid #ddd;
	border-bottom: 3px solid #ddd;

	-webkit-border-radius: 0;
	   -moz-border-radius: 0;
			border-radius: 0;
}
.panel-clean>.panel-heading {
	padding: 11px 15px;
	background:#fff !important;
	color:#000;	
	border-bottom: #eee 1px solid;
}
.panel>.panel-heading .btn {
	margin-bottom: 0 !important;
}

.panel>.panel-heading .progress {
	background-color:#ddd;
}

.panel>.panel-heading .pagination {
	margin:-5px;
}

.panel-default {
	border:0;
}

.panel-light {
	border:rgba(0,0,0,0.1) 1px solid;
}
.panel-light>.panel-heading {
	padding: 11px 15px;
	background:transaprent;
	border-bottom:rgba(0,0,0,0.1) 1px solid;
}

.panel-heading a.opt>.fa {
    display: inline-block;
    font-size: 14px;
    font-style: normal;
    font-weight: normal;
    margin-right: 2px;
    padding: 5px;
    position: relative;
    text-align: right;
    top: -1px;
}

.panel-heading>label>.form-control {
	display:inline-block;
	margin-top:-8px;
	margin-right:0;
	height:30px;
	padding:0 15px;
}
.panel-heading ul.options>li>a {
	color:#999;
}
.panel-heading ul.options>li>a:hover {
	color:#333;
}
.panel-title a {
	text-decoration:none;
	display:block;
	color:#333;
}

.panel-body {
	background-color:#fff;
	padding: 15px;

	-webkit-border-radius: 0;
	   -moz-border-radius: 0;
			border-radius: 0;
}
.panel-body.panel-row {
	padding:8px;
}

 .panel-footer {
	font-size:12px;
	border-top:rgba(0,0,0,0.02) 1px solid;
	background-color:rgba(0255,255,255,1);

	-webkit-border-radius: 0;
	   -moz-border-radius: 0;
			border-radius: 0; 
}


</style>
 
</head>
<body>

  <div class="container">
    <div class="panel panel-default">
      <div class="panel-body">
       
        {{-- <div class="my-4 text-center">
          <img src="{{ asset('dist/img/neda_letterhead2.png') }}">
        </div> --}}
  
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                {{-- <td scope="col" colspan="5">
                    <span><center><strong><h5>OBLIGATION REQUEST AND STATUS</h5></strong></center></span><br><br>
                    <span class="text-decoration-underline"><center><strong><h6>Department of Economy, Planning, and Development</strong></h6></center></span>
                    <span><center>Entity Name</center></span>
                </td> --}}
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
                    <td>{{ $project->description }}</td>
					<td>{{ $project->project_indicator->indicator->indicator_name }}</td>
					<td></td>
					<td></td>
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
        </div>
      </div>
    </div>
  </div>
</body>
</html>