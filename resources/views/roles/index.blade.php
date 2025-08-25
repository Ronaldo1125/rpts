@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <h3 class="p-5">Manage Roles</h3>
         <p class="text-end">
          <button class="btn btn-success btn-sm" data-bs-toggle="modal"
            data-bs-target="#addRole">Create Role</button>
        </p>

<!-- Hoverable Table rows -->
    <div class="card">
        <div class="table-responsive text-nowrap p-5">
            <table class="table table-hover" id="myTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Permissions</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  @foreach($roles as $role)
                    <tr>
                        <td>
                          <i class="icon-base bx bxl-angular icon-md text-danger me-4"></i> <span>{{ $role->name }}</span>
                        </td>
                        <td>
                          @if($role->permissions->count() > 0)
                            @foreach($role->permissions->pluck('name') as $name )
                            <span class="badge bg-label-alert me-1 mb-1">{{ $name }}</span>
                            @endforeach
                          @endif
                        </td>
                        <td><span class="badge bg-label-primary me-1">{{ $role->created_at->diffForHumans() }}</span></td>
                        <td>
                          <form action="{{ route('roles.destroy', $role->id) }}" method="POST">
                            <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                              <i class="icon-base bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" href="javascript:void(0);" 
                              data-bs-toggle="modal" data-bs-target="#editRole{{ $role->id }}"
                                ><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a
                              >
                              @csrf
                              @method('DELETE')
                              <a class="dropdown-item" href="{{route('roles.destroy', $role->id)}}" data-confirm-delete="true"
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
<div class="modal fade" id="addRole" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel3">Add Role</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
      </div>
      <form action="{{ route('roles.store') }}" method="POST" id="add-form">
              @csrf
      <div class="modal-body">
        <div class="row">
          <div class="col mb-6">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Enter Role Name" />
          </div>
        </div>

        <div class="row">
            <div class="col-md-6 form-control-validation2">
              <label class="form-label" for="permission">Permissions</label>
              <select id="permission" name="permission[]" class="form-control selectpicker" multiple data-live-search="true">
              {{-- <option selected disabled>Choose Permission</option> --}}
                @foreach($permissions as $permission)
                <option value="{{ $permission }}">{{ $permission }}</option>
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
@foreach($roles as $role)
<div class="modal fade" id="editRole{{$role->id}}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel3">Edit Role Name</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
      </div>
      <form action="{{ route('roles.update', $role->id) }}" method="POST" id="add-form">
              @csrf
              @method('PUT')
      <div class="modal-body">
        <div class="row">
          <div class="col mb-6">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $role->name }}" placeholder="Enter Role Name" />
          </div>
        </div>

        <div class="row">
            <div class="col-md-6 form-control-validation2">
              <label class="form-label" for="permission">Permissions</label>
              <select id="permission" name="permission[]" class="form-control selectpicker" multiple data-live-search="true">
              {{-- <option selected disabled>Choose Permission</option> --}}
                @foreach($permissions as $permission)
                <option value="{{ $permission }}" {{ in_array($permission, $role->permissions->pluck('name')->toArray()) ? "selected" : "" }}>{{ $permission }}</option>
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
{!! JsValidator::formRequest('App\Http\Requests\StoreRoleRequest') !!}
@endsection

@section('script')

<script type="text/javascript">
$(document).ready(function() {
    $('.js-example-basic-multiple').select2({
        placeholder: 'Select Permissions'
    });
});


</script>
@endsection