<?php

use App\Http\Controllers\Api\AgentsController;
use App\Http\Controllers\Api\BibliographyController;
use App\Http\Controllers\Api\ContentsController;
use App\Http\Controllers\Api\FeaturesController;
use App\Http\Controllers\Api\FormContextsController;
use App\Http\Controllers\Api\FormsController;
use App\Http\Controllers\Api\LanguagesController;
use App\Http\Controllers\Api\ManuscriptsController;
use App\Http\Controllers\Api\PartsController;
use App\Http\Controllers\Api\PlacesController;
use App\Http\Controllers\Api\ReferencesController;
use App\Http\Controllers\Api\WorksController;
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

Route::apiResource('content-units', ContentsController::class, [
    'as' => 'api'
]);

Route::apiResource('works', WorksController::class, [
    'as' => 'api'
]);

Route::apiResource('agents', AgentsController::class, [
    'as' => 'api'
]);

Route::apiResource('places', PlacesController::class, [
    'as' => 'api'
]);

Route::apiResource('bibliography', BibliographyController::class, [
    'as' => 'api'
]);

Route::apiResource('languages', LanguagesController::class, [
    'as' => 'api'
]);

Route::apiResource('references', ReferencesController::class, [
    'as' => 'api'
]);

Route::apiResource('features', FeaturesController::class, [
    'as' => 'api'
]);

Route::apiResource('form-contexts', FormContextsController::class, [
    'as' => 'api'
]);

// forms
Route::get('forms/codicological-units/{codicological_unit?}', [FormsController::class, 'codUnit'])->name('api.forms.codicological-units');
Route::get('forms/assoc_name', [FormsController::class, 'createAssocName'])->name('api.forms.assoc_name');
Route::get('forms/assoc_place', [FormsController::class, 'createAssocPlace'])->name('api.forms.assoc_place');
Route::get('forms/bib', [FormsController::class, 'createBib'])->name('api.forms.bib');
