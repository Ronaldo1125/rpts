@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <h3 class="p-5">Manage Sectors</h3>
         <p class="text-end">
          <button class="btn btn-success btn-sm" data-bs-toggle="modal"
            data-bs-target="#addSector">Create Sector</button>
        </p>

<!-- Hoverable Table rows -->
    <div class="card">
        <div class="table-responsive text-nowrap p-5">
            <table class="table table-hover" id="myTable">
                <thead>
                    <tr>
                        <th>Sector Name</th>
                        <th>Sector Acronym</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  @foreach($sectors as $sector)
                    <tr>
                        <td>
                         <span>{{ $sector->sector_name }}</span>
                        </td>
                        <td>{{ $sector->sector_acronym }}</td>
                        <td><span class="badge bg-label-primary me-1">{{ $sector->created_at->diffForHumans() }}</span></td>
                        <td>
                          <form action="{{ route('sectors.destroy', $sector->id) }}" method="POST>
                            <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                              <i class="icon-base bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" href="javascript:void(0);" 
                              data-bs-toggle="modal" data-bs-target="#editSector{{ $sector->id }}"
                                ><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a
                              >
                              @csrf
                              @method('DELETE')
                              <a class="dropdown-item" href="{{route('sectors.destroy', $sector->id)}}" data-confirm-delete="true"
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
<div class="modal fade" id="addSector" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel3">Add Sector</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
      </div>
         <hr>
      <form action="{{ route('sectors.store') }}" method="POST" id="add-form">
              @csrf
      <div class="modal-body">
        <div class="row">
          <div class="col mb-6">
            <label for="sector_name" class="form-label">Sector Name</label>
            <input type="text" name="sector_name" id="sector_name" class="form-control" placeholder="Enter Sector Name" />
          </div>
        </div>

        <div class="row">
          <div class="col mb-6">
            <label for="sector_acronym" class="form-label">Sector Acronym</label>
            <input type="text" name="sector_acronym" id="sector_acronym" class="form-control" placeholder="Enter Sector Acronym" />
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

<!-- Edit Sector Modal -->
@foreach($sectors as $sector)
<div class="modal fade" id="editSector{{$sector->id}}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel3">Add Sector</h5>
         
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
      </div>
         <hr>
      <form action="{{ route('sectors.update', $sector->id) }}" method="POST" id="add-form">
              @csrf
              @method('PUT')
      <div class="modal-body">
        <div class="row">
          <div class="col mb-6">
            <label for="sector_name" class="form-label">Sector Name</label>
            <input type="text" name="sector_name" id="sector_name" class="form-control" value="{{ $sector->sector_name }}" placeholder="Enter Sector Name" />
          </div>
        </div>

        <div class="row">
          <div class="col mb-6">
            <label for="sector_acronym" class="form-label">Sector Acronym</label>
            <input type="text" name="sector_acronym" id="sector_acronym" class="form-control" value="{{ $sector->sector_acronym }}" placeholder="Enter Sector Acronym" />
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
@endforeach
@endsection

@section('jsvalidator')
{!! JsValidator::formRequest('App\Http\Requests\StoreSectorRequest') !!}
@endsection