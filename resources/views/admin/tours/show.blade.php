@extends('admin.layouts.app')

@section('title', $tour->name)

@section('content')

<div class="admin-page">

    <div class="admin-page__header">

        <div>

            <span class="admin-eyebrow">
                TOUR PACKAGE
            </span>

            <h1 class="admin-page__title">
                {{ $tour->name }}
            </h1>

            <p class="admin-page__description">
                {{ $tour->package_code }}
            </p>

        </div>

        <div class="admin-page__actions">

            <a
                href="{{ route('admin.tours.edit', $tour) }}"
                class="admin-button admin-button--dark"
            >
                Edit Package
            </a>

            <a
                href="{{ route('admin.tours.index') }}"
                class="admin-button"
            >
                ← Back
            </a>

        </div>

    </div>


    {{-- Status --}}

    <section class="admin-card">

        <div class="admin-card__header">

            <div>
                <span class="admin-eyebrow">
                    STATUS
                </span>

                <h2>
                    Package Status
                </h2>
            </div>

        </div>


        <div class="admin-status">

            @if($tour->status === 'published')

                <span class="admin-status__dot"></span>

                <div>
                    <strong>
                        Published
                    </strong>

                    <p>
                        This tour package is currently published.
                    </p>
                </div>

            @elseif($tour->status === 'inactive')

                <div>
                    <strong>
                        Inactive
                    </strong>

                    <p>
                        This tour package is currently inactive.
                    </p>
                </div>

            @else

                <div>
                    <strong>
                        Draft
                    </strong>

                    <p>
                        This package has not been published yet.
                    </p>
                </div>

            @endif

        </div>

    </section>


    {{-- Information --}}

    <div class="admin-grid admin-grid--main">

        <section class="admin-card">

            <div class="admin-card__header">

                <div>

                    <span class="admin-eyebrow">
                        INFORMATION
                    </span>

                    <h2>
                        Package Details
                    </h2>

                </div>

            </div>


            <div class="admin-detail-list">

                <div>
                    <span>Package Code</span>
                    <strong>{{ $tour->package_code }}</strong>
                </div>

                <div>
                    <span>Tour Type</span>
                    <strong>
                        {{ $tour->tour_type
                            ? ucfirst($tour->tour_type)
                            : '—'
                        }}
                    </strong>
                </div>

                <div>
                    <span>Duration</span>
                    <strong>
                        {{ $tour->duration_days }} Days /
                        {{ $tour->duration_nights }} Nights
                    </strong>
                </div>

                <div>
                    <span>Difficulty</span>
                    <strong>
                        {{ $tour->difficulty_level
                            ? ucfirst($tour->difficulty_level)
                            : '—'
                        }}
                    </strong>
                </div>

                <div>
                    <span>Age Range</span>
                    <strong>
                        @if($tour->age_min !== null || $tour->age_max !== null)
                            {{ $tour->age_min ?? '—' }}
                            -
                            {{ $tour->age_max ?? '—' }}
                        @else
                            —
                        @endif
                    </strong>
                </div>

                <div>
                    <span>Best Time</span>
                    <strong>
                        {{ $tour->best_time ?: '—' }}
                    </strong>
                </div>

                <div>
                    <span>Featured</span>
                    <strong>
                        {{ $tour->featured ? 'Yes' : 'No' }}
                    </strong>
                </div>

                <div>
                    <span>Created</span>
                    <strong>
                        {{ $tour->created_at?->format('d M Y, h:i A') }}
                    </strong>
                </div>

            </div>

        </section>


        <section class="admin-card">

            <div class="admin-card__header">

                <div>

                    <span class="admin-eyebrow">
                        DESCRIPTION
                    </span>

                    <h2>
                        Package Content
                    </h2>

                </div>

            </div>


            @if($tour->short_description)

                <p>
                    {{ $tour->short_description }}
                </p>

            @endif


            @if($tour->description)

                <div>
                    {!! nl2br(e($tour->description)) !!}
                </div>

            @else

                <p>
                    No detailed description added yet.
                </p>

            @endif

        </section>

    </div>


    {{-- Status Controls --}}

    <section class="admin-card">

        <div class="admin-card__header">

            <div>

                <span class="admin-eyebrow">
                    MANAGEMENT
                </span>

                <h2>
                    Package Actions
                </h2>

            </div>

        </div>


        <div class="admin-actions">

            @if($tour->status !== 'published')

                <form
                    method="POST"
                    action="{{ route('admin.tours.status', $tour) }}"
                >

                    @csrf
                    @method('PATCH')

                    <input
                        type="hidden"
                        name="status"
                        value="published"
                    >

                    <button
                        type="submit"
                        class="admin-action"
                    >
                        <span class="admin-action__icon">
                            ✓
                        </span>

                        <span class="admin-action__content">
                            <strong>Publish Package</strong>
                            <small>
                                Make this package available for customers.
                            </small>
                        </span>

                        <span class="admin-action__arrow">
                            →
                        </span>

                    </button>

                </form>

            @endif


            @if($tour->status === 'published')

                <form
                    method="POST"
                    action="{{ route('admin.tours.status', $tour) }}"
                >

                    @csrf
                    @method('PATCH')

                    <input
                        type="hidden"
                        name="status"
                        value="inactive"
                    >

                    <button
                        type="submit"
                        class="admin-action"
                    >
                        <span class="admin-action__icon">
                            ◼
                        </span>

                        <span class="admin-action__content">
                            <strong>Deactivate Package</strong>
                            <small>
                                Temporarily remove it from publication.
                            </small>
                        </span>

                        <span class="admin-action__arrow">
                            →
                        </span>

                    </button>

                </form>

            @endif


            <form
                method="POST"
                action="{{ route('admin.tours.duplicate', $tour) }}"
            >

                @csrf

                <button
                    type="submit"
                    class="admin-action"
                >

                    <span class="admin-action__icon">
                        +
                    </span>

                    <span class="admin-action__content">

                        <strong>
                            Duplicate Package
                        </strong>

                        <small>
                            Create a draft copy of this package.
                        </small>

                    </span>

                    <span class="admin-action__arrow">
                        →
                    </span>

                </button>

            </form>


            <form
                method="POST"
                action="{{ route('admin.tours.destroy', $tour) }}"
                onsubmit="return confirm('Are you sure you want to delete this tour package?');"
            >

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="admin-action"
                >

                    <span class="admin-action__icon">
                        ×
                    </span>

                    <span class="admin-action__content">

                        <strong>
                            Delete Package
                        </strong>

                        <small>
                            Move this package to trash.
                        </small>

                    </span>

                    <span class="admin-action__arrow">
                        →
                    </span>

                </button>

            </form>

        </div>

    </section>

</div>

@endsection