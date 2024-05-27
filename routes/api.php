<?php

use App\Http\Controllers\Api\DatesController as DatesApiController;
use App\Http\Controllers\Api\ManuscriptsController as ManuscriptsApiController;
use App\Http\Controllers\Api\PartsController as PartsApiController;
use App\Http\Controllers\Api\PlacesController as PlacesApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('manuscripts', ManuscriptsApiController::class, [
    'as' => 'api'
]);

Route::apiResource('codicological-units', PartsApiController::class, [
    'as' => 'api'
]);

Route::apiResource('places', PlacesApiController::class, [
    'as' => 'api'
]);

Route::apiResource('dates', DatesApiController::class, [
    'as' => 'api'
]);
