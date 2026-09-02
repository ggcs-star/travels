<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\TourDepartureController;
use App\Http\Controllers\Admin\TourPackageController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\TourCategoryController;

/*
|--------------------------------------------------------------------------
| Public Website
|--------------------------------------------------------------------------
*/

Route::get('/', [TourController::class, 'home'])
    ->name('home');

Route::view('/about', 'pages.about')
    ->name('about');

Route::get('/tours', [TourController::class, 'index'])
    ->name('tours.index');

Route::get('/tours/{tour:slug}', [TourController::class, 'show'])
    ->name('tours.show');

Route::get('/destinations', [TourController::class, 'index'])
    ->name('destinations.index');

Route::get('/blog', [BlogController::class, 'index'])
    ->name('blog.index');

Route::get('/blog/{slug}', [BlogController::class, 'show'])
    ->name('blog.show');

Route::view('/contact', 'pages.contact')
    ->name('contact');
Route::view('/about', 'pages.about')->name('about');



    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile');


    /*
    |--------------------------------------------------------------------------
    | Edit Profile
    |--------------------------------------------------------------------------
    |
    | Abhi static UI hai, isliye edit page baad mein connect kar sakte hain.
    |
    */

    Route::get('/profile/edit', function () {
        return view('profile.edit');
    })->name('profile.edit');

/*
|--------------------------------------------------------------------------
| Payment Webhook
|--------------------------------------------------------------------------
*/

Route::post('/payments/razorpay/webhook', [PaymentController::class, 'webhook'])
    ->withoutMiddleware([
        \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ])
    ->name('payments.razorpay.webhook');

/*
|--------------------------------------------------------------------------
| Guest Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.submit');

    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
        ->name('password.email');
});

/*
|--------------------------------------------------------------------------
| Authenticated Users
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [BookingController::class, 'index'])
        ->name('dashboard');

    Route::get('/my-bookings', [BookingController::class, 'index'])
        ->name('bookings.index');

    Route::get('/tours/{tour:slug}/book', [BookingController::class, 'create'])
        ->name('bookings.create');

    Route::post('/tours/{tour:slug}/book', [BookingController::class, 'store'])
        ->name('bookings.store');

    Route::get('/bookings/{booking}', [BookingController::class, 'show'])
        ->name('bookings.show');

    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])
        ->name('bookings.cancel');

    Route::get('/bookings/{booking}/payment', [PaymentController::class, 'checkout'])
        ->name('payments.checkout');

    Route::post('/bookings/{booking}/payment/verify', [PaymentController::class, 'verify'])
        ->name('payments.verify');

    Route::get('/email/verify', [AuthController::class, 'verificationNotice'])
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/change-password', [AuthController::class, 'showChangePassword'])
        ->name('password.change');

    Route::post('/change-password', [AuthController::class, 'changePassword'])
        ->name('password.change.update');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Panel
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');
Route::resource(
    'tour-categories',
    TourCategoryController::class
)->except(['show']);
        /*
        |--------------------------------------------------------------------------
        | Tour Packages
        |--------------------------------------------------------------------------
        */

        Route::prefix('tours')
            ->name('tours.')
            ->group(function () {
                Route::get('/', [TourPackageController::class, 'index'])
                    ->name('index');

                Route::get('/create', [TourPackageController::class, 'create'])
                    ->name('create');

                Route::post('/', [TourPackageController::class, 'store'])
                    ->name('store');

                Route::get('/{tour}', [TourPackageController::class, 'show'])
                    ->name('show');

                Route::get('/{tour}/edit', [TourPackageController::class, 'edit'])
                    ->name('edit');

                Route::put('/{tour}', [TourPackageController::class, 'update'])
                    ->name('update');

                Route::post('/{tour}/duplicate', [TourPackageController::class, 'duplicate'])
                    ->name('duplicate');

                Route::patch('/{tour}/status', [TourPackageController::class, 'status'])
                    ->name('status');

                Route::delete('/{tour}', [TourPackageController::class, 'destroy'])
                    ->name('destroy');

                /*
                |--------------------------------------------------------------------------
                | Tour Departures
                |--------------------------------------------------------------------------
                */

                Route::get('/{tour}/departures', [TourDepartureController::class, 'index'])
                    ->name('departures.index');

                Route::get('/{tour}/departures/create', [TourDepartureController::class, 'create'])
                    ->name('departures.create');

                Route::post('/{tour}/departures', [TourDepartureController::class, 'store'])
                    ->name('departures.store');

                Route::get('/{tour}/departures/{departure}/edit', [TourDepartureController::class, 'edit'])
                    ->name('departures.edit');

                Route::put('/{tour}/departures/{departure}', [TourDepartureController::class, 'update'])
                    ->name('departures.update');

                Route::delete('/{tour}/departures/{departure}', [TourDepartureController::class, 'destroy'])
                    ->name('departures.destroy');
            });

     /*
|--------------------------------------------------------------------------
| Bookings
|--------------------------------------------------------------------------
*/

Route::get('/bookings', [AdminBookingController::class, 'index'])
    ->name('bookings.index');

/*
|--------------------------------------------------------------------------
| Traveller ID Proof
|--------------------------------------------------------------------------
*/

Route::get(
    '/bookings/{booking}/travellers/{traveller}/id-proof',
    [AdminBookingController::class, 'idProof']
)->name('bookings.travellers.id-proof');

/*
|--------------------------------------------------------------------------
| Booking Details
|--------------------------------------------------------------------------
*/

Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])
    ->name('bookings.show');
Route::get(
    'blog-categories',
    [BlogCategoryController::class, 'index']
)->name('blog-categories.index');

Route::get(
    'blog-categories/create',
    [BlogCategoryController::class, 'create']
)->name('blog-categories.create');

Route::post(
    'blog-categories',
    [BlogCategoryController::class, 'store']
)->name('blog-categories.store');

Route::get(
    'blog-categories/{blog_category}/edit',
    [BlogCategoryController::class, 'edit']
)->name('blog-categories.edit');

Route::put(
    'blog-categories/{blog_category}',
    [BlogCategoryController::class, 'update']
)->name('blog-categories.update');

Route::delete(
    'blog-categories/{blog_category}',
    [BlogCategoryController::class, 'destroy']
)->name('blog-categories.destroy');

Route::patch(
    'blog-categories/{blog_category}/status',
    [BlogCategoryController::class, 'status']
)->name('blog-categories.status');




Route::resource('blog', AdminBlogController::class);

Route::patch(
    'blog/{blog}/status',
    [AdminBlogController::class, 'status']
)->name('blog.status');

Route::patch(
    'blog/{blog}/featured',
    [AdminBlogController::class, 'featured']
)->name('blog.featured');

Route::post(
    'blog/{blog}/duplicate',
    [AdminBlogController::class, 'duplicate']
)->name('blog.duplicate');
        /*
        |--------------------------------------------------------------------------
        | Admin Profile
        |--------------------------------------------------------------------------
        */

        Route::get('/profile', [AdminController::class, 'profile'])
            ->name('profile');

        Route::put('/profile', [AdminController::class, 'updateProfile'])
            ->name('profile.update');

        /*
        |--------------------------------------------------------------------------
        | Admin Change Password
        |--------------------------------------------------------------------------
        */

        Route::get('/change-password', [AdminController::class, 'showChangePassword'])
            ->name('password.change');

        Route::put('/change-password', [AdminController::class, 'changePassword'])
            ->name('password.change.update');
    });