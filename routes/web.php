<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cms\FilesController;
use App\Http\Controllers\Cms\UsersController;
use App\Http\Controllers\Cms\ResourcesController;
use App\Http\Controllers\Frontend\AgentsController;
use App\Http\Controllers\Frontend\FilesController as FrontendFilesController;
use App\Http\Controllers\Frontend\LayersController;
use App\Http\Controllers\Frontend\ManuscriptsController;
use App\Http\Controllers\Frontend\PlacesController;
use App\Http\Controllers\Frontend\TextUnitsController;
use App\Http\Controllers\Frontend\WorksController;

// frontend
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::resource('/agents', AgentsController::class)->only(['index', 'show'])->names('frontend.agents');
Route::resource('/places', PlacesController::class)->only(['index', 'show'])->names('frontend.places');
Route::resource('/works', WorksController::class)->only(['index', 'show'])->names('frontend.works');
Route::resource('/manuscripts', ManuscriptsController::class)->only(['index', 'show'])->names('frontend.manuscripts');
Route::resource('/layers', LayersController::class)->only(['index', 'show'])->names('frontend.layers');
Route::resource('/textunits', TextUnitsController::class)->only(['index', 'show'])->names('frontend.textunits');

// files
Route::get('download/{recordType}', [FrontendFilesController::class, 'downloadZipFile'])->name('frontend.files.download.zip');

Route::get('/about', function () {
    return Inertia::render('About');
})->name('frontend.about');

Route::get('/datamodel', function () {
    return Inertia::render('Datamodel');
})->name('frontend.datamodel');


// cms
Route::group(['prefix' => 'cms', 'middleware' => ['auth:sanctum', config('jetstream.auth_session'), 'verified', 'can:view cms']], function () {
    // dashboard
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('cms');

    // users
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UsersController::class, 'index'])->name('users.index');
        Route::get('/create', [UsersController::class, 'create'])->name('users.create');
        Route::post('/', [UsersController::class, 'store'])->name('users.store');
        Route::get('/{resourceId}/edit', [UsersController::class, 'edit'])->name('users.edit');
        Route::put('/{resourceId}', [UsersController::class, 'update'])->name('users.update');
    });

    // resources
    Route::pattern('resourceName', 'manuscripts|layers|contents|works|agents|places|bibliography|languages|references|features|locations|scripts|text-units');
    Route::group(['prefix' => '{resourceName}'], function () {
        Route::get('/', [ResourcesController::class, 'index'])->name('resources.index');
        Route::get('/create', [ResourcesController::class, 'create'])->name('resources.create');
        Route::post('/', [ResourcesController::class, 'store'])->name('resources.store');
        Route::get('/{resourceId}/edit', [ResourcesController::class, 'edit'])->name('resources.edit');
        Route::put('/{resourceId}', [ResourcesController::class, 'update'])->name('resources.update');
        Route::delete('/{resourceId}', [ResourcesController::class, 'destroy'])->name('resources.destroy');
    });

    // files
    Route::group(['prefix' => 'files/{resourceName}'], function () {
        // upload
        Route::post('upload', [FilesController::class, 'storeOnUpload'])->name('files.upload.store');
        Route::post('upload/batch', [FilesController::class, 'batchUpload'])->name('files.upload.batch');
        Route::post('upload/{resourceId}', [FilesController::class, 'updateOnUpload'])->name('files.upload.update');
    });
});
