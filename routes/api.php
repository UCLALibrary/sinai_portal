<?php

use App\Http\Controllers\Api\ManuscriptsController as ManuscriptsApiController;
use App\Http\Controllers\Api\PartsController as PartsApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('manuscripts', ManuscriptsApiController::class, [
    'as' => 'api'
]);

Route::apiResource('parts', PartsApiController::class, [
    'as' => 'api'
]);

