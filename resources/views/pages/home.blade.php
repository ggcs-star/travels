@extends('layouts.app')

@section('title', 'Travels — Discover Your Next Journey')

@section(
    'meta_description',
    'Discover unforgettable journeys, curated tours and meaningful travel experiences with Travels.'
)

@section('content')

    {{-- Hero --}}
    @include('components.home.hero')

    {{-- Featured Tours --}}
    @include('components.storefront.featured-tours', [
        'featuredTours' => $featuredTours ?? collect(),
    ])

    {{-- Journey Categories --}}
    @include('components.home.journey-categories')

    {{-- Monthly Tours --}}
    @include('components.home.monthly-tours')

    {{-- Handpicked Tours --}}
    @include('components.home.handpicked-tours')

    {{-- About --}}
    @include('components.home.about')

    {{-- Key Factors --}}
    @include('components.home.key-factors')

    {{-- Why Choose Us --}}
    @include('components.home.why-us')

    {{-- Quote CTA --}}
    @include('components.home.quote-cta')

    {{-- Testimonials --}}
    @include('components.home.testimonials')

    {{-- Blog --}}
    @include('components.home.blog')

@endsection