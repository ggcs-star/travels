@extends('layouts.app')

@section('title', 'About Us | '.config('travels.brand.name', 'Travels'))

@section(
    'meta_description',
    'Learn more about '.config('travels.brand.name', 'Travels').' and discover how we create memorable travel experiences.'
)

@section('content')

    {{-- =========================================================
         ABOUT HERO
         ========================================================= --}}
    <section class="about-hero">

        <div class="about-hero__overlay"></div>

        <div class="container about-hero__inner">

            <span class="storefront-kicker">
                About {{ config('travels.brand.name', 'Travels') }}
            </span>

            <h1>
                We make every journey
                worth remembering.
            </h1>

            <p>
                Thoughtfully planned journeys, memorable experiences,
                and travel made simple from start to finish.
            </p>

        </div>

    </section>


    {{-- =========================================================
         INTRO / WHO WE ARE
         ========================================================= --}}
    <section class="storefront-section about-intro-section">

        <div class="container about-intro">

            <div class="about-intro__image">

                <img
                    src="{{ asset('images/hero/tour-bg.jpg') }}"
                    alt="Travel experience"
                    loading="lazy"
                >

                <div class="about-intro__image-badge">

                    <span class="about-badge-icon">
                        ✦
                    </span>

                    <div>
                        <strong>
                            Travel with confidence
                        </strong>

                        <small>
                            Planned with care
                        </small>
                    </div>

                </div>

            </div>


            <div class="about-intro__content">

                <span class="storefront-kicker">
                    Who We Are
                </span>

                <h2>
                    More than a trip.
                    It's an experience.
                </h2>

                <p class="about-lead">
                    {{ config('travels.brand.name', 'Travels') }}
                    is built around one simple idea:
                    travel should be exciting, comfortable,
                    and easy to plan.
                </p>

                <p>
                    We bring together carefully planned tours,
                    convenient departures, transparent pricing,
                    and the information you need before starting
                    your journey.
                </p>

                <p>
                    Whether you're travelling with family,
                    friends, students, colleagues, or independently,
                    our goal is to make the journey smoother from
                    the moment you discover a tour to the moment
                    you return home.
                </p>


                <div class="about-intro__points">

                    <div class="about-point">
                        <span>✓</span>
                        <div>
                            <strong>Thoughtfully planned</strong>
                            <small>
                                Tours designed around real travel needs.
                            </small>
                        </div>
                    </div>

                    <div class="about-point">
                        <span>✓</span>
                        <div>
                            <strong>Transparent booking</strong>
                            <small>
                                Clear departure dates and pricing.
                            </small>
                        </div>
                    </div>

                    <div class="about-point">
                        <span>✓</span>
                        <div>
                            <strong>Support when you need it</strong>
                            <small>
                                We're here to help throughout your journey.
                            </small>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- =========================================================
         WHY CHOOSE US
         ========================================================= --}}
    <section class="storefront-section about-why-section">

        <div class="container">

            <div class="about-section-heading">

                <span class="storefront-kicker">
                    Why Choose Us
                </span>

                <h2>
                    Travel planning without
                    the unnecessary stress.
                </h2>

                <p>
                    Everything is designed to make discovering,
                    booking, and preparing for your journey simpler.
                </p>

            </div>


            <div class="about-feature-grid">

                {{-- CARD 1 --}}
                <article class="about-feature-card">

                    <span class="about-feature-card__number">
                        01
                    </span>

                    <div class="about-feature-card__icon">
                        ✦
                    </div>

                    <h3>
                        Carefully Planned Tours
                    </h3>

                    <p>
                        Explore journeys that are structured with
                        destinations, durations, departures, and
                        traveller needs in mind.
                    </p>

                </article>


                {{-- CARD 2 --}}
                <article class="about-feature-card">

                    <span class="about-feature-card__number">
                        02
                    </span>

                    <div class="about-feature-card__icon">
                        ◇
                    </div>

                    <h3>
                        Clear & Transparent
                    </h3>

                    <p>
                        See available departures, traveller pricing,
                        and booking information before you reserve
                        your seats.
                    </p>

                </article>


                {{-- CARD 3 --}}
                <article class="about-feature-card">

                    <span class="about-feature-card__number">
                        03
                    </span>

                    <div class="about-feature-card__icon">
                        ✓
                    </div>

                    <h3>
                        Simple Online Booking
                    </h3>

                    <p>
                        Choose your departure, provide traveller
                        details, and securely complete your booking
                        online.
                    </p>

                </article>


                {{-- CARD 4 --}}
                <article class="about-feature-card">

                    <span class="about-feature-card__number">
                        04
                    </span>

                    <div class="about-feature-card__icon">
                        ♢
                    </div>

                    <h3>
                        Traveller Focused
                    </h3>

                    <p>
                        Every part of the experience is designed
                        around making your journey comfortable
                        and memorable.
                    </p>

                </article>

            </div>

        </div>

    </section>


    {{-- =========================================================
         MISSION
         ========================================================= --}}
    <section class="about-mission-section">

        <div class="container">

            <div class="about-mission">

                <div class="about-mission__content">

                    <span class="storefront-kicker">
                        Our Mission
                    </span>

                    <h2>
                        Making meaningful travel
                        easier for everyone.
                    </h2>

                    <p>
                        Our mission is to connect travellers with
                        thoughtfully planned journeys while keeping
                        the booking experience clear, convenient,
                        and reliable.
                    </p>

                    <p>
                        We believe the best travel experiences begin
                        before you even leave home — with good planning,
                        useful information, and confidence in your
                        booking.
                    </p>

                </div>


                <div class="about-mission__quote">

                    <span class="about-quote-mark">
                        “
                    </span>

                    <blockquote>
                        The journey matters just as much
                        as the destination.
                    </blockquote>

                    <span class="about-quote-line"></span>

                    <small>
                        Travel. Discover. Remember.
                    </small>

                </div>

            </div>

        </div>

    </section>


    {{-- =========================================================
         HOW WE WORK
         ========================================================= --}}
    <section class="storefront-section about-process-section">

        <div class="container">

            <div class="about-section-heading about-section-heading--center">

                <span class="storefront-kicker">
                    How It Works
                </span>

                <h2>
                    From discovery to departure.
                </h2>

                <p>
                    A simple booking journey designed around you.
                </p>

            </div>


            <div class="about-process">

                <div class="about-process__item">

                    <span class="about-process__number">
                        01
                    </span>

                    <div>
                        <h3>
                            Discover
                        </h3>

                        <p>
                            Browse destinations and find a tour
                            that matches your plans.
                        </p>
                    </div>

                </div>


                <div class="about-process__line"></div>


                <div class="about-process__item">

                    <span class="about-process__number">
                        02
                    </span>

                    <div>
                        <h3>
                            Choose
                        </h3>

                        <p>
                            Select your preferred departure date
                            and number of travellers.
                        </p>
                    </div>

                </div>


                <div class="about-process__line"></div>


                <div class="about-process__item">

                    <span class="about-process__number">
                        03
                    </span>

                    <div>
                        <h3>
                            Book
                        </h3>

                        <p>
                            Enter traveller information and securely
                            complete your reservation.
                        </p>
                    </div>

                </div>


                <div class="about-process__line"></div>


                <div class="about-process__item">

                    <span class="about-process__number">
                        04
                    </span>

                    <div>
                        <h3>
                            Travel
                        </h3>

                        <p>
                            Get ready and enjoy your journey with
                            confidence.
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- =========================================================
         CTA
         ========================================================= --}}
    <section class="about-cta-section">

        <div class="container">

            <div class="about-cta">

                <div>

                    <span class="storefront-kicker">
                        Your next journey awaits
                    </span>

                    <h2>
                        Ready to discover
                        somewhere new?
                    </h2>

                    <p>
                        Explore our tours and find the journey
                        that's right for you.
                    </p>

                </div>


                <div class="about-cta__actions">

                    <a
                        href="{{ route('tours.index') }}"
                        class="storefront-button"
                    >
                        Explore Tours →
                    </a>

                    <a
                        href="{{ route('contact') }}"
                        class="storefront-button storefront-button--secondary"
                    >
                        Contact Us
                    </a>

                </div>

            </div>

        </div>

    </section>

@endsection