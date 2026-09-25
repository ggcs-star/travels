@extends('admin.layouts.app')

@section('title', $tour->name)

@section('content')

<div class="admin-page tp-page">

    {{-- =========================================================
         BREADCRUMB
         ========================================================= --}}

    <div class="tp-breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Home</a>
        <span>›</span>
        <a href="{{ route('admin.tours.index') }}">Tour Packages</a>
        <span>›</span>
        <strong>{{ $tour->name }}</strong>
    </div>


    {{-- =========================================================
         HERO
         ========================================================= --}}

    <section class="tp-hero" @if($tour->cover_image_url) style="background-image:linear-gradient(120deg, rgba(11,48,43,.88), rgba(217,119,6,.78)), url('{{ $tour->cover_image_url }}')" @endif>

        <div class="tp-hero__top">

            <div>
                <span class="tp-hero__eyebrow">Tour Package</span>
                <h1>{{ $tour->name }}</h1>
                <p>
                    {{ $tour->package_code }}
                    @if($tour->category)
                        <span>•</span>
                        {{ $tour->category->name }}
                    @endif
                    @if($tour->destination)
                        <span>•</span>
                        {{ $tour->destination }}
                    @endif
                </p>
            </div>

            <div class="tp-hero__actions">
                <a href="{{ route('admin.tours.edit', $tour) }}" class="tp-hero__btn tp-hero__btn--solid">
                    Edit Package
                </a>
                <a href="{{ route('admin.tours.index') }}" class="tp-hero__btn">
                    ← Back
                </a>
            </div>

        </div>

    </section>


    {{-- =========================================================
         STAT CARDS
         ========================================================= --}}

    <div class="bd-stats tp-stats">

        <div class="bd-stat">
            <span class="bd-stat__icon {{ $tour->status === 'published' ? 'bd-stat__icon--green' : 'bd-stat__icon--orange' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
            </span>
            <div>
                <small>Status</small>
                <strong>{{ Str::headline($tour->status) }}</strong>
            </div>
        </div>

        <div class="bd-stat">
            <span class="bd-stat__icon bd-stat__icon--blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></svg>
            </span>
            <div>
                <small>Duration</small>
                <strong>{{ $tour->duration_days }}D / {{ $tour->duration_nights }}N</strong>
            </div>
        </div>

        <div class="bd-stat">
            <span class="bd-stat__icon bd-stat__icon--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7Z"/></svg>
            </span>
            <div>
                <small>Departures</small>
                <strong>{{ $tour->departures->count() }}</strong>
            </div>
        </div>

        <div class="bd-stat">
            <span class="bd-stat__icon bd-stat__icon--gold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01Z"/></svg>
            </span>
            <div>
                <small>Featured</small>
                <strong>{{ $tour->featured ? 'Yes' : 'No' }}</strong>
            </div>
        </div>

        <div class="bd-stat">
            <span class="bd-stat__icon bd-stat__icon--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h8l-1 8 10-12h-8l1-8Z"/></svg>
            </span>
            <div>
                <small>Difficulty</small>
                <strong>{{ $tour->difficulty_level ? Str::headline($tour->difficulty_level) : '—' }}</strong>
            </div>
        </div>

    </div>


    {{-- =========================================================
         ROW 1: PACKAGE DETAILS / MEDIA
         ========================================================= --}}

    <div class="bd-grid">

        <section class="bd-card">

            <div class="bd-card__header bd-card__header--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92Z"/></svg>
                <h2>Package Details</h2>
            </div>

            <div class="admin-detail-list">

                <div>
                    <span>Package code</span>
                    <strong>{{ $tour->package_code }}</strong>
                </div>

                <div>
                    <span>Slug</span>
                    <strong>{{ $tour->slug }}</strong>
                </div>

                <div>
                    <span>Category</span>
                    <strong>{{ $tour->category->name ?? '—' }}</strong>
                </div>

                <div>
                    <span>Destination</span>
                    <strong>{{ $tour->destination ?: '—' }}</strong>
                </div>

                <div>
                    <span>Starting city</span>
                    <strong>{{ $tour->starting_city ?: '—' }}</strong>
                </div>

                <div>
                    <span>Ending city</span>
                    <strong>{{ $tour->ending_city ?: '—' }}</strong>
                </div>

                <div>
                    <span>Age range</span>
                    <strong>
                        @if($tour->age_min !== null || $tour->age_max !== null)
                            {{ $tour->age_min ?? '—' }} - {{ $tour->age_max ?? '—' }}
                        @else
                            —
                        @endif
                    </strong>
                </div>

                <div>
                    <span>Best time</span>
                    <strong>{{ $tour->best_time ?: '—' }}</strong>
                </div>

                <div>
                    <span>Created by</span>
                    <strong>{{ $tour->creator->name ?? '—' }}</strong>
                </div>

                <div>
                    <span>Created</span>
                    <strong>{{ $tour->created_at?->format('d M Y, h:i A') }}</strong>
                </div>

                <div>
                    <span>Last updated</span>
                    <strong>{{ $tour->updated_at?->format('d M Y, h:i A') }}</strong>
                </div>

            </div>

        </section>


        <section class="bd-card">

            <div class="bd-card__header bd-card__header--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                <h2>Media</h2>
            </div>

            <div id="tourMediaGallery" data-lightbox-gallery>

                @if($tour->cover_image_url)
                    <div
                        class="tp-cover tp-media-clickable"
                        style="background-image:url('{{ $tour->cover_image_url }}')"
                        data-lightbox-trigger
                        data-src="{{ $tour->cover_image_url }}"
                        data-caption="Cover image"
                        role="button"
                        tabindex="0"
                    ></div>
                @else
                    <p class="admin-muted">No cover image uploaded.</p>
                @endif

                @if($tour->images->isNotEmpty())

                    <div class="tp-gallery">
                        @foreach($tour->images as $image)
                            <div
                                class="tp-gallery__item tp-media-clickable"
                                style="background-image:url('{{ $image->image_url }}')"
                                title="{{ $image->alt_text }}"
                                data-lightbox-trigger
                                data-src="{{ $image->image_url }}"
                                data-caption="{{ $image->alt_text ?: 'Gallery image' }}"
                                role="button"
                                tabindex="0"
                            ></div>
                        @endforeach
                    </div>

                @else

                    <p class="admin-muted">No gallery images uploaded.</p>
                @endif

            </div>

        </section>

    </div>


    {{-- =========================================================
         ROW 2: DESCRIPTION / HIGHLIGHTS
         ========================================================= --}}

    <div class="bd-grid">

        <section class="bd-card">

            <div class="bd-card__header bd-card__header--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2Z"/></svg>
                <h2>Description</h2>
            </div>

            @if($tour->short_description)
                <p class="tp-lead">{{ $tour->short_description }}</p>
            @endif

            @if($tour->description)
                <div class="bd-notes">{!! nl2br(e($tour->description)) !!}</div>
            @else
                <p class="admin-muted">No detailed description added yet.</p>
            @endif

        </section>


        <section class="bd-card">

            <div class="bd-card__header bd-card__header--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01Z"/></svg>
                <h2>Highlights</h2>
            </div>

            @php $highlights = is_array($tour->highlights) ? array_filter($tour->highlights) : []; @endphp

            @if(count($highlights))
                <ul class="tp-list tp-list--check">
                    @foreach($highlights as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            @else
                <p class="admin-muted">No highlights added yet.</p>
            @endif

        </section>

    </div>


    {{-- =========================================================
         ROW 3: INCLUDED / EXCLUDED
         ========================================================= --}}

    <div class="bd-grid">

        <section class="bd-card">

            <div class="bd-card__header bd-card__header--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                <h2>Included</h2>
            </div>

            @php $included = is_array($tour->included_items) ? array_filter($tour->included_items) : []; @endphp

            @if(count($included))
                <ul class="tp-list tp-list--check">
                    @foreach($included as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            @else
                <p class="admin-muted">No inclusions added yet.</p>
            @endif

        </section>


        <section class="bd-card">

            <div class="bd-card__header" style="color:var(--admin-danger,#b42318)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                <h2>Excluded</h2>
            </div>

            @php $excluded = is_array($tour->excluded_items) ? array_filter($tour->excluded_items) : []; @endphp

            @if(count($excluded))
                <ul class="tp-list tp-list--cross">
                    @foreach($excluded as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            @else
                <p class="admin-muted">No exclusions added yet.</p>
            @endif

        </section>

    </div>


    {{-- =========================================================
         ITINERARY (full width)
         ========================================================= --}}

    @php $itinerary = is_array($tour->itinerary) ? $tour->itinerary : []; @endphp

    <section class="bd-card">

        <div class="bd-card__header bd-card__header--orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h4l2 3h9a2 2 0 0 1 2 2v3"/><path d="M9 16h1M14 21v-7l3 3 3-3v7"/></svg>
            <h2>Itinerary</h2>
            <span class="bd-card__count">{{ count($itinerary) }} {{ Str::plural('day', count($itinerary)) }}</span>
        </div>

        @if(count($itinerary))

            <div class="tp-itinerary">

                @foreach($itinerary as $index => $day)

                    @php
                        $dayNumber = (int) ($day['day'] ?? ($index + 1));
                        $dayTitle = $day['title'] ?? $day['name'] ?? '';
                        $dayLocation = $day['location'] ?? $day['places'] ?? '';
                        $dayDescription = $day['description'] ?? $day['details'] ?? '';
                        $activities = $day['activities'] ?? $day['items'] ?? [];
                        if (! is_array($activities)) { $activities = array_filter(preg_split('/\R/', (string) $activities)); }
                    @endphp

                    <article class="tp-itinerary-day">

                        <span class="tp-itinerary-day__badge">Day {{ $dayNumber }}</span>

                        <div>
                            <strong>{{ $dayTitle ?: 'Untitled day' }}</strong>

                            @if($dayLocation)
                                <span class="tp-itinerary-day__location">📍 {{ $dayLocation }}</span>
                            @endif

                            @if($dayDescription)
                                <p>{{ $dayDescription }}</p>
                            @endif

                            @if(count($activities))
                                <ul class="tp-list tp-list--dot">
                                    @foreach($activities as $activity)
                                        <li>{{ $activity }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                    </article>

                @endforeach

            </div>

        @else

            <p class="admin-muted">No itinerary added yet.</p>

        @endif

    </section>


    {{-- =========================================================
         ROW 4: IMPORTANT NOTES / POLICIES
         ========================================================= --}}

    <div class="bd-grid">

        <section class="bd-card">

            <div class="bd-card__header bd-card__header--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg>
                <h2>Important Notes</h2>
            </div>

            @if($tour->important_notes)
                <div class="bd-notes">{!! nl2br(e($tour->important_notes)) !!}</div>
            @else
                <p class="admin-muted">No important notes added yet.</p>
            @endif

        </section>


        <section class="bd-card">

            <div class="bd-card__header bd-card__header--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Z"/><path d="M14 2v6h6"/></svg>
                <h2>Policies</h2>
            </div>

            <div class="tp-policy">
                <strong>Terms &amp; Conditions</strong>
                <p>{{ $tour->terms_conditions ?: 'Not specified.' }}</p>
            </div>

            <div class="tp-policy">
                <strong>Cancellation Policy</strong>
                <p>{{ $tour->cancellation_policy ?: 'Not specified.' }}</p>
            </div>

            <div class="tp-policy">
                <strong>Privacy Policy</strong>
                <p>{{ $tour->privacy_policy ?: 'Not specified.' }}</p>
            </div>

        </section>

    </div>


    {{-- =========================================================
         DEPARTURES (full width)
         ========================================================= --}}

    <section class="bd-card">

        <div class="bd-card__header bd-card__header--green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            <h2>Departures</h2>
            <a href="{{ route('admin.tours.departures.create', $tour) }}" class="bd-card__count bd-card__count--link">
                + Add Departure
            </a>
        </div>

        @if($tour->departures->isNotEmpty())

            <div class="admin-table-wrapper">

                <table class="admin-table">

                    <thead>
                        <tr>
                            <th>Departure</th>
                            <th>Return</th>
                            <th>Capacity</th>
                            <th>Price</th>
                            <th>Sale Price</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($tour->departures->sortBy('departure_date') as $departure)

                            <tr>
                                <td>{{ $departure->departure_date?->format('d M Y') }}</td>
                                <td>{{ $departure->return_date?->format('d M Y') }}</td>
                                <td>{{ $departure->capacity }}</td>
                                <td>{{ $departure->currency }} {{ number_format((float) $departure->price, 2) }}</td>
                                <td>
                                    {{ $departure->sale_price ? $departure->currency.' '.number_format((float) $departure->sale_price, 2) : '—' }}
                                </td>
                                <td>
                                    <span class="booking-pill booking-pill--status-{{ $departure->status === 'open' ? 'confirmed' : ($departure->status === 'cancelled' ? 'cancelled' : 'expired') }}">
                                        {{ Str::headline($departure->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.tours.departures.edit', ['tour' => $tour, 'departure' => $departure]) }}" class="admin-table-action" title="Edit">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                        <span class="admin-sr-only">Edit</span>
                                    </a>
                                </td>
                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <p class="admin-muted">No departures scheduled yet.</p>

        @endif

    </section>


    {{-- =========================================================
         SEO SETTINGS
         ========================================================= --}}

    <section class="bd-card">

        <div class="bd-card__header bd-card__header--orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <h2>SEO Settings</h2>
        </div>

        <div class="admin-detail-list">

            <div>
                <span>Meta title</span>
                <strong>{{ $tour->meta_title ?: '—' }}</strong>
            </div>

            <div>
                <span>Meta keywords</span>
                <strong>{{ $tour->meta_keywords ?: '—' }}</strong>
            </div>

            <div>
                <span>Robots</span>
                <strong>{{ $tour->robots ?: 'index,follow' }}</strong>
            </div>

            <div>
                <span>Canonical URL</span>
                <strong>{{ $tour->canonical_url }}</strong>
            </div>

        </div>

        <div class="tp-policy">
            <strong>Meta description</strong>
            <p>{{ $tour->meta_description ?: 'Not specified.' }}</p>
        </div>

    </section>


    {{-- =========================================================
         PACKAGE ACTIONS
         ========================================================= --}}

    <section class="bd-card">

        <div class="bd-card__header bd-card__header--orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1.04 1.56V21a2 2 0 1 1-4 0v-.09A1.7 1.7 0 0 0 9 19.4a1.7 1.7 0 0 0-1.87.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.56-1.04H3a2 2 0 1 1 0-4h.09A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.34-1.87l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1.04-1.56V3a2 2 0 1 1 4 0v.09A1.7 1.7 0 0 0 15 4.6a1.7 1.7 0 0 0 1.87-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 1.56 1.04H21a2 2 0 1 1 0 4h-.09A1.7 1.7 0 0 0 19.4 15Z"/></svg>
            <h2>Package Actions</h2>
        </div>

        <div class="admin-actions">

            @if($tour->status !== 'published')

                <form method="POST" action="{{ route('admin.tours.status', $tour) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="published">
                    <button type="submit" class="admin-action">
                        <span class="admin-action__icon">✓</span>
                        <span class="admin-action__content">
                            <strong>Publish Package</strong>
                            <small>Make this package available for customers.</small>
                        </span>
                        <span class="admin-action__arrow">→</span>
                    </button>
                </form>

            @endif


            @if($tour->status === 'published')

                <form method="POST" action="{{ route('admin.tours.status', $tour) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="inactive">
                    <button type="submit" class="admin-action">
                        <span class="admin-action__icon">◼</span>
                        <span class="admin-action__content">
                            <strong>Deactivate Package</strong>
                            <small>Temporarily remove it from publication.</small>
                        </span>
                        <span class="admin-action__arrow">→</span>
                    </button>
                </form>

            @endif


            <form method="POST" action="{{ route('admin.tours.duplicate', $tour) }}">
                @csrf
                <button type="submit" class="admin-action">
                    <span class="admin-action__icon">+</span>
                    <span class="admin-action__content">
                        <strong>Duplicate Package</strong>
                        <small>Create a draft copy of this package.</small>
                    </span>
                    <span class="admin-action__arrow">→</span>
                </button>
            </form>


            <form
                method="POST"
                action="{{ route('admin.tours.destroy', $tour) }}"
                onsubmit="return confirm('Are you sure you want to delete this tour package?');"
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="admin-action">
                    <span class="admin-action__icon">×</span>
                    <span class="admin-action__content">
                        <strong>Delete Package</strong>
                        <small>Move this package to trash.</small>
                    </span>
                    <span class="admin-action__arrow">→</span>
                </button>
            </form>

        </div>

    </section>

</div>


<style>

.tp-page {
    max-width: 1360px;
    margin: 0 auto;
}


/* Shared stat-card / paired-grid / pill components (same pattern as the booking detail page) */

.bd-stats {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 20px;
}

.bd-stat {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    min-width: 0;
    padding: 16px;
    border: 1px solid #ece3d6;
    border-radius: 13px;
    background: #fffaf3;
}

.bd-stat__icon {
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
}

.bd-stat__icon svg {
    width: 18px;
    height: 18px;
}

.bd-stat__icon--blue {
    background: #e8f1ff;
    color: #2563eb;
}

.bd-stat__icon--green {
    background: var(--admin-success-bg, #ecfdf3);
    color: var(--admin-success, #15803d);
}

.bd-stat__icon--orange {
    background: var(--admin-primary-soft, #fff7ed);
    color: var(--admin-primary, #d97706);
}

.bd-stat__icon--gold {
    background: #fff8e1;
    color: #b7871a;
}

.bd-stat > div {
    min-width: 0;
}

.bd-stat small {
    display: block;
    color: #8c95a2;
    font-size: 12px;
    font-weight: 650;
}

.bd-stat strong {
    display: block;
    margin-top: 3px;
    color: #202b3e;
    font-size: 16px;
    font-weight: 750;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.bd-grid {
    display: grid;
    grid-template-columns: 1.7fr 1fr;
    gap: 18px;
    align-items: stretch;
    margin-bottom: 18px;
}

.bd-grid > .bd-card {
    display: flex;
    flex-direction: column;
}

.bd-grid > .bd-card {
    min-width: 0;
}

.bd-card {
    border: 1px solid #e5e8ed;
    border-radius: 14px;
    background: #fff;
    padding: 20px;
    margin-bottom: 18px;
}

.bd-grid > .bd-card {
    margin-bottom: 0;
}

.bd-card__header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 18px;
    padding-bottom: 14px;
    border-bottom: 1px solid #eef0f2;
}

.bd-card__header svg {
    width: 19px;
    height: 19px;
    flex: 0 0 19px;
}

.bd-card__header h2 {
    margin: 0;
    font-size: 16px;
    font-weight: 800;
    color: var(--admin-text, #202b3e);
}

.bd-card__header--orange svg {
    color: var(--admin-primary, #d97706);
}

.bd-card__header--green svg {
    color: var(--admin-success, #15803d);
}

.bd-card__count {
    margin-left: auto;
    padding: 4px 10px;
    border-radius: 999px;
    background: #f1f3f5;
    color: #586273;
    font-size: 11px;
    font-weight: 650;
}

.bd-notes {
    margin: 0;
    padding: 14px 16px;
    border-radius: 10px;
    background: #fafbfc;
    color: #4b5666;
    font-size: 13.5px;
    line-height: 1.6;
}

.booking-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 999px;
    background: #f1f3f5;
    color: #4b5666;
    font-size: 13px;
    font-weight: 700;
}

.booking-pill--status-confirmed,
.booking-pill--payment-paid {
    background: var(--admin-success-bg, #ecfdf3);
    color: var(--admin-success, #15803d);
}

.booking-pill--status-cancelled,
.booking-pill--payment-failed {
    background: var(--admin-danger-bg, #fff1f2);
    color: var(--admin-danger, #b42318);
}

.booking-pill--status-expired,
.booking-pill--payment-refunded {
    background: #fffbeb;
    color: #b45309;
}

.booking-pill--status-pending_payment,
.booking-pill--payment-unpaid,
.booking-pill--payment-created {
    background: #f1f3f5;
    color: #4b5666;
}


/* Breadcrumb */

.tp-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
    color: #9aa2ae;
    font-size: 13px;
}

.tp-breadcrumb a {
    color: #7d8796;
    text-decoration: none;
}

.tp-breadcrumb a:hover {
    color: var(--admin-primary, #d97706);
}

.tp-breadcrumb strong {
    color: #344054;
}


/* Hero */

.tp-hero {
    padding: 26px 28px;
    margin-bottom: 20px;
    border-radius: 16px;
    background: linear-gradient(120deg, #0b302b 0%, #14532d 45%, #d97706 130%);
    background-size: cover;
    background-position: center;
    color: #fff;
}

.tp-hero__top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
}

.tp-hero__eyebrow {
    display: block;
    margin-bottom: 6px;
    color: rgba(255, 255, 255, .85);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.tp-hero__top h1 {
    margin: 0;
    color: #fff;
    font-size: 26px;
    font-weight: 800;
}

.tp-hero__top p {
    margin: 8px 0 0;
    color: rgba(255, 255, 255, .9);
    font-size: 13.5px;
}

.tp-hero__top p span {
    margin: 0 6px;
    color: rgba(255, 255, 255, .55);
}

.tp-hero__actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.tp-hero__btn {
    display: inline-flex;
    align-items: center;
    padding: 10px 16px;
    border-radius: 999px;
    background: rgba(255, 255, 255, .16);
    color: #fff;
    text-decoration: none;
    font-size: 13px;
    font-weight: 650;
    white-space: nowrap;
}

.tp-hero__btn:hover {
    background: rgba(255, 255, 255, .28);
}

.tp-hero__btn--solid {
    background: #fff;
    color: #0b302b;
}

.tp-hero__btn--solid:hover {
    background: #f1f3f5;
}

.tp-stats {
    margin-bottom: 18px;
}


/* Media */

.tp-cover {
    height: 200px;
    margin-bottom: 14px;
    border-radius: 12px;
    background-size: cover;
    background-position: center;
    background-color: #f1f3f5;
}

.tp-gallery {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 8px;
}

.tp-gallery__item {
    aspect-ratio: 1;
    border-radius: 9px;
    background-size: cover;
    background-position: center;
    background-color: #f1f3f5;
}


/* Text */

.tp-lead {
    margin: 0 0 14px;
    color: #4b5666;
    font-size: 14px;
    line-height: 1.6;
}

.tp-policy {
    padding: 14px 0;
    border-top: 1px solid #eef0f2;
}

.tp-policy:first-of-type {
    padding-top: 0;
    border-top: 0;
}

.tp-policy strong {
    display: block;
    margin-bottom: 5px;
    color: #202b3e;
    font-size: 13px;
    font-weight: 700;
}

.tp-policy p {
    margin: 0;
    color: #697384;
    font-size: 13px;
    line-height: 1.6;
    white-space: pre-line;
}


/* Lists */

.tp-list {
    margin: 0;
    padding: 0;
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.tp-list li {
    position: relative;
    padding-left: 24px;
    color: #4b5666;
    font-size: 13.5px;
    line-height: 1.5;
}

.tp-list--check li::before {
    content: '✓';
    position: absolute;
    left: 0;
    color: var(--admin-success, #15803d);
    font-weight: 800;
}

.tp-list--cross li::before {
    content: '✕';
    position: absolute;
    left: 0;
    color: var(--admin-danger, #b42318);
    font-weight: 800;
}

.tp-list--dot li::before {
    content: '';
    position: absolute;
    left: 6px;
    top: 7px;
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: var(--admin-primary, #d97706);
}


/* Itinerary */

.tp-itinerary {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.tp-itinerary-day {
    display: flex;
    gap: 16px;
    padding: 16px;
    border: 1px solid #eef0f2;
    border-radius: 12px;
    background: #fafbfc;
}

.tp-itinerary-day__badge {
    flex: 0 0 auto;
    height: 28px;
    padding: 0 12px;
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    background: var(--admin-primary-soft, #fff7ed);
    color: var(--admin-primary-dark, #b45309);
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
}

.tp-itinerary-day > div {
    min-width: 0;
}

.tp-itinerary-day strong {
    display: block;
    color: #202b3e;
    font-size: 14.5px;
    font-weight: 700;
}

.tp-itinerary-day__location {
    display: inline-block;
    margin-top: 4px;
    color: #8c95a2;
    font-size: 12px;
}

.tp-itinerary-day p {
    margin: 8px 0 0;
    color: #697384;
    font-size: 13px;
    line-height: 1.6;
}

.tp-itinerary-day .tp-list {
    margin-top: 10px;
}


/* Card count link (Departures "+ Add") */

.bd-card__count--link {
    text-decoration: none;
    background: var(--admin-primary, #d97706);
    color: #fff;
}

.bd-card__count--link:hover {
    background: var(--admin-primary-dark, #b45309);
}


@media (max-width: 1150px) {
    .bd-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 700px) {
    .tp-gallery {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .tp-itinerary-day {
        flex-direction: column;
    }
}


/* Lightbox */

.tp-media-clickable {
    position: relative;
    cursor: pointer;
    transition: filter .15s ease;
}

.tp-media-clickable:hover {
    filter: brightness(.88);
}

.tp-media-clickable::after {
    content: '⤢';
    position: absolute;
    top: 8px;
    right: 8px;
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 7px;
    background: rgba(0, 0, 0, .45);
    color: #fff;
    font-size: 13px;
    opacity: 0;
    transition: opacity .15s ease;
}

.tp-media-clickable:hover::after {
    opacity: 1;
}

.tp-lightbox {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 999;
    align-items: center;
    justify-content: center;
    background: rgba(11, 20, 33, .92);
    padding: 40px;
}

.tp-lightbox.is-open {
    display: flex;
}

.tp-lightbox__stage {
    position: relative;
    max-width: 1100px;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.tp-lightbox__img {
    max-width: 100%;
    max-height: 76vh;
    border-radius: 10px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, .5);
}

.tp-lightbox__caption {
    margin-top: 14px;
    color: #e5e8ed;
    font-size: 13px;
    text-align: center;
}

.tp-lightbox__counter {
    margin-top: 4px;
    color: #8c95a2;
    font-size: 11px;
}

.tp-lightbox__close,
.tp-lightbox__prev,
.tp-lightbox__next {
    position: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, .12);
    color: #fff;
    cursor: pointer;
    transition: background .15s ease;
}

.tp-lightbox__close:hover,
.tp-lightbox__prev:hover,
.tp-lightbox__next:hover {
    background: rgba(255, 255, 255, .25);
}

.tp-lightbox__close {
    top: 22px;
    right: 22px;
    font-size: 20px;
}

.tp-lightbox__prev,
.tp-lightbox__next {
    top: 50%;
    transform: translateY(-50%);
    font-size: 22px;
}

.tp-lightbox__prev {
    left: 22px;
}

.tp-lightbox__next {
    right: 22px;
}

.tp-lightbox__prev:disabled,
.tp-lightbox__next:disabled {
    opacity: .3;
    cursor: not-allowed;
}

@media (max-width: 600px) {
    .tp-lightbox {
        padding: 16px;
    }

    .tp-lightbox__prev,
    .tp-lightbox__next {
        width: 38px;
        height: 38px;
    }

    .tp-lightbox__prev {
        left: 8px;
    }

    .tp-lightbox__next {
        right: 8px;
    }
}

</style>


{{-- =============================================================
     LIGHTBOX MODAL
     ============================================================= --}}

<div class="tp-lightbox" id="tpLightbox" aria-hidden="true">

    <button type="button" class="tp-lightbox__close" id="tpLightboxClose" aria-label="Close">✕</button>
    <button type="button" class="tp-lightbox__prev" id="tpLightboxPrev" aria-label="Previous image">‹</button>
    <button type="button" class="tp-lightbox__next" id="tpLightboxNext" aria-label="Next image">›</button>

    <div class="tp-lightbox__stage">
        <img class="tp-lightbox__img" id="tpLightboxImg" src="" alt="">
        <div class="tp-lightbox__caption" id="tpLightboxCaption"></div>
        <div class="tp-lightbox__counter" id="tpLightboxCounter"></div>
    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const gallery = document.getElementById('tourMediaGallery');

    if (!gallery) {
        return;
    }

    const triggers = Array.from(gallery.querySelectorAll('[data-lightbox-trigger]'));

    if (!triggers.length) {
        return;
    }

    const images = triggers.map(function (el) {
        return {
            src: el.dataset.src,
            caption: el.dataset.caption || '',
        };
    });

    const modal = document.getElementById('tpLightbox');
    const imgEl = document.getElementById('tpLightboxImg');
    const captionEl = document.getElementById('tpLightboxCaption');
    const counterEl = document.getElementById('tpLightboxCounter');
    const closeBtn = document.getElementById('tpLightboxClose');
    const prevBtn = document.getElementById('tpLightboxPrev');
    const nextBtn = document.getElementById('tpLightboxNext');

    let currentIndex = 0;

    function render() {

        const item = images[currentIndex];

        imgEl.src = item.src;
        imgEl.alt = item.caption;
        captionEl.textContent = item.caption;
        counterEl.textContent = (currentIndex + 1) + ' / ' + images.length;

        prevBtn.disabled = images.length <= 1;
        nextBtn.disabled = images.length <= 1;
    }

    function open(index) {

        currentIndex = index;
        render();
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function close() {

        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function showPrev() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        render();
    }

    function showNext() {
        currentIndex = (currentIndex + 1) % images.length;
        render();
    }

    triggers.forEach(function (el, index) {

        el.addEventListener('click', function () {
            open(index);
        });

        el.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                open(index);
            }
        });

    });

    closeBtn.addEventListener('click', close);
    prevBtn.addEventListener('click', showPrev);
    nextBtn.addEventListener('click', showNext);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            close();
        }
    });

    document.addEventListener('keydown', function (event) {

        if (!modal.classList.contains('is-open')) {
            return;
        }

        if (event.key === 'Escape') {
            close();
        } else if (event.key === 'ArrowLeft') {
            showPrev();
        } else if (event.key === 'ArrowRight') {
            showNext();
        }

    });

});
</script>

@endsection
