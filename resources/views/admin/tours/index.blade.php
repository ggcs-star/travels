@extends('admin.layouts.app')

@section('title', 'Tour Packages')

@section('description', 'Create, manage and publish your travel tour packages.')

@section('content')

<div class="admin-page">

    {{-- Flash Message --}}
    @if(session('success'))

        <div class="admin-alert admin-alert--success">
            {{ session('success') }}
        </div>

    @endif


    {{-- Filters --}}
    <section class="admin-card">

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
                    class="admin-button admin-button--primary"
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

        <div class="admin-card__header">

            <div>
                <span class="admin-eyebrow">
                    MANAGE
                </span>

                <h2>
                    All Tour Packages
                </h2>
            </div>

            <a
                href="{{ route('admin.tours.create') }}"
                class="admin-button admin-button--primary"
            >
                <span>+</span>
                Add Tour Package
            </a>

        </div>


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

                                    <form
                                        method="POST"
                                        action="{{ route('admin.tours.status', $tour) }}"
                                        class="admin-inline-status-form"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <select
                                            name="status"
                                            title="Change status"
                                            data-status-select
                                            class="admin-status-select admin-status-select--{{ $tour->status }}"
                                            onchange="this.form.submit()"
                                        >
                                            <option value="draft" @selected($tour->status === 'draft')>Draft</option>
                                            <option value="published" @selected($tour->status === 'published')>Published</option>
                                            <option value="inactive" @selected($tour->status === 'inactive')>Inactive</option>
                                        </select>
                                    </form>

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
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1.5 12S5 5 12 5s10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            <span class="admin-sr-only">View</span>
                                        </a>

                                        <a
                                            href="{{ route('admin.tours.edit', $tour) }}"
                                            class="admin-icon-button"
                                            title="Edit"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                            <span class="admin-sr-only">Edit</span>
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.tours.duplicate', $tour) }}"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="admin-icon-button"
                                                title="Duplicate"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                                <span class="admin-sr-only">Duplicate</span>
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.tours.destroy', $tour) }}"
                                            onsubmit="return confirm('Delete this tour package? It can be restored later if needed.')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="admin-icon-button admin-icon-button--danger"
                                                title="Delete"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                                <span class="admin-sr-only">Delete</span>
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

            <div class="admin-pagination">

                {{ $tours->links() }}

            </div>

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
                    class="admin-button admin-button--primary"
                >
                    Add Tour Package
                </a>

            </div>

        @endif

    </section>

</div>


<style>

.admin-inline-status-form {
    display: inline-block;
}

.admin-status-select {
    height: 30px;
    border: 1px solid #dfe3e9;
    border-radius: 6px;
    background: #fff;
    color: #4b5666;
    font-size: 11px;
    font-weight: 650;
    padding: 0 8px;
    cursor: pointer;
}

.admin-status-select:focus {
    outline: none;
    border-color: #8993a3;
}

.admin-status-select--published {
    background: var(--admin-success-bg, #ecfdf3);
    border-color: var(--admin-success-bg, #ecfdf3);
    color: var(--admin-success, #15803d);
}

.admin-status-select--inactive {
    background: var(--admin-danger-bg, #fff1f2);
    border-color: var(--admin-danger-bg, #fff1f2);
    color: var(--admin-danger, #b42318);
}

.admin-status-select--draft {
    background: #fffbeb;
    border-color: #fffbeb;
    color: #b45309;
}

</style>


<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('[data-status-select]').forEach(function (select) {

        select.addEventListener('change', function () {

            select.className = 'admin-status-select admin-status-select--' + select.value;

        });

    });

});
</script>

@endsection