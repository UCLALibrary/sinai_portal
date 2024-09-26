<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FormContextResource;
use App\Models\FormContext;


class FormContextsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return FormContextResource::collection(FormContext::all());
    }
}
