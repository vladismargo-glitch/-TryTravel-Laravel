<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/index.html'))->name('home');

Route::middleware('guest')->group(function () {
    Route::view('/register.html', 'register')->name('register');
    Route::view('/login.html', 'login')->name('login');
    Route::post('/register.html', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('register.submit');
    Route::post('/login.html', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::view('/personal-account.html', 'account')->name('account');
    Route::view('/favorites.html', 'favorites');
    Route::view('/documents.html', 'documents');
    Route::view('/logout.html', 'logout')->name('logout');
    Route::post('/logout.html', [AuthController::class, 'logout'])->name('logout.submit');
});
