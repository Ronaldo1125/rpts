@extends('layouts.portal')

@section('title', 'Division Head Portal')

@section('config')
    <script>
        // Portal Configuration
        window.APP_CONFIG = {
            sidebarPath: '{{ asset("components/division-head-sidebar.html") }}',
            defaultPage: 'division-head-dashboard'
        };
    </script>
@endsection

@section('sidebar')
    @include('partials.portal.division-head-sidebar')
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

    {{-- Division Head Pages --}}
    @include('pages.division-head.division-head-dashboard')
    @include('pages.division-head.division-head-comments-recommendations')
    @include('pages.division-head.division-head-project-assessment')
    @include('pages.division-head.division-head-referrals')
@endsection

@section('scripts')
    <!-- Core System Script (Type Module) -->
    <script type="module" src="{{ asset('js/common/main.js') }}"></script>
    <script src="{{ asset('js/division-head/division-head-referrals-loader.js') }}"></script>
@endsection
