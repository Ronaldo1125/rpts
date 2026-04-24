<?php

use App\Http\Controllers\ProjectController_v2;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CipgController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\SubSectorController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\EndorsementController;
use App\Http\Controllers\EndorseYearController;
use App\Http\Controllers\CipgSubmissionController;
use App\Http\Controllers\FundingCategoryController;
use App\Http\Controllers\ProjectDashBoardController;
use App\Http\Controllers\FAQController;

Route::get('/', function () {
    return view('welcome_v2');
})->name('landing');

// Portal routes used by the new landing page JS (landing.js role-based redirects)
Route::get('/admin/portal', function () {
    return view('portals.admin');
})->name('admin.portal');

Route::get('/agency/portal', function () {
    return view('portals.agency');
})->name('agency.portal');

Route::get('/division-head/portal', function () {
    return view('portals.division-head');
})->name('division-head.portal');

Route::get('/staff/pdipbd-portal', function () {
    return view('portals.pdipbd-staff');
})->name('pdipbd-staff.portal');

Route::get('/staff/portal', function () {
    return view('portals.staff');
})->name('staff.portal');


Route::get('/project-dashboard', [ProjectDashBoardController::class, 'index_v2'])->name('projectDashboard.index');
Route::get('/about', [AboutController::class, 'index_v2'])->name('about.index');
Route::get('/faq', [FAQController::class, 'index'])->name('faq.index');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index_v2'])->name('home');
Route::get('/admin/home', [App\Http\Controllers\HomeController::class, 'admin'])->name('admin.dashboard');
Route::get('/agency/home', [App\Http\Controllers\HomeController::class, 'agency'])->name('agency.dashboard');
Route::get('/staff/home', [App\Http\Controllers\HomeController::class, 'staff'])->name('staff.dashboard');
Route::get('/chief/home', [App\Http\Controllers\HomeController::class, 'chief'])->name('chief.dashboard');

Route::group(['middleware' => ['auth']], function() {

    // CIPG Submission — requires login + explicit permission
    Route::get('/cipgSubmission', [CipgSubmissionController::class, 'index'])
        ->name('cipg_submissions.index')
        ->middleware('can:cipg_submission-view');

    //Route of Users
    Route::resource('/users', UserController::class);
    Route::get('v2/users', [UserController::class, 'index_v2'])->name('users.index_v2');

    //Route of Projects
    //Route::resource('/projects', ProjectController::class);

    Route::get('projects', [ProjectController_v2::class, 'index'])->name('projects.index');
    Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::get('projects/edit/{id}', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::post('projects/update/{id}', [ProjectController::class, 'update'])->name('projects.update');
    Route::post('projects/store', [ProjectController::class, 'store'])->name('projects.store');
    Route::post('projects/media', [ProjectController::class, 'storeMedia'])->name('projects.storeMedia');
    Route::delete('projects/{id}',[ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::get('projects/getSubSectors', [ProjectController::class, 'getSubSectors'])->name('projects.getSubSectors');

    Route::get('components', [ComponentController::class, 'index'])->name('components.index');
    Route::get('components/create/{component_id}', [ComponentController::class, 'create'])->name('components.create');
    Route::post('components/store', [ComponentController::class, 'store'])->name('components.store');
    Route::get('components/edit/{id}', [ComponentController::class, 'edit'])->name('components.edit');
    Route::delete('components/{id}', [ComponentController::class, 'destroy'])->name('components.destroy');
    Route::delete('components/subProjectDestroy/{component_id}/{id}', [ComponentController::class, 'subProjectDestroy'])->name('components.subProjectDestroy');
    Route::get('components/{component_id}/subproject/edit/{id}', [ComponentController::class, 'editSubProject'])->name('components.editSubProject');
    Route::post('components/subproject/update/{id}', [ComponentController::class, 'updateSubProject'])->name('components.updateSubProject');

    //Route of Sectors
    Route::resource('/sectors', SectorController::class);
    Route::get('v2/sectors', [SectorController::class, 'index_v2'])->name('sectors.index_v2');

    //Route of Sectors
    Route::resource('/sub_sectors', SubSectorController::class);
    Route::get('v2/sub_sectors', [SubSectorController::class, 'index_v2'])->name('sub_sectors.index_v2');

    //Route of Agencies
    Route::resource('/agencies', AgencyController::class);
    Route::get('v2/agencies', [AgencyController::class, 'index_v2'])->name('agencies.index_v2');
    Route::post('v2/agencies', [AgencyController::class, 'store_v2'])->name('agencies.store_v2');
    Route::put('v2/agencies/{agency}', [AgencyController::class, 'update_v2'])->name('agencies.update_v2');
    Route::delete('v2/agencies/{agency}', [AgencyController::class, 'destroy_v2'])->name('agencies.destroy_v2');
    Route::post('v2/agencies/validate', [AgencyController::class, 'validate_v2'])->name('agencies.validate_v2');

    //Route of RDP Chapters
    Route::resource('/chapters', ChapterController::class);
    Route::get('v2/chapters', [ChapterController::class, 'index_v2'])->name('chapters.index_v2');

    //Route of Indicators
    Route::resource('/indicators', IndicatorController::class);
    Route::get('v2/indicators', [IndicatorController::class, 'index_v2'])->name('indicators.index_v2');

     //Route of Endorse Years
    Route::resource('/endorse_years', EndorseYearController::class);
    Route::get('v2/endorse_years', [EndorseYearController::class, 'index_v2'])->name('endorse_years.index_v2');

    //Route of Report Generation
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/searchReport', [ReportController::class, 'searchReport'])->name('reports.searchReport');
    Route::get('/reports/generatePdf', [ReportController::class, 'generatePdf'])->name('reports.generatePdf');
    Route::get('/reports/generateExcel', [ReportController::class, 'generateExcel'])->name('reports.generateExcel');

    //Route of Role
    Route::resource('/roles', RoleController::class);
    Route::get('v2/roles', [RoleController::class, 'index_v2'])->name('roles.index_v2');

    //Route of Permission
    Route::resource('/permissions', PermissionController::class);
    Route::get('v2/permissions', [PermissionController::class, 'index_v2'])->name('permissions.index_v2');

    //Route of CIPG Submission
    Route::post('cipgs/media', [CipgController::class, 'storeMedia'])->name('cipgs.storeMedia')->middleware('can:cipg_submission-view');
    Route::resource('/cipgs', CipgController::class)->middleware('can:cipg_submission-view');

    //Route of Activity Logs
    Route::get('/activity_logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');
    Route::get('v2/activity_logs', [ActivityLogController::class, 'index_v2'])->name('activity_logs.index_v2');
    
    
    // Route of Profiles

    //Route::resource('/profiles', ProfileController::class);
    Route::get('/profiles', [ProfileController::class, 'index'])->name('profiles.index');
    Route::get('v2/profiles', [ProfileController::class, 'index_v2'])->name('profiles.index_v2');
    Route::post('/profiles/update', [ProfileController::class, 'update'])->name('profiles.update');
    //Route::post('/profiles/store', [ProfileController::class, 'store'])->name('profiles.store');
    Route::post('/profiles/update-pic', [ProfileController::class, 'updatePic'])->name('profiles.updatePic');
    Route::post('/profiles/update-password/{id}', [ProfileController::class, 'updatePassword'])->name('profiles.updatePassword');

    //Route Get Location

    Route::get('/location/geDistricts', [LocationController::class, 'getDistricts'])->name('location.getDistricts');
    Route::get('/location/getMunicipalities', [LocationController::class, 'getMunicipalities'])->name('location.getMunicipalities');
    

});




