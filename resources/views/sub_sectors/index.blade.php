@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <h3 class="p-5">Manage Sub-Sectors</h3>
         <p class="text-end">
          <button class="btn btn-success btn-sm" data-bs-toggle="modal"
            data-bs-target="#addSector"><i class="icon-base bx bx-bell-plus icon-sm"></i>Create Sub-Sector</button>
        </p>

<!-- Hoverable Table rows -->
    <div class="card">
        <div class="table-responsive text-nowrap p-5">
            <table class="table table-hover" id="myTable">
                <thead>
                    <tr>
                        <th>SubSector Name</th>
                        <th>Sector Id</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  @foreach($sub_sectors as $sub_sector)
                    <tr>
                        <td>
                         <span>{{ $sub_sector->subsector_name }}</span>
                        </td>
                        <td>{{ $sub_sector->sector_id }}</td>
                        <td><span class="badge bg-label-primary me-1">{{ $sub_sector->created_at->diffForHumans() }}</span></td>
                        <td>
                          <form action="{{ route('sub_sectors.destroy', $sub_sector->id) }}" method="POST>
                            <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                              <i class="icon-base bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" href="javascript:void(0);" 
                              data-bs-toggle="modal" data-bs-target="#editSubSector{{ $sub_sector->id }}"
                                ><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a
                              >
                              @csrf
                              @method('DELETE')
                              <a class="dropdown-item" href="{{route('sub_sectors.destroy', $sub_sector->id)}}" data-confirm-delete="true"
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

<!-- Add SubSector Modal -->
<div class="modal fade" id="addSector" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel3">Add Sub-Sector</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
      </div>
         <hr>
      <form action="{{ route('sub_sectors.store') }}" method="POST" id="add-form">
              @csrf
      <div class="modal-body">
        <div class="row">
          <div class="col mb-6">
            <label for="subsector_name" class="form-label">Sub-Sector Name</label>
            <input type="text" name="subsector_name" id="subsector_name" class="form-control" placeholder="Enter Sub-Sector Name" />
          </div>
        </div>

        <div class="row">
          <div class="col mb-6">
            <label for="sector_id" class="form-label">Sector:</label>
              <select class="form-control" name="sector_id" id="sector_id">
                <option value="">Select sector ...</option> 
                  @foreach ($sectors as $key => $sector)
                    <option value="{{ $key }}">{{ $sector }}</option>
                  @endforeach                
              </select>
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
@foreach($sub_sectors as $sub_sector)
<div class="modal fade" id="editSubSector{{$sub_sector->id}}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel3">Update Sub-Sector</h5>
         
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
      </div>
         <hr>
      <form action="{{ route('sub_sectors.update', $sub_sector->id) }}" method="POST" id="add-form">
              @csrf
              @method('PUT')
      <div class="modal-body">
        <div class="row">
          <div class="col mb-6">
            <label for="subsector_name" class="form-label">Sub-Sector Name</label>
            <input type="text" name="subsector_name" id="subsector_name" class="form-control" value="{{ $sub_sector->subsector_name }}" placeholder="Enter Sector Name" />
          </div>
        </div>

        <div class="row">
          <div class="col mb-6">
            <label for="sector_id" class="form-label">Sector:</label>
              <select class="form-control" name="sector_id" id="sector_id">
                <option value="">Select sector ...</option> 
                  @foreach ($sectors as $key => $sector)
                    <option value="{{ $key }}" {{ ($key == $sub_sector->sector_id) ? "selected='selected'" : "" }}>{{ $sector }}</option>
                  @endforeach                
              </select>
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
{!! JsValidator::formRequest('App\Http\Requests\StoreSubSectorRequest') !!}
@endsection