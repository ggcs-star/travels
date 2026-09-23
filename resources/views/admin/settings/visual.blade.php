@extends('admin.layouts.app')

@section('title', 'Visual Settings')

@section('description', 'Manage your website branding, colors, layout and appearance.')

@section('content')

@php
    $value = fn ($key, $default = '') =>
        old($key, $settings[$key] ?? $default);

    $image = function ($path) {
        if (!$path) {
            return null;
        }

        return filter_var($path, FILTER_VALIDATE_URL)
            ? $path
            : asset('storage/' . ltrim($path, '/'));
    };
@endphp

<div class="website-settings visual-settings">

    {{-- =========================================================
         ALERTS
    ========================================================== --}}
    @if(session('success'))
        <div class="alert success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert error">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert error">
            <div>
                <strong>Please fix the following:</strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif


    {{-- =========================================================
         FORM
    ========================================================== --}}
    <form
        action="{{ route('admin.settings.visual.update') }}"
        method="POST"
        enctype="multipart/form-data"
        id="visual-settings-form"
    >

        @csrf
        @method('PUT')


        {{-- =====================================================
             01. BRANDING
        ====================================================== --}}
        <section class="settings-card">

            <div class="card-title">
                <div class="title-icon">
                    <i class="fas fa-paint-brush"></i>
                </div>

                <div>
                    <h2>Branding</h2>
                    <p>
                        Manage the main website logo, favicon and login page logo.
                    </p>
                </div>
            </div>


            <div class="visual-media-grid">

                {{-- WEBSITE LOGO --}}
                <div class="visual-media-card">

                    <div class="visual-media-preview logo-preview">

                        @if(!empty($settings['visual.logo']))

                            <img
                                src="{{ $image($settings['visual.logo']) }}"
                                alt="Website Logo"
                            >

                        @else

                            <div class="empty-media">
                                <i class="fas fa-image"></i>
                                <span>No logo</span>
                            </div>

                        @endif

                    </div>

                    <div class="visual-media-content">

                        <h3>Website Logo</h3>

                        <p>
                            Main logo used across the website.
                        </p>

                        <input
                            type="file"
                            name="visual_logo"
                            accept=".jpg,.jpeg,.png,.webp"
                        >

                        <small>
                            JPG, JPEG, PNG or WEBP · Maximum 2MB
                        </small>

                        @if(!empty($settings['visual.logo']))

                            <label class="remove-option">
                                <input
                                    type="checkbox"
                                    name="remove_visual_logo"
                                    value="1"
                                >
                                Remove current logo
                            </label>

                        @endif

                    </div>

                </div>


                {{-- FAVICON --}}
                <div class="visual-media-card">

                    <div class="visual-media-preview favicon-preview">

                        @if(!empty($settings['visual.favicon']))

                            <img
                                src="{{ $image($settings['visual.favicon']) }}"
                                alt="Website Favicon"
                            >

                        @else

                            <div class="empty-media">
                                <i class="fas fa-globe"></i>
                                <span>No favicon</span>
                            </div>

                        @endif

                    </div>

                    <div class="visual-media-content">

                        <h3>Website Favicon</h3>

                        <p>
                            Icon displayed in the browser tab.
                        </p>

                        <input
                            type="file"
                            name="visual_favicon"
                            accept=".ico,.png,.jpg,.jpeg,.webp"
                        >

                        <small>
                            ICO, PNG, JPG, JPEG or WEBP · Maximum 1MB
                        </small>

                        @if(!empty($settings['visual.favicon']))

                            <label class="remove-option">
                                <input
                                    type="checkbox"
                                    name="remove_visual_favicon"
                                    value="1"
                                >
                                Remove current favicon
                            </label>

                        @endif

                    </div>

                </div>


                {{-- LOGIN LOGO --}}
                <div class="visual-media-card">

                    <div class="visual-media-preview login-logo-preview">

                        @if(!empty($settings['visual.login_logo']))

                            <img
                                src="{{ $image($settings['visual.login_logo']) }}"
                                alt="Login Page Logo"
                            >

                        @else

                            <div class="empty-media">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>No login logo</span>
                            </div>

                        @endif

                    </div>

                    <div class="visual-media-content">

                        <h3>Login Page Logo</h3>

                        <p>
                            Logo displayed on the user login page.
                        </p>

                        <input
                            type="file"
                            name="visual_login_logo"
                            accept=".jpg,.jpeg,.png,.webp"
                        >

                        <small>
                            JPG, JPEG, PNG or WEBP · Maximum 2MB
                        </small>

                        @if(!empty($settings['visual.login_logo']))

                            <label class="remove-option">
                                <input
                                    type="checkbox"
                                    name="remove_visual_login_logo"
                                    value="1"
                                >
                                Remove current login logo
                            </label>

                        @endif

                    </div>

                </div>


                {{-- FOOTER LOGO --}}
                <div class="visual-media-card">

                    <div class="visual-media-preview footer-logo-preview">

                        @if(!empty($settings['footer.logo']))

                            <img
                                src="{{ $image($settings['footer.logo']) }}"
                                alt="Footer Logo"
                            >

                        @else

                            <div class="empty-media">
                                <i class="fas fa-shoe-prints"></i>
                                <span>No footer logo</span>
                            </div>

                        @endif

                    </div>

                    <div class="visual-media-content">

                        <h3>Footer Logo</h3>

                        <p>
                            Logo displayed in the website footer.
                        </p>

                        <input
                            type="file"
                            name="footer_logo"
                            accept=".jpg,.jpeg,.png,.webp"
                        >

                        <small>
                            JPG, JPEG, PNG or WEBP · Maximum 2MB
                        </small>

                        @if(!empty($settings['footer.logo']))

                            <label class="remove-option">
                                <input
                                    type="checkbox"
                                    name="remove_footer_logo"
                                    value="1"
                                >
                                Remove current footer logo
                            </label>

                        @endif

                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             02. THEME COLORS
        ====================================================== --}}
        <section class="settings-card">

            <div class="card-title">
                <div class="title-icon">
                    <i class="fas fa-palette"></i>
                </div>

                <div>
                    <h2>Theme Colors</h2>
                    <p>
                        Define the main color system used throughout the website.
                    </p>
                </div>
            </div>


            <div class="form-grid">

                {{-- PRIMARY --}}
                <div class="field">

                    <label for="visual_primary_color">
                        Primary Color
                    </label>

                    <div class="color-input-wrap">

                        <input
                            type="color"
                            id="visual_primary_color_picker"
                            value="{{ $value('visual_primary_color', '#2563eb') }}"
                            class="visual-color-picker"
                        >

                        <input
                            type="text"
                            id="visual_primary_color"
                            name="visual_primary_color"
                            value="{{ $value('visual_primary_color', '#2563eb') }}"
                            placeholder="#2563EB"
                            maxlength="7"
                        >

                    </div>

                    <small>
                        Main brand and primary action color.
                    </small>

                </div>


                {{-- SECONDARY --}}
                <div class="field">

                    <label for="visual_secondary_color">
                        Secondary Color
                    </label>

                    <div class="color-input-wrap">

                        <input
                            type="color"
                            id="visual_secondary_color_picker"
                            value="{{ $value('visual_secondary_color', '#64748b') }}"
                            class="visual-color-picker"
                        >

                        <input
                            type="text"
                            id="visual_secondary_color"
                            name="visual_secondary_color"
                            value="{{ $value('visual_secondary_color', '#64748b') }}"
                            placeholder="#64748B"
                            maxlength="7"
                        >

                    </div>

                    <small>
                        Secondary UI and supporting elements.
                    </small>

                </div>


                {{-- ACCENT --}}
                <div class="field">

                    <label for="visual_accent_color">
                        Accent Color
                    </label>

                    <div class="color-input-wrap">

                        <input
                            type="color"
                            id="visual_accent_color_picker"
                            value="{{ $value('visual_accent_color', '#f59e0b') }}"
                            class="visual-color-picker"
                        >

                        <input
                            type="text"
                            id="visual_accent_color"
                            name="visual_accent_color"
                            value="{{ $value('visual_accent_color', '#f59e0b') }}"
                            placeholder="#F59E0B"
                            maxlength="7"
                        >

                    </div>

                    <small>
                        Highlights, badges and important accents.
                    </small>

                </div>


                {{-- TEXT --}}
                <div class="field">

                    <label for="visual_text_color">
                        Text Color
                    </label>

                    <div class="color-input-wrap">

                        <input
                            type="color"
                            id="visual_text_color_picker"
                            value="{{ $value('visual_text_color', '#111827') }}"
                            class="visual-color-picker"
                        >

                        <input
                            type="text"
                            id="visual_text_color"
                            name="visual_text_color"
                            value="{{ $value('visual_text_color', '#111827') }}"
                            placeholder="#111827"
                            maxlength="7"
                        >

                    </div>

                    <small>
                        Default website text color.
                    </small>

                </div>


                {{-- BACKGROUND --}}
                <div class="field">

                    <label for="visual_background_color">
                        Background Color
                    </label>

                    <div class="color-input-wrap">

                        <input
                            type="color"
                            id="visual_background_color_picker"
                            value="{{ $value('visual_background_color', '#ffffff') }}"
                            class="visual-color-picker"
                        >

                        <input
                            type="text"
                            id="visual_background_color"
                            name="visual_background_color"
                            value="{{ $value('visual_background_color', '#ffffff') }}"
                            placeholder="#FFFFFF"
                            maxlength="7"
                        >

                    </div>

                    <small>
                        Main website background color.
                    </small>

                </div>

            </div>

        </section>


        {{-- =====================================================
             03. LAYOUT
        ====================================================== --}}
        <section class="settings-card">

            <div class="card-title">
                <div class="title-icon">
                    <i class="fas fa-layer-group"></i>
                </div>

                <div>
                    <h2>Layout</h2>
                    <p>
                        Control the overall website width, corners, buttons and cards.
                    </p>
                </div>
            </div>


            <div class="form-grid">

                {{-- CONTAINER WIDTH --}}
                <div class="field">

                    <label for="visual_container_width">
                        Container Width
                    </label>

                    <div class="input-with-unit">

                        <input
                            type="number"
                            id="visual_container_width"
                            name="visual_container_width"
                            value="{{ $value('visual_container_width', 1200) }}"
                            min="800"
                            max="2000"
                            step="10"
                            placeholder="1200"
                        >

                        <span>px</span>

                    </div>

                    <small>
                        Recommended range: 1000px – 1400px.
                    </small>

                </div>


                {{-- BORDER RADIUS --}}
                <div class="field">

                    <label for="visual_border_radius">
                        Border Radius
                    </label>

                    <div class="input-with-unit">

                        <input
                            type="number"
                            id="visual_border_radius"
                            name="visual_border_radius"
                            value="{{ $value('visual_border_radius', 8) }}"
                            min="0"
                            max="50"
                            step="1"
                            placeholder="8"
                        >

                        <span>px</span>

                    </div>

                    <small>
                        Controls rounded corners across UI components.
                    </small>

                </div>


                {{-- BUTTON STYLE --}}
                <div class="field">

                    <label for="visual_button_style">
                        Button Style
                    </label>

                    <select
                        id="visual_button_style"
                        name="visual_button_style"
                    >

                        <option
                            value="rounded"
                            @selected($value('visual_button_style', 'rounded') === 'rounded')
                        >
                            Rounded
                        </option>

                        <option
                            value="sharp"
                            @selected($value('visual_button_style') === 'sharp')
                        >
                            Sharp
                        </option>

                        <option
                            value="pill"
                            @selected($value('visual_button_style') === 'pill')
                        >
                            Pill
                        </option>

                    </select>

                    <small>
                        Default style for website buttons.
                    </small>

                </div>


                {{-- CARD STYLE --}}
                <div class="field">

                    <label for="visual_card_style">
                        Card Style
                    </label>

                    <select
                        id="visual_card_style"
                        name="visual_card_style"
                    >

                        <option
                            value="flat"
                            @selected($value('visual_card_style', 'flat') === 'flat')
                        >
                            Flat
                        </option>

                        <option
                            value="bordered"
                            @selected($value('visual_card_style') === 'bordered')
                        >
                            Bordered
                        </option>

                        <option
                            value="shadow"
                            @selected($value('visual_card_style') === 'shadow')
                        >
                            Shadow
                        </option>

                    </select>

                    <small>
                        Default appearance for content cards.
                    </small>

                </div>

            </div>

        </section>


        {{-- =====================================================
             04. TYPOGRAPHY
        ====================================================== --}}
        @php
            $fontOptions = [
                'Inter' => 'Inter',
                'Poppins' => 'Poppins',
                'Roboto' => 'Roboto',
                'Open Sans' => 'Open Sans',
                'Lato' => 'Lato',
                'Montserrat' => 'Montserrat',
                'Nunito' => 'Nunito',
                'Raleway' => 'Raleway',
                'Playfair Display' => 'Playfair Display',
                'Merriweather' => 'Merriweather',
                'System UI' => 'System UI',
            ];
        @endphp

        <section class="settings-card">
            <div class="card-title">
                <div class="title-icon"><i class="fas fa-font"></i></div>
                <div><h2>Typography</h2><p>Control the fonts and text sizing used across the user website.</p></div>
            </div>
            <div class="form-grid">
                @foreach ([
                    'body_font' => 'Body Font',
                    'heading_font' => 'Heading Font',
                    'nav_font' => 'Navigation Font',
                    'button_font' => 'Button Font',
                ] as $key => $label)
                    <div class="field">
                        <label for="visual_{{ $key }}">{{ $label }}</label>
                        <select id="visual_{{ $key }}" name="visual_{{ $key }}">
                            @foreach($fontOptions as $fontValue => $fontLabel)
                                <option value="{{ $fontValue }}" @selected($value('visual.' . $key, $fontValue === 'Poppins' && $key === 'heading_font' ? 'Poppins' : 'Inter') === $fontValue)>{{ $fontLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach

                <div class="field">
                    <label for="visual_body_weight">Body Font Weight</label>
                    <select id="visual_body_weight" name="visual_body_weight">
                        @foreach([400,500,600,700] as $weight)
                            <option value="{{ $weight }}" @selected((int)$value('visual.body_weight', 400) === $weight)>{{ $weight }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="visual_heading_weight">Heading Weight</label>
                    <select id="visual_heading_weight" name="visual_heading_weight">
                        @foreach([400,500,600,700,800] as $weight)
                            <option value="{{ $weight }}" @selected((int)$value('visual.heading_weight', 700) === $weight)>{{ $weight }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="visual_font_size">Base Font Size</label>
                    <div class="input-with-unit"><input type="number" id="visual_font_size" name="visual_font_size" value="{{ $value('visual.font_size', 16) }}" min="12" max="24" step="0.5"><span>px</span></div>
                </div>

                <div class="field">
                    <label for="visual_line_height">Line Height</label>
                    <input type="number" id="visual_line_height" name="visual_line_height" value="{{ $value('visual.line_height', 1.6) }}" min="1" max="2.5" step="0.1">
                </div>
            </div>
        </section>


        {{-- =====================================================
             05. APPEARANCE
        ====================================================== --}}
        <section class="settings-card">

            <div class="card-title">
                <div class="title-icon">
                    <i class="fas fa-desktop"></i>
                </div>

                <div>
                    <h2>Appearance</h2>
                    <p>
                        Configure theme mode and website interaction preferences.
                    </p>
                </div>
            </div>


            <div class="form-grid">

                {{-- THEME MODE --}}
                <div class="field">

                    <label for="visual_theme_mode">
                        Theme Mode
                    </label>

                    <select
                        id="visual_theme_mode"
                        name="visual_theme_mode"
                    >

                        <option
                            value="light"
                            @selected($value('visual_theme_mode', 'light') === 'light')
                        >
                            Light
                        </option>

                        <option
                            value="dark"
                            @selected($value('visual_theme_mode') === 'dark')
                        >
                            Dark
                        </option>

                        <option
                            value="system"
                            @selected($value('visual_theme_mode') === 'system')
                        >
                            System Default
                        </option>

                    </select>

                    <small>
                        Select how the website theme should behave.
                    </small>

                </div>


                {{-- PAGE LOADER --}}
                <div class="field">

                    <label class="toggle-field">

                        <input
                            type="hidden"
                            name="visual_page_loader"
                            value="0"
                        >

                        <input
                            type="checkbox"
                            name="visual_page_loader"
                            value="1"
                            @checked((bool) $value('visual_page_loader', true))
                        >

                        <span class="toggle-ui"></span>

                        <span class="toggle-content">
                            <strong>Page Loader</strong>
                            <small>
                                Show a loader while pages are loading.
                            </small>
                        </span>

                    </label>

                </div>


                {{-- ANIMATIONS --}}
                <div class="field">

                    <label class="toggle-field">

                        <input
                            type="hidden"
                            name="visual_animations"
                            value="0"
                        >

                        <input
                            type="checkbox"
                            name="visual_animations"
                            value="1"
                            @checked((bool) $value('visual_animations', true))
                        >

                        <span class="toggle-ui"></span>

                        <span class="toggle-content">
                            <strong>Animations</strong>
                            <small>
                                Enable smooth website animations and transitions.
                            </small>
                        </span>

                    </label>

                </div>


                {{-- BACK TO TOP --}}
                <div class="field">

                    <label class="toggle-field">

                        <input
                            type="hidden"
                            name="visual_back_to_top"
                            value="0"
                        >

                        <input
                            type="checkbox"
                            name="visual_back_to_top"
                            value="1"
                            @checked((bool) $value('visual_back_to_top', true))
                        >

                        <span class="toggle-ui"></span>

                        <span class="toggle-content">
                            <strong>Back to Top</strong>
                            <small>
                                Show a back-to-top button when scrolling.
                            </small>
                        </span>

                    </label>

                </div>

            </div>

        </section>


        {{-- =====================================================
             SAVE BAR
        ====================================================== --}}
        <div class="settings-save-bar">

            <div class="save-info">
                <i class="fas fa-info-circle"></i>

                <span>
                    Changes will be applied across the website.
                </span>
            </div>

            <div class="save-actions">

                <a
                    href="{{ route('admin.settings.general') }}"
                    class="btn-secondary"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="btn-primary"
                >
                    <i class="fas fa-save"></i>
                    Save Visual Settings
                </button>

            </div>

        </div>

    </form>

</div>


{{-- =============================================================
     COLOR PICKER SYNC
============================================================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const colorFields = [
        'primary',
        'secondary',
        'accent',
        'text',
        'background'
    ];

    colorFields.forEach(function (field) {

        const picker = document.getElementById(
            'visual_' + field + '_color_picker'
        );

        const input = document.getElementById(
            'visual_' + field + '_color'
        );

        if (!picker || !input) {
            return;
        }

        picker.addEventListener('input', function () {
            input.value = picker.value.toUpperCase();
        });

        input.addEventListener('input', function () {

            let value = input.value.trim();

            if (!value.startsWith('#')) {
                value = '#' + value;
            }

            if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
                picker.value = value;
            }

        });

        input.addEventListener('blur', function () {

            let value = input.value.trim();

            if (!value) {
                return;
            }

            if (!value.startsWith('#')) {
                value = '#' + value;
            }

            if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
                input.value = value.toUpperCase();
                picker.value = value;
            }

        });

    });

});
</script>

@endsection