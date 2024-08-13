<?php

use App\Http\Controllers\Cms\AgentsController;
use App\Http\Controllers\Cms\BibliographyController;
use App\Http\Controllers\Cms\ContentsController;
use App\Http\Controllers\Cms\ManuscriptsController;
use App\Http\Controllers\Cms\PartsController;
use App\Http\Controllers\Cms\PlacesController;
use App\Http\Controllers\Cms\UsersController;
use App\Http\Controllers\Cms\WorksController;
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

    Route::get('/users', [UsersController::class, 'index'])->middleware('can:read user')->name('users.index');
    Route::post('/users', [UsersController::class, 'store'])->middleware('can:create user')->name('users.store');
    Route::get('/users/create', [UsersController::class, 'create'])->middleware('can:create user')->name('users.create');
    Route::get('/users/{user}/edit', [UsersController::class, 'edit'])->middleware('can:edit user')->name('users.edit');
    Route::match(['put', 'patch'], '/users/{user}', [UsersController::class, 'update'])->middleware('can:edit user')->name('users.update');

    // users
    //Route::resource('users', UsersController::class);

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
});
