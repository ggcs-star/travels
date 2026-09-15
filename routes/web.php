<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| USER SIDE CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| ADMIN SIDE CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\TourDepartureController;
use App\Http\Controllers\Admin\TourPackageController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\TourCategoryController;
use App\Http\Controllers\Admin\PointSettingController as AdminPointSettingController;
use App\Http\Controllers\Admin\AdminPointWalletController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ContactInquiryController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\PreferenceController;

/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|                           USER SIDE ROUTES
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
| All customer / public website routes live in this section.
|
*/


/*
|--------------------------------------------------------------------------
| USER PROFILE
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::get('/profile/points', [ProfileController::class, 'points'])
        ->name('profile.points');

});


/*
|--------------------------------------------------------------------------
| PUBLIC WEBSITE
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

Route::post('/contact', [ContactController::class, 'store'])
    ->name('contact.store');

Route::get('/tours/{tour:slug}/itinerary/pdf', [TourController::class, 'itineraryPdf'])
    ->name('tours.itinerary.pdf');
/*
|--------------------------------------------------------------------------
| GUEST AUTHENTICATION
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

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
        ->name('password.reset');

    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->name('password.store');
});


/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [BookingController::class, 'index'])
        ->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | My Bookings
    |--------------------------------------------------------------------------
    */

    Route::get('/my-bookings', [BookingController::class, 'index'])
        ->name('bookings.index');


    /*
    |--------------------------------------------------------------------------
    | Booking
    |--------------------------------------------------------------------------
    */

    Route::get('/tours/{tour:slug}/book', [BookingController::class, 'create'])
        ->name('bookings.create');

    Route::post('/tours/{tour:slug}/book', [BookingController::class, 'store'])
        ->name('bookings.store');

    Route::get('/bookings/{booking}', [BookingController::class, 'show'])
        ->name('bookings.show');

    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])
        ->name('bookings.cancel');


    /*
    |--------------------------------------------------------------------------
    | Payments
    |--------------------------------------------------------------------------
    */

    Route::get('/bookings/{booking}/payment', [PaymentController::class, 'checkout'])
        ->name('payments.checkout');

    Route::post('/bookings/{booking}/payment/verify', [PaymentController::class, 'verify'])
        ->name('payments.verify');


    /*
    |--------------------------------------------------------------------------
    | Booking Points Redemption
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/bookings/{booking}/payment/points/apply',
        [PaymentController::class, 'applyPoints']
    )->name('payments.points.apply');

    Route::delete(
        '/bookings/{booking}/payment/points',
        [PaymentController::class, 'removePoints']
    )->name('payments.points.remove');
/*
|--------------------------------------------------------------------------
| SITEMAP
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', function () {

    $path = public_path('sitemap.xml');

    if (!file_exists($path)) {
        app(\App\Http\Controllers\Admin\SeoController::class)
            ->cronUpdateSitemap();
    }

    return response()->file(
        public_path('sitemap.xml'),
        [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]
    );

})->name('sitemap');


/*
|--------------------------------------------------------------------------
| SITEMAP CRON
|--------------------------------------------------------------------------
*/

Route::get(
    '/cron/update-sitemap',
    [SeoController::class, 'cronUpdateSitemap']
)->name('cron.update-sitemap');

    /*
    |--------------------------------------------------------------------------
    | Email Verification
    |--------------------------------------------------------------------------
    */

    Route::get('/email/verify', [AuthController::class, 'verificationNotice'])
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:6,1')
        ->name('verification.send');


    /*
    |--------------------------------------------------------------------------
    | Change Password
    |--------------------------------------------------------------------------
    */

    Route::get('/change-password', [AuthController::class, 'showChangePassword'])
        ->name('password.change');

    Route::post('/change-password', [AuthController::class, 'changePassword'])
        ->name('password.change.update');

    // Backward-compatible route name used by the existing login UI.
    Route::post('/change-password', [AuthController::class, 'changePassword'])
        ->name('password.update');


    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});


/*
|--------------------------------------------------------------------------
| PAYMENT WEBHOOK
|--------------------------------------------------------------------------
|
| Razorpay webhook is intentionally outside the authenticated user group.
|
*/

Route::post('/payments/razorpay/webhook', [PaymentController::class, 'webhook'])
    ->withoutMiddleware([
        \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ])
    ->name('payments.razorpay.webhook');


/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|                           ADMIN SIDE ROUTES
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
| All admin panel routes live inside this group.
|
| Middleware:
|   auth  -> user must be logged in
|   admin -> user must have admin access
|
| Prefix:
|   /admin
|
| Route name prefix:
|   admin.
|
*/


Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {


        /*
        |--------------------------------------------------------------------------
        | ADMIN DASHBOARD
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | ADMIN PROFILE
        |--------------------------------------------------------------------------
        |
        | Admin profile is handled by AdminProfileController.
        |
        */

Route::get('/profile', [
    AdminProfileController::class,
    'index',
])->name('profile');

Route::put('/profile', [
    AdminProfileController::class,
    'update',
])->name('profile.update');

        Route::get('/inquiries', [
            ContactInquiryController::class,
            'index'
        ])->name('inquiries.index');

        Route::get('/inquiries/{inquiry}', [
            ContactInquiryController::class,
            'show'
        ])->name('inquiries.show');

        Route::patch('/inquiries/{inquiry}/status', [
            ContactInquiryController::class,
            'updateStatus'
        ])->name('inquiries.status');

        Route::delete('/inquiries/{inquiry}', [
            ContactInquiryController::class,
            'destroy'
        ])->name('inquiries.destroy');
    
/*
|--------------------------------------------------------------------------
| SEO TOOLS
|--------------------------------------------------------------------------
*/

Route::get(
    '/settings/seo',
    [SeoController::class, 'index']
)->name('settings.seo');

Route::put(
    '/settings/seo',
    [SeoController::class, 'update']
)->name('settings.seo.update');

Route::post(
    '/settings/seo/generate-sitemap',
    [SeoController::class, 'generateSitemap']
)->name('settings.seo.generate-sitemap');

/*
|--------------------------------------------------------------------------
| PREFERENCES
|--------------------------------------------------------------------------
*/

Route::get(
    '/settings/preferences',
    [PreferenceController::class, 'index']
)->name('settings.preferences');

Route::put(
    '/settings/preferences/uploads',
    [PreferenceController::class, 'updateUploads']
)->name('settings.preferences.uploads.update');

Route::put(
    '/settings/preferences/payments',
    [PreferenceController::class, 'updatePayments']
)->name('settings.preferences.payments.update');

Route::post(
    '/settings/preferences/payments/test',
    [PreferenceController::class, 'testPayments']
)->name('settings.preferences.payments.test');

Route::post(
    '/settings/preferences/reset',
    [PreferenceController::class, 'reset']
)->name('settings.preferences.reset');

        /*
        |--------------------------------------------------------------------------
        {{ route('admin.profile.password') }}        |--------------------------------------------------------------------------
        */

Route::get('/profile', [
    AdminProfileController::class,
    'index',
])->name('profile');

Route::put('/profile', [
    AdminProfileController::class,
    'update',
])->name('profile.update');

Route::get('/profile/password', [
    AdminProfileController::class,
    'password',
])->name('profile.password');

Route::put('/profile/password', [
    AdminProfileController::class,
    'updatePassword',
])->name('profile.password.update');





/*
|--------------------------------------------------------------------------
| Website Settings
|--------------------------------------------------------------------------
*/

Route::get('/settings', [SettingsController::class, 'index'])
    ->name('settings.index');

Route::put('/settings', [SettingsController::class, 'update'])
    ->name('settings.update');


// =====================================================
// SETTINGS - GENERAL
// =====================================================

Route::get('/settings/general', [SettingsController::class, 'general'])
    ->name('settings.general');

Route::put('/settings/general', [SettingsController::class, 'updateGeneral'])
    ->name('settings.general.update');


// =====================================================
// SETTINGS - VISUAL
// =====================================================

Route::get('/settings/visual', [SettingsController::class, 'visual'])
    ->name('settings.visual');

Route::put('/settings/visual', [SettingsController::class, 'updateVisual'])
    ->name('settings.visual.update');


// =====================================================
// SETTINGS - FONTS
// =====================================================

Route::get('/settings/fonts', [SettingsController::class, 'fonts'])
    ->name('settings.fonts');

Route::put('/settings/fonts', [SettingsController::class, 'updateFonts'])
    ->name('settings.fonts.update');


// =====================================================
// SETTINGS - HOME PAGE
// =====================================================

Route::get('/settings/home', [SettingsController::class, 'home'])
    ->name('settings.home');

Route::put('/settings/home', [SettingsController::class, 'updateHome'])
    ->name('settings.home.update');


Route::resource('pages', \App\Http\Controllers\Admin\PageController::class)
    ->except(['show']);

Route::patch(
    '/pages/{page}/status',
    [\App\Http\Controllers\Admin\PageController::class, 'status']
)
    ->name('pages.status');
        /*
        |--------------------------------------------------------------------------
        | POINT WALLETS
        |--------------------------------------------------------------------------
        */

        Route::prefix('point-wallets')
            ->name('point-wallets.')
            ->group(function () {

                Route::get('/', [
                    AdminPointWalletController::class,
                    'index',
                ])->name('index');

                Route::get('/{user}', [
                    AdminPointWalletController::class,
                    'show',
                ])->name('show');

                Route::post('/{user}/adjust', [
                    AdminPointWalletController::class,
                    'adjust',
                ])->name('adjust');
            });


        /*
        |--------------------------------------------------------------------------
        | POINT SETTINGS
        |--------------------------------------------------------------------------
        */

        Route::get('/point-settings', [
            AdminPointSettingController::class,
            'index',
        ])->name('point-settings.index');

        Route::put('/point-settings', [
            AdminPointSettingController::class,
            'update',
        ])->name('point-settings.update');


        /*
        |--------------------------------------------------------------------------
        | TOUR CATEGORIES
        |--------------------------------------------------------------------------
        */

        Route::prefix('tour-categories')
            ->name('tour-categories.')
            ->group(function () {

                /*
                |--------------------------------------------------------------------------
                | List
                |--------------------------------------------------------------------------
                */

                Route::get('/', [
                    TourCategoryController::class,
                    'index',
                ])->name('index');


                /*
                |--------------------------------------------------------------------------
                | Create
                |--------------------------------------------------------------------------
                */

                Route::get('/create', [
                    TourCategoryController::class,
                    'create',
                ])->name('create');


                /*
                |--------------------------------------------------------------------------
                | Store
                |--------------------------------------------------------------------------
                */

                Route::post('/', [
                    TourCategoryController::class,
                    'store',
                ])->name('store');


                /*
                |--------------------------------------------------------------------------
                | Show
                |--------------------------------------------------------------------------
                */

                Route::get('/{category}', [
                    TourCategoryController::class,
                    'show',
                ])->name('show');


                /*
                |--------------------------------------------------------------------------
                | Edit
                |--------------------------------------------------------------------------
                */

                Route::get('/{category}/edit', [
                    TourCategoryController::class,
                    'edit',
                ])->name('edit');


                /*
                |--------------------------------------------------------------------------
                | Update
                |--------------------------------------------------------------------------
                */

                Route::put('/{category}', [
                    TourCategoryController::class,
                    'update',
                ])->name('update');


                /*
                |--------------------------------------------------------------------------
                | Duplicate
                |--------------------------------------------------------------------------
                */

                Route::post('/{category}/duplicate', [
                    TourCategoryController::class,
                    'duplicate',
                ])->name('duplicate');


                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                Route::patch('/{category}/status', [
                    TourCategoryController::class,
                    'status',
                ])->name('status');


                /*
                |--------------------------------------------------------------------------
                | Delete
                |--------------------------------------------------------------------------
                */

                Route::delete('/{category}', [
                    TourCategoryController::class,
                    'destroy',
                ])->name('destroy');
            });


        /*
        |--------------------------------------------------------------------------
        | TOUR PACKAGES
        |--------------------------------------------------------------------------
        */

        Route::prefix('tours')
            ->name('tours.')
            ->group(function () {

                /*
                |--------------------------------------------------------------------------
                | Tour List
                |--------------------------------------------------------------------------
                */

                Route::get('/', [
                    TourPackageController::class,
                    'index',
                ])->name('index');


                /*
                |--------------------------------------------------------------------------
                | Create Tour
                |--------------------------------------------------------------------------
                */

                Route::get('/create', [
                    TourPackageController::class,
                    'create',
                ])->name('create');


                /*
                |--------------------------------------------------------------------------
                | Store Tour
                |--------------------------------------------------------------------------
                */

                Route::post('/', [
                    TourPackageController::class,
                    'store',
                ])->name('store');


                /*
                |--------------------------------------------------------------------------
                | Show Tour
                |--------------------------------------------------------------------------
                */

                Route::get('/{tour}', [
                    TourPackageController::class,
                    'show',
                ])->name('show');


                /*
                |--------------------------------------------------------------------------
                | Edit Tour
                |--------------------------------------------------------------------------
                */

                Route::get('/{tour}/edit', [
                    TourPackageController::class,
                    'edit',
                ])->name('edit');


                /*
                |--------------------------------------------------------------------------
                | Update Tour
                |--------------------------------------------------------------------------
                */

                Route::put('/{tour}', [
                    TourPackageController::class,
                    'update',
                ])->name('update');


                /*
                |--------------------------------------------------------------------------
                | Duplicate Tour
                |--------------------------------------------------------------------------
                */

                Route::post('/{tour}/duplicate', [
                    TourPackageController::class,
                    'duplicate',
                ])->name('duplicate');


                /*
                |--------------------------------------------------------------------------
                | Tour Status
                |--------------------------------------------------------------------------
                */

                Route::patch('/{tour}/status', [
                    TourPackageController::class,
                    'status',
                ])->name('status');


                /*
                |--------------------------------------------------------------------------
                | Delete Tour
                |--------------------------------------------------------------------------
                */

                Route::delete('/{tour}', [
                    TourPackageController::class,
                    'destroy',
                ])->name('destroy');


                /*
                |--------------------------------------------------------------------------
                | TOUR DEPARTURES
                |--------------------------------------------------------------------------
                */

                Route::get('/{tour}/departures', [
                    TourDepartureController::class,
                    'index',
                ])->name('departures.index');

                Route::get('/{tour}/departures/create', [
                    TourDepartureController::class,
                    'create',
                ])->name('departures.create');

                Route::post('/{tour}/departures', [
                    TourDepartureController::class,
                    'store',
                ])->name('departures.store');

                Route::get('/{tour}/departures/{departure}/edit', [
                    TourDepartureController::class,
                    'edit',
                ])->name('departures.edit');

                Route::put('/{tour}/departures/{departure}', [
                    TourDepartureController::class,
                    'update',
                ])->name('departures.update');

                Route::delete('/{tour}/departures/{departure}', [
                    TourDepartureController::class,
                    'destroy',
                ])->name('departures.destroy');
            });


        /*
        |--------------------------------------------------------------------------
        | ADMIN BOOKINGS
        |--------------------------------------------------------------------------
        */

        Route::get('/bookings', [
            AdminBookingController::class,
            'index',
        ])->name('bookings.index');


        /*
        |--------------------------------------------------------------------------
        | TRAVELLER ID PROOF
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/bookings/{booking}/travellers/{traveller}/id-proof',
            [AdminBookingController::class, 'idProof']
        )->name('bookings.travellers.id-proof');


        /*
        |--------------------------------------------------------------------------
        | BOOKING DETAILS
        |--------------------------------------------------------------------------
        */

        Route::get('/bookings/{booking}', [
            AdminBookingController::class,
            'show',
        ])->name('bookings.show');


        /*
        |--------------------------------------------------------------------------
        | BLOG CATEGORIES
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | ADMIN BLOG
        |--------------------------------------------------------------------------
        */

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
| Website Settings
|--------------------------------------------------------------------------
*/

Route::get('/settings', [SettingsController::class, 'index'])
    ->name('settings.index');

Route::put('/settings', [SettingsController::class, 'update'])
    ->name('settings.update');

    });

/*
|--------------------------------------------------------------------------
| PUBLIC CMS PAGES
|--------------------------------------------------------------------------
|
| Must stay OUTSIDE the admin group and at the very bottom.
| It handles published Page CMS slugs such as /how-to-work.
|
*/

Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '(?!api(?:/|$)).*')
    ->name('pages.public');

