<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TourCatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/index.html'))->name('home');
Route::middleware('auth.session')->group(function () {
    Route::get('/tours.html', [TourCatalogController::class, 'index'])->name('tours.index');
    Route::get('/hot-tours.html', [TourCatalogController::class, 'index'])
        ->defaults('section', 'hot')->name('tours.hot');
    Route::get('/newyear-tours.html', [TourCatalogController::class, 'index'])
        ->defaults('section', 'newyear')->name('tours.newyear');
});

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
    Route::get('/personal-account.html', [BookingController::class, 'index'])->name('account');
    Route::get('/profile.html', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile.html', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/tours/{tourId}/booking', [BookingController::class, 'create'])
        ->whereNumber('tourId')->name('bookings.create');
    Route::post('/tours/{tourId}/booking', [BookingController::class, 'store'])
        ->whereNumber('tourId')->name('bookings.store');
    Route::get('/favorites.html', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{tourId}', [FavoriteController::class, 'store'])
        ->whereNumber('tourId')->name('favorites.store');
    Route::delete('/favorites/{tourId}', [FavoriteController::class, 'destroy'])
        ->whereNumber('tourId')->name('favorites.destroy');
    Route::view('/documents.html', 'documents')->name('documents');
    Route::view('/logout.html', 'logout')->name('logout');
    Route::post('/logout.html', [AuthController::class, 'logout'])->name('logout.submit');
});
