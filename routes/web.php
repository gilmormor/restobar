<?php

use Illuminate\Support\Facades\Route;

// Todas las rutas las maneja Vue Router (SPA)
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
