@csrf

@if(isset($departure))
    @method('PUT')
@endif

@if($errors->any())
    <div class="admin-alert admin-alert--danger">
        Please correct the highlighted fields.
    </div>
@endif

<section class="admin-card">

    <div class="admin-card__header">
        <div>
            <span class="admin-eyebrow">SCHEDULE</span>
            <h2>Departure details</h2>
        </div>
    </div>

    <div class="admin-form-grid admin-form-grid--three">

        {{-- Departure Date --}}
        <div class="admin-form-group">
            <label for="departure_date">
                Departure date <span>*</span>
            </label>

            <input
                id="departure_date"
                type="date"
                name="departure_date"
                value="{{ old('departure_date', isset($departure) ? $departure->departure_date->format('Y-m-d') : '') }}"
                required
            >

            @error('departure_date')
                <small class="admin-form-error">{{ $message }}</small>
            @enderror
        </div>

        {{-- Return Date --}}
        <div class="admin-form-group">
            <label for="return_date">
                Return date <span>*</span>
            </label>

            <input
                id="return_date"
                type="date"
                name="return_date"
                value="{{ old('return_date', isset($departure) ? $departure->return_date->format('Y-m-d') : '') }}"
                required
            >

            @error('return_date')
                <small class="admin-form-error">{{ $message }}</small>
            @enderror
        </div>

        {{-- Seat Capacity --}}
        <div class="admin-form-group">
            <label for="capacity">
                Seat capacity <span>*</span>
            </label>

            <input
                id="capacity"
                type="number"
                min="1"
                max="1000"
                name="capacity"
                value="{{ old('capacity', $departure->capacity ?? '') }}"
                required
            >

            @error('capacity')
                <small class="admin-form-error">{{ $message }}</small>
            @enderror
        </div>

        {{-- Regular Price --}}
        <div class="admin-form-group">
            <label for="price">
                Regular price <span>*</span>
            </label>

            <input
                id="price"
                type="number"
                step="0.01"
                min="0"
                name="price"
                value="{{ old('price', $departure->price ?? '') }}"
                required
            >

            @error('price')
                <small class="admin-form-error">{{ $message }}</small>
            @enderror
        </div>

        {{-- Sale Price --}}
        <div class="admin-form-group">
            <label for="sale_price">
                Sale price
            </label>

            <input
                id="sale_price"
                type="number"
                step="0.01"
                min="0"
                name="sale_price"
                value="{{ old('sale_price', $departure->sale_price ?? '') }}"
            >

            @error('sale_price')
                <small class="admin-form-error">{{ $message }}</small>
            @enderror
        </div>

        {{-- Currency --}}
        <div class="admin-form-group">
            <label for="currency">
                Currency <span>*</span>
            </label>

            <input
                id="currency"
                type="text"
                name="currency"
                maxlength="3"
                value="{{ old('currency', $departure->currency ?? 'INR') }}"
                required
            >

            @error('currency')
                <small class="admin-form-error">{{ $message }}</small>
            @enderror
        </div>

        {{-- Meeting Point --}}
        <div class="admin-form-group admin-form-group--full">
            <label for="meeting_point">
                Meeting point
            </label>

            <input
                id="meeting_point"
                type="text"
                name="meeting_point"
                maxlength="255"
                value="{{ old('meeting_point', $departure->meeting_point ?? '') }}"
                placeholder="e.g. Hyderabad airport terminal 1"
            >

            @error('meeting_point')
                <small class="admin-form-error">{{ $message }}</small>
            @enderror
        </div>

        {{-- Status --}}
        <div class="admin-form-group">
            <label for="status">
                Status <span>*</span>
            </label>

            <select id="status" name="status" required>

                @foreach([
                    'open' => 'Open for booking',
                    'closed' => 'Closed',
                    'cancelled' => 'Cancelled',
                ] as $value => $label)

                    <option
                        value="{{ $value }}"
                        @selected(old('status', $departure->status ?? 'open') === $value)
                    >
                        {{ $label }}
                    </option>

                @endforeach

            </select>

            @error('status')
                <small class="admin-form-error">{{ $message }}</small>
            @enderror
        </div>

    </div>

</section>

<div class="admin-page__actions">

    <a
        href="{{ route('admin.tours.departures.index', $tour) }}"
        class="admin-button"
    >
        Cancel
    </a>

    <button
        type="submit"
        class="admin-button admin-button--dark"
    >
        {{ isset($departure) ? 'Update departure' : 'Create departure' }}
    </button>

</div>