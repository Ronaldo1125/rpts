@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <h3 class="px-5">Reports</h3>
        <h5 class="px-5">Project Report</h5>
                <!-- Text alignment -->
                <div class="row mb-12 g-6">
                  <div class="col-md-6 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                          <form action="{{ route('reports.searchReport') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                             <div class="col-md-3 form-control-validation">
                                <label class="form-label" for="funding_category_id">Funding Category</label>
                                <select id="funding_category_id" name="funding_category_id" class="form-select">
                                  <option value="">-- Select Funding Category --</option>
                                  @foreach($funding_categories as $key => $category_name)
                                  <option value="{{ $key }}" {{ (isset($selectedFundingCategoryId) && $selectedFundingCategoryId == $key) ? 'selected="selected"' : ''}}>{{ $category_name }}</option>
                                  @endforeach
                                </select>  
                              </div>

                              <div class="col-md-3 form-control-validation">
                                <label class="form-label" for="status_id">Status</label>
                                <select id="status_id" name="status_id" class="form-select">
                                  <option value="">-- Select Status --</option>
                                  @foreach($statuses as $key => $status_name)
                                  <option value="{{ $key }}" {{ (isset($selectedStatusId) && $selectedStatusId == $key) ? 'selected="selected"' : ''}}>{{ $status_name }}</option>
                                  @endforeach
                                </select>  
                              </div>
                            </div>
                            <button type="submit" class="btn btn-danger mt-5"><i class="icon-base bx bx-filter-alt icon-sm"></i>Filter</button>
                            
                          </form>
                        </div>
                        
                    </div>
                </div>

               
             
                   
                   
                </div>
                <!--/ Text alignment -->
        <div class="card">
          <div class="card-body text-end">
            <p>Download Reports</p>
            <a href="{{ route('reports.generateExcel', ['funding_category_id' => $selectedFundingCategoryId, 'status_id' => $selectedStatusId])}}" onclick="return confirm('Are you sure you want to export excel file?');">
                <button class="btn btn-success btn-sm"><i class="icon-base bx bx-export icon-sm"></i> Export Excel</button>
            </a> &nbsp; &nbsp;
             <a href="{{ route('reports.generatePdf', ['funding_category_id' => $selectedFundingCategoryId, 'status_id' => $selectedStatusId])}}" onclick="return confirm('Are you sure you want to download on pdf file?');">
                <button class="btn btn-primary btn-sm"><i class="icon-base bx bx-bxs-file-pdf icon-sm"></i> Download PDF</button>
            </a>
          </div>
        </div>

        <!-- Hoverable Table rows -->
            <div class="card">
                <div class="table-responsive text-nowrap p-5">
                    <table class="table table-hover" id="myTableProject">
                        <thead>
                            <tr>
                                <th>Project Name {{(isset($page)) ? $page : '' }}</th>
                                <th>Description</th>
                                <th>Documents</th>
                                <th>Status</th>
                                <th>Funding Category</th>
                                <th>Funding Req't</th>
                                <th>Sector</th>
                                <th>Sub-Sector</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        @foreach($projects as $project)
                            <tr>
                                <td>
                                {{$project->project_title }}
                                </td>
                                <td>{{ $project->description }}</td>

                                @php
                                $medias = $project->getMedia('document');
                                @endphp

                                <td class="text-center">
                                @foreach ($medias as $media)
                                    <a href="{{ $media->getUrl() }}" data-toggle="tooltip" data-placement="bottom" title="{{ $media->name }}"><p class="mb-2 small"><i class="menu-icon tf-icons bx bx-paperclip"></i></p></a>
                                @endforeach
                                </td>
                                <td>
                                  {{ $project->status->status_name }}
                                </td>
                                <td>
                                    {{ $project->funding_category->category_name }}
                                </td>
                                
                                <td class="text-end">
                                    {{ number_format($project->funding_requirement, 2) }}
                                </td>

                                <td>
                                    {{ $project->project_sector->sector->sector_name }}
                                </td>
                                <td>
                                    {{ $project->project_sector->sub_sector->subsector_name }}
                                </td>
                            </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>
            </div>
                    <!--/ Hoverable Table rows --> 
    </div>
</div>


@endsection

@section('jsvalidator')

@endsection

@section('script')

<script type="text/javascript">


$(document).ready(function() {

  $('#myTableProject').DataTable({
    autoWidth: false,
    columns: [
        { width: '20%' },
        { width: '25%' },
        { width: '10%' },
        { width: '12%' },
        { width: '13%' },
        { width: '10%' },
        { width: '10%' },
        { width: '10%' },
    ],
    // layout: {
    //     topStart: {
    //         buttons: [
    //           'copyHtml5', 
    //           {extend: 'excelHtml5', title: 'project_data'}, 
    //           'csvHtml5', 
    //           {extend: 'pdfHtml5', title: 'project_data'},
    //           'print',
    //         ]
    //     }
    // }
    
  });
});

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})




</script>
@endsection