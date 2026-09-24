@extends('layouts.app')

@section('content')

    {{-- Hero --}}
    @include('components.home.hero')

    {{-- Featured Tours --}}
    @include('components.storefront.featured-tours', [
        'featuredTours' => $featuredTours ?? collect(),
    ])

    {{-- Monthly Tours --}}
    @include('components.home.monthly-tours')

    {{-- Journey Categories --}}
    @include('components.home.journey-categories')

    {{-- Handpicked Tours --}}
    @include('components.home.handpicked-tours')

    {{-- About --}}
    @include('components.home.about')

    {{-- Key Factors --}}
    @include('components.home.key-factors')

    {{-- Quote CTA --}}
    @include('components.home.quote-cta')

    {{-- Testimonials --}}
    @include('components.home.testimonials')

@endsection