<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\EndorsementController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function() {

    //Route of Users
    Route::resource('/users', UserController::class);

    //Route of Projects
    //Route::resource('/projects', ProjectController::class);

    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('projects/store', [ProjectController::class, 'store'])->name('projects.store');
    Route::post('projects/media', [ProjectController::class, 'storeMedia'])->name('projects.storeMedia');


    //Route of Sectors
    Route::resource('/sectors', SectorController::class);

    //Route of Agencies
    Route::resource('/agencies', AgencyController::class);

    //Route of RDP Chapters
    Route::resource('/chapters', ChapterController::class);

    //Route of Statuses
    Route::resource('/statuses', StatusController::class);

    //Route of Endorsements
    Route::resource('endorsements', EndorsementController::class);

    //Route of Report Generation
    Route::resource('/reports', ReportController::class);

    //Route of Role
    Route::resource('/roles', RoleController::class);

    //Route of Permission
    Route::resource('/permissions', PermissionController::class);
    
    // Route of Profiles

    //Route::resource('/profiles', ProfileController::class);
    Route::get('/profiles', [ProfileController::class, 'index'])->name('profiles.index');
    Route::post('/profiles/update', [ProfileController::class, 'update'])->name('profiles.update');
    Route::post('/profiles/store', [ProfileController::class, 'store'])->name('profiles.store');
    Route::post('/profiles/update-pic', [ProfileController::class, 'updatePic'])->name('profiles.updatePic');
    Route::post('/profiles/update-password/{id}', [ProfileController::class, 'updatePassword'])->name('profiles.updatePassword');

    //Route Get Location

    Route::get('/location/geDistricts', [LocationController::class, 'getDistricts'])->name('location.getDistricts');
    Route::get('/location/getMunicipalities', [LocationController::class, 'getMunicipalities'])->name('location.getMunicipalities');
    

});




