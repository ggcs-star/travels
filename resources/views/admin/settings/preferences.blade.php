@extends('admin.layouts.app')

@section('title', 'Preferences')

@section('description', 'Manage website-wide upload security and payment gateway configuration from one central place.')

@section('content')
<div class="admin-page preferences-page">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="preferences-hero">

        <div class="preferences-hero-status
            {{ $razorpay['enabled']
                && $razorpay['has_key_secret']
                && filled($razorpay['key_id'])
                    ? 'is-on'
                    : 'is-off' }}">

            <span class="status-dot"></span>

            <div>
                <strong>
                    {{
                        $razorpay['enabled']
                        && $razorpay['has_key_secret']
                        && filled($razorpay['key_id'])
                            ? 'Razorpay Ready'
                            : 'Razorpay Not Ready'
                    }}
                </strong>

                <small>
                    {{ count($extensions) }}
                    upload type(s) allowed
                </small>
            </div>
        </div>
    </div>


    {{-- =========================================================
         SUCCESS MESSAGE
    ========================================================== --}}
    @if(session('success'))
        <div class="admin-alert admin-alert--success preferences-alert">

            <i class="fas fa-check-circle"></i>

            <div>
                <strong>
                    Success
                </strong>

                <span>
                    {{ session('success') }}
                </span>
            </div>

        </div>
    @endif


    {{-- =========================================================
         ERROR MESSAGE
    ========================================================== --}}
    @if(session('error'))
        <div class="admin-alert admin-alert--error preferences-alert">

            <i class="fas fa-exclamation-circle"></i>

            <div>
                <strong>
                    Error
                </strong>

                <span>
                    {{ session('error') }}
                </span>
            </div>

        </div>
    @endif


    {{-- =========================================================
         VALIDATION ERRORS
    ========================================================== --}}
    @if($errors->any())
        <div class="admin-alert admin-alert--error preferences-alert">

            <i class="fas fa-exclamation-circle"></i>

            <div>
                <strong>
                    Please fix the following:
                </strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>
    @endif


    {{-- =========================================================
         MAIN GRID
    ========================================================== --}}
    <div class="preferences-grid">


        {{-- =====================================================
             FILE UPLOAD PREFERENCES
        ====================================================== --}}
        <section class="admin-card preference-section">

            <div class="preference-section-head">

                <div class="preference-icon preference-icon--upload">
                    <i class="fas fa-file-upload"></i>
                </div>

                <div class="preference-section-title">

                    <span class="admin-eyebrow">
                        UPLOAD SECURITY
                    </span>

                    <h2>
                        Allowed File Extensions
                    </h2>

                    <p>
                        Select exactly which file extensions can be
                        uploaded through the website and API.
                    </p>

                </div>

                <div class="preference-metric">

                    <strong id="allowed-extension-count">
                        {{ count($extensions) }}
                    </strong>

                    <span>
                        Allowed
                    </span>

                </div>

            </div>


            <form
                method="POST"
                action="{{ route('admin.settings.preferences.uploads.update') }}"
                id="uploads-preferences-form"
            >

                @csrf

                @method('PUT')


                {{-- =================================================
                     STATUS BOX
                ================================================== --}}
                <div
                    class="upload-status-box
                    {{ count($extensions) > 0
                        ? 'upload-status-box--active'
                        : 'upload-status-box--disabled' }}"
                    id="upload-status-box"
                >

                    <div class="upload-status-icon">
                        <i
                            class="fas
                            {{ count($extensions) > 0
                                ? 'fa-check'
                                : 'fa-ban' }}"
                            id="upload-status-icon"
                        ></i>
                    </div>

                    <div>

                        <strong id="upload-status-title">
                            {{
                                count($extensions) > 0
                                    ? 'File uploads are enabled'
                                    : 'All file uploads are disabled'
                            }}
                        </strong>

                        <p id="upload-status-text">
                            {{
                                count($extensions) > 0
                                    ? 'Only the selected extensions below can be uploaded.'
                                    : 'No file extension is currently allowed. Save to keep uploads disabled.'
                            }}
                        </p>

                    </div>

                </div>


                {{-- =================================================
                     STANDARD EXTENSIONS
                ================================================== --}}
                <div class="preference-field">

                    <div class="extension-heading-row">

                        <label>
                            File Extensions
                        </label>

                        <span>
                            Check / uncheck to allow or disable
                        </span>

                    </div>


                    <div class="extension-grid" id="extension-grid">

                        @foreach($allExtensions as $extension)

                            @php
                                $isAllowed = in_array(
                                    $extension,
                                    $extensions,
                                    true
                                );
                            @endphp

                            <label
                                class="extension-option
                                {{ $isAllowed ? 'is-allowed' : '' }}"
                                data-extension="{{ $extension }}"
                            >

                                <input
                                    type="checkbox"
                                    name="allowed_extensions[]"
                                    value="{{ $extension }}"
                                    {{ $isAllowed ? 'checked' : '' }}
                                >

                                <span class="extension-option-check">
                                    <i class="fas fa-check"></i>
                                </span>

                                <span class="extension-option-content">

                                    <strong>
                                        .{{ strtoupper($extension) }}
                                    </strong>

                                    <small>
                                        <span class="extension-state">
                                            {{
                                                $isAllowed
                                                    ? 'Allowed'
                                                    : 'Disabled'
                                            }}
                                        </span>
                                    </small>

                                </span>

                            </label>

                        @endforeach

                    </div>

                </div>


                {{-- =================================================
                     CUSTOM EXTENSION
                ================================================== --}}
                <div class="custom-extension-box">

                    <div class="custom-extension-heading">

                        <div class="custom-extension-icon">
                            <i class="fas fa-plus"></i>
                        </div>

                        <div>
                            <strong>
                                Add Custom Extension
                            </strong>

                            <p>
                                Add another safe extension if your
                                website needs a custom file type.
                            </p>
                        </div>

                    </div>


                    <div class="custom-extension-form">

                        <input
                            type="text"
                            id="custom-extension-input"
                            placeholder="Example: avif"
                            autocomplete="off"
                            spellcheck="false"
                            maxlength="20"
                        >

                        <button
                            type="button"
                            class="admin-button admin-button--secondary"
                            id="add-extension-button"
                        >
                            <i class="fas fa-plus"></i>
                            Add Extension
                        </button>

                    </div>

                    <small class="custom-extension-help">
                        Enter only letters and numbers.
                        Example: <code>avif</code>, <code>json</code>.
                    </small>

                </div>


                {{-- =================================================
                     PROTECTED EXTENSIONS
                ================================================== --}}
                <div class="preference-info-box preference-info-box--warning">

                    <i class="fas fa-shield-alt"></i>

                    <div>

                        <strong>
                            Permanently protected extensions
                        </strong>

                        <p>
                            Executable, server and configuration
                            file types can never be enabled from
                            Preferences.
                        </p>

                    </div>

                </div>


                <div class="blocked-list">

                    @foreach($blockedExtensions as $extension)

                        <span>
                            .{{ $extension }}
                        </span>

                    @endforeach

                </div>


                {{-- =================================================
                     UPLOAD FOOTER
                ================================================== --}}
                <div class="preference-footer">

                    <div class="preference-footer-note">

                        <i class="fas fa-lock"></i>

                        <span>
                            Server-side enforcement enabled
                        </span>

                    </div>

                    <div class="preference-actions">

                        <button
                            type="submit"
                            class="admin-button admin-button--primary"
                        >
                            <i class="fas fa-save"></i>
                            Save Upload Rules
                        </button>

                    </div>

                </div>

            </form>

        </section>



        {{-- =====================================================
             RAZORPAY
        ====================================================== --}}
        <section
            class="admin-card preference-section preference-section--razorpay"
        >

            <div class="preference-section-head">

                <div class="preference-icon preference-icon--payment">
                    <i class="fas fa-credit-card"></i>
                </div>

                <div class="preference-section-title">

                    <span class="admin-eyebrow">
                        PAYMENT GATEWAY
                    </span>

                    <h2>
                        Razorpay Integration
                    </h2>

                    <p>
                        Manage Razorpay credentials directly from
                        the admin Preferences module.
                    </p>

                </div>

                <div
                    class="gateway-badge
                    {{ $razorpay['enabled']
                        && $razorpay['has_key_secret']
                        && filled($razorpay['key_id'])
                            ? 'gateway-badge--on'
                            : '' }}"
                >

                    <span></span>

                    {{
                        $razorpay['enabled']
                            ? 'Enabled'
                            : 'Disabled'
                    }}

                </div>

            </div>


            <form
                method="POST"
                action="{{ route('admin.settings.preferences.payments.update') }}"
                class="razorpay-preferences-form"
                autocomplete="off"
            >

                @csrf

                @method('PUT')


                {{-- Gateway switch --}}
                <div class="gateway-status-row">

                    <div>

                        <strong>
                            Gateway Status
                        </strong>

                        <span>
                            Turn Razorpay payments on or off.
                        </span>

                    </div>

                    <label class="switch">

                        <input
                            type="checkbox"
                            name="razorpay_enabled"
                            value="1"
                            {{ old(
                                'razorpay_enabled',
                                $razorpay['enabled']
                            ) ? 'checked' : '' }}
                        >

                        <span class="switch-slider"></span>

                    </label>

                </div>


                <div class="razorpay-fields">


                    {{-- Key ID --}}
                    <div class="preference-field">

                        <label for="razorpay_key_id">

                            Razorpay Key ID

                            <span>
                                Public
                            </span>

                        </label>

                        <input
                            id="razorpay_key_id"
                            name="razorpay_key_id"
                            type="text"
                            value="{{ old(
                                'razorpay_key_id',
                                $razorpay['key_id']
                            ) }}"
                            placeholder="rzp_live_... / rzp_test_..."
                            autocomplete="off"
                            spellcheck="false"
                        >

                        <p class="preference-help">

                            Your saved Key ID is displayed here.

                        </p>

                    </div>


                    {{-- Key Secret --}}
                    <div class="preference-field">

                        <label for="razorpay_key_secret">

                            Razorpay Key Secret

                            <span>
                                Encrypted
                            </span>

                        </label>

                        <div class="secret-input-wrap">

                            <input
                                id="razorpay_key_secret"
                                name="razorpay_key_secret"
                                type="password"
                                placeholder="{{
                                    $razorpay['has_key_secret']
                                        ? 'Saved securely — enter only to replace'
                                        : 'Enter Razorpay Key Secret'
                                }}"
                                autocomplete="new-password"
                            >

                            <button
                                type="button"
                                class="secret-toggle"
                                data-target="razorpay_key_secret"
                                aria-label="Show or hide secret"
                            >
                                <i class="fas fa-eye"></i>
                            </button>

                        </div>


                        @if($razorpay['has_key_secret'])

                            <div class="saved-secret-status">

                                <i class="fas fa-lock"></i>

                                <span>
                                    Key Secret is already saved securely.
                                </span>

                            </div>

                            <label class="clear-secret">

                                <input
                                    type="checkbox"
                                    name="clear_razorpay_key_secret"
                                    value="1"
                                >

                                Remove saved Key Secret

                            </label>

                        @endif

                    </div>


                    {{-- Webhook Secret --}}
                    <div class="preference-field">

                        <label for="razorpay_webhook_secret">

                            Webhook Secret

                            <span>
                                Encrypted
                            </span>

                        </label>

                        <div class="secret-input-wrap">

                            <input
                                id="razorpay_webhook_secret"
                                name="razorpay_webhook_secret"
                                type="password"
                                placeholder="{{
                                    $razorpay['has_webhook_secret']
                                        ? 'Saved securely — enter only to replace'
                                        : 'Enter Webhook Secret'
                                }}"
                                autocomplete="new-password"
                            >

                            <button
                                type="button"
                                class="secret-toggle"
                                data-target="razorpay_webhook_secret"
                                aria-label="Show or hide webhook secret"
                            >
                                <i class="fas fa-eye"></i>
                            </button>

                        </div>


                        @if($razorpay['has_webhook_secret'])

                            <div class="saved-secret-status">

                                <i class="fas fa-lock"></i>

                                <span>
                                    Webhook Secret is already saved securely.
                                </span>

                            </div>

                            <label class="clear-secret">

                                <input
                                    type="checkbox"
                                    name="clear_razorpay_webhook_secret"
                                    value="1"
                                >

                                Remove saved Webhook Secret

                            </label>

                        @endif

                    </div>


                    {{-- Base URL --}}
                    <div class="preference-field">

                        <label for="razorpay_base_url">

                            Razorpay API Base URL

                            <span>
                                Advanced
                            </span>

                        </label>

                        <input
                            id="razorpay_base_url"
                            name="razorpay_base_url"
                            type="url"
                            value="{{ old(
                                'razorpay_base_url',
                                $razorpay['base_url']
                            ) }}"
                            required
                        >

                        <p class="preference-help">

                            Default:

                            <code>
                                https://api.razorpay.com/v1
                            </code>

                        </p>

                    </div>

                </div>


                {{-- Security information --}}
                <div
                    class="preference-info-box preference-info-box--secure"
                >

                    <i class="fas fa-lock"></i>

                    <div>

                        <strong>
                            Secure credential storage
                        </strong>

                        <p>
                            Key Secret and Webhook Secret are encrypted
                            before being stored in the database.
                            They are never returned by public APIs.
                        </p>

                    </div>

                </div>


                {{-- Save --}}
                <div class="preference-footer">

                    <div class="preference-footer-note">

                        <i class="fas fa-database"></i>

                        <span>
                            Database configuration active
                        </span>

                    </div>

                    <div class="preference-actions">

                        <button
                            type="submit"
                            class="admin-button admin-button--primary"
                        >
                            <i class="fas fa-save"></i>
                            Save Razorpay
                        </button>

                    </div>

                </div>

            </form>


            {{-- Connection test --}}
            <div class="gateway-test-row">

                <div>

                    <strong>
                        Connection Test
                    </strong>

                    <span>
                        Test the saved Razorpay credentials without
                        creating a payment order.
                    </span>

                </div>

                <form
                    method="POST"
                    action="{{ route('admin.settings.preferences.payments.test') }}"
                >

                    @csrf

                    <button
                        type="submit"
                        class="admin-button admin-button--secondary"
                    >
                        <i class="fas fa-plug"></i>
                        Test Connection
                    </button>

                </form>

            </div>

        </section>

    </div>


    {{-- =========================================================
         GUIDE
    ========================================================== --}}
    <section class="admin-card preferences-guide">

        <div class="guide-heading">

            <div class="preference-icon preference-icon--guide">
                <i class="fas fa-info"></i>
            </div>

            <div>

                <span class="admin-eyebrow">
                    QUICK GUIDE
                </span>

                <h2>
                    How Preferences works
                </h2>

            </div>

        </div>


        <div class="guide-grid">

            <div class="guide-item">

                <b>01</b>

                <div>

                    <strong>
                        Select extensions
                    </strong>

                    <p>
                        Enable or disable JPG, PNG, PDF and other
                        supported file types individually.
                    </p>

                </div>

            </div>


            <div class="guide-item">

                <b>02</b>

                <div>

                    <strong>
                        Save configuration
                    </strong>

                    <p>
                        Changes are stored in the website database
                        and remain active after refresh.
                    </p>

                </div>

            </div>


            <div class="guide-item">

                <b>03</b>

                <div>

                    <strong>
                        Server validates
                    </strong>

                    <p>
                        Browser file-picker restrictions are not trusted.
                        Uploads are checked server-side.
                    </p>

                </div>

            </div>


            <div class="guide-item">

                <b>04</b>

                <div>

                    <strong>
                        Secure payments
                    </strong>

                    <p>
                        Razorpay payment services read credentials
                        from Preferences.
                    </p>

                </div>

            </div>

        </div>


        <div class="guide-footer">

            <form
                method="POST"
                action="{{ route('admin.settings.preferences.reset') }}"
            >

                @csrf

                <button
                    type="submit"
                    class="admin-button admin-button--secondary"
                    onclick="return confirm('Restore all default allowed upload extensions?')"
                >
                    <i class="fas fa-undo"></i>
                    Restore Upload Defaults
                </button>

            </form>

        </div>

    </section>

</div>


{{-- =============================================================
     PAGE JAVASCRIPT
============================================================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
     * =========================================================
     * EXTENSION CHECKBOXES
     * =========================================================
     */

    const form =
        document.getElementById(
            'uploads-preferences-form'
        );

    const grid =
        document.getElementById(
            'extension-grid'
        );

    const countElement =
        document.getElementById(
            'allowed-extension-count'
        );

    const statusBox =
        document.getElementById(
            'upload-status-box'
        );

    const statusIcon =
        document.getElementById(
            'upload-status-icon'
        );

    const statusTitle =
        document.getElementById(
            'upload-status-title'
        );

    const statusText =
        document.getElementById(
            'upload-status-text'
        );


    function getCheckboxes() {

        return Array.from(
            grid.querySelectorAll(
                'input[type="checkbox"][name="allowed_extensions[]"]'
            )
        );

    }


    function updateExtensionUI() {

        const checkboxes =
            getCheckboxes();

        const allowedCount =
            checkboxes.filter(
                checkbox => checkbox.checked
            ).length;


        /*
         * Counter
         */
        countElement.textContent =
            allowedCount;


        /*
         * Each card state
         */
        checkboxes.forEach(function (checkbox) {

            const option =
                checkbox.closest(
                    '.extension-option'
                );

            const state =
                option.querySelector(
                    '.extension-state'
                );


            if (checkbox.checked) {

                option.classList.add(
                    'is-allowed'
                );

                state.textContent =
                    'Allowed';

            } else {

                option.classList.remove(
                    'is-allowed'
                );

                state.textContent =
                    'Disabled';

            }

        });


        /*
         * Overall status
         */
        if (allowedCount > 0) {

            statusBox.classList.remove(
                'upload-status-box--disabled'
            );

            statusBox.classList.add(
                'upload-status-box--active'
            );

            statusIcon.className =
                'fas fa-check';

            statusTitle.textContent =
                'File uploads are enabled';

            statusText.textContent =
                'Only the selected extensions below can be uploaded.';

        } else {

            statusBox.classList.remove(
                'upload-status-box--active'
            );

            statusBox.classList.add(
                'upload-status-box--disabled'
            );

            statusIcon.className =
                'fas fa-ban';

            statusTitle.textContent =
                'All file uploads are disabled';

            statusText.textContent =
                'No file extension is currently allowed. Save to keep uploads disabled.';

        }

    }


    grid.addEventListener(
        'change',
        function (event) {

            if (
                event.target.matches(
                    'input[type="checkbox"]'
                )
            ) {
                updateExtensionUI();
            }

        }
    );


    /*
     * =========================================================
     * CUSTOM EXTENSION
     * =========================================================
     */

    const customInput =
        document.getElementById(
            'custom-extension-input'
        );

    const addButton =
        document.getElementById(
            'add-extension-button'
        );


    function normalizeExtension(value) {

        return value
            .trim()
            .toLowerCase()
            .replace(/^\.+/, '');

    }


    function addCustomExtension() {

        const value =
            normalizeExtension(
                customInput.value
            );


        if (!value) {

            customInput.focus();

            return;

        }


        if (
            !/^[a-z0-9]{1,20}$/.test(
                value
            )
        ) {

            alert(
                'Please enter a valid extension using only letters and numbers.'
            );

            customInput.focus();

            return;

        }


        const blockedExtensions = @json(
            $blockedExtensions
        );


        if (
            blockedExtensions.includes(
                value
            )
        ) {

            alert(
                'This extension is permanently blocked for security reasons.'
            );

            customInput.focus();

            return;

        }


        const existing =
            Array.from(
                grid.querySelectorAll(
                    'input[name="allowed_extensions[]"]'
                )
            ).some(
                input =>
                    input.value.toLowerCase() === value
            );


        if (existing) {

            alert(
                '.' + value + ' is already available.'
            );

            customInput.focus();

            return;

        }


        /*
         * Create new extension card.
         */
        const label =
            document.createElement(
                'label'
            );

        label.className =
            'extension-option is-allowed';

        label.dataset.extension =
            value;


        label.innerHTML = `
            <input
                type="checkbox"
                name="allowed_extensions[]"
                value="${value}"
                checked
            >

            <span class="extension-option-check">
                <i class="fas fa-check"></i>
            </span>

            <span class="extension-option-content">

                <strong>
                    .${value.toUpperCase()}
                </strong>

                <small>
                    <span class="extension-state">
                        Allowed
                    </span>
                </small>

            </span>
        `;


        grid.appendChild(
            label
        );


        customInput.value = '';

        customInput.focus();

        updateExtensionUI();

    }


    addButton.addEventListener(
        'click',
        addCustomExtension
    );


    customInput.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Enter'
            ) {

                event.preventDefault();

                addCustomExtension();

            }

        }
    );


    /*
     * =========================================================
     * SECRET SHOW/HIDE
     * =========================================================
     */

    document
        .querySelectorAll(
            '.secret-toggle'
        )
        .forEach(function (button) {

            button.addEventListener(
                'click',
                function () {

                    const target =
                        document.getElementById(
                            button.dataset.target
                        );

                    const icon =
                        button.querySelector(
                            'i'
                        );


                    if (
                        target.type ===
                        'password'
                    ) {

                        target.type =
                            'text';

                        icon.className =
                            'fas fa-eye-slash';

                    } else {

                        target.type =
                            'password';

                        icon.className =
                            'fas fa-eye';

                    }

                }
            );

        });


    /*
     * Initial state.
     */
    updateExtensionUI();

});
</script>

@endsection