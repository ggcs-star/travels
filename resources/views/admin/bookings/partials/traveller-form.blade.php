<fieldset class="admin-traveller-fieldset" data-traveller-card>

    <div class="admin-traveller-fieldset__header">

        <legend>
            Traveller
            <span data-traveller-number>
                {{ is_numeric($index) ? $index + 1 : '' }}
            </span>
        </legend>

        <button
            type="button"
            class="admin-traveller-fieldset__remove"
            data-remove-traveller
        >
            Remove
        </button>

    </div>


    <div class="admin-form-grid">

        {{-- Full Name --}}
        <div class="admin-form-group">

            <label for="traveller_{{ $index }}_full_name">
                Full name
                <span>*</span>
            </label>

            <input
                id="traveller_{{ $index }}_full_name"
                type="text"
                name="travellers[{{ $index }}][full_name]"
                value="{{ old('travellers.'.$index.'.full_name', $traveller['full_name'] ?? '') }}"
                maxlength="150"
                required
            >

            @error('travellers.'.$index.'.full_name')
                <small class="admin-form-error">{{ $message }}</small>
            @enderror

        </div>


        {{-- Email --}}
        <div class="admin-form-group">

            <label for="traveller_{{ $index }}_email">
                Email
            </label>

            <input
                id="traveller_{{ $index }}_email"
                type="email"
                name="travellers[{{ $index }}][email]"
                value="{{ old('travellers.'.$index.'.email', $traveller['email'] ?? '') }}"
                maxlength="255"
            >

            @error('travellers.'.$index.'.email')
                <small class="admin-form-error">{{ $message }}</small>
            @enderror

        </div>


        {{-- Phone --}}
        <div class="admin-form-group">

            <label for="traveller_{{ $index }}_phone">
                Phone
            </label>

            <input
                id="traveller_{{ $index }}_phone"
                type="tel"
                name="travellers[{{ $index }}][phone]"
                value="{{ old('travellers.'.$index.'.phone', $traveller['phone'] ?? '') }}"
                maxlength="40"
            >

            @error('travellers.'.$index.'.phone')
                <small class="admin-form-error">{{ $message }}</small>
            @enderror

        </div>


        {{-- Date of Birth --}}
        <div class="admin-form-group">

            <label for="traveller_{{ $index }}_date_of_birth">
                Date of birth
                <span>*</span>
            </label>

            <input
                id="traveller_{{ $index }}_date_of_birth"
                type="date"
                name="travellers[{{ $index }}][date_of_birth]"
                value="{{ old(
                    'travellers.'.$index.'.date_of_birth',
                    isset($traveller['date_of_birth'])
                        ? \Illuminate\Support\Carbon::parse($traveller['date_of_birth'])->format('Y-m-d')
                        : ''
                ) }}"
                max="{{ now()->subDay()->format('Y-m-d') }}"
                required
            >

            @error('travellers.'.$index.'.date_of_birth')
                <small class="admin-form-error">{{ $message }}</small>
            @enderror

        </div>


        {{-- Gender --}}
        <div class="admin-form-group">

            <label for="traveller_{{ $index }}_gender">
                Gender
                <span>*</span>
            </label>

            @php
                $selectedGender = old('travellers.'.$index.'.gender', $traveller['gender'] ?? '');
            @endphp

            <select
                id="traveller_{{ $index }}_gender"
                name="travellers[{{ $index }}][gender]"
                required
            >
                <option value="">Select gender</option>
                <option value="female" @selected($selectedGender === 'female')>Female</option>
                <option value="male" @selected($selectedGender === 'male')>Male</option>
                <option value="non_binary" @selected($selectedGender === 'non_binary')>Non-binary</option>
                <option value="prefer_not_to_say" @selected($selectedGender === 'prefer_not_to_say')>Prefer not to say</option>
            </select>

            @error('travellers.'.$index.'.gender')
                <small class="admin-form-error">{{ $message }}</small>
            @enderror

        </div>


        {{-- ID Proof Type --}}
        <div class="admin-form-group">

            <label for="traveller_{{ $index }}_id_proof_type">
                ID proof type
                <span>*</span>
            </label>

            @php
                $selectedIdType = old('travellers.'.$index.'.id_proof_type', $traveller['id_proof_type'] ?? '');
            @endphp

            <select
                id="traveller_{{ $index }}_id_proof_type"
                name="travellers[{{ $index }}][id_proof_type]"
                required
            >
                <option value="">Select ID proof</option>
                <option value="aadhaar" @selected($selectedIdType === 'aadhaar')>Aadhaar Card</option>
                <option value="passport" @selected($selectedIdType === 'passport')>Passport</option>
                <option value="driving_license" @selected($selectedIdType === 'driving_license')>Driving Licence</option>
                <option value="voter_id" @selected($selectedIdType === 'voter_id')>Voter ID</option>
                <option value="pan_card" @selected($selectedIdType === 'pan_card')>PAN Card</option>
                <option value="other" @selected($selectedIdType === 'other')>Other</option>
            </select>

            @error('travellers.'.$index.'.id_proof_type')
                <small class="admin-form-error">{{ $message }}</small>
            @enderror

        </div>


        {{-- ID Proof Number --}}
        <div class="admin-form-group">

            <label for="traveller_{{ $index }}_id_proof_number">
                ID proof number
                <span>*</span>
            </label>

            <input
                id="traveller_{{ $index }}_id_proof_number"
                type="text"
                name="travellers[{{ $index }}][id_proof_number]"
                value="{{ old('travellers.'.$index.'.id_proof_number', $traveller['id_proof_number'] ?? '') }}"
                maxlength="100"
                required
            >

            @error('travellers.'.$index.'.id_proof_number')
                <small class="admin-form-error">{{ $message }}</small>
            @enderror

        </div>


        {{-- ID Proof Document --}}
        <div class="admin-form-group">

            <label for="traveller_{{ $index }}_id_proof_document">
                Upload ID proof
                <span>*</span>
            </label>

            <input
                id="traveller_{{ $index }}_id_proof_document"
                type="file"
                name="travellers[{{ $index }}][id_proof_document]"
                accept=".jpg,.jpeg,.png,.webp,.pdf"
                required
            >

            <small>JPG, JPEG, PNG, WEBP or PDF · Maximum 5 MB</small>

            @error('travellers.'.$index.'.id_proof_document')
                <small class="admin-form-error">{{ $message }}</small>
            @enderror

        </div>

    </div>

</fieldset>
