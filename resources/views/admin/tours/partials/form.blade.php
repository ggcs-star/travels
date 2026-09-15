@csrf

@if(isset($tour))
    @method('PUT')
@endif


@if($errors->any())

    <div class="admin-alert admin-alert--danger">

        <strong>Please fix the following errors:</strong>

        <ul>

            @foreach($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif


{{-- ================================================================
     STEP 01 — BASIC INFORMATION
================================================================ --}}

<section class="admin-card">

    <div class="admin-card__header">

        <div>

            <span class="admin-eyebrow">
                STEP 01
            </span>

            <h2>
                Basic Information
            </h2>

        </div>

    </div>


    <div class="admin-form-grid">

        {{-- Category --}}

        <div class="admin-form-group">

            <label for="category_id">
                Tour Category <span>*</span>
            </label>

            <select
                id="category_id"
                name="category_id"
                required
            >

                <option value="">
                    Select tour category
                </option>

                @foreach($categories ?? [] as $category)

                    <option
                        value="{{ $category->id }}"
                        @selected(
                            (string) old(
                                'category_id',
                                $tour->category_id ?? ''
                            ) === (string) $category->id
                        )
                    >
                        {{ $category->name }}
                    </option>

                @endforeach

            </select>

            <small>
                Choose where this package should appear on the website.
            </small>

            @error('category_id')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        {{-- Package Code --}}

        <div class="admin-form-group">

            <label for="package_code">
                Package Code <span>*</span>
            </label>

            <input
                type="text"
                id="package_code"
                name="package_code"
                value="{{ old('package_code', $tour->package_code ?? '') }}"
                placeholder="e.g. RJ-HT-001"
                maxlength="50"
                required
            >

            <small>
                Letters, numbers, hyphens or underscores.
            </small>

            @error('package_code')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        {{-- Package Name --}}

        <div class="admin-form-group admin-form-group--full">

            <label for="name">
                Package Name <span>*</span>
            </label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $tour->name ?? '') }}"
                placeholder="e.g. Rajasthan Heritage Tour"
                maxlength="255"
                required
            >

            @error('name')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        {{-- Slug --}}

        <div class="admin-form-group">

            <label for="slug">
                URL Slug <span>*</span>
            </label>

            <input
                type="text"
                id="slug"
                name="slug"
                value="{{ old('slug', $tour->slug ?? '') }}"
                placeholder="rajasthan-heritage-tour"
                maxlength="255"
                required
            >

            <small>
                Used for the package URL.
            </small>

            @error('slug')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        {{-- Destination --}}

        <div class="admin-form-group">

            <label for="destination">
                Destination <span>*</span>
            </label>

            <input
                type="text"
                id="destination"
                name="destination"
                value="{{ old('destination', $tour->destination ?? '') }}"
                placeholder="e.g. Rajasthan, India"
                maxlength="150"
                required
            >

            @error('destination')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        {{-- Starting City --}}

        <div class="admin-form-group">

            <label for="starting_city">
                Starting City <span>*</span>
            </label>

            <input
                type="text"
                id="starting_city"
                name="starting_city"
                value="{{ old('starting_city', $tour->starting_city ?? '') }}"
                placeholder="e.g. Jaipur"
                maxlength="150"
                required
            >

            @error('starting_city')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        {{-- Ending City --}}

        <div class="admin-form-group">

            <label for="ending_city">
                Ending City <span>*</span>
            </label>

            <input
                type="text"
                id="ending_city"
                name="ending_city"
                value="{{ old('ending_city', $tour->ending_city ?? '') }}"
                placeholder="e.g. Udaipur"
                maxlength="150"
                required
            >

            @error('ending_city')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>

    </div>

</section>


{{-- ================================================================
     STEP 02 — TOUR DETAILS
================================================================ --}}

<section class="admin-card">

    <div class="admin-card__header">

        <div>

            <span class="admin-eyebrow">
                STEP 02
            </span>

            <h2>
                Tour Details
            </h2>

        </div>

    </div>


    <div class="admin-form-grid admin-form-grid--three">

        {{-- Days --}}

        <div class="admin-form-group">

            <label for="duration_days">
                Duration Days <span>*</span>
            </label>

            <input
                type="number"
                id="duration_days"
                name="duration_days"
                value="{{ old('duration_days', $tour->duration_days ?? '') }}"
                min="1"
                max="365"
                required
            >

            @error('duration_days')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        {{-- Nights --}}

        <div class="admin-form-group">

            <label for="duration_nights">
                Duration Nights <span>*</span>
            </label>

            <input
                type="number"
                id="duration_nights"
                name="duration_nights"
                value="{{ old('duration_nights', $tour->duration_nights ?? '') }}"
                min="0"
                max="364"
                required
            >

            @error('duration_nights')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        {{-- Difficulty --}}

        <div class="admin-form-group">

            <label for="difficulty_level">
                Difficulty
            </label>

            <select
                id="difficulty_level"
                name="difficulty_level"
            >

                <option value="">
                    Select difficulty
                </option>

                @foreach([
                    'easy' => 'Easy',
                    'moderate' => 'Moderate',
                    'challenging' => 'Challenging',
                    'difficult' => 'Difficult',
                ] as $value => $label)

                    <option
                        value="{{ $value }}"
                        @selected(
                            old(
                                'difficulty_level',
                                $tour->difficulty_level ?? ''
                            ) === $value
                        )
                    >
                        {{ $label }}
                    </option>

                @endforeach

            </select>

            @error('difficulty_level')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>

    </div>

</section>


{{-- ================================================================
     STEP 03 — TRAVELER INFORMATION
================================================================ --}}

<section class="admin-card">

    <div class="admin-card__header">

        <div>

            <span class="admin-eyebrow">
                STEP 03
            </span>

            <h2>
                Traveler Information
            </h2>

        </div>

    </div>


    <div class="admin-form-grid admin-form-grid--three">

        <div class="admin-form-group">

            <label for="age_min">
                Minimum Age
            </label>

            <input
                type="number"
                id="age_min"
                name="age_min"
                value="{{ old('age_min', $tour->age_min ?? '') }}"
                min="0"
                max="100"
                placeholder="e.g. 5"
            >

            @error('age_min')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        <div class="admin-form-group">

            <label for="age_max">
                Maximum Age
            </label>

            <input
                type="number"
                id="age_max"
                name="age_max"
                value="{{ old('age_max', $tour->age_max ?? '') }}"
                min="0"
                max="100"
                placeholder="e.g. 70"
            >

            @error('age_max')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        <div class="admin-form-group">

            <label for="best_time">
                Best Time to Visit
            </label>

            <input
                type="text"
                id="best_time"
                name="best_time"
                value="{{ old('best_time', $tour->best_time ?? '') }}"
                placeholder="e.g. October to March"
                maxlength="255"
            >

            @error('best_time')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>

    </div>

</section>


{{-- ================================================================
     STEP 04 — DESCRIPTION
================================================================ --}}

<section class="admin-card">

    <div class="admin-card__header">

        <div>

            <span class="admin-eyebrow">
                STEP 04
            </span>

            <h2>
                Package Description
            </h2>

        </div>

    </div>


    <div class="admin-form-grid">

        <div class="admin-form-group admin-form-group--full">

            <label for="short_description">
                Short Description <span>*</span>
            </label>

            <textarea
                id="short_description"
                name="short_description"
                rows="4"
                maxlength="500"
                placeholder="Write a short summary of this tour package..."
                required
            >{{ old('short_description', $tour->short_description ?? '') }}</textarea>

            @error('short_description')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        <div class="admin-form-group admin-form-group--full">

            <label for="description">
                Detailed Description <span>*</span>
            </label>

            <textarea
                id="description"
                name="description"
                rows="10"
                maxlength="20000"
                placeholder="Describe the complete tour package..."
                required
            >{{ old('description', $tour->description ?? '') }}</textarea>

            @error('description')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>

    </div>

</section>


@php

    $highlights = old(
        'highlights',
        $tour->highlights ?? []
    );

    $includedItems = old(
        'included_items',
        $tour->included_items ?? []
    );

    $excludedItems = old(
        'excluded_items',
        $tour->excluded_items ?? []
    );

    $itinerary = old(
        'itinerary',
        $tour->itinerary ?? []
    );

    if (! is_array($itinerary)) {
        $itinerary = [];
    }

    $importantNotes = old(
        'important_notes',
        $tour->important_notes ?? ''
    );

    /*
     * Important Notes are stored as plain text, one point per line.
     * Normalize legacy array values here, outside the textarea, so Blade
     * never prints an @php block literally inside the field.
     */
    if (is_array($importantNotes)) {
        $importantNotesForForm = collect($importantNotes)
            ->map(function ($note) {
                if (is_array($note)) {
                    return trim((string) (
                        $note['description']
                        ?? $note['content']
                        ?? $note['text']
                        ?? $note['title']
                        ?? ''
                    ));
                }

                return trim((string) $note);
            })
            ->filter()
            ->implode("\n");
    } else {
        $importantNotesForForm = (string) $importantNotes;
    }

    $termsConditions = old(
        'terms_conditions',
        $tour->terms_conditions ?? ''
    );

    $cancellationPolicy = old(
        'cancellation_policy',
        $tour->cancellation_policy ?? ''
    );

    $privacyPolicy = old(
        'privacy_policy',
        $tour->privacy_policy ?? ''
    );

@endphp


{{-- ================================================================
     STEP 05 — PACKAGE CONTENT
================================================================ --}}

<section class="admin-card">

    <div class="admin-card__header">

        <div>

            <span class="admin-eyebrow">
                STEP 05
            </span>

            <h2>
                Package Content
            </h2>

        </div>

    </div>


    <div class="admin-form-grid">

        <div class="admin-form-group admin-form-group--full">

            <label for="highlights">
                Tour Highlights
            </label>

            <textarea
                id="highlights"
                name="highlights"
                rows="6"
                placeholder="One highlight per line&#10;Visit Amber Fort&#10;Explore Udaipur City Palace&#10;Sunset at Lake Pichola"
            >{{ is_array($highlights) ? implode("\n", $highlights) : $highlights }}</textarea>

            <small>
                Enter one highlight per line.
            </small>

            @error('highlights')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        <div class="admin-form-group">

            <label for="included_items">
                What's Included
            </label>

            <textarea
                id="included_items"
                name="included_items"
                rows="7"
                placeholder="One item per line&#10;Hotel accommodation&#10;Daily breakfast&#10;Airport transfers"
            >{{ is_array($includedItems) ? implode("\n", $includedItems) : $includedItems }}</textarea>

            <small>
                Enter one item per line.
            </small>

            @error('included_items')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        <div class="admin-form-group">

            <label for="excluded_items">
                What's Not Included
            </label>

            <textarea
                id="excluded_items"
                name="excluded_items"
                rows="7"
                placeholder="One item per line&#10;Personal expenses&#10;Travel insurance&#10;Optional activities"
            >{{ is_array($excludedItems) ? implode("\n", $excludedItems) : $excludedItems }}</textarea>

            <small>
                Enter one item per line.
            </small>

            @error('excluded_items')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>

    </div>

</section>


{{-- ================================================================
     STEP 06 — ITINERARY
================================================================ --}}

<section class="admin-card">

    <div class="admin-card__header">
        <div>
            <span class="admin-eyebrow">STEP 06</span>
            <h2>Tour Itinerary</h2>
            <p>
                Add each day with title, destination/places covered, a day image,
                description and activities. Day image and location are used only
                in the generated PDF; the public tour page continues to show its
                existing itinerary design.
            </p>
        </div>
    </div>

    <div id="tour-itinerary" class="tour-itinerary" data-itinerary-container>

        <div class="tour-itinerary__list" data-itinerary-list>

            @forelse($itinerary as $index => $day)

                @php
                    $dayNumber = (int) ($day['day'] ?? ($index + 1));
                    $dayTitle = $day['title'] ?? $day['name'] ?? '';
                    $dayLocation = $day['location'] ?? $day['places'] ?? '';
                    $dayDescription = $day['description'] ?? $day['details'] ?? '';
                    $dayImage = $day['image'] ?? $day['image_path'] ?? '';
                    $activities = $day['activities'] ?? $day['items'] ?? [];

                    if (is_string($activities)) {
                        $activities = preg_split('/\R/', $activities, -1, PREG_SPLIT_NO_EMPTY);
                    }

                    if (! is_array($activities)) {
                        $activities = [];
                    }
                @endphp

                <div class="tour-itinerary-day" data-itinerary-day>

                    <input
                        type="hidden"
                        name="itinerary[{{ $index }}][day]"
                        value="{{ $dayNumber }}"
                        data-itinerary-day-number
                    >

                    <div class="tour-itinerary-day__header">
                        <div>
                            <span class="tour-itinerary-day__number" data-itinerary-day-label>
                                Day {{ $dayNumber }}
                            </span>
                            <strong>Day Itinerary</strong>
                        </div>

                        <button
                            type="button"
                            class="admin-button admin-button--danger"
                            data-remove-itinerary-day
                        >
                            Remove Day
                        </button>
                    </div>

                    <div class="admin-form-grid">

                        <div class="admin-form-group admin-form-group--full">
                            <label>
                                Day Title <span>*</span>
                            </label>
                            <input
                                type="text"
                                name="itinerary[{{ $index }}][title]"
                                value="{{ $dayTitle }}"
                                maxlength="255"
                                placeholder="e.g. Kashi Vishwanath Temple & Spiritual Varanasi"
                                required
                            >
                            @error("itinerary.$index.title")
                                <small class="admin-form-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label>Location / Places Covered</label>
                            <input
                                type="text"
                                name="itinerary[{{ $index }}][location]"
                                value="{{ $dayLocation }}"
                                maxlength="500"
                                placeholder="e.g. Varanasi – Kashi Vishwanath, Vishalakshi & Annapurna"
                            >
                            <small>This is shown in the PDF only.</small>
                            @error("itinerary.$index.location")
                                <small class="admin-form-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label>Day Image</label>
                            <input
                                type="file"
                                name="itinerary[{{ $index }}][image]"
                                accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif"
                                data-itinerary-image-input
                            >
                            <small>JPG, PNG, WebP or AVIF · max 5 MB · PDF only.</small>

                            @if($dayImage)
                                @php
                                    $previewUrl = \Illuminate\Support\Str::startsWith($dayImage, ['http://', 'https://', '//'])
                                        ? $dayImage
                                        : \Illuminate\Support\Facades\Storage::disk('public')->url(ltrim($dayImage, '/'));
                                @endphp
                                <div class="tour-itinerary-image-preview">
                                    <img src="{{ $previewUrl }}" alt="Day {{ $dayNumber }}">
                                    <span>Current PDF image</span>
                                </div>
                            @endif

                            <div class="tour-itinerary-image-preview tour-itinerary-image-preview--new" data-itinerary-image-preview hidden>
                                <img src="" alt="Selected day image" data-itinerary-image-preview-img>
                                <span>New image selected</span>
                            </div>

                            @error("itinerary.$index.image")
                                <small class="admin-form-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="admin-form-group admin-form-group--full">
                            <label>Day Description</label>
                            <textarea
                                name="itinerary[{{ $index }}][description]"
                                rows="5"
                                maxlength="10000"
                                placeholder="Describe the day's travel, sightseeing, meals, hotel and other details..."
                            >{{ $dayDescription }}</textarea>
                            @error("itinerary.$index.description")
                                <small class="admin-form-error">{{ $message }}</small>
                            @enderror
                        </div>

                    </div>

                    <div class="tour-itinerary-activities">
                        <div class="tour-itinerary-activities__header">
                            <div>
                                <strong>Activities / Schedule</strong>
                                <small>Add one activity or schedule item per row.</small>
                            </div>
                            <button type="button" class="admin-button" data-add-itinerary-activity>
                                + Add Activity
                            </button>
                        </div>

                        <div data-itinerary-activities>
                            @forelse($activities as $activityIndex => $activity)
                                @php
                                    if (is_array($activity)) {
                                        $activity = $activity['title'] ?? $activity['name'] ?? $activity['text'] ?? '';
                                    }
                                @endphp
                                <div class="tour-itinerary-activity" data-itinerary-activity>
                                    <input
                                        type="text"
                                        name="itinerary[{{ $index }}][activities][{{ $activityIndex }}]"
                                        value="{{ $activity }}"
                                        maxlength="500"
                                        placeholder="e.g. Visit Kashi Vishwanath Temple"
                                    >
                                    <button
                                        type="button"
                                        class="admin-button admin-button--danger"
                                        data-remove-itinerary-activity
                                        title="Remove activity"
                                    >×</button>
                                </div>
                            @empty
                                <div class="tour-itinerary-activity" data-itinerary-activity>
                                    <input
                                        type="text"
                                        name="itinerary[{{ $index }}][activities][0]"
                                        value=""
                                        maxlength="500"
                                        placeholder="e.g. Visit Kashi Vishwanath Temple"
                                    >
                                    <button
                                        type="button"
                                        class="admin-button admin-button--danger"
                                        data-remove-itinerary-activity
                                        title="Remove activity"
                                    >×</button>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

            @empty

                <div class="tour-itinerary-day" data-itinerary-day>
                    <input type="hidden" name="itinerary[0][day]" value="1" data-itinerary-day-number>

                    <div class="tour-itinerary-day__header">
                        <div>
                            <span class="tour-itinerary-day__number" data-itinerary-day-label>Day 1</span>
                            <strong>Day Itinerary</strong>
                        </div>
                        <button type="button" class="admin-button admin-button--danger" data-remove-itinerary-day>
                            Remove Day
                        </button>
                    </div>

                    <div class="admin-form-grid">
                        <div class="admin-form-group admin-form-group--full">
                            <label>Day Title <span>*</span></label>
                            <input
                                type="text"
                                name="itinerary[0][title]"
                                maxlength="255"
                                placeholder="e.g. Arrival in Varanasi & Ganga Aarti"
                                required
                            >
                        </div>

                        <div class="admin-form-group">
                            <label>Location / Places Covered</label>
                            <input
                                type="text"
                                name="itinerary[0][location]"
                                maxlength="500"
                                placeholder="e.g. Varanasi – Dashashwamedh Ghat"
                            >
                            <small>This is shown in the PDF only.</small>
                        </div>

                        <div class="admin-form-group">
                            <label>Day Image</label>
                            <input
                                type="file"
                                name="itinerary[0][image]"
                                accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif"
                                data-itinerary-image-input
                            >
                            <small>JPG, PNG, WebP or AVIF · max 5 MB · PDF only.</small>
                            <div class="tour-itinerary-image-preview tour-itinerary-image-preview--new" data-itinerary-image-preview hidden>
                                <img src="" alt="Selected day image" data-itinerary-image-preview-img>
                                <span>New image selected</span>
                            </div>
                        </div>

                        <div class="admin-form-group admin-form-group--full">
                            <label>Day Description</label>
                            <textarea
                                name="itinerary[0][description]"
                                rows="5"
                                maxlength="10000"
                                placeholder="Describe the day's travel, sightseeing, meals, hotel and other details..."
                            ></textarea>
                        </div>
                    </div>

                    <div class="tour-itinerary-activities">
                        <div class="tour-itinerary-activities__header">
                            <div>
                                <strong>Activities / Schedule</strong>
                                <small>Add one activity or schedule item per row.</small>
                            </div>
                            <button type="button" class="admin-button" data-add-itinerary-activity>+ Add Activity</button>
                        </div>
                        <div data-itinerary-activities>
                            <div class="tour-itinerary-activity" data-itinerary-activity>
                                <input
                                    type="text"
                                    name="itinerary[0][activities][0]"
                                    maxlength="500"
                                    placeholder="e.g. Airport pickup and hotel check-in"
                                >
                                <button type="button" class="admin-button admin-button--danger" data-remove-itinerary-activity title="Remove activity">×</button>
                            </div>
                        </div>
                    </div>
                </div>

            @endforelse

        </div>

        <div class="tour-itinerary__actions">
            <button type="button" class="admin-button admin-button--dark" data-add-itinerary-day>
                + Add Another Day
            </button>
            <small>Add all days that should appear in the itinerary PDF.</small>
        </div>

    </div>

    @error('itinerary')
        <small class="admin-form-error">{{ $message }}</small>
    @enderror

</section>

{{-- ================================================================
     STEP 07 — IMPORTANT NOTES & POLICIES
================================================================ --}}

<section class="admin-card">

    <div class="admin-card__header">
        <div>
            <span class="admin-eyebrow">STEP 07</span>
            <h2>Important Notes & Policies</h2>
            <p>
                These sections are displayed as separate modules on the
                public tour page and are also included in the dynamic PDF.
            </p>
        </div>
    </div>

    <div class="admin-form-grid">

        <div class="admin-form-group admin-form-group--full">
            <label for="important_notes">
                Important Notes
            </label>

            <textarea
                id="important_notes"
                name="important_notes"
                rows="10"
                maxlength="30000"
                placeholder="Enter ONE short Important Note per line. Keep each point short enough to display in about 1–2 lines on the website.

Example:
Tours may be cancelled, postponed, or modified due to natural calamities, extreme weather, government restrictions, political disturbances, or other unforeseen circumstances.
SSB Travelz will not be held responsible for delays, cancellations, or changes caused by airlines, helicopter, hotels, transport providers, or other third parties.
Any additional expenses arising due to unforeseen circumstances must be borne by the traveler."
            >{{ $importantNotesForForm }}</textarea>

            <small class="important-notes-admin-help">
                <strong>SSB-style format:</strong> Enter one short point per line. Do not add
                <code>&lt;h3&gt;</code>, <code>&lt;p&gt;</code>, <code>&lt;ul&gt;</code> or
                <code>&lt;li&gt;</code> tags here. The frontend automatically converts each line
                into a numbered yellow card.
            </small>

            @error('important_notes')
                <small class="admin-form-error">
                    {{ $message }}
                </small>
            @enderror
        </div>


        <div class="admin-form-group admin-form-group--full">
            <label for="terms_conditions">
                Terms & Conditions
            </label>

            <textarea
                id="terms_conditions"
                name="terms_conditions"
                rows="10"
                maxlength="30000"
                placeholder="Enter the terms and conditions applicable to this tour package..."
            >{{ $termsConditions }}</textarea>

            <small>
                This content will appear under the Terms & Conditions module
                and in the PDF.
            </small>

            @error('terms_conditions')
                <small class="admin-form-error">
                    {{ $message }}
                </small>
            @enderror
        </div>


        <div class="admin-form-group admin-form-group--full">
            <label for="cancellation_policy">
                Cancellation Policy
            </label>

            <textarea
                id="cancellation_policy"
                name="cancellation_policy"
                rows="10"
                maxlength="30000"
                placeholder="Enter cancellation charges, refund rules, timelines and other cancellation conditions..."
            >{{ $cancellationPolicy }}</textarea>

            <small>
                Keep the policy clear and customer-friendly.
            </small>

            @error('cancellation_policy')
                <small class="admin-form-error">
                    {{ $message }}
                </small>
            @enderror
        </div>


        <div class="admin-form-group admin-form-group--full">
            <label for="privacy_policy">
                Privacy Policy
            </label>

            <textarea
                id="privacy_policy"
                name="privacy_policy"
                rows="10"
                maxlength="30000"
                placeholder="Enter the privacy information applicable to bookings and this tour package..."
            >{{ $privacyPolicy }}</textarea>

            <small>
                This tour-specific privacy content will also be available in
                the generated PDF.
            </small>

            @error('privacy_policy')
                <small class="admin-form-error">
                    {{ $message }}
                </small>
            @enderror
        </div>

    </div>

</section>


{{-- ================================================================
     STEP 08 — IMAGES
================================================================ --}}

<section class="admin-card">

    <div class="admin-card__header">

        <div>

            <span class="admin-eyebrow">
                STEP 06
            </span>

            <h2>
                Package Images
            </h2>

        </div>

    </div>


    <div class="admin-form-grid">

        {{-- Cover --}}

        <div class="admin-form-group admin-form-group--full">

            <label for="cover_image">

                Main Package Image

                @if(!isset($tour))
                    <span>*</span>
                @endif

            </label>

            <input
                type="file"
                id="cover_image"
                name="cover_image"
                accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif"
                {{ isset($tour) ? '' : 'required' }}
            >

            <small>
                JPG, PNG, WebP or AVIF.
                Maximum 5 MB.
                Recommended landscape image.
            </small>


            @if(isset($tour) && $tour->cover_image_url)

                <div class="admin-image-preview">

                    <img
                        src="{{ $tour->cover_image_url }}"
                        alt="{{ $tour->name }}"
                    >

                    <label class="admin-checkbox">

                        <input
                            type="checkbox"
                            name="remove_cover_image"
                            value="1"
                            @checked(old('remove_cover_image'))
                        >

                        <span>
                            Remove current main image
                        </span>

                    </label>

                </div>

            @endif


            @error('cover_image')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        {{-- Gallery --}}

        <div class="admin-form-group admin-form-group--full">

            <label for="gallery">
                Gallery Images
            </label>

            <input
                type="file"
                id="gallery"
                name="gallery[]"
                accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif"
                multiple
            >

            <small>
                Upload up to 20 gallery images.
                Each image can be up to 5 MB.
            </small>

            @error('gallery')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

            @error('gallery.*')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        {{-- Existing gallery --}}

    @if(isset($tour) && $tour->images && $tour->images->isNotEmpty())

    <div class="admin-form-group admin-form-group--full">

        <div class="admin-gallery-heading">
            <div>
                <label>Current Gallery</label>
                <small>
                    Manage your existing package images.
                </small>
            </div>
        </div>

        <div class="admin-tour-gallery">

            @foreach($tour->images->sortBy('sort_order') as $image)

                <div class="admin-tour-gallery__item">

                    <div class="admin-tour-gallery__image-wrap">

                        <img
                            src="{{ $image->image_url }}"
                            alt="{{ $image->alt_text ?: $tour->name }}"
                        >

                        {{-- UI ONLY --}}
        <button
    type="button"
    class="admin-tour-gallery__remove"
    title="Remove image"
    data-gallery-remove
    data-image-id="{{ $image->id }}"
>
    <span>×</span>
</button>
                    </div>

                    <div class="admin-tour-gallery__footer">

                        <span>
                            Position {{ $image->sort_order + 1 }}
                        </span>

                        <span class="admin-tour-gallery__remove-label">
                            Remove
                        </span>

                    </div>

                </div>

            @endforeach

        </div>

    </div>

@endif

    </div>

</section>


{{-- ================================================================
     STEP 09 — SEO
================================================================ --}}

<section class="admin-card">

    <div class="admin-card__header">

        <div>

            <span class="admin-eyebrow">
                STEP 07
            </span>

            <h2>
                Search Engine Optimization
            </h2>

            <p>
                Control how this package appears in search engines.
            </p>

        </div>

    </div>


    <div class="admin-form-grid">

        {{-- SEO Key --}}

        <div class="admin-form-group">

            <label for="seo_key">
                SEO Key <span>*</span>
            </label>

            <input
                type="text"
                id="seo_key"
                name="seo_key"
                value="{{ old('seo_key', $tour->seo_key ?? '') }}"
                placeholder="rajasthan-heritage-tour"
                maxlength="180"
                required
            >

            <small>
                Unique SEO identifier.
            </small>

            @error('seo_key')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        {{-- Robots --}}

        <div class="admin-form-group">

            <label for="robots">
                Robots
            </label>

            @php

                $robots = old(
                    'robots',
                    $tour->robots ?? 'index,follow'
                );

            @endphp

            <select
                id="robots"
                name="robots"
            >

                <option
                    value="index,follow"
                    @selected($robots === 'index,follow')
                >
                    Index, Follow
                </option>

                <option
                    value="index,nofollow"
                    @selected($robots === 'index,nofollow')
                >
                    Index, Nofollow
                </option>

                <option
                    value="noindex,follow"
                    @selected($robots === 'noindex,follow')
                >
                    Noindex, Follow
                </option>

                <option
                    value="noindex,nofollow"
                    @selected($robots === 'noindex,nofollow')
                >
                    Noindex, Nofollow
                </option>

            </select>

            @error('robots')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        {{-- Meta Title --}}

        <div class="admin-form-group admin-form-group--full">

            <label for="meta_title">
                Meta Title
            </label>

            <input
                type="text"
                id="meta_title"
                name="meta_title"
                value="{{ old('meta_title', $tour->meta_title ?? '') }}"
                placeholder="Rajasthan Heritage Tour | Your Travel Brand"
                maxlength="255"
            >

            <small>
                Recommended: around 50–60 characters.
            </small>

            @error('meta_title')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        {{-- Meta Description --}}

        <div class="admin-form-group admin-form-group--full">

            <label for="meta_description">
                Meta Description
            </label>

            <textarea
                id="meta_description"
                name="meta_description"
                rows="4"
                maxlength="500"
                placeholder="Explore Rajasthan with our curated heritage tour..."
            >{{ old('meta_description', $tour->meta_description ?? '') }}</textarea>

            <small>
                Recommended: around 150–160 characters.
            </small>

            @error('meta_description')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        {{-- Keywords --}}

        <div class="admin-form-group admin-form-group--full">

            <label for="meta_keywords">
                Meta Keywords
            </label>

            <textarea
                id="meta_keywords"
                name="meta_keywords"
                rows="3"
                maxlength="1000"
                placeholder="rajasthan tour, heritage tour, jaipur tour, udaipur tour"
            >{{ old('meta_keywords', $tour->meta_keywords ?? '') }}</textarea>

            <small>
                Comma-separated keywords.
            </small>

            @error('meta_keywords')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        {{-- Canonical --}}

        <div class="admin-form-group admin-form-group--full">

            <label for="canonical_url">
                Canonical URL
            </label>

            <input
                type="url"
                id="canonical_url"
                name="canonical_url"
                value="{{ old('canonical_url', $tour->canonical_url ?? '') }}"
                placeholder="https://example.com/tours/rajasthan-heritage-tour"
                maxlength="500"
            >

            <small>
                Leave empty to automatically use the package URL.
            </small>

            @error('canonical_url')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>

    </div>

</section>


{{-- ================================================================
     STEP 10 — VISIBILITY
================================================================ --}}

<section class="admin-card">

    <div class="admin-card__header">

        <div>

            <span class="admin-eyebrow">
                STEP 08
            </span>

            <h2>
                Visibility & Status
            </h2>

        </div>

    </div>


    @php

        $currentStatus = old(
            'status',
            $tour->status ?? 'draft'
        );

        $isFeatured = old(
            'featured',
            $tour->featured ?? false
        );

    @endphp


    <div class="admin-form-grid">

        {{-- Status --}}

        <div class="admin-form-group">

            <label for="status">
                Status <span>*</span>
            </label>

            <select
                id="status"
                name="status"
                required
            >

                <option
                    value="draft"
                    @selected($currentStatus === 'draft')
                >
                    Draft
                </option>

                <option
                    value="published"
                    @selected($currentStatus === 'published')
                >
                    Published
                </option>

                <option
                    value="inactive"
                    @selected($currentStatus === 'inactive')
                >
                    Inactive
                </option>

            </select>

            @error('status')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>


        {{-- Featured --}}

        <div class="admin-form-group">

            <label>
                Featured Package
            </label>

            <label class="admin-checkbox">

                <input
                    type="checkbox"
                    name="featured"
                    value="1"
                    @checked($isFeatured)
                >

                <span>
                    Show this package as featured
                </span>

            </label>

            @error('featured')

                <small class="admin-form-error">
                    {{ $message }}
                </small>

            @enderror

        </div>

    </div>

</section>


{{-- ================================================================
     STEP 11 — TOUR DEPARTURES
================================================================ --}}

@php

    $oldDepartures = old('departures');

    if (is_array($oldDepartures) && count($oldDepartures)) {

        $departureRows = $oldDepartures;

    } elseif (
        isset($tour)
        && $tour->departures
        && $tour->departures->isNotEmpty()
    ) {

        $departureRows = $tour->departures
            ->sortBy('departure_date')
            ->map(function ($departure) {

                return [
                    'id' => $departure->id,
                    'departure_date' => optional(
                        $departure->departure_date
                    )->format('Y-m-d'),

                    'return_date' => optional(
                        $departure->return_date
                    )->format('Y-m-d'),

                    'capacity' => $departure->capacity,

                    'price' => $departure->price,

                    'sale_price' => $departure->sale_price,

                    'currency' => $departure->currency ?: 'INR',

                    'meeting_point' => $departure->meeting_point,

                    'status' => $departure->status ?: 'open',
                ];

            })
            ->values()
            ->all();

    } else {

        $departureRows = [
            [
                'id' => null,
                'departure_date' => '',
                'return_date' => '',
                'capacity' => '',
                'price' => '',
                'sale_price' => '',
                'currency' => 'INR',
                'meeting_point' => '',
                'status' => 'open',
            ],
        ];

    }

@endphp


<section class="admin-card">

    <div class="admin-card__header">

        <div>

            <span class="admin-eyebrow">
                STEP 09
            </span>

            <h2>
                Tour Departures
            </h2>

            <p>
                Add future departure dates, capacity and pricing for this package.
            </p>

        </div>

    </div>


    <div
        id="tour-departures"
        class="tour-departures"
    >

        @foreach($departureRows as $index => $departure)

            <div
                class="tour-departure-card"
                data-departure-row
            >

                @if(!empty($departure['id']))

                    <input
                        type="hidden"
                        name="departures[{{ $index }}][id]"
                        value="{{ $departure['id'] }}"
                    >

                @endif


                <div class="tour-departure-card__header">

                    <div>

                        <span class="tour-departure-card__number">
                            Departure {{ $index + 1 }}
                        </span>

                        <strong>
                            Schedule & Pricing
                        </strong>

                    </div>


                    <button
                        type="button"
                        class="admin-button admin-button--danger tour-departure-remove"
                    >
                        Remove
                    </button>

                </div>


                <div class="admin-form-grid">


                    {{-- Departure Date --}}

                    <div class="admin-form-group">

                        <label>
                            Departure Date <span>*</span>
                        </label>

                        <input
                            type="date"
                            name="departures[{{ $index }}][departure_date]"
                            value="{{ $departure['departure_date'] ?? '' }}"
                            min="{{ now()->format('Y-m-d') }}"
                            required
                            data-departure-date
                        >

                        @error("departures.$index.departure_date")

                            <small class="admin-form-error">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- Return Date --}}

                    <div class="admin-form-group">

                        <label>
                            Return Date <span>*</span>
                        </label>

                        <input
                            type="date"
                            name="departures[{{ $index }}][return_date]"
                            value="{{ $departure['return_date'] ?? '' }}"
                            min="{{ $departure['departure_date'] ?: now()->format('Y-m-d') }}"
                            required
                            data-return-date
                        >

                        @error("departures.$index.return_date")

                            <small class="admin-form-error">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- Capacity --}}

                    <div class="admin-form-group">

                        <label>
                            Capacity <span>*</span>
                        </label>

                        <input
                            type="number"
                            name="departures[{{ $index }}][capacity]"
                            value="{{ $departure['capacity'] ?? '' }}"
                            min="1"
                            max="1000"
                            placeholder="30"
                            required
                        >

                        @error("departures.$index.capacity")

                            <small class="admin-form-error">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- Price --}}

                    <div class="admin-form-group">

                        <label>
                            Price / Traveller <span>*</span>
                        </label>

                        <input
                            type="number"
                            name="departures[{{ $index }}][price]"
                            value="{{ $departure['price'] ?? '' }}"
                            min="0"
                            step="0.01"
                            placeholder="47999"
                            required
                        >

                        @error("departures.$index.price")

                            <small class="admin-form-error">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- Sale Price --}}

                    <div class="admin-form-group">

                        <label>
                            Sale Price / Traveller
                        </label>

                        <input
                            type="number"
                            name="departures[{{ $index }}][sale_price]"
                            value="{{ $departure['sale_price'] ?? '' }}"
                            min="0"
                            step="0.01"
                            placeholder="Optional"
                        >

                        @error("departures.$index.sale_price")

                            <small class="admin-form-error">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- Currency --}}

                    <div class="admin-form-group">

                        <label>
                            Currency <span>*</span>
                        </label>

                        <select
                            name="departures[{{ $index }}][currency]"
                            required
                        >

                            <option
                                value="INR"
                                @selected(
                                    strtoupper(
                                        $departure['currency'] ?? 'INR'
                                    ) === 'INR'
                                )
                            >
                                INR — Indian Rupee
                            </option>

                            <option
                                value="USD"
                                @selected(
                                    strtoupper(
                                        $departure['currency'] ?? ''
                                    ) === 'USD'
                                )
                            >
                                USD — US Dollar
                            </option>

                            <option
                                value="EUR"
                                @selected(
                                    strtoupper(
                                        $departure['currency'] ?? ''
                                    ) === 'EUR'
                                )
                            >
                                EUR — Euro
                            </option>

                            <option
                                value="GBP"
                                @selected(
                                    strtoupper(
                                        $departure['currency'] ?? ''
                                    ) === 'GBP'
                                )
                            >
                                GBP — British Pound
                            </option>

                        </select>

                        @error("departures.$index.currency")

                            <small class="admin-form-error">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- Meeting Point --}}

                    <div class="admin-form-group admin-form-group--full">

                        <label>
                            Meeting Point
                        </label>

                        <input
                            type="text"
                            name="departures[{{ $index }}][meeting_point]"
                            value="{{ $departure['meeting_point'] ?? '' }}"
                            maxlength="255"
                            placeholder="e.g. Jaipur Railway Station"
                        >

                        @error("departures.$index.meeting_point")

                            <small class="admin-form-error">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>


                    {{-- Status --}}

                    <div class="admin-form-group">

                        <label>
                            Departure Status <span>*</span>
                        </label>

                        <select
                            name="departures[{{ $index }}][status]"
                            required
                        >

                            <option
                                value="open"
                                @selected(
                                    ($departure['status'] ?? 'open') === 'open'
                                )
                            >
                                Open
                            </option>

                            <option
                                value="closed"
                                @selected(
                                    ($departure['status'] ?? '') === 'closed'
                                )
                            >
                                Closed
                            </option>

                            <option
                                value="cancelled"
                                @selected(
                                    ($departure['status'] ?? '') === 'cancelled'
                                )
                            >
                                Cancelled
                            </option>

                        </select>

                        @error("departures.$index.status")

                            <small class="admin-form-error">
                                {{ $message }}
                            </small>

                        @enderror

                    </div>

                </div>

            </div>

        @endforeach

    </div>


    <div class="tour-departures__actions">

        <button
            type="button"
            class="admin-button"
            id="add-tour-departure"
        >
            + Add Another Departure
        </button>

        <small>
            You can add multiple future departures for the same package.
        </small>

    </div>

</section>


{{-- ================================================================
     DEPARTURE TEMPLATE
================================================================ --}}

<template id="tour-departure-template">

    <div
        class="tour-departure-card"
        data-departure-row
    >

        <div class="tour-departure-card__header">

            <div>

                <span class="tour-departure-card__number">
                    Departure __INDEX__
                </span>

                <strong>
                    Schedule & Pricing
                </strong>

            </div>


            <button
                type="button"
                class="admin-button admin-button--danger tour-departure-remove"
            >
                Remove
            </button>

        </div>


        <div class="admin-form-grid">


            <div class="admin-form-group">

                <label>
                    Departure Date <span>*</span>
                </label>

                <input
                    type="date"
                    name="departures[__INDEX__][departure_date]"
                    min="{{ now()->format('Y-m-d') }}"
                    required
                    data-departure-date
                >

            </div>


            <div class="admin-form-group">

                <label>
                    Return Date <span>*</span>
                </label>

                <input
                    type="date"
                    name="departures[__INDEX__][return_date]"
                    min="{{ now()->format('Y-m-d') }}"
                    required
                    data-return-date
                >

            </div>


            <div class="admin-form-group">

                <label>
                    Capacity <span>*</span>
                </label>

                <input
                    type="number"
                    name="departures[__INDEX__][capacity]"
                    min="1"
                    max="1000"
                    placeholder="30"
                    required
                >

            </div>


            <div class="admin-form-group">

                <label>
                    Price / Traveller <span>*</span>
                </label>

                <input
                    type="number"
                    name="departures[__INDEX__][price]"
                    min="0"
                    step="0.01"
                    placeholder="47999"
                    required
                >

            </div>


            <div class="admin-form-group">

                <label>
                    Sale Price / Traveller
                </label>

                <input
                    type="number"
                    name="departures[__INDEX__][sale_price]"
                    min="0"
                    step="0.01"
                    placeholder="Optional"
                >

            </div>


            <div class="admin-form-group">

                <label>
                    Currency <span>*</span>
                </label>

                <select
                    name="departures[__INDEX__][currency]"
                    required
                >

                    <option value="INR">
                        INR — Indian Rupee
                    </option>

                    <option value="USD">
                        USD — US Dollar
                    </option>

                    <option value="EUR">
                        EUR — Euro
                    </option>

                    <option value="GBP">
                        GBP — British Pound
                    </option>

                </select>

            </div>


            <div class="admin-form-group admin-form-group--full">

                <label>
                    Meeting Point
                </label>

                <input
                    type="text"
                    name="departures[__INDEX__][meeting_point]"
                    maxlength="255"
                    placeholder="e.g. Jaipur Railway Station"
                >

            </div>


            <div class="admin-form-group">

                <label>
                    Departure Status <span>*</span>
                </label>

                <select
                    name="departures[__INDEX__][status]"
                    required
                >

                    <option value="open">
                        Open
                    </option>

                    <option value="closed">
                        Closed
                    </option>

                    <option value="cancelled">
                        Cancelled
                    </option>

                </select>

            </div>

        </div>

    </div>

</template>


{{-- ================================================================
     ACTIONS
================================================================ --}}

<div class="admin-page__actions">

    <a
        href="{{ route('admin.tours.index') }}"
        class="admin-button"
    >
        Cancel
    </a>


    <button
        type="submit"
        class="admin-button admin-button--dark"
    >
        {{ isset($tour) ? 'Update Tour Package' : 'Create Tour Package' }}
    </button>

</div>




<style>
.tour-itinerary-day {
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 20px;
    margin-bottom: 18px;
    background: #fff;
    box-shadow: 0 6px 20px rgba(15, 23, 42, .04);
}
.tour-itinerary-day__header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    margin-bottom:20px;
    padding-bottom:15px;
    border-bottom:1px solid #edf1f5;
}
.tour-itinerary-day__header > div {
    display:flex;
    align-items:center;
    gap:12px;
}
.tour-itinerary-day__number {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:64px;
    padding:7px 11px;
    border-radius:999px;
    background:#fff3e6;
    color:#e8790b;
    font-weight:800;
    font-size:12px;
}
.tour-itinerary-activities {
    margin-top:18px;
    padding:16px;
    border-radius:14px;
    background:#f8fafc;
    border:1px solid #edf1f5;
}
.tour-itinerary-activities__header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    margin-bottom:12px;
}
.tour-itinerary-activities__header div {
    display:flex;
    flex-direction:column;
    gap:3px;
}
.tour-itinerary-activities__header small,
.tour-itinerary-day .admin-form-group > small {
    color:#64748b;
    font-size:12px;
}
.tour-itinerary-activity {
    display:grid;
    grid-template-columns:1fr auto;
    gap:8px;
    margin-top:8px;
}
.tour-itinerary-image-preview {
    position:relative;
    width:100%;
    max-width:240px;
    margin-top:10px;
    border:1px solid #e2e8f0;
    border-radius:12px;
    overflow:hidden;
    background:#f8fafc;
}
.tour-itinerary-image-preview img {
    display:block;
    width:100%;
    height:120px;
    object-fit:cover;
}
.tour-itinerary-image-preview span {
    display:block;
    padding:7px 9px;
    font-size:11px;
    font-weight:700;
    color:#475569;
}
@media (max-width:700px) {
    .tour-itinerary-day { padding:14px; }
    .tour-itinerary-day__header,
    .tour-itinerary-activities__header { align-items:flex-start; flex-direction:column; }
    .tour-itinerary-day__header { gap:10px; }
    .tour-itinerary-activity { grid-template-columns:1fr; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Dynamic Itinerary Builder
    |--------------------------------------------------------------------------
    */

    const itinerary = document.querySelector('[data-itinerary-container]');

    if (itinerary) {
        const list = itinerary.querySelector('[data-itinerary-list]');
        const addDayButton = itinerary.querySelector('[data-add-itinerary-day]');

        function renumberItinerary() {
            const days = list.querySelectorAll('[data-itinerary-day]');

            days.forEach(function (day, dayIndex) {
                const dayNumber = dayIndex + 1;
                const numberInput = day.querySelector('[data-itinerary-day-number]');
                const label = day.querySelector('[data-itinerary-day-label]');
                const titleInput = day.querySelector('[data-itinerary-title]') || day.querySelector('input[name*="[title]"]');
                const locationInput = day.querySelector('[data-itinerary-location]') || day.querySelector('input[name*="[location]"]');
                const imageInput = day.querySelector('[data-itinerary-image-input]');
                const descriptionInput = day.querySelector('[data-itinerary-description]') || day.querySelector('textarea[name*="[description]"]');

                if (numberInput) {
                    numberInput.value = dayNumber;
                    numberInput.name = 'itinerary[' + dayIndex + '][day]';
                }

                if (label) {
                    label.textContent = 'Day ' + dayNumber;
                }

                if (titleInput) {
                    titleInput.name = 'itinerary[' + dayIndex + '][title]';
                }

                if (locationInput) {
                    locationInput.name = 'itinerary[' + dayIndex + '][location]';
                }

                if (imageInput) {
                    imageInput.name = 'itinerary[' + dayIndex + '][image]';
                }

                if (descriptionInput) {
                    descriptionInput.name = 'itinerary[' + dayIndex + '][description]';
                }

                day.querySelectorAll('[data-itinerary-activity]').forEach(function (activity, activityIndex) {
                    const input = activity.querySelector('input');
                    if (input) {
                        input.name = 'itinerary[' + dayIndex + '][activities][' + activityIndex + ']';
                    }
                });
            });
        }

        function bindImagePreview(day) {
            const input = day.querySelector('[data-itinerary-image-input]');
            const preview = day.querySelector('[data-itinerary-image-preview]');
            const previewImage = day.querySelector('[data-itinerary-image-preview-img]');

            if (!input || !preview || !previewImage || input.dataset.previewBound === '1') {
                return;
            }

            input.dataset.previewBound = '1';

            input.addEventListener('change', function () {
                const file = input.files && input.files[0];
                if (!file) {
                    preview.hidden = true;
                    previewImage.removeAttribute('src');
                    return;
                }

                const objectUrl = URL.createObjectURL(file);
                previewImage.src = objectUrl;
                preview.hidden = false;
            });
        }

        function createActivity(day) {
            const activities = day.querySelector('[data-itinerary-activities]');
            if (!activities) return;

            const activity = document.createElement('div');
            activity.className = 'tour-itinerary-activity';
            activity.setAttribute('data-itinerary-activity', '');
            activity.innerHTML = `
                <input type="text" maxlength="500" placeholder="e.g. Sightseeing / Transfer / Meal / Hotel">
                <button type="button" class="admin-button admin-button--danger" data-remove-itinerary-activity title="Remove activity">×</button>
            `;

            activities.appendChild(activity);
            renumberItinerary();

            const input = activity.querySelector('input');
            if (input) input.focus();
        }

        function createDay() {
            const dayIndex = list.querySelectorAll('[data-itinerary-day]').length;
            const day = document.createElement('div');
            day.className = 'tour-itinerary-day';
            day.setAttribute('data-itinerary-day', '');

            day.innerHTML = `
                <input type="hidden" value="${dayIndex + 1}" data-itinerary-day-number>

                <div class="tour-itinerary-day__header">
                    <div>
                        <span class="tour-itinerary-day__number" data-itinerary-day-label>Day ${dayIndex + 1}</span>
                        <strong>Day Itinerary</strong>
                    </div>
                    <button type="button" class="admin-button admin-button--danger" data-remove-itinerary-day>Remove Day</button>
                </div>

                <div class="admin-form-grid">
                    <div class="admin-form-group admin-form-group--full">
                        <label>Day Title <span>*</span></label>
                        <input type="text" maxlength="255" placeholder="e.g. Kashi Vishwanath Temple & Spiritual Varanasi" required data-itinerary-title>
                    </div>

                    <div class="admin-form-group">
                        <label>Location / Places Covered</label>
                        <input type="text" maxlength="500" placeholder="e.g. Varanasi – Kashi Vishwanath, Vishalakshi & Annapurna" data-itinerary-location>
                        <small>This is shown in the PDF only.</small>
                    </div>

                    <div class="admin-form-group">
                        <label>Day Image</label>
                        <input type="file" accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif" data-itinerary-image-input>
                        <small>JPG, PNG, WebP or AVIF · max 5 MB · PDF only.</small>
                        <div class="tour-itinerary-image-preview tour-itinerary-image-preview--new" data-itinerary-image-preview hidden>
                            <img src="" alt="Selected day image" data-itinerary-image-preview-img>
                            <span>New image selected</span>
                        </div>
                    </div>

                    <div class="admin-form-group admin-form-group--full">
                        <label>Day Description</label>
                        <textarea rows="5" maxlength="10000" placeholder="Describe the day's travel, sightseeing, meals, hotel and other details..." data-itinerary-description></textarea>
                    </div>
                </div>

                <div class="tour-itinerary-activities">
                    <div class="tour-itinerary-activities__header">
                        <div>
                            <strong>Activities / Schedule</strong>
                            <small>Add one activity or schedule item per row.</small>
                        </div>
                        <button type="button" class="admin-button" data-add-itinerary-activity>+ Add Activity</button>
                    </div>
                    <div data-itinerary-activities>
                        <div class="tour-itinerary-activity" data-itinerary-activity>
                            <input type="text" maxlength="500" placeholder="e.g. Sightseeing / Transfer / Meal">
                            <button type="button" class="admin-button admin-button--danger" data-remove-itinerary-activity title="Remove activity">×</button>
                        </div>
                    </div>
                </div>
            `;

            list.appendChild(day);
            renumberItinerary();
            bindImagePreview(day);

            day.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        addDayButton?.addEventListener('click', createDay);

        itinerary.addEventListener('click', function (event) {
            const addActivity = event.target.closest('[data-add-itinerary-activity]');
            if (addActivity) {
                const day = addActivity.closest('[data-itinerary-day]');
                if (day) createActivity(day);
                return;
            }

            const removeActivity = event.target.closest('[data-remove-itinerary-activity]');
            if (removeActivity) {
                const activity = removeActivity.closest('[data-itinerary-activity]');
                if (activity) {
                    activity.remove();
                    renumberItinerary();
                }
                return;
            }

            const removeDay = event.target.closest('[data-remove-itinerary-day]');
            if (removeDay) {
                const days = list.querySelectorAll('[data-itinerary-day]');
                if (days.length <= 1) {
                    alert('At least one itinerary day is required.');
                    return;
                }

                const day = removeDay.closest('[data-itinerary-day]');
                if (day) {
                    day.remove();
                    renumberItinerary();
                }
            }
        });

        list.querySelectorAll('[data-itinerary-day]').forEach(bindImagePreview);
        renumberItinerary();
    }


    /*
    |--------------------------------------------------------------------------
    | Existing Tour Departures
    |--------------------------------------------------------------------------
    */

    const container =
        document.getElementById('tour-departures');

    const addButton =
        document.getElementById('add-tour-departure');

    const template =
        document.getElementById('tour-departure-template');

    if (
        !container ||
        !addButton ||
        !template
    ) {
        return;
    }


    let departureIndex =
        container.querySelectorAll(
            '[data-departure-row]'
        ).length;


    function updateNumbers() {

        const rows =
            container.querySelectorAll(
                '[data-departure-row]'
            );

        rows.forEach(function (row, index) {

            const number =
                row.querySelector(
                    '.tour-departure-card__number'
                );

            if (number) {

                number.textContent =
                    'Departure ' + (index + 1);

            }

        });

    }


    function updateRemoveButtons() {

        const rows =
            container.querySelectorAll(
                '[data-departure-row]'
            );

        rows.forEach(function (row) {

            const button =
                row.querySelector(
                    '.tour-departure-remove'
                );

            if (!button) {
                return;
            }

            /*
             * Always allow removal on edit/create.
             * Server-side validation still requires
             * at least one departure.
             */

            button.disabled =
                rows.length === 1;

        });

    }


    function setupDateValidation(row) {

        const departureInput =
            row.querySelector(
                '[data-departure-date]'
            );

        const returnInput =
            row.querySelector(
                '[data-return-date]'
            );

        if (
            !departureInput ||
            !returnInput
        ) {
            return;
        }


        function updateReturnDate() {

            if (!departureInput.value) {
                return;
            }

            returnInput.min =
                departureInput.value;


            if (
                returnInput.value
                &&
                returnInput.value <
                departureInput.value
            ) {

                returnInput.value =
                    departureInput.value;

            }

        }


        departureInput.addEventListener(
            'change',
            updateReturnDate
        );


        updateReturnDate();

    }


    container.querySelectorAll(
        '[data-departure-row]'
    ).forEach(function (row) {

        setupDateValidation(row);

    });


    addButton.addEventListener(
        'click',
        function () {

            const html =
                template.innerHTML.replaceAll(
                    '__INDEX__',
                    departureIndex
                );


            container.insertAdjacentHTML(
                'beforeend',
                html
            );


            const rows =
                container.querySelectorAll(
                    '[data-departure-row]'
                );


            const newRow =
                rows[rows.length - 1];


            setupDateValidation(newRow);


            departureIndex++;


            updateNumbers();
            updateRemoveButtons();


            newRow.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

        }
    );


    container.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest(
                    '.tour-departure-remove'
                );


            if (!button) {
                return;
            }


            const rows =
                container.querySelectorAll(
                    '[data-departure-row]'
                );


            if (rows.length <= 1) {
                return;
            }


            const row =
                button.closest(
                    '[data-departure-row]'
                );


            if (!row) {
                return;
            }


            row.remove();


            updateNumbers();
            updateRemoveButtons();

        }
    );


    updateNumbers();
    updateRemoveButtons();

});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const gallery = document.querySelector('.admin-tour-gallery');

    if (!gallery) {
        return;
    }

    gallery.addEventListener('click', function (event) {

        const button = event.target.closest('[data-gallery-remove]');

        if (!button) {
            return;
        }

        const item = button.closest('.admin-tour-gallery__item');

        if (!item) {
            return;
        }

        const imageId = button.dataset.imageId;

        if (!imageId) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Send image ID to Laravel on Update
        |--------------------------------------------------------------------------
        */

        const input = document.createElement('input');

        input.type = 'hidden';
        input.name = 'remove_gallery[]';
        input.value = imageId;

        gallery.parentElement.appendChild(input);

        /*
        |--------------------------------------------------------------------------
        | UI remove
        |--------------------------------------------------------------------------
        */

        item.style.transition =
            'opacity .2s ease, transform .2s ease';

        item.style.opacity = '0';
        item.style.transform = 'scale(.96)';

        setTimeout(function () {

            item.remove();

            /*
            |--------------------------------------------------------------------------
            | Re-number remaining images
            |--------------------------------------------------------------------------
            */

            gallery.querySelectorAll(
                '.admin-tour-gallery__item'
            ).forEach(function (item, index) {

                const position = item.querySelector(
                    '.admin-tour-gallery__footer > span:first-child'
                );

                if (position) {
                    position.textContent =
                        'Position ' + (index + 1);
                }

            });

        }, 200);

    });

});
</script>
<style>
/* =========================================================
   IMPORTANT NOTES — ADMIN
   One short point per line, matching the public SSB card UI.
========================================================= */
.important-notes-admin-help {
    display: block;
    margin-top: 8px;
    color: #64748b;
    line-height: 1.55;
}

.important-notes-admin-help code {
    padding: 2px 5px;
    border-radius: 4px;
    background: #f1f5f9;
    color: #334155;
    font-size: .9em;
}

#important_notes {
    min-height: 190px;
    line-height: 1.65;
    white-space: pre-wrap;
}
</style>
