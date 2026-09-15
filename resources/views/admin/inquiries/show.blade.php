@extends('admin.layouts.app')

@section('title', 'Inquiry Details')

@section('content')

<div class="container-fluid inquiry-show-page">
        <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="h3 mb-1">
                Inquiry Details
            </h1>

            <p class="text-muted mb-0">
                View and manage customer enquiry.
            </p>

        </div>


        <a
            href="{{ route('admin.inquiries.index') }}"
            class="btn btn-outline-secondary"
        >
            ← Back
        </a>

    </div>


    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    <div class="row g-4">

        {{-- MAIN --}}
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">

                    <div class="mb-4">

                        <small class="text-muted">
                            Subject
                        </small>

                        <h3 class="mt-1">
                            {{ ucwords(str_replace('-', ' ', $inquiry->subject)) }}
                        </h3>

                    </div>


                    <div class="mb-4">

                        <small class="text-muted">
                            Message
                        </small>

                        <div class="mt-2 p-3 bg-light rounded">
                            {!! nl2br(e($inquiry->message)) !!}
                        </div>

                    </div>


                    <div>

                        <small class="text-muted">
                            Submitted
                        </small>

                        <div class="mt-1">
                            {{ $inquiry->created_at->format('d M Y, h:i A') }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- SIDEBAR --}}
        <div class="col-lg-4">

            {{-- CUSTOMER --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body">

                    <h5 class="mb-4">
                        Customer
                    </h5>


                    <div class="mb-3">

                        <small class="text-muted">
                            Name
                        </small>

                        <div class="fw-semibold">
                            {{ $inquiry->name }}
                        </div>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted">
                            Email
                        </small>

                        <div>
                            <a href="mailto:{{ $inquiry->email }}">
                                {{ $inquiry->email }}
                            </a>
                        </div>

                    </div>


                    @if($inquiry->phone)

                        <div>

                            <small class="text-muted">
                                Phone
                            </small>

                            <div>
                                <a href="tel:{{ $inquiry->phone }}">
                                    {{ $inquiry->phone }}
                                </a>
                            </div>

                        </div>

                    @endif

                </div>

            </div>


            {{-- STATUS --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body">

                    <h5 class="mb-3">
                        Status
                    </h5>


                    <form
                        method="POST"
                        action="{{ route('admin.inquiries.status', $inquiry) }}"
                    >

                        @csrf
                        @method('PATCH')


                        <select
                            name="status"
                            class="form-select mb-3"
                        >

                            <option
                                value="new"
                                @selected($inquiry->status === 'new')
                            >
                                New
                            </option>

                            <option
                                value="read"
                                @selected($inquiry->status === 'read')
                            >
                                Read
                            </option>

                            <option
                                value="replied"
                                @selected($inquiry->status === 'replied')
                            >
                                Replied
                            </option>

                        </select>


                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Update Status
                        </button>

                    </form>

                </div>

            </div>


            {{-- DELETE --}}
            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <form
                        method="POST"
                        action="{{ route('admin.inquiries.destroy', $inquiry) }}"
                        onsubmit="return confirm('Are you sure you want to delete this inquiry?');"
                    >

                        @csrf
                        @method('DELETE')


                        <button
                            type="submit"
                            class="btn btn-outline-danger w-100"
                        >
                            Delete Inquiry
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection