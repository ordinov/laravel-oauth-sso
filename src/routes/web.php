<?php

use Ordinov\OauthSSO\Controllers\SSOController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {

    if (!Route::has('login')) Route::get('/login', [SSOController::class, 'getLogin'])->name('login');
    if (!Route::has('register')) Route::get('/register', [SSOController::class, 'getLogin'])->name('register');
    if (!Route::has('logout')) Route::get('/logout', [SSOController::class, 'doLogout'])->name('logout');

    Route::get('/logged-out', [SSOController::class, 'loggedOut'])->name('logged-out');
    Route::get('/sso/login', [SSOController::class, 'getLogin'])->name('sso.login');
    Route::get('/sso/callback', [SSOController::class, 'getCallback'])->name('sso.callback');

    // json
    Route::get('/sso/user', [SSOController::class, 'getUserData'])->name('sso.user');

});
