<?php

use App\Http\Controllers\Cms\ResourcesController;
use App\Http\Controllers\Cms\UsersController;
use App\Http\Controllers\Frontend\AgentsController;
use App\Http\Controllers\Frontend\LayersController;
use App\Http\Controllers\Frontend\PlacesController;
use App\Http\Controllers\Frontend\ManuscriptsController;
use App\Http\Controllers\Frontend\WorksController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// frontend
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::resource('/agents', AgentsController::class)->only(['index', 'show'])->names('frontend.agents');
Route::resource('/places', PlacesController::class)->only(['index', 'show'])->names('frontend.places');
Route::resource('/works', WorksController::class)->only(['index', 'show'])->names('frontend.works');
Route::resource('/manuscripts', ManuscriptsController::class)->only(['index', 'show'])->names('frontend.manuscripts');
Route::resource('/layers', LayersController::class)->only(['index', 'show'])->names('frontend.layers');
// Route::resource('/about', FrontendAboutController::class)->only(['index', 'show'])->names('frontend.about');

Route::get('/about', function () {
    return Inertia::render('About');
})->name('frontend.about');

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

    Route::pattern('resourceName', 'manuscripts|layers|contents|works|agents|places|bibliography|languages|references|features|locations|scripts');
    Route::group(['prefix' => '{resourceName}'], function () {
        Route::get('/', [ResourcesController::class, 'index'])->name('resources.index');
        Route::get('/create', [ResourcesController::class, 'create'])->name('resources.create');
        Route::get('/{resourceId}/edit', [ResourcesController::class, 'edit'])->name('resources.edit');
    });
});
