<?php

use App\Http\Controllers\Cms\AgentsController;
use App\Http\Controllers\Cms\BibliographyController;
use App\Http\Controllers\Cms\ContentsController;
use App\Http\Controllers\Cms\FeaturesController;
use App\Http\Controllers\Cms\LanguagesController;
use App\Http\Controllers\Cms\ManuscriptsController;
use App\Http\Controllers\Cms\PartsController;
use App\Http\Controllers\Cms\PlacesController;
use App\Http\Controllers\Cms\ReferencesController;
use App\Http\Controllers\Cms\UsersController;
use App\Http\Controllers\Cms\WorksController;
use App\Http\Controllers\Frontend\AgentsController as FrontendAgentsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// frontend
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::resource('/agents', FrontendAgentsController::class)->only(['index', 'show'])->names('frontend.agents');

// cms
Route::group(['prefix' => 'cms', 'middleware' => ['auth:sanctum', config('jetstream.auth_session'), 'verified']], function () {
    // dashboard
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('cms');

    // users
    Route::resource('users', UsersController::class);

    // manuscripts
    Route::resource('manuscripts', ManuscriptsController::class);

    // parts
    Route::resource('codicological-units', PartsController::class);

    // contents
    Route::resource('content-units', ContentsController::class);

    // works
    Route::resource('works', WorksController::class);

    // agents
    Route::resource('agents', AgentsController::class);

    // places
    Route::resource('places', PlacesController::class);

    // bibliography
    Route::resource('bibliography', BibliographyController::class);

    // languages
    Route::resource('languages', LanguagesController::class);

    // references
    Route::resource('references', ReferencesController::class);

    // features
    Route::resource('features', FeaturesController::class);
});
