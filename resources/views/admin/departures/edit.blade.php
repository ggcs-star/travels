@extends('admin.layouts.app')

@section('title', 'Edit Departure')

@section('content')
<div class="admin-page">
    <div class="admin-page__header"><div><span class="admin-eyebrow">TOURS / DEPARTURES</span><h1 class="admin-page__title">Edit departure</h1><p class="admin-page__description">{{ $tour->name }}</p></div></div>
    <form method="POST" action="{{ route('admin.tours.departures.update', [$tour, $departure]) }}">@include('admin.departures.partials.form')</form>
</div>
@endsection
