@extends('admin.layouts.app')

@section('title', 'Inquiries')

@section('content')

<div class="container-fluid inquiries-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Inquiries</h1>

            <p class="text-muted mb-0">
                Manage customer enquiries received from the website.
            </p>
        </div>
    </div>


    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    {{-- STATS --}}
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Total</small>

                    <h3 class="mb-0">
                        {{ $counts['all'] }}
                    </h3>
                </div>
            </div>
        </div>


        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">New</small>

                    <h3 class="mb-0">
                        {{ $counts['new'] }}
                    </h3>
                </div>
            </div>
        </div>


        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Read</small>

                    <h3 class="mb-0">
                        {{ $counts['read'] }}
                    </h3>
                </div>
            </div>
        </div>


        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Replied</small>

                    <h3 class="mb-0">
                        {{ $counts['replied'] }}
                    </h3>
                </div>
            </div>
        </div>

    </div>


    {{-- FILTER --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('admin.inquiries.index') }}">

                <div class="row g-3">

                    <div class="col-md-6">

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Search name, email, phone..."
                        >

                    </div>


                    <div class="col-md-3">

                        <select
                            name="status"
                            class="form-select"
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


                    <div class="col-md-3">

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Filter
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- TABLE --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead>
                        <tr>

                            <th class="px-4">
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

                            <th class="text-end px-4">
                                Action
                            </th>

                        </tr>
                    </thead>


                    <tbody>

                        @forelse($inquiries as $inquiry)

                            <tr>

                                <td class="px-4">

                                    <strong>
                                        {{ $inquiry->name }}
                                    </strong>

                                </td>


                                <td>

                                    <div>
                                        {{ $inquiry->email }}
                                    </div>

                                    @if($inquiry->phone)
                                        <small class="text-muted">
                                            {{ $inquiry->phone }}
                                        </small>
                                    @endif

                                </td>


                                <td>
                                    {{ ucwords(str_replace('-', ' ', $inquiry->subject)) }}
                                </td>


                                <td>

                                    @if($inquiry->status === 'new')

                                        <span class="badge bg-danger">
                                            New
                                        </span>

                                    @elseif($inquiry->status === 'read')

                                        <span class="badge bg-warning text-dark">
                                            Read
                                        </span>

                                    @else

                                        <span class="badge bg-success">
                                            Replied
                                        </span>

                                    @endif

                                </td>


                                <td>
                                    {{ $inquiry->created_at->format('d M Y, h:i A') }}
                                </td>


                                <td class="text-end px-4">

                                    <a
                                        href="{{ route('admin.inquiries.show', $inquiry) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        View
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center py-5"
                                >

                                    <div class="text-muted">
                                        No inquiries found.
                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        @if($inquiries->hasPages())

            <div class="card-footer bg-white">
                {{ $inquiries->links() }}
            </div>

        @endif

    </div>

</div>

@endsection