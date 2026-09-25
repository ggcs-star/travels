@extends('admin.layouts.app')

@section('title', 'Inquiries')

@section('description', 'Manage customer enquiries received from the website.')

@section('content')

<div class="admin-page">

    {{-- ALERT --}}
    @if(session('success'))
        <div class="admin-alert admin-alert--success">
            {{ session('success') }}
        </div>
    @endif


    {{-- STATS --}}
    <div class="admin-stats">

        <div class="admin-stat">

            <div class="admin-stat__top">

                <div class="admin-stat__icon admin-stat__icon--green">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="8" height="8" rx="2" fill="currentColor"/><rect x="13" y="3" width="8" height="8" rx="2" fill="currentColor"/><rect x="3" y="13" width="8" height="8" rx="2" fill="currentColor"/><rect x="13" y="13" width="8" height="8" rx="2" fill="currentColor"/></svg>
                </div>

                <span class="admin-badge">
                    TOTAL
                </span>

            </div>

            <strong class="admin-stat__value">
                {{ number_format($counts['all']) }}
            </strong>

            <span class="admin-stat__label">
                Total Inquiries
            </span>

        </div>


        <div class="admin-stat">

            <div class="admin-stat__top">

                <div class="admin-stat__icon">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>

                <span class="admin-badge admin-badge--danger">
                    NEW
                </span>

            </div>

            <strong class="admin-stat__value">
                {{ number_format($counts['new']) }}
            </strong>

            <span class="admin-stat__label">
                New Inquiries
            </span>

        </div>


        <div class="admin-stat">

            <div class="admin-stat__top">

                <div class="admin-stat__icon admin-stat__icon--teal">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.5 12S5 5 12 5s10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
                </div>

                <span class="admin-badge admin-badge--warning">
                    READ
                </span>

            </div>

            <strong class="admin-stat__value">
                {{ number_format($counts['read']) }}
            </strong>

            <span class="admin-stat__label">
                Read Inquiries
            </span>

        </div>


        <div class="admin-stat">

            <div class="admin-stat__top">

                <div class="admin-stat__icon">
                    ✓
                </div>

                <span class="admin-badge admin-badge--success">
                    REPLIED
                </span>

            </div>

            <strong class="admin-stat__value">
                {{ number_format($counts['replied']) }}
            </strong>

            <span class="admin-stat__label">
                Replied Inquiries
            </span>

        </div>

    </div>


    {{-- FILTER --}}
    <section class="admin-card">

        <form
            method="GET"
            action="{{ route('admin.inquiries.index') }}"
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
                    placeholder="Search name, email, phone..."
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
                        value="new"
                        @selected(request('status') === 'new')
                    >
                        New
                    </option>

                    <option
                        value="read"
                        @selected(request('status') === 'read')
                    >
                        Read
                    </option>

                    <option
                        value="replied"
                        @selected(request('status') === 'replied')
                    >
                        Replied
                    </option>

                </select>

            </div>


            <div class="admin-filter-form__actions">

                <button
                    type="submit"
                    class="admin-button admin-button--primary"
                >
                    Filter
                </button>

                <a
                    href="{{ route('admin.inquiries.index') }}"
                    class="admin-button"
                >
                    Reset
                </a>

            </div>

        </form>

    </section>


    {{-- TABLE --}}
    <section class="admin-card">

        <div class="admin-card__header">

            <div>
                <span class="admin-eyebrow">
                    MANAGE
                </span>

                <h2>
                    All Inquiries
                </h2>
            </div>

        </div>


        @if($inquiries->isNotEmpty())

            <div class="admin-table-wrapper">

                <table class="admin-table">

                    <thead>
                        <tr>

                            <th>
                                Name
                            </th>

                            <th>
                                Contact
                            </th>

                            <th>
                                Subject
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>
                    </thead>


                    <tbody>

                        @foreach($inquiries as $inquiry)

                            <tr>

                                <td>
                                    <strong>
                                        {{ $inquiry->name }}
                                    </strong>
                                </td>


                                <td>

                                    <div>
                                        {{ $inquiry->email }}
                                    </div>

                                    @if($inquiry->phone)
                                        <small>
                                            {{ $inquiry->phone }}
                                        </small>
                                    @endif

                                </td>


                                <td>
                                    {{ ucwords(str_replace('-', ' ', $inquiry->subject)) }}
                                </td>


                                <td>

                                    @if($inquiry->status === 'new')

                                        <span class="admin-badge admin-badge--danger">
                                            New
                                        </span>

                                    @elseif($inquiry->status === 'read')

                                        <span class="admin-badge admin-badge--warning">
                                            Read
                                        </span>

                                    @else

                                        <span class="admin-badge admin-badge--success">
                                            Replied
                                        </span>

                                    @endif

                                </td>


                                <td>
                                    {{ $inquiry->created_at->format('d M Y, h:i A') }}
                                </td>


                                <td>

                                    <div class="admin-table__actions">

                                        <a
                                            href="{{ route('admin.inquiries.show', $inquiry) }}"
                                            class="admin-icon-button"
                                            title="View"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1.5 12S5 5 12 5s10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            <span class="admin-sr-only">View</span>
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.inquiries.destroy', $inquiry) }}"
                                            onsubmit="return confirm('Delete this inquiry? It can be restored later if needed.')"
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


            @if($inquiries->hasPages())

                <div class="admin-pagination">
                    {{ $inquiries->links() }}
                </div>

            @endif

        @else

            <div class="admin-empty">

                <div class="admin-empty__icon">
                    #
                </div>

                <h3>
                    No inquiries found
                </h3>

                <p>
                    Customer enquiries submitted from the website will show up here.
                </p>

            </div>

        @endif

    </section>

</div>

@endsection
