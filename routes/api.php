<?php

use App\Http\Controllers\Api\DatesController;
use App\Http\Controllers\Api\FormsController;
use App\Http\Controllers\Api\ManuscriptsController;
use App\Http\Controllers\Api\PartsController;
use App\Http\Controllers\Api\PersonsController;
use App\Http\Controllers\Api\PlacesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('manuscripts', ManuscriptsController::class, [
    'as' => 'api'
]);

Route::apiResource('codicological-units', PartsController::class, [
    'as' => 'api'
]);

Route::apiResource('persons', PersonsController::class, [
    'as' => 'api'
]);

Route::apiResource('places', PlacesController::class, [
    'as' => 'api'
]);

Route::apiResource('dates', DatesController::class, [
    'as' => 'api'
]);

// forms
Route::get('forms/assoc_name', [FormsController::class, 'createAssocName'])->name('api.forms.assoc_name');
Route::get('forms/assoc_place', [FormsController::class, 'createAssocPlace'])->name('api.forms.assoc_place');
Route::get('forms/assoc_date', [FormsController::class, 'createAssocDate'])->name('api.forms.assoc_date');
Route::get('forms/cod_unit', [FormsController::class, 'createCodUnit'])->name('api.forms.cod_unit');
