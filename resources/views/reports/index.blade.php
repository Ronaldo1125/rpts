@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <h3 class="p-5">Report Generation</h3>
        

        <!-- Button in the start -->
<div class="input-group mb-3">
    <button
        class="btn btn-outline-secondary dropdown-toggle"
        type="button"
        data-bs-toggle="dropdown"
        aria-expanded="false"
    >
        Dropdown
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Action</a></li>
        <li><a class="dropdown-item" href="#">Another action</a></li>
        <li><a class="dropdown-item" href="#">Something else here</a></li>
        <li><hr class="dropdown-divider" /></li>
        <li><a class="dropdown-item" href="#">Separated link</a></li>
    </ul>
    <input
        type="text"
        class="form-control"
        aria-label="Text input with dropdown button"
    />
</div>



    
    </div>
</div>


@endsection

@section('jsvalidator')
{!! JsValidator::formRequest('App\Http\Requests\StoreSectorRequest') !!}
@endsection










