<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/menu', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
