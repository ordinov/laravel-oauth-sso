<?php

use Ordinov\OauthSSO\Controllers\SSOController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {

    Route::get('/login', [SSOController::class, 'getSSOLoginPage'])->name('login');
    Route::get('/register', [SSOController::class, 'getSSORegisterPage'])->name('register');
    Route::get('/logout', [SSOController::class, 'doLogout'])->name('logout');
    Route::get('/logged-out', [SSOController::class, 'loggedOut'])->name('logged-out');
    Route::get('/sso/login', [SSOController::class, 'getLogin'])->name('sso.login');
    Route::get('/sso/callback', [SSOController::class, 'getCallback'])->name('sso.callback');

    // json
    Route::get('/sso/user', [SSOController::class, 'getUserData'])->name('sso.user');

});
