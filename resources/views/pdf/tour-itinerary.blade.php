<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        {{ $tour->name }} - Itinerary
    </title>

    <style>

        @page {
            margin: 16mm 14mm 19mm 14mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #17283a;
            font-size: 10px;
            line-height: 1.5;
            background: #ffffff;
        }


        /* =========================================================
           HEADER
        ========================================================= */

        .page-header {
            width: 100%;
            border-bottom: 2px solid #f28a18;
            padding-bottom: 9px;
            margin-bottom: 14px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 45%;
            vertical-align: middle;
        }

        .contact-cell {
            width: 55%;
            vertical-align: middle;
            text-align: right;
            color: #5d6b78;
            font-size: 8px;
            line-height: 1.55;
        }

        .contact-phone {
            color: #173651;
            font-size: 8.5px;
            font-weight: 700;
        }

        .contact-email {
            color: #5d6b78;
        }

        .contact-address {
            color: #697785;
        }

        .logo {
            max-width: 145px;
            max-height: 58px;
        }

        .brand-name {
            color: #102f4b;
            font-size: 18px;
            font-weight: 800;
        }


        /* =========================================================
           COVER / HERO
        ========================================================= */

        .hero {
            position: relative;
            width: 100%;
            height: 205px;
            margin-bottom: 14px;
            overflow: hidden;
            border-radius: 12px;
            background: #102f4b;
        }

        .hero-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 205px;
            object-fit: cover;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 205px;
            background: rgba(7, 29, 47, 0.62);
        }

        .hero-content {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 205px;
            text-align: center;
            padding: 38px 40px 28px;
        }

        .hero-label {
            display: inline-block;
            padding: 5px 13px;
            margin-bottom: 15px;
            border-radius: 30px;
            background: #f28a18;
            color: #ffffff;
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .hero-title {
            margin: 0 auto;
            max-width: 600px;
            color: #ffffff;
            font-size: 22px;
            line-height: 1.25;
            font-weight: 800;
        }

        .hero-line {
            width: 55px;
            height: 4px;
            margin: 17px auto 14px;
            background: #f28a18;
        }

        .hero-description {
            max-width: 520px;
            margin: 0 auto;
            color: #f3f6f8;
            font-size: 9.5px;
            line-height: 1.6;
        }

        .hero-meta {
            margin-top: 17px;
            color: #ffffff;
            font-size: 9px;
        }

        .hero-meta strong {
            color: #ffad50;
        }


        /* =========================================================
           SECTION TITLE
        ========================================================= */

        .section-title {
            margin: 5px 0 13px;
            color: #102f4b;
            font-size: 17px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .section-title span {
            color: #f28a18;
        }


        /* =========================================================
           DAY CARD
        ========================================================= */

        .day {
            width: 100%;
            margin-bottom: 15px;
            border: 1px solid #dfe6eb;
            border-radius: 10px;
            overflow: hidden;
            background: #ffffff;
            page-break-inside: avoid;
        }

        .day-head {
            width: 100%;
            padding: 10px 12px;
            background: #f7f9fb;
            border-bottom: 1px solid #e4e9ed;
        }

        .day-head-table {
            width: 100%;
            border-collapse: collapse;
        }

        .day-number-cell {
            width: 58px;
            vertical-align: middle;
        }

        .day-title-cell {
            vertical-align: middle;
            padding-left: 9px;
        }

        .day-badge {
            display: inline-block;
            min-width: 49px;
            padding: 5px 8px;
            border-radius: 20px;
            background: #f28a18;
            color: #ffffff;
            font-size: 8px;
            font-weight: 800;
            text-align: center;
            text-transform: uppercase;
        }

        .day-title {
            color: #173651;
            font-size: 13px;
            line-height: 1.35;
            font-weight: 800;
        }

        .day-body {
            padding: 10px;
        }


        /* =========================================================
           DAY IMAGE
        ========================================================= */

        .day-image-wrap {
            width: 100%;
            margin-bottom: 10px;
        }

        .day-image {
            display: block;
            width: 100%;
            height: 145px;
            object-fit: cover;
            border-radius: 8px;
        }


        /* =========================================================
           LOCATION
        ========================================================= */

        .location {
            width: 100%;
            padding: 8px 10px;
            margin-bottom: 10px;
            border-left: 3px solid #f28a18;
            background: #fff7ed;
            color: #4b5966;
            font-size: 9px;
            line-height: 1.45;
        }

        .location strong {
            color: #173651;
        }


        /* =========================================================
           DESCRIPTION
        ========================================================= */

        .description {
            margin: 0 0 9px;
            color: #465562;
            font-size: 9.5px;
            line-height: 1.6;
        }


        /* =========================================================
           ACTIVITIES
        ========================================================= */

        .activities-title {
            margin: 9px 0 5px;
            color: #173651;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        ul.activities {
            margin: 0;
            padding-left: 17px;
        }

        ul.activities li {
            margin-bottom: 3px;
            color: #465562;
            font-size: 9px;
            line-height: 1.45;
        }


        /* =========================================================
           EMPTY STATE
        ========================================================= */

        .empty-message {
            width: 100%;
            padding: 28px;
            text-align: center;
            border: 1px solid #dfe6eb;
            border-radius: 10px;
            background: #f8fafc;
            color: #687684;
        }


        /* =========================================================
           FOOTER
        ========================================================= */

        .footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: -12mm;
            height: 9mm;
            padding-top: 5px;
            border-top: 1px solid #dfe6eb;
            color: #687684;
            font-size: 7.5px;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-left {
            width: 75%;
            text-align: left;
        }

        .footer-right {
            width: 25%;
            text-align: right;
        }

        .page-number:after {
            content: counter(page);
        }

    </style>

</head>


<body>

@php

    /*
    |--------------------------------------------------------------------------
    | GLOBAL SETTINGS
    |--------------------------------------------------------------------------
    | Logo       -> Visual Settings -> visual.logo
    | Phone      -> General Settings -> site.phone
    | Email      -> General Settings -> site.email
    | Address    -> General Settings -> site.address
    |--------------------------------------------------------------------------
    */

    $settingsService = app(\App\Services\SettingsService::class);

    $settings = $settingsService->all() ?: [];


    /*
    |--------------------------------------------------------------------------
    | SITE INFORMATION
    |--------------------------------------------------------------------------
    */

    $siteName = trim(
        (string) (
            $settings['site.name']
            ?? config('travels.brand.name', 'travels')
        )
    );


    $sitePhone = trim(
        (string) (
            $settings['site.phone']
            ?? config('travels.contact.phone', '+91-9182498843, +91-9014534878')
        )
    );


    $siteEmail = trim(
        (string) (
            $settings['site.email']
            ?? config('travels.contact.email', 'bookings@ssbtravelz.com')
        )
    );


    $siteAddress = trim(
        (string) (
            $settings['site.address']
            ?? config('travels.contact.address', 'Hyderabad, Telangana, India')
        )
    );


    /*
    |--------------------------------------------------------------------------
    | VISUAL SETTINGS LOGO
    |--------------------------------------------------------------------------
    */

    $brandLogo = $settings['visual.logo']
        ?? config('travels.brand.logo')
        ?? 'uploads/1784531657409-WhatsApp_Image_2026-07-16_at_12.17.24_AM.jpeg';

    $logoSrc = null;


    if (!empty($brandLogo)) {

        $brandLogo = trim(
            (string) $brandLogo
        );

        /*
        |--------------------------------------------------------------------------
        | External URL
        |--------------------------------------------------------------------------
        */

        if (
            filter_var(
                $brandLogo,
                FILTER_VALIDATE_URL
            )
        ) {

            $logoSrc = $brandLogo;

        } else {

            /*
            |--------------------------------------------------------------------------
            | Stored public file
            |--------------------------------------------------------------------------
            */

            $logoPath = ltrim(
                $brandLogo,
                '/'
            );


            if (
                \Illuminate\Support\Facades\Storage::disk('public')->exists(
                    $logoPath
                )
            ) {

                $logoSrc =
                    \Illuminate\Support\Facades\Storage::disk('public')->path(
                        $logoPath
                    );

            } elseif (
                file_exists(
                    public_path($logoPath)
                )
            ) {

                $logoSrc =
                    public_path($logoPath);

            } elseif (
                file_exists(
                    storage_path(
                        'app/public/' . $logoPath
                    )
                )
            ) {

                $logoSrc =
                    storage_path(
                        'app/public/' . $logoPath
                    );
            }

        }

    }

    if (!$logoSrc) {
        $fallbackLogoPath = 'uploads/1784531657409-WhatsApp_Image_2026-07-16_at_12.17.24_AM.jpeg';

        if (file_exists(public_path($fallbackLogoPath))) {
            $logoSrc = public_path($fallbackLogoPath);
        } elseif (
            \Illuminate\Support\Facades\Storage::disk('public')->exists($fallbackLogoPath)
        ) {
            $logoSrc = \Illuminate\Support\Facades\Storage::disk('public')->path($fallbackLogoPath);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ITINERARY
    |--------------------------------------------------------------------------
    */

    $itinerary = $tour->itinerary;


    if (is_string($itinerary)) {

        $decoded = json_decode(
            $itinerary,
            true
        );

        $itinerary =
            json_last_error() === JSON_ERROR_NONE
                ? $decoded
                : [];

    }


    if (
        $itinerary instanceof
        \Illuminate\Support\Collection
    ) {

        $itinerary =
            $itinerary->all();

    }


    if (!is_array($itinerary)) {

        $itinerary = [];

    }


    /*
    |--------------------------------------------------------------------------
    | MAIN TOUR IMAGE
    |--------------------------------------------------------------------------
    */

    $coverImageValue = null;


    try {

        $coverImageValue =
            $tour->cover_image_url
            ?? null;

    } catch (\Throwable $e) {

        $coverImageValue = null;

    }


    $coverImageSrc = null;


    if (!empty($coverImageValue)) {

        $coverImageValue =
            trim(
                (string) $coverImageValue
            );


        /*
        |--------------------------------------------------------------------------
        | External image URL
        |--------------------------------------------------------------------------
        */

        if (
            filter_var(
                $coverImageValue,
                FILTER_VALIDATE_URL
            )
        ) {

            /*
             * Try to convert local website URLs into a local
             * filesystem path so Dompdf can load them reliably.
             */

            $parsedPath =
                parse_url(
                    $coverImageValue,
                    PHP_URL_PATH
                );


            if ($parsedPath) {

                $localPath =
                    public_path(
                        ltrim(
                            $parsedPath,
                            '/'
                        )
                    );


                if (
                    file_exists($localPath)
                ) {

                    $coverImageSrc =
                        $localPath;

                }

            }


            /*
             * If local conversion was not possible,
             * keep the URL for Dompdf.
             */

            if (!$coverImageSrc) {

                $coverImageSrc =
                    $coverImageValue;

            }

        } else {

            /*
            |--------------------------------------------------------------------------
            | Stored / public path
            |--------------------------------------------------------------------------
            */

            $coverImagePath =
                ltrim(
                    $coverImageValue,
                    '/'
                );


            if (
                \Illuminate\Support\Facades\Storage::disk('public')->exists(
                    $coverImagePath
                )
            ) {

                $coverImageSrc =
                    \Illuminate\Support\Facades\Storage::disk('public')->path(
                        $coverImagePath
                    );

            } elseif (
                file_exists(
                    public_path($coverImagePath)
                )
            ) {

                $coverImageSrc =
                    public_path($coverImagePath);

            } elseif (
                file_exists(
                    storage_path(
                        'app/public/' . $coverImagePath
                    )
                )
            ) {

                $coverImageSrc =
                    storage_path(
                        'app/public/' . $coverImagePath
                    );

            }

        }

    }

@endphp


{{-- ================================================================
     HEADER
================================================================ --}}

<div class="page-header">

    <table class="header-table">

        <tr>

            <td class="logo-cell">

                @if($logoSrc)

                    <img
                        class="logo"
                        src="{{ $logoSrc }}"
                        alt="{{ $siteName }}"
                    >

                @else

                    <div class="brand-name">
                        {{ $siteName }}
                    </div>

                @endif

            </td>


            <td class="contact-cell">

                @if($sitePhone)

                    <div class="contact-phone">
                        {{ $sitePhone }}
                    </div>

                @endif


                @if($siteEmail)

                    <div class="contact-email">
                        {{ $siteEmail }}
                    </div>

                @endif


                @if($siteAddress)

                    <div class="contact-address">
                        {{ $siteAddress }}
                    </div>

                @endif

            </td>

        </tr>

    </table>

</div>



{{-- ================================================================
     TOUR HERO
================================================================ --}}

<div class="hero">

    @if($coverImageSrc)

        <img
            class="hero-image"
            src="{{ $coverImageSrc }}"
            alt="{{ $tour->name }}"
        >

    @endif


    <div class="hero-overlay"></div>


    <div class="hero-content">

        <div class="hero-label">
            TOUR ITINERARY
        </div>


        <h1 class="hero-title">
            {{ $tour->name }}
        </h1>


        <div class="hero-line"></div>


        @if($tour->short_description)

            <div class="hero-description">
                {{ \Illuminate\Support\Str::limit(
                    strip_tags($tour->short_description),
                    300
                ) }}
            </div>

        @endif


        <div class="hero-meta">

            <strong>
                {{ $tour->duration_days }} Days
                /
                {{ $tour->duration_nights }} Nights
            </strong>


            @if($tour->destination)

                &nbsp;&nbsp;•&nbsp;&nbsp;

                {{ $tour->destination }}

            @endif

        </div>

    </div>

</div>



{{-- ================================================================
     ITINERARY TITLE
================================================================ --}}

<div class="section-title">

    <span>Tour</span>
    Itinerary

</div>



{{-- ================================================================
     ITINERARY DAYS
================================================================ --}}

@if(count($itinerary))

    @foreach($itinerary as $key => $day)

        @php

            /*
            |--------------------------------------------------------------------------
            | DAY DATA
            |--------------------------------------------------------------------------
            */

            $dayNumber =
                is_array($day)
                    ? ($day['day'] ?? ($key + 1))
                    : ($key + 1);


            $title =
                is_array($day)
                    ? (
                        $day['title']
                        ?? $day['name']
                        ?? $day['heading']
                        ?? 'Day ' . $dayNumber
                    )
                    : 'Day ' . $dayNumber;


            $location =
                is_array($day)
                    ? (
                        $day['location']
                        ?? $day['places']
                        ?? $day['city']
                        ?? ''
                    )
                    : '';

            if (is_array($location)) {
                $location = implode(', ', array_filter(array_map('strval', $location)));
            }

            $location = trim((string) $location);


            $description =
                is_array($day)
                    ? (
                        $day['description']
                        ?? $day['details']
                        ?? $day['content']
                        ?? ''
                    )
                    : $day;

            if (is_array($description)) {
                $description = implode("\n", array_filter(array_map('strval', $description)));
            }

            $description = trim((string) $description);


            $activities =
                is_array($day)
                    ? (
                        $day['activities']
                        ?? $day['items']
                        ?? []
                    )
                    : [];


            /*
            |--------------------------------------------------------------------------
            | NORMALIZE ACTIVITIES
            |--------------------------------------------------------------------------
            */

            if (is_string($activities)) {

                $activities =
                    preg_split(
                        '/\R/',
                        $activities,
                        -1,
                        PREG_SPLIT_NO_EMPTY
                    );

            }


            if (!is_array($activities)) {

                $activities = [];

            }


            /*
            |--------------------------------------------------------------------------
            | DAY IMAGE
            |--------------------------------------------------------------------------
            */

            $dayImage =
                is_array($day)
                    ? (
                        $day['image']
                        ?? $day['image_path']
                        ?? ''
                    )
                    : '';


            $dayImageSrc = null;


            if (!empty($dayImage)) {

                $dayImage =
                    trim(
                        (string) $dayImage
                    );


                /*
                |--------------------------------------------------------------------------
                | External URL
                |--------------------------------------------------------------------------
                */

                if (
                    filter_var(
                        $dayImage,
                        FILTER_VALIDATE_URL
                    )
                ) {

                    $parsedPath =
                        parse_url(
                            $dayImage,
                            PHP_URL_PATH
                        );


                    if ($parsedPath) {

                        $localDayPath =
                            public_path(
                                ltrim(
                                    $parsedPath,
                                    '/'
                                )
                            );


                        if (
                            file_exists($localDayPath)
                        ) {

                            $dayImageSrc =
                                $localDayPath;

                        }

                    }


                    if (!$dayImageSrc) {

                        $dayImageSrc =
                            $dayImage;

                    }

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | Stored image path
                    |--------------------------------------------------------------------------
                    */

                    $dayImagePath =
                        ltrim(
                            $dayImage,
                            '/'
                        );


                    if (
                        \Illuminate\Support\Facades\Storage::disk('public')->exists(
                            $dayImagePath
                        )
                    ) {

                        $dayImageSrc =
                            \Illuminate\Support\Facades\Storage::disk('public')->path(
                                $dayImagePath
                            );

                    } elseif (
                        file_exists(
                            public_path($dayImagePath)
                        )
                    ) {

                        $dayImageSrc =
                            public_path($dayImagePath);

                    } elseif (
                        file_exists(
                            storage_path(
                                'app/public/' . $dayImagePath
                            )
                        )
                    ) {

                        $dayImageSrc =
                            storage_path(
                                'app/public/' . $dayImagePath
                            );

                    }

                }

            }

        @endphp


        <div class="day">


            {{-- ====================================================
                 DAY HEADER
            ===================================================== --}}

            <div class="day-head">

                <table class="day-head-table">

                    <tr>

                        <td class="day-number-cell">

                            <span class="day-badge">

                                Day
                                {{ str_pad(
                                    $dayNumber,
                                    2,
                                    '0',
                                    STR_PAD_LEFT
                                ) }}

                            </span>

                        </td>


                        <td class="day-title-cell">

                            <div class="day-title">
                                {{ $title }}
                            </div>

                        </td>

                    </tr>

                </table>

            </div>



            {{-- ====================================================
                 DAY BODY
            ===================================================== --}}

            <div class="day-body">


                {{-- DAY IMAGE --}}

                @if($dayImageSrc)

                    <div class="day-image-wrap">

                        <img
                            class="day-image"
                            src="{{ $dayImageSrc }}"
                            alt="{{ $title }}"
                        >

                    </div>

                @endif



                {{-- LOCATION --}}

                @if($location)

                    <div class="location">

                        <strong>
                            Location / Places Covered:
                        </strong>

                        {{ $location }}

                    </div>

                @endif



                {{-- DESCRIPTION --}}

                @if($description)

                    <div class="description">

                        {!! nl2br(
                            e($description)
                        ) !!}

                    </div>

                @endif



                {{-- ACTIVITIES --}}

                @if(count($activities))

                    <div class="activities-title">
                        Activities / Schedule
                    </div>


                    <ul class="activities">

                        @foreach($activities as $activity)

                            @php

                                $activityText =
                                    is_array($activity)
                                        ? (
                                            $activity['title']
                                            ?? $activity['name']
                                            ?? $activity['activity']
                                            ?? $activity['text']
                                            ?? ''
                                        )
                                        : $activity;

                            @endphp


                            @if($activityText)

                                <li>
                                    {{ $activityText }}
                                </li>

                            @endif

                        @endforeach

                    </ul>

                @endif

            </div>

        </div>

    @endforeach


@else

    <div class="empty-message">

        No itinerary has been added for this tour package yet.

    </div>

@endif



{{-- ================================================================
     FOOTER
================================================================ --}}

<div class="footer">

    <table class="footer-table">

        <tr>

            <td class="footer-left">

                {{ $siteName }}

                @if($sitePhone)
                    · {{ $sitePhone }}
                @endif

                @if($siteEmail)
                    · {{ $siteEmail }}
                @endif

            </td>


            <td class="footer-right">

                Page
                <span class="page-number"></span>

            </td>

        </tr>

    </table>

</div>


</body>

</html>