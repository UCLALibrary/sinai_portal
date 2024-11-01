<?php

use App\Http\Controllers\Api\FilesController;
use App\Http\Controllers\Api\FormContextsController;
use App\Http\Controllers\Api\FormsController;
use App\Http\Controllers\Api\ResourcesController;
use App\Http\Controllers\Api\RolesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// form contexts
Route::apiResource('form-contexts', FormContextsController::class, ['as' => 'api'])->only('store', 'update', 'destroy');

// roles
Route::apiResource('roles', RolesController::class, ['as' => 'api'])->only('store', 'update', 'destroy', 'index');

Route::pattern('resourceName', 'manuscripts|layers|contents|works|agents|places|bibliography|languages|references|features|locations|scripts');

// resources
Route::group(['prefix' => '{resourceName}'], function () {
    Route::get('/', [ResourcesController::class, 'index'])->name('api.resources.index');
    Route::post('/', [ResourcesController::class, 'store'])->name('api.resources.store');
    Route::put('/{resourceId}', [ResourcesController::class, 'update'])->name('api.resources.update');
    Route::delete('/{resourceId}', [ResourcesController::class, 'destroy'])->name('api.resources.destroy');
});

// forms
Route::get('forms/codicological-units/{codicological_unit?}', [FormsController::class, 'codUnit'])->name('api.forms.codicological-units');
Route::get('forms/assoc_name', [FormsController::class, 'createAssocName'])->name('api.forms.assoc_name');
Route::get('forms/assoc_place', [FormsController::class, 'createAssocPlace'])->name('api.forms.assoc_place');
Route::get('forms/bib', [FormsController::class, 'createBib'])->name('api.forms.bib');
