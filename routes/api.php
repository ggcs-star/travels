<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PublicController;
use App\Http\Controllers\Api\V1\UserController;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Public
    |--------------------------------------------------------------------------
    */

    Route::get('/settings/public', [PublicController::class, 'publicSettings']);
    Route::get('/categories', [PublicController::class, 'categories']);
    Route::get('/destinations', [PublicController::class, 'destinations']);
    Route::get('/tours', [PublicController::class, 'tours']);
    Route::get('/tours/{tour:slug}', [PublicController::class, 'tour']);
    Route::get('/departures/{departure}', [PublicController::class, 'departure']);
    Route::get('/blogs', [PublicController::class, 'blogs']);
    Route::get('/blogs/{slug}', [PublicController::class, 'blog']);
    Route::get('/pages/{slug}', [PublicController::class, 'page'])
        ->where('slug', '.*');

    Route::post('/contact', [ContactController::class, 'store'])
        ->middleware('throttle:10,1');

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])
            ->middleware('throttle:10,1');

        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:10,1');

        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
            ->middleware('throttle:5,1');
    });

    /*
    |--------------------------------------------------------------------------
    | Authenticated customer API
    |--------------------------------------------------------------------------
    */

    Route::middleware('api.token')->group(function () {

        Route::prefix('auth')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/logout-all', [AuthController::class, 'logoutAll']);
            Route::post('/resend-verification', [AuthController::class, 'resendVerification'])
                ->middleware('throttle:6,1');
        });

        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::put('/profile/password', [UserController::class, 'changePassword']);
        Route::get('/points', [UserController::class, 'points']);

        Route::get('/bookings', [BookingController::class, 'index']);
        Route::get('/bookings/{booking}', [BookingController::class, 'show']);
        Route::post('/tours/{tour:slug}/bookings', [BookingController::class, 'store']);
        Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);

        Route::post('/bookings/{booking}/payment/order', [PaymentController::class, 'createOrder']);
        Route::post('/bookings/{booking}/payment/verify', [PaymentController::class, 'verify']);
        Route::post('/bookings/{booking}/payment/points/apply', [PaymentController::class, 'applyPoints']);
        Route::delete('/bookings/{booking}/payment/points', [PaymentController::class, 'removePoints']);
    });
});
