<?php

use App\Http\Controllers\Api\ManuscriptsController as ManuscriptsApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('manuscripts', ManuscriptsApiController::class, [
    'as' => 'api'
]);
