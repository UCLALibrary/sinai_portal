<?php

use App\Http\Controllers\Cms\DatesController;
use App\Http\Controllers\Cms\ManuscriptsController;
use App\Http\Controllers\Cms\PartsController;
use App\Http\Controllers\Cms\UsersController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::group(['prefix' => 'cms', 'middleware' => ['auth:sanctum', config('jetstream.auth_session'), 'verified']], function () {
    // cms
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('cms');

    // users
    Route::resource('users', UsersController::class);

    // manuscripts
    Route::resource('manuscripts', ManuscriptsController::class);

    // parts
    Route::resource('codicological-units', PartsController::class);

    // dates
    Route::resource('dates', DatesController::class);
});
