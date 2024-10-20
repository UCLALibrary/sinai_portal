<?php

use App\Http\Controllers\Api\AgentsController;
use App\Http\Controllers\Api\BibliographyController;
use App\Http\Controllers\Api\ContentsController;
use App\Http\Controllers\Api\FeaturesController;
use App\Http\Controllers\Api\FilesController;
use App\Http\Controllers\Api\FormContextsController;
use App\Http\Controllers\Api\FormsController;
use App\Http\Controllers\Api\LanguagesController;
use App\Http\Controllers\Api\LocationsController;
use App\Http\Controllers\Api\ManuscriptsController;
use App\Http\Controllers\Api\PartsController;
use App\Http\Controllers\Api\PlacesController;
use App\Http\Controllers\Api\ReferencesController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\WorksController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// manuscripts
Route::apiResource('manuscripts', ManuscriptsController::class, ['as' => 'api'])->only('store', 'update', 'destroy');

// parts
Route::apiResource('codicological-units', PartsController::class, ['as' => 'api'])->only('store', 'update', 'destroy');

// text units
Route::apiResource('content-units', ContentsController::class, ['as' => 'api'])->only('store', 'update', 'destroy');

// works
Route::apiResource('works', WorksController::class, ['as' => 'api'])->only('store', 'update', 'destroy');

// agents
Route::apiResource('agents', AgentsController::class, ['as' => 'api'])->only('store', 'update', 'destroy');

// places
Route::apiResource('places', PlacesController::class, ['as' => 'api'])->only('store', 'update', 'destroy');

// bibliography
Route::apiResource('bibliography', BibliographyController::class, ['as' => 'api'])->only('store', 'update', 'destroy');

// languages
Route::apiResource('languages', LanguagesController::class, ['as' => 'api'])->only('store', 'update', 'destroy');

// references
Route::apiResource('references', ReferencesController::class, ['as' => 'api'])->only('store', 'update', 'destroy');

// features
Route::apiResource('features', FeaturesController::class, ['as' => 'api'])->only('store', 'update', 'destroy');

// locations
Route::apiResource('locations', LocationsController::class, ['as' => 'api'])->only('store', 'update', 'destroy');

// form contexts
Route::apiResource('form-contexts', FormContextsController::class, ['as' => 'api'])->only('store', 'update', 'destroy');

// roles
Route::apiResource('roles', RolesController::class, ['as' => 'api'])->only('store', 'update', 'destroy');

// files
Route::pattern('resourceType', 'manuscript|codicologicalUnit|contentUnit|work|agent|place|bibliography');
Route::group(['prefix' => 'files/{resourceType}'], function () {
    // upload
    Route::post('upload', [FilesController::class, 'storeOnUpload'])->name('api.files.upload.store');
    Route::post('upload/batch', [FilesController::class, 'batchUpload'])->name('api.files.upload.batch');
    Route::post('upload/{resourceId}', [FilesController::class, 'updateOnUpload'])->name('api.files.upload.update');
});

// forms
Route::get('forms/codicological-units/{codicological_unit?}', [FormsController::class, 'codUnit'])->name('api.forms.codicological-units');
Route::get('forms/assoc_name', [FormsController::class, 'createAssocName'])->name('api.forms.assoc_name');
Route::get('forms/assoc_place', [FormsController::class, 'createAssocPlace'])->name('api.forms.assoc_place');
Route::get('forms/bib', [FormsController::class, 'createBib'])->name('api.forms.bib');
