<?php

use App\Http\Controllers\SSOController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [SSOController::class, 'redirectToSSO'])->name('login');

Route::get('/callback', [SSOController::class, 'callback']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::get('/logout', function () {
    // Get the access token from session
    $token = session('access_token', 'none');

    // Revoke token on SSO server if exists
    if ($token && $token !== 'none') {
        try {
            Http::withToken($token)
                ->post(config('app.sso_server') . '/api/logout');
        } catch (\Exception $e) {
            // Ignore errors from SSO server
        }
    }

    // Clear all session data
    Auth::logout();
    session()->flush();

    // Redirect to SSO server logout to clear SSO session, then return to client home
    return redirect(config('app.sso_server') . '/logout?return_url=' . urlencode(config('app.url') . '/'));
})->name('logout');
