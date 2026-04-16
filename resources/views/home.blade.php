@extends('layouts.app')

@section('content')

 <!-- Content wrapper -->
 <div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
      <div class="row">
        <div class="col-xxl-12 mb-6 order-0">
          <div class="card">
            <div class="d-flex align-items-start row">
              <div class="col-sm-7">
                <div class="card-body">
                  <h5 class="card-title text-primary mb-3">Welcome <span class="text-warning">{{ Auth::user()->name }}</span>! 🎉</h5>
                  {{-- <p class="mb-6">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Quam delectus illo odit autem asperiores culpa?
                  </p>

                  <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-primary">View Projects</a> --}}
                </div>
              </div>
              {{-- <div class="col-sm-5 text-center text-sm-left">
                <div class="card-body pb-0 px-0 px-md-6">
                  <img
                    src="../assets/img/illustrations/man-with-laptop.png"
                    height="175"
                    alt="View Badge User" />
                </div>
              </div> --}}
            </div>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-lg-2 col-2 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between mb-4">
                <div class="avatar flex-shrink-0">
                  <img
                    src="../assets/img/icons/unicons/project_totals.png"
                    alt="chart success"
                    class="rounded" />
                </div>
              </div>
              <div class="text-center">
                <p class="mb-1">
                  <h4 class="text-success">Projects</h4>
                </p>
                  <h2 class="card-title mb-3 text-success">{{ number_format($totalProjectCost, 2) }}</h2>
                  <small class="text-success fw-medium text-center">Total Cost (in Millions)</small>
              </div>
            </div>
          </div>
        </div>

        
          
        

        <div class="col-lg-2 col-2 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between mb-4">
                <div class="avatar flex-shrink-0">
                  <img
                    src="../assets/img/icons/unicons/proposed.png"
                    alt="chart success"
                    class="rounded" />
                </div>
              </div>
              <div class="text-center">
                <p class="mb-1">
                  <h4 class="text-primary">Proposed</h4>
                </p>
                  <h2 class="card-title mb-3 text-primary">{{ (isset($statusGroupNameCounts['proposed'])) ? $statusGroupNameCounts['proposed'] : 0 }}</h2>
                  <small class="text-primary fw-medium text-center">Number of Projects</small>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-2 col-2 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between mb-4">
                <div class="avatar flex-shrink-0">
                  <img
                    src="../assets/img/icons/unicons/terminated.png"
                    alt="chart success"
                    class="rounded" />
                </div>
              </div>
              <div class="text-center">
                <p class="mb-1">
                  <h4 class="text-danger">Terminated</h4>
                </p>
                  <h2 class="text-danger card-title mb-3">{{ (isset($statusGroupNameCounts['terminated'])) ? $statusGroupNameCounts['terminated'] : 0 }}</h2>
                  <small class="text-danger fw-medium text-center">Number of Projects</small>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-2 col-2 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between mb-4">
                <div class="avatar flex-shrink-0">
                  <img
                    src="../assets/img/icons/unicons/ad-blocker.png"
                    alt="chart success"
                    class="rounded" />
                </div>
              </div>
              <div class="text-center">
                <p class="mb-1">
                  <h4 class="text-danger">Suspended</h4>
                </p>
                  <h2 class="card-title mb-3 text-danger">{{ (isset($statusGroupNameCounts['suspended'])) ? $statusGroupNameCounts['suspended'] : 0 }}</h2>
                  <small class="text-danger fw-medium text-center">Number of Projects</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- / Content -->
@endsection
