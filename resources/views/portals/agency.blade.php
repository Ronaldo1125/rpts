@extends('layouts.portal')

@section('title', 'Agency Portal')

@section('config')
    <script>
        // Portal Configuration
        window.APP_CONFIG = {
            sidebarPath: '{{ asset("components/agency-sidebar.html") }}',
            defaultPage: 'agency-dashboard'
        };
    </script>
@endsection

@section('sidebar')
    @include('partials.portal.agency-sidebar')
@endsection

@section('topnav')
    @include('partials.portal.topnav')
@endsection

@section('footer')
    @include('partials.portal.footer')
@endsection

@section('pages')
    {{-- Common Pages --}}
    @include('pages.profile')
    @include('pages.project-workspace')
    @include('pages.cpp-view')

    {{-- Agency Pages --}}
    @include('pages.agency.submissions')
    @include('pages.agency.agency-dashboard')
    @include('pages.agency.agency-comments')
    @include('pages.agency.cipg-guide')
    @include('pages.agency.cpp-form')
    @include('pages.agency.create-component-project')

    {{-- Needed Admin Pages for Agency --}}
    @include('pages.admin.projects')
    @include('pages.admin.component-project')
@endsection

@section('scripts')
    <!-- Core System Script (Type Module) -->
    <script type="module" src="{{ asset('js/common/main.js') }}"></script>
@endsection
