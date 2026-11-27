<?php

use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\AdminPromotionController;
use App\Http\Controllers\AdminReviewController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\AdminTourController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\TourCatalogController;
use App\Http\Controllers\TourReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/index.html'))->name('home');
Route::get('/promotions.html', [PromotionController::class, 'index'])->name('promotions.index');
Route::middleware('auth.session')->group(function () {
    Route::get('/tours.html', [TourCatalogController::class, 'index'])->name('tours.index');
    Route::get('/tours/{tourId}', [TourCatalogController::class, 'show'])->whereNumber('tourId')->name('tours.show');
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
    Route::get('/tours/{tourId}/review', [TourReviewController::class, 'create'])
        ->whereNumber('tourId')->name('reviews.create');
    Route::post('/tours/{tourId}/review', [TourReviewController::class, 'store'])
        ->whereNumber('tourId')->name('reviews.store');
    Route::get('/favorites.html', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{tourId}', [FavoriteController::class, 'store'])
        ->whereNumber('tourId')->name('favorites.store');
    Route::delete('/favorites/{tourId}', [FavoriteController::class, 'destroy'])
        ->whereNumber('tourId')->name('favorites.destroy');
    Route::middleware('cache.headers:private;no_store')->controller(DocumentController::class)->group(function () {
        Route::get('/documents.html', 'index')->name('documents');
        Route::get('/documents/new', 'create')->name('documents.create');
        Route::post('/documents', 'store')->name('documents.store');
        Route::get('/documents/{documentId}/edit', 'edit')->whereNumber('documentId')->name('documents.edit');
        Route::patch('/documents/{documentId}', 'update')->whereNumber('documentId')->name('documents.update');
        Route::get('/documents/{documentId}/delete', 'confirmDelete')
            ->whereNumber('documentId')->name('documents.confirm-delete');
        Route::delete('/documents/{documentId}', 'destroy')->whereNumber('documentId')->name('documents.destroy');
    });
    Route::view('/logout.html', 'logout')->name('logout');
    Route::post('/logout.html', [AuthController::class, 'logout'])->name('logout.submit');

    Route::middleware(['can:manage-bookings', 'cache.headers:private;no_store'])
        ->prefix('admin/bookings')->name('admin.bookings.')->controller(AdminBookingController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{bookingId}', 'show')->whereNumber('bookingId')->name('show');
            Route::patch('/{bookingId}', 'confirm')->whereNumber('bookingId')->name('confirm');
        });

    Route::middleware('can:manage-catalog')->prefix('admin/tours')->name('admin.tours.')
        ->controller(AdminTourController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/new', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{tourId}/edit', 'edit')->whereNumber('tourId')->name('edit');
            Route::patch('/{tourId}', 'update')->whereNumber('tourId')->name('update');
            Route::get('/{tourId}/archive', 'confirmArchive')->whereNumber('tourId')->name('confirm-archive');
            Route::delete('/{tourId}', 'archive')->whereNumber('tourId')->name('archive');
        });

    Route::middleware('can:manage-promotions')->prefix('admin/promotions')->name('admin.promotions.')
        ->controller(AdminPromotionController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/new', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{promotionId}/edit', 'edit')->whereNumber('promotionId')->name('edit');
            Route::patch('/{promotionId}', 'update')->whereNumber('promotionId')->name('update');
            Route::get('/{promotionId}/delete', 'confirmDelete')->whereNumber('promotionId')->name('confirm-delete');
            Route::delete('/{promotionId}', 'destroy')->whereNumber('promotionId')->name('destroy');
        });

    Route::middleware(['can:manage-roles', 'cache.headers:private;no_store'])
        ->prefix('admin/users')->name('admin.users.')->controller(AdminRoleController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{userId}/role', 'edit')->whereNumber('userId')->name('role.edit');
            Route::patch('/{userId}/role', 'update')->whereNumber('userId')
                ->middleware('throttle:10,1,trytravel-role-change')->name('role.update');
        });

    Route::middleware(['can:moderate-reviews', 'cache.headers:private;no_store'])
        ->prefix('admin/reviews')->name('admin.reviews.')->controller(AdminReviewController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::patch('/{reviewId}', 'update')->whereNumber('reviewId')->name('update');
        });
});
