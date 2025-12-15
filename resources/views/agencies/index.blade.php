@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <h3 class="p-5">Manage Agencies</h3>
         <p class="text-end">
          <button class="btn btn-success btn-sm" data-bs-toggle="modal"
            data-bs-target="#addAgency"><i class="icon-base bx bx-bell-plus icon-sm"></i>Create Agency</button>
        </p>

<!-- Hoverable Table rows -->
    <div class="card">
        <div class="table-responsive text-nowrap p-5">
            <table class="table table-hover" id="myTable">
                <thead>
                    <tr>
                        <th>Agency Name</th>
                        <th>Agency Acronym</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  @foreach($agencies as $agency)
                    <tr>
                        <td>
                          <span>{{ $agency->agency_name }}</span>
                        </td>
                        <td class="text-center">
                          {{ $agency->agency_acronym }}
                        </td>
                        <td><span class="badge bg-label-primary me-1">{{ $agency->created_at->diffForHumans() }}</span></td>
                        <td>
                          <form action="{{ route('agencies.destroy', $agency->id) }}" method="POST">
                            <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                              <i class="icon-base bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" href="javascript:void(0);" 
                              data-bs-toggle="modal" data-bs-target="#editAgency{{ $agency->id }}"
                                ><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a
                              >
                              @csrf
                              @method('DELETE')
                              <a class="dropdown-item" href="{{route('agencies.destroy', $agency->id)}}" data-confirm-delete="true"
                                ><i class="icon-base bx bx-trash me-1"></i> Delete</a
                              >
                            </div>
                          </div>
                          </form>
                          
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

<!-- Add Sector Modal -->
<div class="modal fade" id="addAgency" tabindex="-1">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel3">Add Agency</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
      </div>
      <hr>
      <form action="{{ route('agencies.store') }}" method="POST" id="add-form">
              @csrf
      <div class="modal-body">
        <div class="row">
          <div class="col mb-6">
            <label for="agency_name" class="form-label">Agency Name</label>
            <input type="text" name="agency_name" id="agency_name" class="form-control" placeholder="Enter Agency Name" />
          </div>
        </div>

        <div class="row">
          <div class="col mb-6">
            <label for="agency_acronym" class="form-label">Agency Acronym</label>
            <input type="text" name="agency_acronym" id="agency_acronym" class="form-control" placeholder="Enter Agency Acronym" />
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </div>
    </form>
  </div>
</div>

<!-- Edit Agency Modal -->
@foreach($agencies as $agency)
<div class="modal fade" id="editAgency{{$agency->id}}" tabindex="-1">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel3">Edit Agency</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
      </div>
      <hr>
      <form action="{{ route('agencies.update', $agency->id) }}" method="POST" id="add-form">
              @csrf
              @method('PUT')
      <div class="modal-body">
        <div class="row">
          <div class="col mb-6">
            <label for="agency_name" class="form-label">Agency Name</label>
            <input type="text" name="agency_name" id="agency_name" class="form-control" value="{{ $agency->agency_name }}" placeholder="Enter Agency Name" />
          </div>
        </div>

        <div class="row">
          <div class="col mb-6">
            <label for="agency_acronym" class="form-label">Agency Acronym</label>
            <input type="text" name="agency_acronym" id="agency_acronym" class="form-control" value="{{ $agency->agency_acronym }}" placeholder="Enter Sector Acronym" />
          </div>
        </div>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
    </div>
    </form>
  </div>
</div>
@endforeach
@endsection

@section('jsvalidator')
{!! JsValidator::formRequest('App\Http\Requests\StoreAgencyRequest') !!}
@endsection