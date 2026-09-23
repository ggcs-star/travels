@extends('admin.layouts.app')

@section('title', 'Website Settings')

@section('description', 'Manage your complete website configuration from one place.')

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

    $navigation = old(
        'header_navigation',
        $settings['header.navigation'] ?? []
    );

    $ctaBadges = old(
        'footer_cta_badges',
        $settings['footer.cta_badges'] ?? []
    );

    $socials = old(
        'footer_socials',
        $settings['footer.socials'] ?? []
    );

    $columns = old(
        'footer_columns',
        $settings['footer.columns'] ?? []
    );

    $contacts = old(
        'footer_contacts',
        $settings['footer.contacts'] ?? []
    );

    $trustBadges = old(
        'footer_trust_badges',
        $settings['footer.trust_badges'] ?? []
    );

    $destinations = old(
        'footer_destinations',
        $settings['footer.destinations'] ?? []
    );

    $bottomLinks = old(
        'footer_bottom_links',
        $settings['footer.bottom_links'] ?? []
    );
@endphp

<div class="website-settings">

    {{-- Alerts --}}
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


    <form
        action="{{ route('admin.settings.update') }}"
        method="POST"
        enctype="multipart/form-data"
        id="settings-form"
    >

        @csrf
        @method('PUT')


        {{-- =========================================================
             GENERAL
        ========================================================== --}}
        <section class="settings-card">

            <div class="card-title">
                <div class="title-icon">
                    <i class="fas fa-globe"></i>
                </div>

                <div>
                    <h2>General</h2>
                    <p>Website identity and common contact information.</p>
                </div>
            </div>


            <div class="form-grid">

                <div class="field">
                    <label>Website Name</label>

                    <input
                        type="text"
                        name="site_name"
                        value="{{ $value('site_name') }}"
                        placeholder="travels"
                    >
                </div>


                <div class="field">
                    <label>Tagline</label>

                    <input
                        type="text"
                        name="site_tagline"
                        value="{{ $value('site_tagline') }}"
                        placeholder="Tourism With Faith"
                    >
                </div>


                <div class="field full">
                    <label>Website Description</label>

                   <textarea
    name="seo_schema"
    rows="7"
    placeholder='{"@@context":"https://schema.org"}'
>{{ $value('seo_schema') }}</textarea>
                </div>


                <div class="field">
                    <label>Email</label>

                    <input
                        type="email"
                        name="site_email"
                        value="{{ $value('site_email') }}"
                        placeholder="bookings@example.com"
                    >
                </div>


                <div class="field">
                    <label>Phone</label>

                    <input
                        type="text"
                        name="site_phone"
                        value="{{ $value('site_phone') }}"
                        placeholder="+91-XXXXXXXXXX"
                    >
                </div>


                <div class="field">
                    <label>WhatsApp</label>

                    <input
                        type="text"
                        name="site_whatsapp"
                        value="{{ $value('site_whatsapp') }}"
                        placeholder="+91-XXXXXXXXXX"
                    >
                </div>


                <div class="field">
                    <label>Working Hours</label>

                    <input
                        type="text"
                        name="site_working_hours"
                        value="{{ $value('site_working_hours') }}"
                        placeholder="Mon - Sat · 9am - 7pm"
                    >
                </div>


                <div class="field full">
                    <label>Main Address</label>

                    <textarea
                        name="site_address"
                        rows="3"
                        placeholder="Main office address..."
                    >{{ $value('site_address') }}</textarea>
                </div>


                <div class="field full">
                    <label>Registered Address</label>

                    <textarea
                        name="site_registered_address"
                        rows="3"
                        placeholder="Registered address..."
                    >{{ $value('site_registered_address') }}</textarea>
                </div>


                <div class="field full">
                    <label>Branch Address</label>

                    <textarea
                        name="site_branch_address"
                        rows="3"
                        placeholder="Branch address..."
                    >{{ $value('site_branch_address') }}</textarea>
                </div>

            </div>

        </section>


        {{-- =========================================================
             BRANDING
        ========================================================== --}}
        <section class="settings-card">

            <div class="card-title">
                <div class="title-icon">
                    <i class="fas fa-palette"></i>
                </div>

                <div>
                    <h2>Branding</h2>
                    <p>One master logo is used throughout the website.</p>
                </div>
            </div>


            {{-- MASTER LOGO --}}
            <div class="media-card">

                <div class="media-preview">

                    @if(!empty($settings['header.logo']))

                        <img
                            src="{{ $image($settings['header.logo']) }}"
                            alt="{{ $settings['header.logo_alt'] ?? 'Website Logo' }}"
                        >

                    @else

                        <div class="empty-media">
                            <i class="fas fa-image"></i>
                            <span>No logo</span>
                        </div>

                    @endif

                </div>


                <div class="media-body">

                    <h3>Master Website Logo</h3>

                    <p>
                        This logo is automatically reused in the header,
                        footer and other website components.
                    </p>

                    <input
                        type="file"
                        name="header_logo"
                        accept=".jpg,.jpeg,.png,.webp"
                    >

                    <small>
                        JPG, JPEG, PNG or WEBP · Maximum 2MB
                    </small>

                    @if(!empty($settings['header.logo']))
                        <label class="remove-option">
                            <input
                                type="checkbox"
                                name="remove_header_logo"
                                value="1"
                            >

                            Remove current logo
                        </label>
                    @endif

                </div>

            </div>


            <div class="form-grid">

                <div class="field full">
                    <label>Logo Alt Text</label>

                    <input
                        type="text"
                        name="header_logo_alt"
                        value="{{ $value('header_logo_alt') }}"
                        placeholder="travels"
                    >
                </div>

            </div>


            {{-- FAVICON --}}
            <div class="sub-card">

                <div class="sub-card-heading">
                    <div>
                        <h3>Favicon</h3>
                        <p>Browser tab icon.</p>
                    </div>
                </div>


                <div class="media-card">

                    <div class="favicon-preview">

                        @if(!empty($settings['site.favicon']))

                            <img
                                src="{{ $image($settings['site.favicon']) }}"
                                alt="Favicon"
                            >

                        @else

                            <div class="empty-media">
                                <i class="fas fa-globe"></i>
                                <span>None</span>
                            </div>

                        @endif

                    </div>


                    <div class="media-body">

                        <h3>Website Favicon</h3>

                        <input
                            type="file"
                            name="favicon"
                            accept=".ico,.png,.jpg,.jpeg,.webp"
                        >

                        <small>
                            ICO, PNG, JPG, JPEG or WEBP · Maximum 1MB
                        </small>

                        @if(!empty($settings['site.favicon']))
                            <label class="remove-option">
                                <input
                                    type="checkbox"
                                    name="remove_favicon"
                                    value="1"
                                >

                                Remove current favicon
                            </label>
                        @endif

                    </div>

                </div>

            </div>

        </section>


        {{-- =========================================================
             TOPBAR
        ========================================================== --}}
        <section class="settings-card">

            <div class="card-title with-toggle">

                <div class="title-content">
                    <div class="title-icon">
                        <i class="fas fa-bars"></i>
                    </div>

                    <div>
                        <h2>Topbar</h2>
                        <p>Information bar displayed above the header.</p>
                    </div>
                </div>


                <label class="switch">
                    <input
                        type="checkbox"
                        name="topbar_enabled"
                        value="1"
                        {{ !empty($settings['topbar.enabled']) ? 'checked' : '' }}
                    >

                    <span></span>
                </label>

            </div>


            <div class="form-grid">

                <div class="field">
                    <label>Phone Label</label>

                    <input
                        type="text"
                        name="topbar_phone_label"
                        value="{{ $value('topbar_phone_label', 'Call Us') }}"
                        placeholder="Call Us"
                    >
                </div>


                <div class="field">
                    <label>Email Label</label>

                    <input
                        type="text"
                        name="topbar_email_label"
                        value="{{ $value('topbar_email_label', 'Email Us') }}"
                        placeholder="Email Us"
                    >
                </div>


                <div class="field full">
                    <label>Topbar Text</label>

                    <input
                        type="text"
                        name="topbar_text"
                        value="{{ $value('topbar_text') }}"
                        placeholder="Welcome to travels"
                    >
                </div>

            </div>


            <div class="info">
                <i class="fas fa-info-circle"></i>
                Phone, email and WhatsApp values are taken from General Settings.
            </div>

        </section>


        {{-- =========================================================
             HEADER
        ========================================================== --}}
        <section class="settings-card">

            <div class="card-title with-toggle">

                <div class="title-content">
                    <div class="title-icon">
                        <i class="fas fa-window-maximize"></i>
                    </div>

                    <div>
                        <h2>Header</h2>
                        <p>Main navigation and header CTA.</p>
                    </div>
                </div>


                <label class="switch">
                    <input
                        type="checkbox"
                        name="header_enabled"
                        value="1"
                        {{ !empty($settings['header.enabled']) ? 'checked' : '' }}
                    >

                    <span></span>
                </label>

            </div>


            <div class="form-grid">

                <div class="field">
                    <label>CTA Text</label>

                    <input
                        type="text"
                        name="header_cta_text"
                        value="{{ $value('header_cta_text') }}"
                        placeholder="Plan My Trip"
                    >
                </div>


                <div class="field">
                    <label>CTA URL</label>

                    <input
                        type="text"
                        name="header_cta_url"
                        value="{{ $value('header_cta_url') }}"
                        placeholder="/contact"
                    >
                </div>

            </div>


            <div class="sub-card">

                <div class="sub-card-heading">

                    <div>
                        <h3>Navigation</h3>
                        <p>Used by desktop and mobile navigation.</p>
                    </div>

                    <button
                        type="button"
                        class="add-btn"
                        onclick="addNavigation()"
                    >
                        <i class="fas fa-plus"></i>
                        Add Item
                    </button>

                </div>


                <div id="navigation-list">

                    @foreach($navigation as $index => $item)

                        <div class="repeat-row">

                            <div class="row-number">
                                {{ $index + 1 }}
                            </div>


                            <div class="repeat-fields">

                                <div class="field">
                                    <label>Label</label>

                                    <input
                                        type="text"
                                        name="header_navigation[{{ $index }}][label]"
                                        value="{{ $item['label'] ?? '' }}"
                                        placeholder="Home"
                                    >
                                </div>


                                <div class="field">
                                    <label>URL</label>

                                    <input
                                        type="text"
                                        name="header_navigation[{{ $index }}][url]"
                                        value="{{ $item['url'] ?? '' }}"
                                        placeholder="/"
                                    >
                                </div>


                                <div class="field">
                                    <label>Icon</label>

                                    <input
                                        type="text"
                                        name="header_navigation[{{ $index }}][icon]"
                                        value="{{ $item['icon'] ?? '' }}"
                                        placeholder="fas fa-home"
                                    >
                                </div>


                                <div class="field">
                                    <label>Target</label>

                                    <select
                                        name="header_navigation[{{ $index }}][target]"
                                    >
                                        <option
                                            value="_self"
                                            {{ ($item['target'] ?? '_self') === '_self' ? 'selected' : '' }}
                                        >
                                            Same Window
                                        </option>

                                        <option
                                            value="_blank"
                                            {{ ($item['target'] ?? '') === '_blank' ? 'selected' : '' }}
                                        >
                                            New Window
                                        </option>
                                    </select>
                                </div>

                            </div>


                            <div class="row-actions">

                                <label class="active-check">
                                    <input
                                        type="checkbox"
                                        name="header_navigation[{{ $index }}][enabled]"
                                        value="1"
                                        {{ !empty($item['enabled']) ? 'checked' : '' }}
                                    >

                                    Active
                                </label>

                                <button
                                    type="button"
                                    class="delete-btn"
                                    onclick="removeRow(this)"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        </section>


        {{-- =========================================================
             FOOTER
        ========================================================== --}}
        <section class="settings-card">

            <div class="card-title with-toggle">

                <div class="title-content">
                    <div class="title-icon">
                        <i class="fas fa-shoe-prints"></i>
                    </div>

                    <div>
                        <h2>Footer</h2>
                        <p>Manage all footer sections and content.</p>
                    </div>
                </div>


                <label class="switch">
                    <input
                        type="checkbox"
                        name="footer_enabled"
                        value="1"
                        {{ !empty($settings['footer.enabled']) ? 'checked' : '' }}
                    >

                    <span></span>
                </label>

            </div>


            {{-- Footer description --}}
            <div class="sub-card">

                <div class="sub-card-heading">
                    <div>
                        <h3>Basic Footer Content</h3>
                        <p>Footer description.</p>
                    </div>
                </div>


                <div class="form-grid">

                    <div class="field full">

                        <label>Footer Description</label>

                        <textarea
                            name="footer_description"
                            rows="4"
                            placeholder="Short company description..."
                        >{{ $value('footer_description') }}</textarea>

                    </div>

                </div>

            </div>


            {{-- CTA --}}
            <div class="sub-card">

                <div class="sub-card-heading">

                    <div>
                        <h3>Footer CTA</h3>
                        <p>Call-to-action section above the footer.</p>
                    </div>

                    <label class="switch">
                        <input
                            type="checkbox"
                            name="footer_cta_enabled"
                            value="1"
                            {{ !empty($settings['footer.cta_enabled']) ? 'checked' : '' }}
                        >

                        <span></span>
                    </label>

                </div>


                <div class="form-grid">

                    <div class="field">
                        <label>CTA Title</label>

                        <input
                            type="text"
                            name="footer_cta_title"
                            value="{{ $value('footer_cta_title') }}"
                            placeholder="Ready to Travel With Faith?"
                        >
                    </div>


                    <div class="field">
                        <label>Button Text</label>

                        <input
                            type="text"
                            name="footer_cta_button_text"
                            value="{{ $value('footer_cta_button_text') }}"
                            placeholder="Plan My Trip"
                        >
                    </div>


                    <div class="field">
                        <label>Button URL</label>

                        <input
                            type="text"
                            name="footer_cta_button_url"
                            value="{{ $value('footer_cta_button_url') }}"
                            placeholder="/contact"
                        >
                    </div>


                    <div class="field full">
                        <label>CTA Description</label>

                        <textarea
                            name="footer_cta_description"
                            rows="3"
                            placeholder="CTA description..."
                        >{{ $value('footer_cta_description') }}</textarea>
                    </div>

                </div>


                <div class="repeat-header">
                    <strong>CTA Badges</strong>

                    <button
                        type="button"
                        class="add-btn small"
                        onclick="addCtaBadge()"
                    >
                        <i class="fas fa-plus"></i>
                        Add
                    </button>
                </div>


                <div id="cta-badges-list">

                    @foreach($ctaBadges as $index => $item)

                        <div class="simple-row">

                            <div class="row-number">
                                {{ $index + 1 }}
                            </div>

                            <div class="repeat-fields">

                                <div class="field">
                                    <label>Text</label>

                                    <input
                                        type="text"
                                        name="footer_cta_badges[{{ $index }}][text]"
                                        value="{{ $item['text'] ?? '' }}"
                                        placeholder="Free Quote"
                                    >
                                </div>


                                <div class="field">
                                    <label>Icon</label>

                                    <input
                                        type="text"
                                        name="footer_cta_badges[{{ $index }}][icon]"
                                        value="{{ $item['icon'] ?? '' }}"
                                        placeholder="fas fa-check"
                                    >
                                </div>

                            </div>


                            <label class="active-check">
                                <input
                                    type="checkbox"
                                    name="footer_cta_badges[{{ $index }}][enabled]"
                                    value="1"
                                    {{ !empty($item['enabled']) ? 'checked' : '' }}
                                >

                                Active
                            </label>


                            <button
                                type="button"
                                class="delete-btn"
                                onclick="removeRow(this)"
                            >
                                <i class="fas fa-trash"></i>
                            </button>

                        </div>

                    @endforeach

                </div>

            </div>


            {{-- Socials --}}
            <div class="sub-card">

                <div class="sub-card-heading">

                    <div>
                        <h3>Footer Socials</h3>
                        <p>Social profiles shown in the footer.</p>
                    </div>

                    <button
                        type="button"
                        class="add-btn"
                        onclick="addSocial()"
                    >
                        <i class="fas fa-plus"></i>
                        Add Social
                    </button>

                </div>


                <div id="socials-list">

                    @foreach($socials as $index => $item)

                        <div class="repeat-row">

                            <div class="row-number">
                                {{ $index + 1 }}
                            </div>


                            <div class="repeat-fields repeat-3">

                                <div class="field">
                                    <label>Name</label>

                                    <input
                                        type="text"
                                        name="footer_socials[{{ $index }}][name]"
                                        value="{{ $item['name'] ?? '' }}"
                                        placeholder="Facebook"
                                    >
                                </div>


                                <div class="field">
                                    <label>URL</label>

                                    <input
                                        type="text"
                                        name="footer_socials[{{ $index }}][url]"
                                        value="{{ $item['url'] ?? '' }}"
                                        placeholder="https://..."
                                    >
                                </div>


                                <div class="field">
                                    <label>Icon</label>

                                    <input
                                        type="text"
                                        name="footer_socials[{{ $index }}][icon]"
                                        value="{{ $item['icon'] ?? '' }}"
                                        placeholder="fab fa-facebook-f"
                                    >
                                </div>

                            </div>


                            <div class="row-actions">

                                <label class="active-check">
                                    <input
                                        type="checkbox"
                                        name="footer_socials[{{ $index }}][enabled]"
                                        value="1"
                                        {{ !empty($item['enabled']) ? 'checked' : '' }}
                                    >

                                    Active
                                </label>


                                <button
                                    type="button"
                                    class="delete-btn"
                                    onclick="removeRow(this)"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>


            {{-- Footer columns --}}
            <div class="sub-card">

                <div class="sub-card-heading">

                    <div>
                        <h3>Footer Columns</h3>
                        <p>Create columns and their links.</p>
                    </div>

                    <button
                        type="button"
                        class="add-btn"
                        onclick="addColumn()"
                    >
                        <i class="fas fa-plus"></i>
                        Add Column
                    </button>

                </div>


                <div id="columns-list">

                    @foreach($columns as $columnIndex => $column)

                        <div class="column-box">

                            <div class="column-header">

                                <div>
                                    <span>FOOTER COLUMN</span>
                                    <strong>
                                        {{ $column['title'] ?? 'Column' }}
                                    </strong>
                                </div>


                                <div class="column-actions">

                                    <label class="active-check">
                                        <input
                                            type="checkbox"
                                            name="footer_columns[{{ $columnIndex }}][enabled]"
                                            value="1"
                                            {{ !empty($column['enabled']) ? 'checked' : '' }}
                                        >

                                        Active
                                    </label>


                                    <button
                                        type="button"
                                        class="delete-btn"
                                        onclick="removeColumn(this)"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>

                                </div>

                            </div>


                            <div class="form-grid">

                                <div class="field full">

                                    <label>Column Title</label>

                                    <input
                                        type="text"
                                        name="footer_columns[{{ $columnIndex }}][title]"
                                        value="{{ $column['title'] ?? '' }}"
                                        placeholder="Quick Links"
                                    >

                                </div>

                            </div>


                            <div class="links-area">

                                <div class="repeat-header">

                                    <strong>Links</strong>

                                    <button
                                        type="button"
                                        class="add-btn small"
                                        onclick="addColumnLink(this)"
                                    >
                                        <i class="fas fa-plus"></i>
                                        Add Link
                                    </button>

                                </div>


                                <div class="column-links">

                                    @foreach(($column['links'] ?? []) as $linkIndex => $link)

                                        <div class="link-row">

                                            <div class="field">
                                                <label>Label</label>

                                                <input
                                                    type="text"
                                                    name="footer_columns[{{ $columnIndex }}][links][{{ $linkIndex }}][label]"
                                                    value="{{ $link['label'] ?? '' }}"
                                                    placeholder="About Us"
                                                >
                                            </div>


                                            <div class="field">
                                                <label>URL</label>

                                                <input
                                                    type="text"
                                                    name="footer_columns[{{ $columnIndex }}][links][{{ $linkIndex }}][url]"
                                                    value="{{ $link['url'] ?? '' }}"
                                                    placeholder="/about"
                                                >
                                            </div>


                                            <div class="field">
                                                <label>Icon</label>

                                                <input
                                                    type="text"
                                                    name="footer_columns[{{ $columnIndex }}][links][{{ $linkIndex }}][icon]"
                                                    value="{{ $link['icon'] ?? '' }}"
                                                    placeholder="fas fa-angle-right"
                                                >
                                            </div>


                                            <label class="active-check">
                                                <input
                                                    type="checkbox"
                                                    name="footer_columns[{{ $columnIndex }}][links][{{ $linkIndex }}][enabled]"
                                                    value="1"
                                                    {{ !empty($link['enabled']) ? 'checked' : '' }}
                                                >

                                                Active
                                            </label>


                                            <button
                                                type="button"
                                                class="delete-btn"
                                                onclick="removeRow(this)"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>


            {{-- Contacts --}}
            <div class="sub-card">

                <div class="sub-card-heading">

                    <div>
                        <h3>Footer Contacts</h3>
                        <p>Additional footer contact items.</p>
                    </div>

                    <button
                        type="button"
                        class="add-btn"
                        onclick="addContact()"
                    >
                        <i class="fas fa-plus"></i>
                        Add Contact
                    </button>

                </div>


                <div class="form-grid">

                    <div class="field full">

                        <label>Contact Section Title</label>

                        <input
                            type="text"
                            name="footer_contact_title"
                            value="{{ $value('footer_contact_title', 'GET IN TOUCH') }}"
                            placeholder="GET IN TOUCH"
                        >

                    </div>

                </div>


                <div id="contacts-list">

                    @foreach($contacts as $index => $item)

                        <div class="repeat-row">

                            <div class="row-number">
                                {{ $index + 1 }}
                            </div>


                            <div class="repeat-fields repeat-4">

                                <div class="field">
                                    <label>Label</label>

                                    <input
                                        type="text"
                                        name="footer_contacts[{{ $index }}][label]"
                                        value="{{ $item['label'] ?? '' }}"
                                        placeholder="Phone"
                                    >
                                </div>


                                <div class="field">
                                    <label>Value</label>

                                    <input
                                        type="text"
                                        name="footer_contacts[{{ $index }}][value]"
                                        value="{{ $item['value'] ?? '' }}"
                                        placeholder="+91..."
                                    >
                                </div>


                                <div class="field">
                                    <label>URL</label>

                                    <input
                                        type="text"
                                        name="footer_contacts[{{ $index }}][url]"
                                        value="{{ $item['url'] ?? '' }}"
                                        placeholder="tel:+91..."
                                    >
                                </div>


                                <div class="field">
                                    <label>Icon</label>

                                    <input
                                        type="text"
                                        name="footer_contacts[{{ $index }}][icon]"
                                        value="{{ $item['icon'] ?? '' }}"
                                        placeholder="fas fa-phone"
                                    >
                                </div>

                            </div>


                            <div class="row-actions">

                                <label class="active-check">
                                    <input
                                        type="checkbox"
                                        name="footer_contacts[{{ $index }}][enabled]"
                                        value="1"
                                        {{ !empty($item['enabled']) ? 'checked' : '' }}
                                    >

                                    Active
                                </label>


                                <button
                                    type="button"
                                    class="delete-btn"
                                    onclick="removeRow(this)"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>


            {{-- Trust badges --}}
            <div class="sub-card">

                <div class="sub-card-heading">

                    <div>
                        <h3>Trust Badges</h3>
                        <p>Trust indicators displayed in the footer.</p>
                    </div>

                    <button
                        type="button"
                        class="add-btn"
                        onclick="addTrustBadge()"
                    >
                        <i class="fas fa-plus"></i>
                        Add Badge
                    </button>

                </div>


                <div id="trust-list">

                    @foreach($trustBadges as $index => $item)

                        <div class="repeat-row">

                            <div class="row-number">
                                {{ $index + 1 }}
                            </div>


                            <div class="repeat-fields repeat-3">

                                <div class="field">
                                    <label>Title</label>

                                    <input
                                        type="text"
                                        name="footer_trust_badges[{{ $index }}][title]"
                                        value="{{ $item['title'] ?? '' }}"
                                        placeholder="4.9 Google Rating"
                                    >
                                </div>


                                <div class="field">
                                    <label>Subtitle</label>

                                    <input
                                        type="text"
                                        name="footer_trust_badges[{{ $index }}][subtitle]"
                                        value="{{ $item['subtitle'] ?? '' }}"
                                        placeholder="Excellent"
                                    >
                                </div>


                                <div class="field">
                                    <label>Icon</label>

                                    <input
                                        type="text"
                                        name="footer_trust_badges[{{ $index }}][icon]"
                                        value="{{ $item['icon'] ?? '' }}"
                                        placeholder="fas fa-star"
                                    >
                                </div>

                            </div>


                            <div class="row-actions">

                                <label class="active-check">
                                    <input
                                        type="checkbox"
                                        name="footer_trust_badges[{{ $index }}][enabled]"
                                        value="1"
                                        {{ !empty($item['enabled']) ? 'checked' : '' }}
                                    >

                                    Active
                                </label>


                                <button
                                    type="button"
                                    class="delete-btn"
                                    onclick="removeRow(this)"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>


            {{-- Destinations --}}
            <div class="sub-card">

                <div class="sub-card-heading">

                    <div>
                        <h3>Popular Destinations</h3>
                        <p>Destinations shown at the bottom of the website.</p>
                    </div>

                    <button
                        type="button"
                        class="add-btn"
                        onclick="addDestination()"
                    >
                        <i class="fas fa-plus"></i>
                        Add Destination
                    </button>

                </div>


                <div class="form-grid">

                    <div class="field full">

                        <label>Section Title</label>

                        <input
                            type="text"
                            name="footer_destinations_title"
                            value="{{ $value('footer_destinations_title', 'Popular Destinations') }}"
                            placeholder="Popular Destinations"
                        >

                    </div>

                </div>


                <div id="destinations-list">

                    @foreach($destinations as $index => $item)

                        <div class="repeat-row">

                            <div class="row-number">
                                {{ $index + 1 }}
                            </div>


                            <div class="repeat-fields">

                                <div class="field">
                                    <label>Destination</label>

                                    <input
                                        type="text"
                                        name="footer_destinations[{{ $index }}][label]"
                                        value="{{ $item['label'] ?? '' }}"
                                        placeholder="Varanasi"
                                    >
                                </div>


                                <div class="field">
                                    <label>URL</label>

                                    <input
                                        type="text"
                                        name="footer_destinations[{{ $index }}][url]"
                                        value="{{ $item['url'] ?? '' }}"
                                        placeholder="/destinations/varanasi"
                                    >
                                </div>

                            </div>


                            <div class="row-actions">

                                <label class="active-check">
                                    <input
                                        type="checkbox"
                                        name="footer_destinations[{{ $index }}][enabled]"
                                        value="1"
                                        {{ !empty($item['enabled']) ? 'checked' : '' }}
                                    >

                                    Active
                                </label>


                                <button
                                    type="button"
                                    class="delete-btn"
                                    onclick="removeRow(this)"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>


            {{-- Footer bottom --}}
            <div class="sub-card">

                <div class="sub-card-heading">

                    <div>
                        <h3>Footer Bottom</h3>
                        <p>Copyright, legal links and crafted text.</p>
                    </div>

                </div>


                <div class="form-grid">

                    <div class="field full">
                        <label>Copyright Text</label>

                        <input
                            type="text"
                            name="footer_copyright_text"
                            value="{{ $value('footer_copyright_text') }}"
                            placeholder="© {year} {site_name} · All rights reserved."
                        >

                        <small>
                            Available placeholders:
                            {year}, {site_name}, {tagline}
                        </small>
                    </div>


                    <div class="field full">

                        <label class="inline-check">

                            <input
                                type="checkbox"
                                name="footer_show_crafted"
                                value="1"
                                {{ !empty($settings['footer.show_crafted']) ? 'checked' : '' }}
                            >

                            Show crafted text

                        </label>

                    </div>


                    <div class="field full">

                        <label>Crafted Text</label>

                        <input
                            type="text"
                            name="footer_crafted_text"
                            value="{{ $value('footer_crafted_text') }}"
                            placeholder="Crafted with ♥ in Hyderabad"
                        >

                    </div>

                </div>


                <div class="repeat-header">

                    <strong>Bottom Links</strong>

                    <button
                        type="button"
                        class="add-btn small"
                        onclick="addBottomLink()"
                    >
                        <i class="fas fa-plus"></i>
                        Add Link
                    </button>

                </div>


                <div id="bottom-links-list">

                    @foreach($bottomLinks as $index => $item)

                        <div class="simple-row">

                            <div class="row-number">
                                {{ $index + 1 }}
                            </div>


                            <div class="repeat-fields">

                                <div class="field">
                                    <label>Label</label>

                                    <input
                                        type="text"
                                        name="footer_bottom_links[{{ $index }}][label]"
                                        value="{{ $item['label'] ?? '' }}"
                                        placeholder="Privacy Policy"
                                    >
                                </div>


                                <div class="field">
                                    <label>URL</label>

                                    <input
                                        type="text"
                                        name="footer_bottom_links[{{ $index }}][url]"
                                        value="{{ $item['url'] ?? '' }}"
                                        placeholder="/privacy-policy"
                                    >
                                </div>

                            </div>


                            <label class="active-check">
                                <input
                                    type="checkbox"
                                    name="footer_bottom_links[{{ $index }}][enabled]"
                                    value="1"
                                    {{ !empty($item['enabled']) ? 'checked' : '' }}
                                >

                                Active
                            </label>


                            <button
                                type="button"
                                class="delete-btn"
                                onclick="removeRow(this)"
                            >
                                <i class="fas fa-trash"></i>
                            </button>

                        </div>

                    @endforeach

                </div>

            </div>

        </section>


        {{-- =========================================================
             SEO
        ========================================================== --}}
        <section class="settings-card">

            <div class="card-title">

                <div class="title-icon">
                    <i class="fas fa-search"></i>
                </div>

                <div>
                    <h2>SEO</h2>
                    <p>Default metadata and search engine configuration.</p>
                </div>

            </div>


            <div class="form-grid">

                <div class="field full">
                    <label>SEO Title</label>

                    <input
                        type="text"
                        name="seo_title"
                        value="{{ $value('seo_title') }}"
                        placeholder="travels | Tourism With Faith"
                    >
                </div>


                <div class="field full">
                    <label>SEO Description</label>

                    <textarea
                        name="seo_description"
                        rows="4"
                        placeholder="Default SEO description..."
                    >{{ $value('seo_description') }}</textarea>
                </div>


                <div class="field">
                    <label>Keywords</label>

                    <input
                        type="text"
                        name="seo_keywords"
                        value="{{ $value('seo_keywords') }}"
                        placeholder="travel, tours, pilgrimage"
                    >
                </div>


                <div class="field">
                    <label>Author</label>

                    <input
                        type="text"
                        name="seo_author"
                        value="{{ $value('seo_author') }}"
                        placeholder="travels"
                    >
                </div>


                <div class="field">
                    <label>Robots</label>

                    <input
                        type="text"
                        name="seo_robots"
                        value="{{ $value('seo_robots', 'index, follow') }}"
                        placeholder="index, follow"
                    >
                </div>


                <div class="field">
                    <label>Canonical URL</label>

                    <input
                        type="text"
                        name="seo_canonical"
                        value="{{ $value('seo_canonical') }}"
                        placeholder="https://example.com"
                    >
                </div>


                <div class="field">
                    <label>OG Title</label>

                    <input
                        type="text"
                        name="seo_og_title"
                        value="{{ $value('seo_og_title') }}"
                        placeholder="travels"
                    >
                </div>


                <div class="field">
                    <label>Twitter Title</label>

                    <input
                        type="text"
                        name="seo_twitter_title"
                        value="{{ $value('seo_twitter_title') }}"
                        placeholder="travels"
                    >
                </div>


                <div class="field full">
                    <label>OG Description</label>

                    <textarea
                        name="seo_og_description"
                        rows="3"
                        placeholder="Open Graph description..."
                    >{{ $value('seo_og_description') }}</textarea>
                </div>


                <div class="field full">
                    <label>Twitter Description</label>

                    <textarea
                        name="seo_twitter_description"
                        rows="3"
                        placeholder="Twitter description..."
                    >{{ $value('seo_twitter_description') }}</textarea>
                </div>


                <div class="field">
                    <label>Twitter Card</label>

                    <select name="seo_twitter_card">

                        <option
                            value="summary"
                            {{ $value('seo_twitter_card', 'summary_large_image') === 'summary' ? 'selected' : '' }}
                        >
                            Summary
                        </option>

                        <option
                            value="summary_large_image"
                            {{ $value('seo_twitter_card', 'summary_large_image') === 'summary_large_image' ? 'selected' : '' }}
                        >
                            Summary Large Image
                        </option>

                        <option
                            value="app"
                            {{ $value('seo_twitter_card') === 'app' ? 'selected' : '' }}
                        >
                            App
                        </option>

                        <option
                            value="player"
                            {{ $value('seo_twitter_card') === 'player' ? 'selected' : '' }}
                        >
                            Player
                        </option>

                    </select>
                </div>


                <div class="field">
                    <label>Google Verification</label>

                    <input
                        type="text"
                        name="seo_google_verification"
                        value="{{ $value('seo_google_verification') }}"
                        placeholder="Verification code"
                    >
                </div>


                <div class="field">
                    <label>Bing Verification</label>

                    <input
                        type="text"
                        name="seo_bing_verification"
                        value="{{ $value('seo_bing_verification') }}"
                        placeholder="Verification code"
                    >
                </div>


                <div class="field full">
                    <label>Schema JSON-LD</label>

                    <textarea
                        name="seo_schema"
                        rows="7"
placeholder='{"@@context":"https://schema.org"}'
                    >{{ $value('seo_schema') }}</textarea>
                </div>


                <div class="field full">
                    <label>Analytics / Scripts — Head</label>

                    <textarea
                        name="seo_analytics_head"
                        rows="6"
                        placeholder="Paste head scripts..."
                    >{{ $value('seo_analytics_head') }}</textarea>
                </div>


                <div class="field full">
                    <label>Analytics / Scripts — Body</label>

                    <textarea
                        name="seo_analytics_body"
                        rows="6"
                        placeholder="Paste body scripts..."
                    >{{ $value('seo_analytics_body') }}</textarea>
                </div>


                {{-- OG image --}}
                <div class="field full">

                    <div class="media-card">

                        <div class="media-preview og-preview">

                            @if(!empty($settings['seo.og_image']))

                                <img
                                    src="{{ $image($settings['seo.og_image']) }}"
                                    alt="OG Image"
                                >

                            @else

                                <div class="empty-media">
                                    <i class="fas fa-image"></i>
                                    <span>No image</span>
                                </div>

                            @endif

                        </div>


                        <div class="media-body">

                            <h3>Open Graph Image</h3>

                            <p>
                                Image used when pages are shared on social platforms.
                            </p>

                            <input
                                type="file"
                                name="seo_og_image"
                                accept=".jpg,.jpeg,.png,.webp"
                            >

                            <small>
                                JPG, JPEG, PNG or WEBP · Maximum 4MB
                            </small>

                            @if(!empty($settings['seo.og_image']))
                                <label class="remove-option">
                                    <input
                                        type="checkbox"
                                        name="remove_seo_og_image"
                                        value="1"
                                    >

                                    Remove current OG image
                                </label>
                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </section>


        {{-- =========================================================
             SOCIAL COMPATIBILITY
        ========================================================== --}}
        <section class="settings-card">

            <div class="card-title">

                <div class="title-icon">
                    <i class="fas fa-share-alt"></i>
                </div>

                <div>
                    <h2>Social Compatibility</h2>
                    <p>Existing social settings retained for compatibility.</p>
                </div>

            </div>


            <div class="form-grid">

                <div class="field">
                    <label>Facebook</label>

                    <input
                        type="url"
                        name="facebook_url"
                        value="{{ $value('facebook_url', $settings['social.facebook'] ?? '') }}"
                        placeholder="https://facebook.com/..."
                    >
                </div>


                <div class="field">
                    <label>Instagram</label>

                    <input
                        type="url"
                        name="instagram_url"
                        value="{{ $value('instagram_url', $settings['social.instagram'] ?? '') }}"
                        placeholder="https://instagram.com/..."
                    >
                </div>


                <div class="field">
                    <label>YouTube</label>

                    <input
                        type="url"
                        name="youtube_url"
                        value="{{ $value('youtube_url', $settings['social.youtube'] ?? '') }}"
                        placeholder="https://youtube.com/..."
                    >
                </div>


                <div class="field">
                    <label>LinkedIn</label>

                    <input
                        type="url"
                        name="linkedin_url"
                        value="{{ $value('linkedin_url', $settings['social.linkedin'] ?? '') }}"
                        placeholder="https://linkedin.com/..."
                    >
                </div>


                <div class="field">
                    <label>X / Twitter</label>

                    <input
                        type="url"
                        name="twitter_url"
                        value="{{ $value('twitter_url', $settings['social.twitter'] ?? '') }}"
                        placeholder="https://x.com/..."
                    >
                </div>


                <div class="field">
                    <label>WhatsApp</label>

                    <input
                        type="url"
                        name="whatsapp_url"
                        value="{{ $value('whatsapp_url', $settings['social.whatsapp'] ?? '') }}"
                        placeholder="https://wa.me/..."
                    >
                </div>

            </div>

        </section>


        {{-- SAVE --}}
        <div class="save-bar">

            <div>
                <strong>All settings are centralized</strong>
                <span>One save updates the complete website configuration.</span>
            </div>


            <button
                type="submit"
                class="save-button"
                id="save-button"
            >
                <i class="fas fa-save"></i>
                Save Website Settings
            </button>

        </div>

    </form>

</div>


{{-- ================================================================
     JAVASCRIPT
================================================================= --}}
<script>
(function () {

    function addRow(containerId, html) {
        const container = document.getElementById(containerId);

        if (!container) {
            return;
        }

        const index = container.children.length;

        container.insertAdjacentHTML(
            'beforeend',
            html.replaceAll('__INDEX__', index)
        );

        updateNumbers(container);
    }


    function updateNumbers(container) {
        [...container.children].forEach(function (row, index) {

            const number = row.querySelector('.row-number');

            if (number) {
                number.textContent = index + 1;
            }

        });
    }


    window.removeRow = function (button) {

        const row = button.closest(
            '.repeat-row, .simple-row, .link-row'
        );

        if (!row) {
            return;
        }

        const parent = row.parentElement;

        row.remove();

        if (parent) {
            updateNumbers(parent);
        }
    };


    window.removeColumn = function (button) {

        const column = button.closest('.column-box');

        if (column) {
            column.remove();
        }
    };


    window.addNavigation = function () {

        addRow(
            'navigation-list',

            `
            <div class="repeat-row">

                <div class="row-number">
                    __INDEX_PLUS__
                </div>

                <div class="repeat-fields">

                    <div class="field">
                        <label>Label</label>
                        <input
                            type="text"
                            name="header_navigation[__INDEX__][label]"
                            placeholder="Home"
                        >
                    </div>

                    <div class="field">
                        <label>URL</label>
                        <input
                            type="text"
                            name="header_navigation[__INDEX__][url]"
                            placeholder="/"
                        >
                    </div>

                    <div class="field">
                        <label>Icon</label>
                        <input
                            type="text"
                            name="header_navigation[__INDEX__][icon]"
                            placeholder="fas fa-home"
                        >
                    </div>

                    <div class="field">
                        <label>Target</label>

                        <select
                            name="header_navigation[__INDEX__][target]"
                        >
                            <option value="_self">Same Window</option>
                            <option value="_blank">New Window</option>
                        </select>
                    </div>

                </div>

                <div class="row-actions">

                    <label class="active-check">
                        <input
                            type="checkbox"
                            name="header_navigation[__INDEX__][enabled]"
                            value="1"
                            checked
                        >
                        Active
                    </label>

                    <button
                        type="button"
                        class="delete-btn"
                        onclick="removeRow(this)"
                    >
                        <i class="fas fa-trash"></i>
                    </button>

                </div>

            </div>
            `
        );
    };


    window.addCtaBadge = function () {

        addRow(
            'cta-badges-list',

            `
            <div class="simple-row">

                <div class="row-number">
                    __INDEX_PLUS__
                </div>

                <div class="repeat-fields">

                    <div class="field">
                        <label>Text</label>

                        <input
                            type="text"
                            name="footer_cta_badges[__INDEX__][text]"
                            placeholder="Free Quote"
                        >
                    </div>

                    <div class="field">
                        <label>Icon</label>

                        <input
                            type="text"
                            name="footer_cta_badges[__INDEX__][icon]"
                            placeholder="fas fa-check"
                        >
                    </div>

                </div>

                <label class="active-check">
                    <input
                        type="checkbox"
                        name="footer_cta_badges[__INDEX__][enabled]"
                        value="1"
                        checked
                    >
                    Active
                </label>

                <button
                    type="button"
                    class="delete-btn"
                    onclick="removeRow(this)"
                >
                    <i class="fas fa-trash"></i>
                </button>

            </div>
            `
        );
    };


    window.addSocial = function () {

        addRow(
            'socials-list',

            `
            <div class="repeat-row">

                <div class="row-number">
                    __INDEX_PLUS__
                </div>

                <div class="repeat-fields repeat-3">

                    <div class="field">
                        <label>Name</label>

                        <input
                            type="text"
                            name="footer_socials[__INDEX__][name]"
                            placeholder="Facebook"
                        >
                    </div>

                    <div class="field">
                        <label>URL</label>

                        <input
                            type="text"
                            name="footer_socials[__INDEX__][url]"
                            placeholder="https://..."
                        >
                    </div>

                    <div class="field">
                        <label>Icon</label>

                        <input
                            type="text"
                            name="footer_socials[__INDEX__][icon]"
                            placeholder="fab fa-facebook-f"
                        >
                    </div>

                </div>

                <div class="row-actions">

                    <label class="active-check">
                        <input
                            type="checkbox"
                            name="footer_socials[__INDEX__][enabled]"
                            value="1"
                            checked
                        >
                        Active
                    </label>

                    <button
                        type="button"
                        class="delete-btn"
                        onclick="removeRow(this)"
                    >
                        <i class="fas fa-trash"></i>
                    </button>

                </div>

            </div>
            `
        );
    };


    window.addContact = function () {

        addRow(
            'contacts-list',

            `
            <div class="repeat-row">

                <div class="row-number">
                    __INDEX_PLUS__
                </div>

                <div class="repeat-fields repeat-4">

                    <div class="field">
                        <label>Label</label>

                        <input
                            type="text"
                            name="footer_contacts[__INDEX__][label]"
                            placeholder="Phone"
                        >
                    </div>

                    <div class="field">
                        <label>Value</label>

                        <input
                            type="text"
                            name="footer_contacts[__INDEX__][value]"
                            placeholder="+91..."
                        >
                    </div>

                    <div class="field">
                        <label>URL</label>

                        <input
                            type="text"
                            name="footer_contacts[__INDEX__][url]"
                            placeholder="tel:+91..."
                        >
                    </div>

                    <div class="field">
                        <label>Icon</label>

                        <input
                            type="text"
                            name="footer_contacts[__INDEX__][icon]"
                            placeholder="fas fa-phone"
                        >
                    </div>

                </div>

                <div class="row-actions">

                    <label class="active-check">
                        <input
                            type="checkbox"
                            name="footer_contacts[__INDEX__][enabled]"
                            value="1"
                            checked
                        >
                        Active
                    </label>

                    <button
                        type="button"
                        class="delete-btn"
                        onclick="removeRow(this)"
                    >
                        <i class="fas fa-trash"></i>
                    </button>

                </div>

            </div>
            `
        );
    };


    window.addTrustBadge = function () {

        addRow(
            'trust-list',

            `
            <div class="repeat-row">

                <div class="row-number">
                    __INDEX_PLUS__
                </div>

                <div class="repeat-fields repeat-3">

                    <div class="field">
                        <label>Title</label>

                        <input
                            type="text"
                            name="footer_trust_badges[__INDEX__][title]"
                            placeholder="4.9 Google Rating"
                        >
                    </div>

                    <div class="field">
                        <label>Subtitle</label>

                        <input
                            type="text"
                            name="footer_trust_badges[__INDEX__][subtitle]"
                            placeholder="Excellent"
                        >
                    </div>

                    <div class="field">
                        <label>Icon</label>

                        <input
                            type="text"
                            name="footer_trust_badges[__INDEX__][icon]"
                            placeholder="fas fa-star"
                        >
                    </div>

                </div>

                <div class="row-actions">

                    <label class="active-check">
                        <input
                            type="checkbox"
                            name="footer_trust_badges[__INDEX__][enabled]"
                            value="1"
                            checked
                        >
                        Active
                    </label>

                    <button
                        type="button"
                        class="delete-btn"
                        onclick="removeRow(this)"
                    >
                        <i class="fas fa-trash"></i>
                    </button>

                </div>

            </div>
            `
        );
    };


    window.addDestination = function () {

        addRow(
            'destinations-list',

            `
            <div class="repeat-row">

                <div class="row-number">
                    __INDEX_PLUS__
                </div>

                <div class="repeat-fields">

                    <div class="field">
                        <label>Destination</label>

                        <input
                            type="text"
                            name="footer_destinations[__INDEX__][label]"
                            placeholder="Varanasi"
                        >
                    </div>

                    <div class="field">
                        <label>URL</label>

                        <input
                            type="text"
                            name="footer_destinations[__INDEX__][url]"
                            placeholder="/destinations/varanasi"
                        >
                    </div>

                </div>

                <div class="row-actions">

                    <label class="active-check">
                        <input
                            type="checkbox"
                            name="footer_destinations[__INDEX__][enabled]"
                            value="1"
                            checked
                        >
                        Active
                    </label>

                    <button
                        type="button"
                        class="delete-btn"
                        onclick="removeRow(this)"
                    >
                        <i class="fas fa-trash"></i>
                    </button>

                </div>

            </div>
            `
        );
    };


    window.addBottomLink = function () {

        addRow(
            'bottom-links-list',

            `
            <div class="simple-row">

                <div class="row-number">
                    __INDEX_PLUS__
                </div>

                <div class="repeat-fields">

                    <div class="field">
                        <label>Label</label>

                        <input
                            type="text"
                            name="footer_bottom_links[__INDEX__][label]"
                            placeholder="Privacy Policy"
                        >
                    </div>

                    <div class="field">
                        <label>URL</label>

                        <input
                            type="text"
                            name="footer_bottom_links[__INDEX__][url]"
                            placeholder="/privacy-policy"
                        >
                    </div>

                </div>

                <label class="active-check">
                    <input
                        type="checkbox"
                        name="footer_bottom_links[__INDEX__][enabled]"
                        value="1"
                        checked
                    >
                    Active
                </label>

                <button
                    type="button"
                    class="delete-btn"
                    onclick="removeRow(this)"
                >
                    <i class="fas fa-trash"></i>
                </button>

            </div>
            `
        );
    };


    window.addColumn = function () {

        const container = document.getElementById('columns-list');

        if (!container) {
            return;
        }

        const index = container.children.length;

        container.insertAdjacentHTML(
            'beforeend',

            `
            <div class="column-box">

                <div class="column-header">

                    <div>
                        <span>FOOTER COLUMN</span>
                        <strong>New Column</strong>
                    </div>

                    <div class="column-actions">

                        <label class="active-check">

                            <input
                                type="checkbox"
                                name="footer_columns[${index}][enabled]"
                                value="1"
                                checked
                            >

                            Active

                        </label>

                        <button
                            type="button"
                            class="delete-btn"
                            onclick="removeColumn(this)"
                        >
                            <i class="fas fa-trash"></i>
                        </button>

                    </div>

                </div>


                <div class="form-grid">

                    <div class="field full">

                        <label>Column Title</label>

                        <input
                            type="text"
                            name="footer_columns[${index}][title]"
                            placeholder="Quick Links"
                        >

                    </div>

                </div>


                <div class="links-area">

                    <div class="repeat-header">

                        <strong>Links</strong>

                        <button
                            type="button"
                            class="add-btn small"
                            onclick="addColumnLink(this)"
                        >
                            <i class="fas fa-plus"></i>
                            Add Link
                        </button>

                    </div>


                    <div class="column-links"></div>

                </div>

            </div>
            `
        );
    };


    window.addColumnLink = function (button) {

        const column = button.closest('.column-box');

        if (!column) {
            return;
        }

        const container = column.querySelector('.column-links');

        if (!container) {
            return;
        }

        const allColumns = [
            ...document.querySelectorAll('#columns-list .column-box')
        ];

        const columnIndex = allColumns.indexOf(column);

        const linkIndex = container.children.length;

        container.insertAdjacentHTML(
            'beforeend',

            `
            <div class="link-row">

                <div class="field">

                    <label>Label</label>

                    <input
                        type="text"
                        name="footer_columns[${columnIndex}][links][${linkIndex}][label]"
                        placeholder="About Us"
                    >

                </div>


                <div class="field">

                    <label>URL</label>

                    <input
                        type="text"
                        name="footer_columns[${columnIndex}][links][${linkIndex}][url]"
                        placeholder="/about"
                    >

                </div>


                <div class="field">

                    <label>Icon</label>

                    <input
                        type="text"
                        name="footer_columns[${columnIndex}][links][${linkIndex}][icon]"
                        placeholder="fas fa-angle-right"
                    >

                </div>


                <label class="active-check">

                    <input
                        type="checkbox"
                        name="footer_columns[${columnIndex}][links][${linkIndex}][enabled]"
                        value="1"
                        checked
                    >

                    Active

                </label>


                <button
                    type="button"
                    class="delete-btn"
                    onclick="removeRow(this)"
                >
                    <i class="fas fa-trash"></i>
                </button>

            </div>
            `
        );
    };


    document.addEventListener('DOMContentLoaded', function () {

        const form = document.getElementById('settings-form');
        const button = document.getElementById('save-button');

        if (!form || !button) {
            return;
        }

        form.addEventListener('submit', function () {

            button.disabled = true;

            button.innerHTML =
                '<i class="fas fa-spinner fa-spin"></i> Saving...';

        });

    });

})();
</script>


<style>
/* ================================================================
   PAGE
================================================================ */

.website-settings {
    width: 100%;
    max-width: 1500px;
    margin: 0 auto;
    padding: 26px;
    box-sizing: border-box;
}


/* ================================================================
   HEADING
================================================================ */

.settings-heading {
    margin-bottom: 22px;
}

.settings-heading h1 {
    margin: 0 0 5px;
    color: #111827;
    font-size: 27px;
    font-weight: 750;
}

.settings-heading p {
    margin: 0;
    color: #6b7280;
    font-size: 13px;
}


/* ================================================================
   ALERT
================================================================ */

.alert {
    display: flex;
    gap: 10px;
    margin-bottom: 18px;
    padding: 12px 14px;
    border-radius: 9px;
    font-size: 13px;
}

.alert.success {
    background: #ecfdf5;
    border: 1px solid #bbf7d0;
    color: #166534;
}

.alert.error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.alert ul {
    margin: 6px 0 0;
    padding-left: 18px;
}


/* ================================================================
   CARD
================================================================ */

.settings-card {
    margin-bottom: 18px;
    padding: 23px;
    border: 1px solid #e5e7eb;
    border-radius: 13px;
    background: #fff;
    box-shadow: 0 2px 8px rgba(15, 23, 42, .025);
}


/* ================================================================
   CARD TITLE
================================================================ */

.card-title {
    display: flex;
    align-items: center;
    gap: 11px;
    margin-bottom: 21px;
    padding-bottom: 16px;
    border-bottom: 1px solid #edf0f3;
}

.card-title.with-toggle {
    justify-content: space-between;
}

.title-content {
    display: flex;
    align-items: center;
    gap: 11px;
}

.title-icon {
    width: 37px;
    height: 37px;
    flex: 0 0 37px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: #f3f4f6;
    color: #374151;
    font-size: 14px;
}

.card-title h2 {
    margin: 0 0 3px;
    color: #111827;
    font-size: 17px;
    font-weight: 700;
}

.card-title p {
    margin: 0;
    color: #7b8491;
    font-size: 11.5px;
}


/* ================================================================
   FORM
================================================================ */

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

.field {
    min-width: 0;
}

.field.full {
    grid-column: 1 / -1;
}

.field label {
    display: block;
    margin-bottom: 6px;
    color: #374151;
    font-size: 12px;
    font-weight: 650;
}

.field input,
.field textarea,
.field select {
    width: 100%;
    min-height: 42px;
    box-sizing: border-box;
    padding: 9px 11px;
    border: 1px solid #d8dde5;
    border-radius: 8px;
    outline: none;
    background: #fff;
    color: #111827;
    font-family: inherit;
    font-size: 13px;
    transition: border-color .15s ease, box-shadow .15s ease;
}

.field textarea {
    min-height: 90px;
    resize: vertical;
    line-height: 1.5;
}

.field input:focus,
.field textarea:focus,
.field select:focus {
    border-color: #111827;
    box-shadow: 0 0 0 3px rgba(17, 24, 39, .055);
}

.field small {
    display: block;
    margin-top: 6px;
    color: #8a94a3;
    font-size: 10.5px;
}


/* ================================================================
   TOGGLE
================================================================ */

.switch {
    position: relative;
    display: inline-flex;
    cursor: pointer;
}

.switch input {
    display: none;
}

.switch span {
    position: relative;
    width: 39px;
    height: 22px;
    display: block;
    border-radius: 999px;
    background: #d1d5db;
    transition: background .15s ease;
}

.switch span::after {
    content: "";
    position: absolute;
    top: 3px;
    left: 3px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .2);
    transition: transform .15s ease;
}

.switch input:checked + span {
    background: #111827;
}

.switch input:checked + span::after {
    transform: translateX(17px);
}


/* ================================================================
   SUB CARD
================================================================ */

.sub-card {
    margin-top: 25px;
    padding-top: 21px;
    border-top: 1px solid #edf0f3;
}

.sub-card-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 15px;
}

.sub-card-heading h3 {
    margin: 0 0 3px;
    color: #1f2937;
    font-size: 14px;
    font-weight: 700;
}

.sub-card-heading p {
    margin: 0;
    color: #7b8491;
    font-size: 11px;
}


/* ================================================================
   MEDIA
================================================================ */

.media-card {
    display: flex;
    align-items: center;
    gap: 19px;
    padding: 15px;
    border: 1px solid #e7eaee;
    border-radius: 10px;
    background: #fafbfc;
}

.media-preview {
    width: 200px;
    height: 105px;
    flex: 0 0 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border: 1px dashed #cbd2dc;
    border-radius: 8px;
    background: #fff;
}

.media-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.favicon-preview {
    width: 90px;
    height: 90px;
    flex: 0 0 90px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border: 1px dashed #cbd2dc;
    border-radius: 8px;
    background: #fff;
}

.favicon-preview img {
    max-width: 80%;
    max-height: 80%;
    object-fit: contain;
}

.og-preview {
    width: 210px;
    height: 115px;
    flex-basis: 210px;
}

.empty-media {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    color: #9ca3af;
    font-size: 10px;
}

.empty-media i {
    font-size: 19px;
}

.media-body {
    flex: 1;
    min-width: 0;
}

.media-body h3 {
    margin: 0 0 6px;
    color: #1f2937;
    font-size: 13px;
    font-weight: 700;
}

.media-body p {
    margin: 0 0 10px;
    color: #6b7280;
    font-size: 11px;
    line-height: 1.5;
}

.media-body input[type="file"] {
    width: 100%;
    box-sizing: border-box;
    padding: 7px;
    border: 1px solid #d8dde5;
    border-radius: 7px;
    background: #fff;
    font-size: 11px;
}

.media-body small {
    display: block;
    margin-top: 5px;
    color: #8a94a3;
    font-size: 10px;
}

.remove-option {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 10px;
    color: #6b7280;
    font-size: 11px;
    cursor: pointer;
}

.remove-option input {
    width: 14px;
    height: 14px;
}


/* ================================================================
   BUTTONS
================================================================ */

.add-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-height: 34px;
    padding: 7px 11px;
    border: 1px solid #d8dde5;
    border-radius: 7px;
    background: #fff;
    color: #374151;
    font-family: inherit;
    font-size: 11px;
    font-weight: 650;
    cursor: pointer;
}

.add-btn:hover {
    background: #f8fafc;
}

.add-btn.small {
    min-height: 30px;
    padding: 5px 9px;
    font-size: 10.5px;
}

.delete-btn {
    width: 33px;
    height: 33px;
    flex: 0 0 33px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #fecaca;
    border-radius: 7px;
    background: #fff;
    color: #dc2626;
    cursor: pointer;
}

.delete-btn:hover {
    background: #fef2f2;
}


/* ================================================================
   REPEATER
================================================================ */

.repeat-row,
.simple-row {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 9px;
    padding: 11px;
    border: 1px solid #e6e9ee;
    border-radius: 8px;
    background: #fafbfc;
}

.row-number {
    width: 25px;
    height: 25px;
    flex: 0 0 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 5px;
    border-radius: 6px;
    background: #eef0f3;
    color: #4b5563;
    font-size: 10px;
    font-weight: 700;
}

.repeat-fields {
    flex: 1;
    min-width: 0;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}

.repeat-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.repeat-4 {
    grid-template-columns: repeat(4, minmax(0, 1fr));
}

.row-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    padding-top: 5px;
}

.active-check,
.inline-check {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #6b7280;
    font-size: 10.5px;
    white-space: nowrap;
    cursor: pointer;
}

.active-check input,
.inline-check input {
    width: 14px;
    height: 14px;
    margin: 0;
    accent-color: #111827;
}

.repeat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin: 20px 0 10px;
}


/* ================================================================
   COLUMNS
================================================================ */

.column-box {
    margin-bottom: 12px;
    padding: 15px;
    border: 1px solid #e1e5ea;
    border-radius: 9px;
    background: #fafbfc;
}

.column-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 15px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e8ebef;
}

.column-header span {
    display: block;
    margin-bottom: 3px;
    color: #8a94a3;
    font-size: 8.5px;
    font-weight: 700;
    letter-spacing: .6px;
}

.column-header strong {
    color: #1f2937;
    font-size: 13px;
}

.column-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.links-area {
    margin-top: 18px;
    padding-top: 15px;
    border-top: 1px solid #e8ebef;
}

.column-links {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.link-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr auto auto;
    align-items: end;
    gap: 9px;
    padding: 9px;
    border: 1px solid #e8ebef;
    border-radius: 7px;
    background: #fff;
}


/* ================================================================
   INFO
================================================================ */

.info {
    display: flex;
    gap: 8px;
    margin-top: 17px;
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 7px;
    background: #f8fafc;
    color: #6b7280;
    font-size: 10.5px;
    line-height: 1.5;
}


/* ================================================================
   SAVE
================================================================ */

.save-bar {
    position: sticky;
    bottom: 14px;
    z-index: 20;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-top: 5px;
    padding: 12px 14px;
    border: 1px solid #e2e6eb;
    border-radius: 10px;
    background: rgba(255,255,255,.96);
    box-shadow: 0 8px 25px rgba(15,23,42,.08);
    backdrop-filter: blur(8px);
}

.save-bar strong {
    display: block;
    margin-bottom: 2px;
    color: #1f2937;
    font-size: 11px;
}

.save-bar span {
    color: #8a94a3;
    font-size: 10px;
}

.save-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-height: 40px;
    padding: 9px 17px;
    border: 0;
    border-radius: 8px;
    background: #111827;
    color: #fff;
    font-family: inherit;
    font-size: 12px;
    font-weight: 650;
    cursor: pointer;
}

.save-button:disabled {
    cursor: wait;
    opacity: .7;
}


/* ================================================================
   RESPONSIVE
================================================================ */

@media (max-width: 1100px) {

    .repeat-4 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .link-row {
        grid-template-columns: 1fr 1fr;
    }

}

@media (max-width: 850px) {

    .website-settings {
        padding: 18px;
    }

    .form-grid,
    .repeat-fields,
    .repeat-3,
    .repeat-4 {
        grid-template-columns: 1fr;
    }

    .field.full {
        grid-column: auto;
    }

    .repeat-row,
    .simple-row {
        flex-wrap: wrap;
    }

    .repeat-fields {
        width: calc(100% - 36px);
    }

    .row-actions {
        width: 100%;
        justify-content: flex-end;
    }

    .link-row {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 650px) {

    .website-settings {
        padding: 12px;
    }

    .settings-card {
        padding: 16px;
    }

    .card-title.with-toggle {
        align-items: flex-start;
    }

    .media-card {
        align-items: flex-start;
        flex-direction: column;
    }

    .media-preview,
    .og-preview {
        width: 100%;
        flex-basis: auto;
    }

    .favicon-preview {
        width: 90px;
        flex-basis: 90px;
    }

    .media-body {
        width: 100%;
    }

    .sub-card-heading {
        align-items: flex-start;
        flex-direction: column;
    }

    .save-bar {
        align-items: stretch;
        flex-direction: column;
    }

    .save-button {
        width: 100%;
    }

}
</style>

@endsection