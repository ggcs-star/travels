@extends('admin.layouts.app')

@section('title', $tourCategory->name)

@section('content')

<div class="admin-page tp-page">

    {{-- =========================================================
         BREADCRUMB
         ========================================================= --}}

    <div class="tp-breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Home</a>
        <span>›</span>
        <a href="{{ route('admin.tour-categories.index') }}">Tour Categories</a>
        <span>›</span>
        <strong>{{ $tourCategory->name }}</strong>
    </div>


    {{-- =========================================================
         HERO
         ========================================================= --}}

    <section
        class="tp-hero"
        @if($tourCategory->image)
            style="background-image:linear-gradient(120deg, rgba(11,48,43,.88), rgba(217,119,6,.78)), url('{{ asset('storage/' . $tourCategory->image) }}')"
        @endif
    >

        <div class="tp-hero__top">

            <div>
                <span class="tp-hero__eyebrow">Tour Category</span>
                <h1>{{ $tourCategory->name }}</h1>
                <p>
                    /{{ $tourCategory->slug }}
                    <span>•</span>
                    {{ $tourCategory->parent ? $tourCategory->parent->name : 'Category' }}
                </p>
            </div>

            <div class="tp-hero__actions">
                <a href="{{ route('admin.tour-categories.edit', $tourCategory) }}" class="tp-hero__btn tp-hero__btn--solid">
                    Edit Category
                </a>
                <a href="{{ route('admin.tour-categories.index') }}" class="tp-hero__btn">
                    ← Back
                </a>
            </div>

        </div>

    </section>


    {{-- =========================================================
         ALERTS
         ========================================================= --}}

    @if(session('success'))
        <div class="admin-alert admin-alert--success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="admin-alert admin-alert--danger">{{ session('error') }}</div>
    @endif


    {{-- =========================================================
         STAT CARDS
         ========================================================= --}}

    <div class="bd-stats tp-stats tp-stats--four">

        <div class="bd-stat">
            <span class="bd-stat__icon {{ $tourCategory->status ? 'bd-stat__icon--green' : 'bd-stat__icon--orange' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
            </span>
            <div>
                <small>Status</small>
                <strong>{{ $tourCategory->status ? 'Active' : 'Inactive' }}</strong>
            </div>
        </div>

        <div class="bd-stat">
            <span class="bd-stat__icon bd-stat__icon--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7Z"/></svg>
            </span>
            <div>
                <small>Tour Packages</small>
                <strong>{{ number_format($tourCategory->packages_count ?? 0) }}</strong>
            </div>
        </div>

        <div class="bd-stat">
            <span class="bd-stat__icon bd-stat__icon--blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
            </span>
            <div>
                <small>Sub Categories</small>
                <strong>{{ number_format($tourCategory->children->count()) }}</strong>
            </div>
        </div>

        <div class="bd-stat">
            <span class="bd-stat__icon bd-stat__icon--gold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01Z"/></svg>
            </span>
            <div>
                <small>Featured</small>
                <strong>{{ $tourCategory->featured ? 'Yes' : 'No' }}</strong>
            </div>
        </div>

    </div>


    {{-- =========================================================
         ROW 1: GENERAL DETAILS / MEDIA
         ========================================================= --}}

    <div class="bd-grid">

        <section class="bd-card">

            <div class="bd-card__header bd-card__header--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92Z"/></svg>
                <h2>General Details</h2>
            </div>

            <div class="admin-detail-list">

                <div>
                    <span>Name</span>
                    <strong>{{ $tourCategory->name }}</strong>
                </div>

                <div>
                    <span>Slug</span>
                    <strong>{{ $tourCategory->slug }}</strong>
                </div>

                <div>
                    <span>Parent category</span>
                    <strong>{{ $tourCategory->parent ? $tourCategory->parent->name : 'Category' }}</strong>
                </div>

                <div>
                    <span>Icon</span>
                    <strong>{{ $tourCategory->icon ?: '—' }}</strong>
                </div>

                <div>
                    <span>Sort order</span>
                    <strong>{{ $tourCategory->sort_order }}</strong>
                </div>

                <div>
                    <span>Created by</span>
                    <strong>{{ $tourCategory->creator->name ?? '—' }}</strong>
                </div>

                <div>
                    <span>Created</span>
                    <strong>{{ $tourCategory->created_at?->format('d M Y, h:i A') ?? '—' }}</strong>
                </div>

                <div>
                    <span>Last updated</span>
                    <strong>{{ $tourCategory->updated_at?->format('d M Y, h:i A') ?? '—' }}</strong>
                </div>

            </div>

        </section>


        <section class="bd-card">

            <div class="bd-card__header bd-card__header--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                <h2>Media</h2>
            </div>

            <div class="tp-media-pair">

                <div>
                    <span class="admin-eyebrow">Category Image</span>

                    @if($tourCategory->image)
                        <div class="tp-cover tp-cover--small" style="background-image:url('{{ asset('storage/' . $tourCategory->image) }}')"></div>
                    @else
                        <p class="admin-muted">No category image.</p>
                    @endif
                </div>

                <div>
                    <span class="admin-eyebrow">Open Graph Image</span>

                    @if($tourCategory->og_image)
                        <div class="tp-cover tp-cover--small" style="background-image:url('{{ asset('storage/' . $tourCategory->og_image) }}')"></div>
                    @else
                        <p class="admin-muted">No OG image.</p>
                    @endif
                </div>

            </div>

        </section>

    </div>


    {{-- =========================================================
         ROW 2: SEO SETTINGS / META DESCRIPTION
         ========================================================= --}}

    <div class="bd-grid">

        <section class="bd-card">

            <div class="bd-card__header bd-card__header--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                <h2>SEO Settings</h2>
            </div>

            <div class="admin-detail-list">

                <div>
                    <span>Meta title</span>
                    <strong>{{ $tourCategory->meta_title ?: '—' }}</strong>
                </div>

                <div>
                    <span>Meta keywords</span>
                    <strong>{{ $tourCategory->meta_keywords ?: '—' }}</strong>
                </div>

                <div>
                    <span>Robots</span>
                    <strong>{{ $tourCategory->robots ?: 'index,follow' }}</strong>
                </div>

                <div>
                    <span>Canonical URL</span>
                    <strong>{{ $tourCategory->canonical_url ?: 'Automatic' }}</strong>
                </div>

            </div>

        </section>


        <section class="bd-card">

            <div class="bd-card__header bd-card__header--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2Z"/></svg>
                <h2>Meta Description</h2>
            </div>

            @if($tourCategory->meta_description)
                <div class="bd-notes">{!! nl2br(e($tourCategory->meta_description)) !!}</div>
            @else
                <p class="admin-muted">No meta description added yet.</p>
            @endif

        </section>

    </div>


    {{-- =========================================================
         SUB CATEGORIES (full width)
         ========================================================= --}}

    @if($tourCategory->children->isNotEmpty())

        <section class="bd-card">

            <div class="bd-card__header bd-card__header--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                <h2>Sub Categories</h2>
                <span class="bd-card__count">{{ $tourCategory->children->count() }}</span>
            </div>

            <div class="admin-table-wrapper">

                <table class="admin-table">

                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Packages</th>
                            <th>Sort</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($tourCategory->children as $child)

                            <tr>
                                <td>
                                    <strong>{{ $child->name }}</strong>
                                    <small style="display:block;margin-top:3px;color:var(--admin-text-light);">/{{ $child->slug }}</small>
                                </td>

                                <td>{{ $child->packages_count ?? 0 }}</td>

                                <td>{{ $child->sort_order }}</td>

                                <td>
                                    <span class="booking-pill booking-pill--status-{{ $child->status ? 'confirmed' : 'cancelled' }}">
                                        {{ $child->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                <td>
                                    <div class="admin-actions">
                                        <a href="{{ route('admin.tour-categories.show', $child) }}" class="admin-icon-button" title="View">View</a>
                                        <a href="{{ route('admin.tour-categories.edit', $child) }}" class="admin-icon-button" title="Edit">Edit</a>
                                    </div>
                                </td>
                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </section>

    @endif


</div>


<style>

.tp-page {
    max-width: 1360px;
    margin: 0 auto;
}


/* Shared stat-card / paired-grid / pill components (same pattern as booking & tour package detail pages) */

.bd-stats {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 20px;
}

.tp-stats--four {
    grid-template-columns: repeat(4, minmax(0, 1fr));
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
    margin-bottom: 0;
}

.bd-card {
    border: 1px solid #e5e8ed;
    border-radius: 14px;
    background: #fff;
    padding: 20px;
    margin-bottom: 18px;
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

.booking-pill--status-confirmed {
    background: var(--admin-success-bg, #ecfdf3);
    color: var(--admin-success, #15803d);
}

.booking-pill--status-cancelled {
    background: var(--admin-danger-bg, #fff1f2);
    color: var(--admin-danger, #b42318);
}


/* Hero */

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


/* Media / Text */

.tp-media-pair {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.tp-cover {
    height: 200px;
    border-radius: 12px;
    background-size: cover;
    background-position: center;
    background-color: #f1f3f5;
}

.tp-cover--small {
    height: 130px;
    margin-top: 8px;
}

.tp-lead {
    margin: 0 0 14px;
    color: #4b5666;
    font-size: 14px;
    line-height: 1.6;
}

.tp-policy {
    margin-top: 16px;
    padding-top: 14px;
    border-top: 1px solid #eef0f2;
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
}


@media (max-width: 1150px) {
    .bd-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 700px) {
    .bd-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .tp-media-pair {
        grid-template-columns: 1fr;
    }
}

</style>

@endsection
