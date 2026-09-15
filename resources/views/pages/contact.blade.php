@extends('layouts.app')

@section('title', 'Contact Us | ' . config('travels.brand.name'))

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | CONTACT SETTINGS
    |--------------------------------------------------------------------------
    | Values are loaded from Admin > Settings > General.
    | Fallbacks are used only when a setting is empty/missing.
    */

    $settings = app(\App\Services\SettingsService::class);

    $contactPhone = trim((string) $settings->get(
        'site.phone',
        config('travels.contact.phone_primary', '+91 0000000000')
    ));

    $contactEmail = trim((string) $settings->get(
        'site.email',
        config('travels.contact.email', 'bookings@travels.com')
    ));

    $contactAddress = trim((string) $settings->get(
        'site.address',
        config('travels.contact.address', 'India')
    ));

    $contactWorkingHours = trim((string) $settings->get(
        'site.working_hours',
        'Mon – Sat | 9:00 AM – 7:00 PM'
    ));

    /*
    |--------------------------------------------------------------------------
    | PHONE FOR TEL LINK
    |--------------------------------------------------------------------------
    */

    $contactPhoneLink = preg_replace(
        '/[^0-9+]/',
        '',
        $contactPhone
    );

    /*
    |--------------------------------------------------------------------------
    | WORKING HOURS DISPLAY
    |--------------------------------------------------------------------------
    | Admin can enter:
    | Mon – Sat | 9:00 AM – 7:00 PM
    |
    | We split it into title + time for the existing design.
    */

    $workingHoursTitle = 'Working Hours';
    $workingHoursTime = $contactWorkingHours;

    if (str_contains($contactWorkingHours, '|')) {
        [$workingHoursTitle, $workingHoursTime] = array_pad(
            array_map(
                'trim',
                explode('|', $contactWorkingHours, 2)
            ),
            2,
            ''
        );
    }
@endphp


<section class="contact-page">

    {{-- =====================================================
         CONTACT HERO
    ====================================================== --}}
    <section class="contact-hero">

        <div class="container contact-hero__inner">

            <div class="contact-hero__content">

                <span class="storefront-kicker">
                    CONTACT US
                </span>

                <h1>
                    We’d love to
                    <span>hear from you.</span>
                </h1>

                <p>
                    Have a question about a tour, booking, destination,
                    or your next trip? Get in touch with our travel team.
                </p>

            </div>

        </div>

    </section>


    {{-- =====================================================
         CONTACT INFORMATION
    ====================================================== --}}
    <section class="contact-info-section">

        <div class="container">

            <div class="contact-section-heading">

                <span class="storefront-kicker">
                    GET IN TOUCH
                </span>

                <h2>
                    How can we help?
                </h2>

                <p>
                    Contact us directly or send us a message using the
                    form below. Our team will be happy to assist you.
                </p>

            </div>


            <div class="contact-info-grid">

                {{-- =================================================
                     PHONE
                ================================================== --}}
                <a
                    href="tel:{{ $contactPhoneLink }}"
                    class="contact-info-card"
                >

                    <div class="contact-info-card__icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>

                    <div class="contact-info-card__content">

                        <span>
                            PHONE
                        </span>

                        <strong>
                            {{ $contactPhone }}
                        </strong>

                        <small>
                            Call us for travel assistance
                        </small>

                    </div>

                </a>


                {{-- =================================================
                     EMAIL
                ================================================== --}}
                <a
                    href="mailto:{{ $contactEmail }}"
                    class="contact-info-card"
                >

                    <div class="contact-info-card__icon">
                        <i class="fas fa-envelope"></i>
                    </div>

                    <div class="contact-info-card__content">

                        <span>
                            EMAIL
                        </span>

                        <strong>
                            {{ $contactEmail }}
                        </strong>

                        <small>
                            Send us your questions anytime
                        </small>

                    </div>

                </a>


                {{-- =================================================
                     ADDRESS
                ================================================== --}}
                <div class="contact-info-card">

                    <div class="contact-info-card__icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>

                    <div class="contact-info-card__content">

                        <span>
                            OFFICE
                        </span>

                        <strong>
                            {{ $contactAddress }}
                        </strong>

                        <small>
                            Our travel office
                        </small>

                    </div>

                </div>


                {{-- =================================================
                     WORKING HOURS
                ================================================== --}}
                <div class="contact-info-card">

                    <div class="contact-info-card__icon">
                        <i class="fas fa-clock"></i>
                    </div>

                    <div class="contact-info-card__content">

                        <span>
                            WORKING HOURS
                        </span>

                        <strong>
                            {{ $workingHoursTitle }}
                        </strong>

                        <small>
                            {{ $workingHoursTime }}
                        </small>

                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- =====================================================
         CONTACT FORM
    ====================================================== --}}
    <section class="contact-form-section">

        <div class="container">

            <div class="contact-form-wrapper">

                <div class="contact-form-heading">

                    <span class="storefront-kicker">
                        SEND US A MESSAGE
                    </span>

                    <h2>
                        Tell us how
                        we can help.
                    </h2>

                    <p>
                        Fill out the form and our team will get back
                        to you with the information you need.
                    </p>

                </div>


                <div class="contact-form-card">


                    {{-- =================================================
                         SUCCESS MESSAGE
                    ================================================== --}}
                    @if(session('success'))

                        <div
                            class="contact-alert contact-alert--success"
                            role="alert"
                        >
                            {{ session('success') }}
                        </div>

                    @endif


                    {{-- =================================================
                         GENERAL ERROR MESSAGE
                    ================================================== --}}
                    @if(session('error'))

                        <div
                            class="contact-alert contact-alert--error"
                            role="alert"
                        >
                            {{ session('error') }}
                        </div>

                    @endif


                    {{-- =================================================
                         VALIDATION ERRORS
                    ================================================== --}}
                    @if($errors->any())

                        <div
                            class="contact-alert contact-alert--error"
                            role="alert"
                        >
                            Please check the highlighted fields and try again.
                        </div>

                    @endif


                    {{-- =================================================
                         CONTACT FORM
                    ================================================== --}}
                    <form
                        method="POST"
                        action="{{ route('contact.store') }}"
                        class="contact-form"
                    >

                        @csrf


                        {{-- =================================================
                             NAME + EMAIL
                        ================================================== --}}
                        <div class="contact-form-row">

                            {{-- NAME --}}
                            <div class="contact-field">

                                <label for="contact_name">
                                    Full Name
                                </label>

                                <input
                                    id="contact_name"
                                    type="text"
                                    name="name"
                                    value="{{ old('name', auth()->user()->name ?? '') }}"
                                    placeholder="Enter your full name"
                                    maxlength="100"
                                    autocomplete="name"
                                    required
                                >

                                @error('name')
                                    <small>{{ $message }}</small>
                                @enderror

                            </div>


                            {{-- EMAIL --}}
                            <div class="contact-field">

                                <label for="contact_email">
                                    Email Address
                                </label>

                                <input
                                    id="contact_email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email', auth()->user()->email ?? '') }}"
                                    placeholder="Enter your email address"
                                    maxlength="150"
                                    autocomplete="email"
                                    required
                                >

                                @error('email')
                                    <small>{{ $message }}</small>
                                @enderror

                            </div>

                        </div>


                        {{-- =================================================
                             PHONE + SUBJECT
                        ================================================== --}}
                        <div class="contact-form-row">

                            {{-- PHONE --}}
                            <div class="contact-field">

                                <label for="contact_phone">
                                    Phone Number
                                </label>

                                <input
                                    id="contact_phone"
                                    type="tel"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    placeholder="+91 98765 43210"
                                    maxlength="30"
                                    autocomplete="tel"
                                >

                                @error('phone')
                                    <small>{{ $message }}</small>
                                @enderror

                            </div>


                            {{-- SUBJECT --}}
                            <div class="contact-field">

                                <label for="contact_subject">
                                    Subject
                                </label>

                                <select
                                    id="contact_subject"
                                    name="subject"
                                    required
                                >

                                    <option value="">
                                        Select a subject
                                    </option>

                                    <option
                                        value="tour-enquiry"
                                        @selected(old('subject') === 'tour-enquiry')
                                    >
                                        Tour Enquiry
                                    </option>

                                    <option
                                        value="booking"
                                        @selected(old('subject') === 'booking')
                                    >
                                        Booking
                                    </option>

                                    <option
                                        value="destination"
                                        @selected(old('subject') === 'destination')
                                    >
                                        Destination Information
                                    </option>

                                    <option
                                        value="custom-trip"
                                        @selected(old('subject') === 'custom-trip')
                                    >
                                        Custom Trip
                                    </option>

                                    <option
                                        value="other"
                                        @selected(old('subject') === 'other')
                                    >
                                        Other
                                    </option>

                                </select>

                                @error('subject')
                                    <small>{{ $message }}</small>
                                @enderror

                            </div>

                        </div>


                        {{-- =================================================
                             MESSAGE
                        ================================================== --}}
                        <div class="contact-field">

                            <label for="contact_message">
                                Your Message
                            </label>

                            <textarea
                                id="contact_message"
                                name="message"
                                rows="7"
                                maxlength="3000"
                                placeholder="Write your message here..."
                                required
                            >{{ old('message') }}</textarea>

                            @error('message')
                                <small>{{ $message }}</small>
                            @enderror

                        </div>


                        {{-- =================================================
                             SUBMIT
                        ================================================== --}}
                        <div class="contact-form-footer">

                            <p>
                                We respect your privacy and will only use
                                your information to respond to your enquiry.
                            </p>

                            <button
                                type="submit"
                                class="contact-submit-button"
                            >

                                <span>
                                    Send Message
                                </span>

                                <i class="fas fa-arrow-right"></i>

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </section>

</section>

@endsection