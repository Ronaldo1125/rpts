<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CipgGuideController;
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
use App\Http\Controllers\V2\ProjectDashBoardController;
use App\Http\Controllers\FAQController;

Route::get('/', function () {
    return view('welcome_v2');
})->name('landing');


Route::get('/project-dashboard', [ProjectDashBoardController::class, 'index_v2'])->name('projectDashboard.index');
Route::get('/project-dashboard/fetch', [ProjectDashBoardController::class, 'fetchData'])->name('projectDashboard.fetch');
Route::get('/about', [AboutController::class, 'index_v2'])->name('about.index');
Route::get('/faq', [FAQController::class, 'index'])->name('faq.index');


Auth::routes();

Route::get('/home', [App\Http\Controllers\V2\HomeController::class, 'index'])->name('home');
Route::get('/admin/home', [App\Http\Controllers\V2\HomeController::class, 'admin'])->name('admin.dashboard');
Route::get('/agency/home', [App\Http\Controllers\V2\HomeController::class, 'agency'])->name('agency.dashboard');
Route::get('/staff/home', [App\Http\Controllers\V2\HomeController::class, 'staff'])->name('staff.dashboard');
Route::get('/chief/home', [App\Http\Controllers\V2\HomeController::class, 'chief'])->name('chief.dashboard');

Route::group(['middleware' => ['auth']], function () {

    // CIPG Submission — requires login + explicit permission
    Route::get('/cipgSubmission', [CipgSubmissionController::class, 'index'])
        ->name('cipg_submissions.index')
        ->middleware('can:cipg_submission-view');

    Route::resource('/cipg_guide', CipgGuideController::class);

    //Route of Manage Users
    Route::resource('/users', UserController::class);

    //Route of Projects
    //Route::resource('/projects', ProjectController::class);

    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::get('projects/edit/{id}', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::post('projects/update/{id}', [ProjectController::class, 'update'])->name('projects.update');
    Route::post('projects/store', [ProjectController::class, 'store'])->name('projects.store');
    Route::post('projects/media', [ProjectController::class, 'storeMedia'])->name('projects.storeMedia');
    Route::delete('projects/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');
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

    //Route of Sub-Sectors
    Route::resource('/sub_sectors', SubSectorController::class);

    //Route of Agencies
    Route::resource('/agencies', AgencyController::class);

    //Route of RDP Chapters
    Route::resource('/chapters', ChapterController::class);

    //Route of Indicators
    Route::resource('/indicators', IndicatorController::class);

    //Route of Endorse Years
    Route::resource('/endorse_years', EndorseYearController::class);

    //Route of Report Generation
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/searchReport', [ReportController::class, 'searchReport'])->name('reports.searchReport');
    Route::get('/reports/generatePdf', [ReportController::class, 'generatePdf'])->name('reports.generatePdf');
    Route::get('/reports/generateExcel', [ReportController::class, 'generateExcel'])->name('reports.generateExcel');

    //Route of Roles
    Route::resource('/roles', RoleController::class);

    //Route of Permission
    Route::resource('/permissions', PermissionController::class);

    //Route of CIPG Submission
    Route::post('cipgs/media', [CipgController::class, 'storeMedia'])->name('cipgs.storeMedia')->middleware('can:cipg_submission-view');
    Route::resource('/cipgs', CipgController::class)->middleware('can:cipg_submission-view');

    //Route of Activity Logs
    Route::get('/activity_logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');

    // Modernized V2 Routes
    Route::prefix('v2')->name('v2.')->group(function () {

        //Route of CIPG Submission
        Route::get('cipg_submissions', [\App\Http\Controllers\V2\CipgSubmissionController::class, 'index'])->name('cipg_submissions.index');
        Route::get('cipg_submissions/manage', [\App\Http\Controllers\V2\CipgSubmissionController::class, 'manage'])->name('cipg_submissions.manage');
        Route::get('cipg_submissions/create', [\App\Http\Controllers\V2\CipgSubmissionController::class, 'create'])->name('cipg_submissions.create');
        Route::get('cipg_submissions/fetch', [\App\Http\Controllers\V2\CipgSubmissionController::class, 'fetchData'])->name('cipg_submissions.fetch');
        Route::get('cipg_submissions/{id}/show', [\App\Http\Controllers\V2\CipgSubmissionController::class, 'show'])->name('cipg_submissions.show');
        Route::get('cipg_submissions/{id}/edit', [\App\Http\Controllers\V2\CipgSubmissionController::class, 'edit'])->name('cipg_submissions.edit');
        Route::post('cipg_submissions/store', [\App\Http\Controllers\V2\CipgSubmissionController::class, 'store'])->name('cipg_submissions.store');
        Route::get('cipg_submissions/{id}/details', [\App\Http\Controllers\V2\CipgSubmissionController::class, 'details'])->name('cipg_submissions.details');
        Route::delete('cipg_submissions/{id}', [\App\Http\Controllers\V2\CipgSubmissionController::class, 'destroy'])->name('cipg_submissions.destroy');

        //Route of Projects
        Route::get('projects/get-sub-sectors', [\App\Http\Controllers\V2\ProjectController::class, 'getSubSectors'])->name('projects.getSubSectors');
        Route::post('projects/media', [\App\Http\Controllers\V2\ProjectController::class, 'storeMedia'])->name('projects.storeMedia');
        Route::resource('projects', \App\Http\Controllers\V2\ProjectController::class);

        Route::get('components', [\App\Http\Controllers\V2\ComponentController::class, 'index'])->name('components.index');
        Route::get('components/create/{component_id}', [\App\Http\Controllers\V2\ComponentController::class, 'create'])->name('components.create');
        Route::post('components/store', [\App\Http\Controllers\V2\ComponentController::class, 'store'])->name('components.store');
        Route::get('components/edit/{id}', [\App\Http\Controllers\V2\ComponentController::class, 'edit'])->name('components.edit');
        Route::delete('components/{id}', [\App\Http\Controllers\V2\ComponentController::class, 'destroy'])->name('components.destroy');
        Route::delete('components/subProjectDestroy/{component_id}/{id}', [\App\Http\Controllers\V2\ComponentController::class, 'subProjectDestroy'])->name('components.subProjectDestroy');
        Route::get('components/{component_id}/subproject/show/{id}', [\App\Http\Controllers\V2\ComponentController::class, 'showSubProject'])->name('components.showSubProject');
        Route::get('components/{component_id}/subproject/edit/{id}', [\App\Http\Controllers\V2\ComponentController::class, 'editSubProject'])->name('components.editSubProject');
        Route::match(['post', 'put'], 'components/subproject/update/{id}', [\App\Http\Controllers\V2\ComponentController::class, 'updateSubProject'])->name('components.updateSubProject');

        Route::resource('users', \App\Http\Controllers\V2\UserController::class);
        Route::post('users/validate', [\App\Http\Controllers\V2\UserController::class, 'validateField'])->name('users.validate');

        Route::resource('sectors', \App\Http\Controllers\V2\SectorController::class);
        Route::post('sectors/validate', [\App\Http\Controllers\V2\SectorController::class, 'validateField'])->name('sectors.validate');

        Route::resource('sub_sectors', \App\Http\Controllers\V2\SubSectorController::class);
        Route::post('sub_sectors/validate', [\App\Http\Controllers\V2\SubSectorController::class, 'validateField'])->name('sub_sectors.validate');

        Route::resource('agencies', \App\Http\Controllers\V2\AgencyController::class);
        Route::post('agencies/validate', [\App\Http\Controllers\V2\AgencyController::class, 'validateField'])->name('agencies.validate');

        Route::resource('chapters', \App\Http\Controllers\V2\ChapterController::class);
        Route::post('chapters/validate', [\App\Http\Controllers\V2\ChapterController::class, 'validateField'])->name('chapters.validate');

        Route::resource('indicators', \App\Http\Controllers\V2\IndicatorController::class);
        Route::post('indicators/validate', [\App\Http\Controllers\V2\IndicatorController::class, 'validateField'])->name('indicators.validate');

        Route::resource('endorse_years', \App\Http\Controllers\V2\EndorseYearController::class);
        Route::post('endorse_years/validate', [\App\Http\Controllers\V2\EndorseYearController::class, 'validateField'])->name('endorse_years.validate');

        Route::resource('roles', \App\Http\Controllers\V2\RoleController::class);
        Route::post('roles/validate', [\App\Http\Controllers\V2\RoleController::class, 'validateField'])->name('roles.validate');

        Route::resource('permissions', \App\Http\Controllers\V2\PermissionController::class);
        Route::post('permissions/validate', [\App\Http\Controllers\V2\PermissionController::class, 'validateField'])->name('permissions.validate');

        Route::get('activity_logs', [\App\Http\Controllers\V2\ActivityLogController::class, 'index'])->name('activity_logs.index');

        Route::get('reports', [\App\Http\Controllers\V2\ReportController::class, 'index'])->name('reports.index');
        Route::post('reports/searchReport', [\App\Http\Controllers\V2\ReportController::class, 'searchReport'])->name('reports.searchReport');
        Route::get('reports/generatePdf', [\App\Http\Controllers\V2\ReportController::class, 'generatePdf'])->name('reports.generatePdf');
        Route::get('reports/generateExcel', [\App\Http\Controllers\V2\ReportController::class, 'generateExcel'])->name('reports.generateExcel');



        Route::get('profiles', [\App\Http\Controllers\V2\ProfileController::class, 'index'])->name('profiles.index');
        Route::post('profiles/update', [\App\Http\Controllers\V2\ProfileController::class, 'update'])->name('profiles.update');
        Route::post('profiles/update-pic', [\App\Http\Controllers\V2\ProfileController::class, 'updatePic'])->name('profiles.updatePic');
        Route::post('profiles/update-password', [\App\Http\Controllers\V2\ProfileController::class, 'updatePassword'])->name('profiles.updatePassword');

    });

    // Referrals API Routes
    Route::get('referrals', [\App\Http\Controllers\ReferralController::class, 'index'])->name('referrals.index');
    Route::get('referrals/validated-projects', [\App\Http\Controllers\ReferralController::class, 'validatedProjects'])->name('referrals.validatedProjects');
    Route::get('referrals/staff', [\App\Http\Controllers\ReferralController::class, 'staffByDivision'])->name('referrals.staffByDivision');
    Route::post('referrals', [\App\Http\Controllers\ReferralController::class, 'store'])->name('referrals.store');
    Route::post('referrals/{referral}/assign-staff', [\App\Http\Controllers\ReferralController::class, 'assignStaff'])->name('referrals.assignStaff');
    Route::delete('referrals/{referral}', [\App\Http\Controllers\ReferralController::class, 'destroy'])->name('referrals.destroy');
    Route::post('referrals/staff-action/{submissionId}', [\App\Http\Controllers\ReferralController::class, 'staffAction'])->name('referrals.staffAction');
    Route::post('referrals/save-comments/{submissionId}', [\App\Http\Controllers\ReferralController::class, 'saveComments'])->name('referrals.saveComments');

    // Route of Profiles

    Route::get('/profiles', [ProfileController::class, 'index'])->name('profiles.index');
    Route::post('/profiles/update', [ProfileController::class, 'update'])->name('profiles.update');
    Route::post('/profiles/update-pic', [ProfileController::class, 'updatePic'])->name('profiles.updatePic');
    Route::post('/profiles/update-password/{id}', [ProfileController::class, 'updatePassword'])->name('profiles.updatePassword');


    //Route Get Location

    Route::get('/location/getDistricts', [LocationController::class, 'getDistricts'])->name('location.getDistricts');
    Route::get('/location/getMunicipalities', [LocationController::class, 'getMunicipalities'])->name('location.getMunicipalities');
    Route::get('/location/getBarangays', [LocationController::class, 'getBarangays'])->name('location.getBarangays');

    // Project Assessment Report Routes
    Route::resource('project-assessment-reports', \App\Http\Controllers\ProjectAssessmentReportController::class);



});




