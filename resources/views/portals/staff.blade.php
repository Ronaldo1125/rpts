@extends('layouts.portal')

@section('title', 'Staff Portal')

@section('config')
    <script>
        // Portal Configuration
        window.APP_CONFIG = {
            sidebarPath: '{{ asset("components/staff-sidebar.html") }}',
            defaultPage: 'staff-dashboard'
        };
    </script>
@endsection

@section('sidebar')
    @include('partials.portal.staff-sidebar')
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

    {{-- Staff Pages --}}
    @include('pages.staff.staff-dashboard')
    @include('pages.staff.staff-comments-recommendations')
    @include('pages.staff.staff-project-assessment')
    @include('pages.staff.staff-referrals')
@endsection

@section('scripts')
    <!-- Core System Script (Type Module) -->
    <script type="module" src="{{ asset('js/common/main.js') }}"></script>
@endsection
