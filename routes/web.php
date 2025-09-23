<?php

use Illuminate\Support\Facades\Route;

// Serve React App
Route::get('/', function () {
    return view('app');
});

// Catch-all route for React Router (SPA)
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
