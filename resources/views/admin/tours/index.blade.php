@extends('admin.layouts.app')

@section('title', 'Tour Packages')

@section('content')

<div class="admin-page">

    {{-- Header --}}
    <div class="admin-page__header">

        <div>
            <span class="admin-eyebrow">
                TOURS
            </span>

            <h1 class="admin-page__title">
                Tour Packages
            </h1>

            <p class="admin-page__description">
                Create, manage and publish your travel tour packages.
            </p>
        </div>

        <a
            href="{{ route('admin.tours.create') }}"
            class="admin-button admin-button--dark"
        >
            <span>+</span>
            Add Tour Package
        </a>

    </div>


    {{-- Flash Message --}}
    @if(session('success'))

        <div class="admin-alert admin-alert--success">
            {{ session('success') }}
        </div>

    @endif


    {{-- Filters --}}
    <section class="admin-card">

        <div class="admin-card__header">

            <div>
                <span class="admin-eyebrow">
                    MANAGE
                </span>

                <h2>
                    All Tour Packages
                </h2>
            </div>

            <span>
                {{ $tours->total() }} packages
            </span>

        </div>


        <form
            method="GET"
            action="{{ route('admin.tours.index') }}"
            class="admin-filter-form"
        >

            <div class="admin-filter-form__group">

                <label for="search">
                    Search
                </label>

                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by name or package code..."
                >

            </div>


            <div class="admin-filter-form__group">

                <label for="status">
                    Status
                </label>

                <select
                    id="status"
                    name="status"
                >
                    <option value="">
                        All Status
                    </option>

                    <option
                        value="draft"
                        @selected(request('status') === 'draft')
                    >
                        Draft
                    </option>

                    <option
                        value="published"
                        @selected(request('status') === 'published')
                    >
                        Published
                    </option>

                    <option
                        value="inactive"
                        @selected(request('status') === 'inactive')
                    >
                        Inactive
                    </option>

                </select>

            </div>


            <div class="admin-filter-form__group">

                <label for="tour_type">
                    Tour Type
                </label>

                <select
                    id="tour_type"
                    name="tour_type"
                >

                    <option value="">
                        All Types
                    </option>

                    <option
                        value="group"
                        @selected(request('tour_type') === 'group')
                    >
                        Group Tour
                    </option>

                    <option
                        value="private"
                        @selected(request('tour_type') === 'private')
                    >
                        Private Tour
                    </option>

                    <option
                        value="custom"
                        @selected(request('tour_type') === 'custom')
                    >
                        Custom Tour
                    </option>

                </select>

            </div>


            <div class="admin-filter-form__group">

                <label for="featured">
                    Featured
                </label>

                <select
                    id="featured"
                    name="featured"
                >

                    <option value="">
                        All
                    </option>

                    <option
                        value="1"
                        @selected(request('featured') === '1')
                    >
                        Featured
                    </option>

                    <option
                        value="0"
                        @selected(request('featured') === '0')
                    >
                        Not Featured
                    </option>

                </select>

            </div>


            <div class="admin-filter-form__actions">

                <button
                    type="submit"
                    class="admin-button admin-button--dark"
                >
                    Search
                </button>

                <a
                    href="{{ route('admin.tours.index') }}"
                    class="admin-button"
                >
                    Reset
                </a>

            </div>

        </form>

    </section>


    {{-- Tour List --}}
    <section class="admin-card">

        @if($tours->count())

            <div class="admin-table-wrapper">

                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>
                                Package
                            </th>

                            <th>
                                Type
                            </th>

                            <th>
                                Duration
                            </th>

                            <th>
                                Featured
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Created
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($tours as $tour)

                            <tr>

                                <td>

                                    <div class="admin-table__primary">

                                        <strong>
                                            {{ $tour->name }}
                                        </strong>

                                        <small>
                                            {{ $tour->package_code }}
                                        </small>

                                    </div>

                                </td>


                                <td>
                                    {{ $tour->tour_type
                                        ? ucfirst($tour->tour_type)
                                        : '—'
                                    }}
                                </td>


                                <td>
                                    {{ $tour->duration_days }}
                                    {{ Str::plural('Day', $tour->duration_days) }}

                                    /

                                    {{ $tour->duration_nights }}
                                    {{ Str::plural('Night', $tour->duration_nights) }}
                                </td>


                                <td>

                                    @if($tour->featured)

                                        <span class="admin-badge admin-badge--success">
                                            Featured
                                        </span>

                                    @else

                                        <span class="admin-badge">
                                            No
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    @if($tour->status === 'published')

                                        <span class="admin-badge admin-badge--success">
                                            Published
                                        </span>

                                    @elseif($tour->status === 'inactive')

                                        <span class="admin-badge admin-badge--danger">
                                            Inactive
                                        </span>

                                    @else

                                        <span class="admin-badge admin-badge--warning">
                                            Draft
                                        </span>

                                    @endif

                                </td>


                                <td>
                                    {{ $tour->created_at?->format('d M Y') }}
                                </td>


                                <td>

                                    <div class="admin-table__actions">

                                        <a
                                            href="{{ route('admin.tours.show', $tour) }}"
                                            class="admin-icon-button"
                                            title="View"
                                        >
                                            View
                                        </a>

                                        <a
                                            href="{{ route('admin.tours.edit', $tour) }}"
                                            class="admin-icon-button"
                                            title="Edit"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.tours.duplicate', $tour) }}"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="admin-icon-button"
                                            >
                                                Duplicate
                                            </button>
                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}

            @if($tours->hasPages())

                <div class="admin-pagination">

                    {{ $tours->links() }}

                </div>

            @endif

        @else

            <div class="admin-empty">

                <div class="admin-empty__icon">
                    ✈
                </div>

                <h3>
                    No tour packages found
                </h3>

                <p>
                    Create your first tour package to get started.
                </p>

                <a
                    href="{{ route('admin.tours.create') }}"
                    class="admin-button admin-button--dark"
                >
                    Add Tour Package
                </a>

            </div>

        @endif

    </section>

</div>

@endsection