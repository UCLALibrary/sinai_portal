<?php

use App\Http\Controllers\Cms\ManuscriptsController;
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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/cms', function () {
        return Inertia::render('Dashboard');
    })->name('cms');

    Route::get('/users', [
        UsersController::class, 'index'
    ])->name('users');

    Route::resource('manuscripts', ManuscriptsController::class);
});
