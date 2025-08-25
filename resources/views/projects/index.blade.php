@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <h3 class="p-5">Manage Projects</h3>
         <p class="text-end">
          <a href="{{ route('projects.create')}}"><button class="btn btn-success btn-sm">Create Project</button></a>
        </p>

<!-- Hoverable Table rows -->
    <div class="card">
        <div class="table-responsive text-nowrap p-5">
            <table class="table table-hover" id="myTable">
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Description</th>
                        <th>Users</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  @foreach($projects as $project)
                    <tr>
                        <td>
                          <i class="icon-base bx bxl-angular icon-md text-danger me-4"></i> <span>{{$project->name }}</span>
                        </td>
                        <td>{{ $project->description }}</td>
                        <td>
                          <ul class="list-unstyled m-0 avatar-group d-flex align-items-center">
                            <li
                              data-bs-toggle="tooltip"
                              data-popup="tooltip-custom"
                              data-bs-placement="top"
                              class="avatar avatar-xs pull-up"
                              title="Lilian Fuller">
                              <img src="../assets/img/avatars/2.png" alt="Avatar" class="rounded-circle" />
                            </li>
                            <li
                              data-bs-toggle="tooltip"
                              data-popup="tooltip-custom"
                              data-bs-placement="top"
                              class="avatar avatar-xs pull-up"
                              title="Sophia Wilkerson">
                              <img src="../assets/img/avatars/3.png" alt="Avatar" class="rounded-circle" />
                            </li>
                            <li
                              data-bs-toggle="tooltip"
                              data-popup="tooltip-custom"
                              data-bs-placement="top"
                              class="avatar avatar-xs pull-up"
                              title="Christina Parker">
                              <img src="../assets/img/avatars/4.png" alt="Avatar" class="rounded-circle" />
                            </li>
                          </ul>
                        </td>
                        <td><span class="badge bg-label-primary me-1">Active</span></td>
                        <td>
                          <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                              <i class="icon-base bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" href="javascript:void(0);"
                                ><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a
                              >
                              <a class="dropdown-item" href="javascript:void(0);"
                                ><i class="icon-base bx bx-trash me-1"></i> Delete</a
                              >
                            </div>
                          </div>
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

<!-- Large Modal -->
<div class="modal fade" id="largeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel3">Add Project</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col mb-6">
            <label for="nameLarge" class="form-label">Name</label>
            <input type="text" id="nameLarge" class="form-control" placeholder="Enter Name" />
          </div>
        </div>
        <div class="row g-6">
          <div class="col mb-0">
            <label for="emailLarge" class="form-label">Email</label>
              <input
                type="email"
                id="emailLarge"
                class="form-control"
                placeholder="xxxx@xxx.xx" />
          </div>
          <div class="col mb-0">
            <label for="dobLarge" class="form-label">DOB</label>
            <input type="date" id="dobLarge" class="form-control" />
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>
@endsection