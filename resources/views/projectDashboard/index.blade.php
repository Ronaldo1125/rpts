@extends('layouts.homeapp')

@section('content')

<!-- Hero Section -->
    {{-- <section id="hero" class="hero section">

      <img src="assets/onepage/img/neda5_office.jpg" alt="" data-aos="fade-in" class="">

      <div class="container">
        <div class="row justify-content-center" data-aos="zoom-out">
          <div class="col-xl-7 col-lg-9 text-center">
            <h1>DEPDev 5 Regional Project Tracking System</h1>
            <p>The Regional Project Tracking System (RPTS) is a system database containing the 
                       priority programs, activities, and projects (PAPs) of regional line agencies, 
                       government owned and controlled corporations, and state universities and colleges in the 
                       Bicol region that are included in the Regional Development Investment Program (RDIP) 2023-2028. 
                       It is being developed to facilitate the tracking and updating of PAPs and can generate reports 
                       as well as investment programming-related documents such as the RDIP, status of RDC-endorsed 
                       projects, and list of projects per province/district/city/municipality, among others. </p>
          </div>
        </div>
        <div class="text-center" data-aos="zoom-out" data-aos-delay="100">
          <a href="{{ route('login') }}" class="btn-get-started">Get Started</a>
        </div>
      </div>

    </section><!-- /Hero Section --> --}}

    <!-- About Section -->
    <section id="about" class="about section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Project Dashboard<br></h2>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">
          <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
             <div id="status"></div>
           
          </div>

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <div id="container"></div>
           
          </div>

        </div>

         <div class="row gy-4 pt-5">
          <div class="col-lg-12 content" data-aos="fade-up" data-aos-delay="100">
                <h4>Component Projects</h4>
                <div class="table-responsive text-nowrap">
                   <table class="table table-hover" id="tableComponentProject">
                <thead>
                    <tr class="table-primary">
                        <th>Component Title</th>
                        <th>Sub Project Title</th>
                        <th>Description</th>
                        <th>Funding Req't</th>
                        <th>Status</th>
                        <th>Funding Category</th>
                        <th>Created At</th>
                     
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  @php
                    $component_name = '';
                  @endphp
                  @foreach($componentProjects as $project)
                    <tr>
                        <td>
                          @if($component_name != $project->component_project->component_project_title)
                          {{$project->component_project->component_project_title }}
                          @endif
                          
                        </td>
                        <td>
                          {{ $project->project_title}}
                        </td>
                        <td>
                          {{ $project->description }}
                        </td>
                        <td>
                            {{ number_format($project->funding_requirement, 2) }}
                        </td>
                        <td>
                          {{ $project->status }}
                        </td>
                        <td>
                          {{ $project->funding_category }}
                        </td>
              
                        <td>
                            {{ $project->created_at->diffForHumans() }}
                        </td>

                        
                      </tr>
                          @php
                            $component_name =  $project->component_project->component_project_title;
                          @endphp
                        @endforeach
                    </tbody>
                  </table>
                </div>
             
           
          </div>
        </div>


        <div class="row gy-4 pt-5">
          <div class="col-lg-12 content" data-aos="fade-up" data-aos-delay="100">
                <h4>Projects</h4>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover" id="tableProject">
                        <thead>
                            <tr class="table-primary">
                                <th>Project Name</th>
                                <th>Description</th>
                                <th>Funding Req't</th>
                                <th>Status</th>
                                <th>Funding Category</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        @foreach($projects as $project)
                            <tr>
                                <td>
                                {{$project->project_title }}
                                </td>
                                <td>
                                    {{ $project->description }}
                                </td>
                                <td>
                                {{ $project->funding_requirement }}
                                </td>

                                <td>
                                    {{ $project->status }}
                                </td>

                                <td>
                                    {{ $project->funding_category }}
                                </td>
                                
                                <td><span>{{ $project->created_at->diffForHumans() }}</span></td>
                            </tr>
                                @endforeach
                            </tbody>
                    </table>
                </div>
             
           
          </div>
        </div>

      </div>

    </section>
@endsection

@section('script')

<script type="text/javascript">

$(document).ready(function() {

  $('#tableProject').DataTable({
    autoWidth: false,
    columns: [
        { width: '18%' },
        { width: '20%' },
        { width: '11%' },
        { width: '10%' },
        { width: '11%' },
        { width: '10%' },
    ]
  });

  $('#tableComponentProject').DataTable({
    autoWidth: false,
    columns: [
        { width: '18%' },
        { width: '20%' },
        { width: '21%' },
        { width: '10%' },
        { width: '11%' },
        { width: '10%' },
        { width: '10%' },
    ],
    "ordering": false, // Disables all user sorting
    "order": [],        // Clears any default sort
    columnDefs: [
        {targets: [1, 2, 3, 4, 5], searchable: false}
    ],
  });
});


const dataKeys = @json(array_keys($projectCountStatus));
const dataValues = @json(array_values($projectCountStatus));

const dataPie = @json($projectCountFunding);

//console.log(dataPie);
//console.log(dataValues);

Highcharts.chart('status', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Number of Projects, by status'
    },
    xAxis: {
        categories: dataKeys,
        crosshair: true,
        accessibility: {
            description: 'Status'
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Status'
        }
    },
    tooltip: {
        valueSuffix: ' Projects'
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [
        {
            name: 'Status',
            color: '#2487ce',
            data: dataValues
        }
    ]
});

Highcharts.setOptions({
    colors: Highcharts.getOptions().colors.map(function (color) {
        return {
            radialGradient: {
                cx: 0.5,
                cy: 0.3,
                r: 0.7
            },
            stops: [
                [0, color],
                [1, Highcharts.color(color).brighten(-0.3).get('rgb')] // darken
            ]
        };
    })
});

// Build the chart
Highcharts.chart('container', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Percentage of Projects, by funding category'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    accessibility: {
        point: {
            valueSuffix: '%'
        }
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<span style="font-size: 1.2em"><b>{point.name}</b>' +
                    '</span><br>' +
                    '<span style="opacity: 0.6">{point.percentage:.1f} ' +
                    '%</span>',
                connectorColor: 'rgba(128,128,128,0.5)'
            }
        }
    },
    series: [{
        name: 'Projects',
        data: dataPie
    }]
});




</script>

@endsection