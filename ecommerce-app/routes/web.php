<?php

use App\Http\Controllers\SSOController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [SSOController::class, 'redirectToSSO'])->name('login');

Route::get('/callback', [SSOController::class, 'callback']);

Route::get('/dashboard', function () {
    return "Dashboard - Logged In";
})->middleware('auth');
