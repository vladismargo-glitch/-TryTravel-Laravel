<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\TourCatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/index.html'))->name('home');
Route::get('/tours.html', [TourCatalogController::class, 'index'])->name('tours.index');
Route::get('/hot-tours.html', [TourCatalogController::class, 'index'])
    ->defaults('section', 'hot')->name('tours.hot');
Route::get('/newyear-tours.html', [TourCatalogController::class, 'index'])
    ->defaults('section', 'newyear')->name('tours.newyear');

Route::middleware('guest')->group(function () {
    Route::view('/register.html', 'register')->name('register');
    Route::view('/login.html', 'login')->name('login');
    Route::post('/register.html', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('register.submit');
    Route::post('/login.html', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.submit');

    Route::view('/forgot-password.html', 'forgot-password')->name('password.request');
    Route::post('/forgot-password.html', [PasswordResetController::class, 'send'])
        ->middleware('throttle:5,1,trytravel-reset-request')->name('password.email');
    Route::get('/reset-password/{resetId}/{token}', [PasswordResetController::class, 'open'])
        ->whereNumber('resetId')->where('token', '[a-f0-9]{64}')->name('password.open');
    Route::get('/reset-password/{resetId}', [PasswordResetController::class, 'show'])
        ->whereNumber('resetId')->name('password.reset');
    Route::post('/reset-password/{resetId}', [PasswordResetController::class, 'update'])
        ->whereNumber('resetId')->middleware('throttle:10,1,trytravel-reset-save')->name('password.update');
});

Route::middleware(['auth', 'auth.session'])->group(function () {
    Route::view('/personal-account.html', 'account')->name('account');
    Route::view('/favorites.html', 'favorites');
    Route::view('/documents.html', 'documents');
    Route::view('/logout.html', 'logout')->name('logout');
    Route::post('/logout.html', [AuthController::class, 'logout'])->name('logout.submit');
});
