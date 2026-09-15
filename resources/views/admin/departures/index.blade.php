@extends('admin.layouts.app')

@section('title', 'Tour Departures')

@section('content')
<div class="admin-page">
    <div class="admin-page__header">
        <div>
            <span class="admin-eyebrow">TOURS / DEPARTURES</span>
            <h1 class="admin-page__title">{{ $tour->name }}</h1>
            <p class="admin-page__description">Create bookable dates, seat capacity, and public pricing.</p>
        </div>
        <div class="admin-page__actions">
            <a href="{{ route('admin.tours.edit', $tour) }}" class="admin-button">Back to tour</a>
            <a href="{{ route('admin.tours.departures.create', $tour) }}" class="admin-button admin-button--dark">+ Add departure</a>
        </div>
    </div>

    @if(session('success'))<div class="admin-alert admin-alert--success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="admin-alert admin-alert--danger">{{ session('error') }}</div>@endif

    <section class="admin-card">
        @if($departures->isNotEmpty())
            <div class="admin-table-wrapper"><table class="admin-table">
                <thead><tr><th>Departure</th><th>Return</th><th>Price</th><th>Seats</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach($departures as $departure)
                        <tr>
                            <td>{{ $departure->departure_date->format('d M Y') }}</td>
                            <td>{{ $departure->return_date->format('d M Y') }}</td>
                            <td>₹{{ number_format((float) $departure->effective_price, 2) }}</td>
                            <td>{{ $departure->available_seats }} / {{ $departure->capacity }}</td>
                            <td><span class="admin-badge {{ $departure->status === 'open' ? 'admin-badge--success' : '' }}">{{ Str::headline($departure->status) }}</span></td>
                            <td>
                                <a href="{{ route('admin.tours.departures.edit', [$tour, $departure]) }}" class="admin-table-action">Edit</a>
                                @if(!$departure->bookings()->exists())
                                    <form method="POST" action="{{ route('admin.tours.departures.destroy', [$tour, $departure]) }}" class="admin-inline-form" onsubmit="return confirm('Delete this departure?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="admin-table-action">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table></div>
            {{ $departures->links() }}
        @else
            <p>No departures have been added yet.</p>
        @endif
    </section>
</div>
@endsection
