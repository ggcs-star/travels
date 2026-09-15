@extends('admin.layouts.app')

@section('title', 'General Settings')

@section('content')

@php
    $value = fn ($key, $default = '') =>
        old($key, $settings[$key] ?? $default);
@endphp


<div class="website-settings">


    {{-- =====================================================
         PAGE HEADER
    ====================================================== --}}

    <div class="settings-heading">

        <div>

            <div class="settings-breadcrumb">
                Settings / General Settings
            </div>

            <h1>
                General Settings
            </h1>

            <p>
                Manage your website identity and general contact information.
            </p>

        </div>

    </div>



    {{-- =====================================================
         SUCCESS MESSAGE
    ====================================================== --}}

    @if(session('success'))

        <div class="general-settings-alert general-settings-alert-success">

            <i class="fas fa-check-circle"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif



    {{-- =====================================================
         ERROR MESSAGE
    ====================================================== --}}

    @if($errors->any())

        <div class="general-settings-alert general-settings-alert-error">

            <i class="fas fa-exclamation-circle"></i>

            <div>

                <strong>
                    Please fix the following:
                </strong>

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endif



    {{-- =====================================================
         GENERAL SETTINGS FORM
    ====================================================== --}}

    <form
        action="{{ route('admin.settings.general.update') }}"
        method="POST"
        class="general-settings-form"
    >

        @csrf

        @method('PUT')



        {{-- =================================================
             WEBSITE INFORMATION
        ================================================== --}}

        <section class="general-settings-card">

            <div class="general-settings-card-header">

                <div class="general-settings-card-icon">
                    <i class="fas fa-globe"></i>
                </div>

                <div>

                    <h2>
                        Website Information
                    </h2>

                    <p>
                        Basic identity and description of your website.
                    </p>

                </div>

            </div>


            <div class="general-settings-grid">


                {{-- WEBSITE NAME --}}

                <div class="general-settings-field">

                    <label for="site_name">
                        Website Name
                    </label>

                    <input
                        type="text"
                        id="site_name"
                        name="site_name"
                        value="{{ $value('site_name') }}"
                        placeholder="SSB Travelz"
                        maxlength="255"
                    >

                    <small>
                        The main name of your website.
                    </small>

                </div>



                {{-- TAGLINE --}}

                <div class="general-settings-field">

                    <label for="site_tagline">
                        Tagline
                    </label>

                    <input
                        type="text"
                        id="site_tagline"
                        name="site_tagline"
                        value="{{ $value('site_tagline') }}"
                        placeholder="Tourism With Faith"
                        maxlength="255"
                    >

                    <small>
                        A short line displayed with your website identity.
                    </small>

                </div>



                {{-- DESCRIPTION --}}

                <div class="general-settings-field general-settings-field-full">

                    <label for="site_description">
                        Website Description
                    </label>

                    <textarea
                        id="site_description"
                        name="site_description"
                        rows="6"
                        maxlength="5000"
                        placeholder="Write a short description about your travel website..."
                    >{{ $value('site_description') }}</textarea>

                    <small>
                        General description of your website and business.
                    </small>

                </div>

            </div>

        </section>



        {{-- =================================================
             CONTACT INFORMATION
        ================================================== --}}

        <section class="general-settings-card">

            <div class="general-settings-card-header">

                <div class="general-settings-card-icon">
                    <i class="fas fa-address-book"></i>
                </div>

                <div>

                    <h2>
                        Contact Information
                    </h2>

                    <p>
                        Main contact details used across the website.
                    </p>

                </div>

            </div>


            <div class="general-settings-grid">


                {{-- EMAIL --}}

                <div class="general-settings-field">

                    <label for="site_email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="site_email"
                        name="site_email"
                        value="{{ $value('site_email') }}"
                        placeholder="bookings@example.com"
                        maxlength="255"
                    >

                    <small>
                        Main business email address.
                    </small>

                </div>



                {{-- PHONE --}}

                <div class="general-settings-field">

                    <label for="site_phone">
                        Phone Number
                    </label>

                    <input
                        type="text"
                        id="site_phone"
                        name="site_phone"
                        value="{{ $value('site_phone') }}"
                        placeholder="+91-XXXXXXXXXX"
                        maxlength="100"
                    >

                    <small>
                        Main contact phone number.
                    </small>

                </div>



                {{-- WHATSAPP --}}

                <div class="general-settings-field">

                    <label for="site_whatsapp">
                        WhatsApp Number
                    </label>

                    <input
                        type="text"
                        id="site_whatsapp"
                        name="site_whatsapp"
                        value="{{ $value('site_whatsapp') }}"
                        placeholder="+91-XXXXXXXXXX"
                        maxlength="100"
                    >

                    <small>
                        WhatsApp contact number.
                    </small>

                </div>



                {{-- WORKING HOURS --}}

                <div class="general-settings-field">

                    <label for="site_working_hours">
                        Working Hours
                    </label>

                    <input
                        type="text"
                        id="site_working_hours"
                        name="site_working_hours"
                        value="{{ $value('site_working_hours') }}"
                        placeholder="Mon - Sat · 9am - 7pm"
                        maxlength="500"
                    >

                    <small>
                        Your normal business working hours.
                    </small>

                </div>

            </div>

        </section>



        {{-- =================================================
             ADDRESS INFORMATION
        ================================================== --}}

        <section class="general-settings-card">

            <div class="general-settings-card-header">

                <div class="general-settings-card-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>

                <div>

                    <h2>
                        Address Information
                    </h2>

                    <p>
                        Manage your office and branch address details.
                    </p>

                </div>

            </div>


            <div class="general-settings-grid">


                {{-- MAIN ADDRESS --}}

                <div class="general-settings-field general-settings-field-full">

                    <label for="site_address">
                        Main Address
                    </label>

                    <textarea
                        id="site_address"
                        name="site_address"
                        rows="4"
                        maxlength="2000"
                        placeholder="Main office address..."
                    >{{ $value('site_address') }}</textarea>

                </div>



                {{-- REGISTERED ADDRESS --}}

                <div class="general-settings-field general-settings-field-full">

                    <label for="site_registered_address">
                        Registered Address
                    </label>

                    <textarea
                        id="site_registered_address"
                        name="site_registered_address"
                        rows="4"
                        maxlength="2000"
                        placeholder="Registered address..."
                    >{{ $value('site_registered_address') }}</textarea>

                </div>



                {{-- BRANCH ADDRESS --}}

                <div class="general-settings-field general-settings-field-full">

                    <label for="site_branch_address">
                        Branch Address
                    </label>

                    <textarea
                        id="site_branch_address"
                        name="site_branch_address"
                        rows="4"
                        maxlength="2000"
                        placeholder="Branch address..."
                    >{{ $value('site_branch_address') }}</textarea>

                </div>

            </div>

        </section>



        {{-- =================================================
             SAVE BAR
        ================================================== --}}

        <div class="general-settings-save-bar">

            <div>

                <strong>
                    General Settings
                </strong>

                <span>
                    Changes will be applied to the website.
                </span>

            </div>


            <button
                type="submit"
                class="general-settings-save-button"
            >

                <i class="fas fa-save"></i>

                <span>
                    Save Changes
                </span>

            </button>

        </div>


    </form>

</div>

@endsection