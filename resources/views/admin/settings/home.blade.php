@extends('admin.layouts.app')

@section('title', 'Home Page Settings')

@section('description', 'Control homepage sections and manage the complete user-side footer from one place.')

@section('content')
@php
    $value = fn ($key, $default = '') => old($key, $settings[$key] ?? $default);

    $defaultFooterCtaBadges = [
        ['text' => 'Free Quote', 'icon' => '✓', 'enabled' => true],
        ['text' => 'Custom Itinerary', 'icon' => '✓', 'enabled' => true],
        ['text' => '24-hr Response', 'icon' => '✓', 'enabled' => true],
    ];

    $defaultFooterSocials = [
        ['name' => 'Facebook', 'icon' => 'f', 'url' => '#', 'enabled' => true],
        ['name' => 'YouTube', 'icon' => '▶', 'url' => '#', 'enabled' => true],
        ['name' => 'Instagram', 'icon' => '◎', 'url' => '#', 'enabled' => true],
        ['name' => 'WhatsApp', 'icon' => '◉', 'url' => '#', 'enabled' => true],
    ];

    $defaultFooterColumns = [
        [
            'title' => 'TOUR PACKAGES',
            'enabled' => true,
            'links' => [
                ['label' => 'Spiritual & Pilgrimage', 'icon' => '♨', 'url' => '#', 'enabled' => true],
                ['label' => 'Holidays', 'icon' => '♨', 'url' => '#', 'enabled' => true],
                ['label' => 'School & College', 'icon' => '♟', 'url' => '#', 'enabled' => true],
                ['label' => 'Business Trips', 'icon' => '▣', 'url' => '#', 'enabled' => true],
                ['label' => 'Monthly Tours', 'icon' => '⟳', 'url' => '#', 'enabled' => true],
                ['label' => 'View All Packages', 'icon' => '✈', 'url' => '#', 'enabled' => true],
            ],
        ],
        [
            'title' => 'QUICK LINKS',
            'enabled' => true,
            'links' => [
                ['label' => 'About Us', 'icon' => '●', 'url' => route('about'), 'enabled' => true],
                ['label' => 'Travel Blog', 'icon' => '✎', 'url' => route('blog.index'), 'enabled' => true],
                ['label' => 'Contact Us', 'icon' => '◉', 'url' => route('contact'), 'enabled' => true],
                ['label' => 'Get Free Quote', 'icon' => '▤', 'url' => route('contact'), 'enabled' => true],
                ['label' => 'Become a Partner', 'icon' => '♧', 'url' => route('contact'), 'enabled' => true],
                ['label' => 'Terms & Conditions', 'icon' => '⚖', 'url' => '/terms', 'enabled' => true],
                ['label' => 'Privacy Policy', 'icon' => '◈', 'url' => '/privacy', 'enabled' => true],
            ],
        ],
    ];

    $defaultFooterContacts = [
        ['label' => 'PHONE / WHATSAPP', 'value' => '+91-8990498843, +91-7894534878', 'url' => 'tel:+918978498843', 'icon' => '☎', 'enabled' => true],
        ['label' => 'EMAIL US', 'value' => 'bookings@travels.com', 'url' => 'mailto:bookings@travels.com', 'icon' => '✉', 'enabled' => true],
        ['label' => 'OUR OFFICE', 'value' => 'India', 'url' => '', 'icon' => '●', 'enabled' => true],
        ['label' => 'REGISTERED ADDRESS', 'value' => '101, Sunrise Business Hub, C.G. Road, Ahmedabad, Gujarat - 380009', 'url' => '', 'icon' => '▣', 'enabled' => true],
        ['label' => 'BRANCH OFFICE', 'value' => '204, Gateway Plaza, Airport Road, Ahmedabad, Gujarat - 380015', 'url' => '', 'icon' => '●', 'enabled' => true],
        ['label' => 'WORKING HOURS', 'value' => 'Mon–Sat · 9am–7pm', 'url' => '', 'icon' => '◷', 'enabled' => true],
    ];

    $defaultFooterBadges = [
        ['title' => '4.9 Google Rating', 'subtitle' => '', 'icon' => '★', 'enabled' => true],
        ['title' => 'IATA Affiliated', 'subtitle' => '', 'icon' => '✓', 'enabled' => true],
        ['title' => 'Secure Payments', 'subtitle' => '', 'icon' => '🔒', 'enabled' => true],
    ];

    $defaultFooterDestinations = [
        ['label' => 'Varanasi', 'url' => '#', 'enabled' => true],
        ['label' => 'Ayodhya', 'url' => '#', 'enabled' => true],
        ['label' => 'Char Dham', 'url' => '#', 'enabled' => true],
        ['label' => 'Prayagraj', 'url' => '#', 'enabled' => true],
        ['label' => 'Shirdi', 'url' => '#', 'enabled' => true],
        ['label' => 'Dubai', 'url' => '#', 'enabled' => true],
        ['label' => 'Sri Lanka', 'url' => '#', 'enabled' => true],
        ['label' => 'Hong Kong', 'url' => '#', 'enabled' => true],
        ['label' => 'Muktinath', 'url' => '#', 'enabled' => true],
        ['label' => 'Tirupati', 'url' => '#', 'enabled' => true],
    ];

    $defaultFooterBottomLinks = [
        ['label' => 'Terms & Conditions', 'url' => '/terms', 'enabled' => true],
        ['label' => 'Privacy Policy', 'url' => '/privacy', 'enabled' => true],
    ];

    $footerCtaBadges = old('footer_cta_badges', $settings['footer.cta_badges'] ?? $defaultFooterCtaBadges);
    $footerSocials = old('footer_socials', $settings['footer.socials'] ?? $defaultFooterSocials);
    $footerColumns = old('footer_columns', $settings['footer.columns'] ?? $defaultFooterColumns);
    $footerContacts = old('footer_contacts', $settings['footer.contacts'] ?? $defaultFooterContacts);
    $footerBadges = old('footer_trust_badges', $settings['footer.trust_badges'] ?? $defaultFooterBadges);
    $footerDestinations = old('footer_destinations', $settings['footer.destinations'] ?? $defaultFooterDestinations);
    $footerBottomLinks = old('footer_bottom_links', $settings['footer.bottom_links'] ?? $defaultFooterBottomLinks);

    if (!is_array($footerSocials)) $footerSocials = [];
    if (!is_array($footerColumns)) $footerColumns = [];
    if (!is_array($footerContacts)) $footerContacts = [];
    if (!is_array($footerBadges)) $footerBadges = [];
    if (!is_array($footerDestinations)) $footerDestinations = [];
    if (!is_array($footerBottomLinks)) $footerBottomLinks = [];
    if (!is_array($footerCtaBadges)) $footerCtaBadges = [];
@endphp

<div class="website-settings home-settings">

    @if(session('success'))
        <div class="general-settings-alert general-settings-alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="general-settings-alert general-settings-alert-error">
            <strong>Please fix the following:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.home.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- HOME --}}
        <div class="general-settings-card">
            <div class="general-settings-card-header">
                <div class="general-settings-card-icon">⌂</div>
                <div>
                    <h2>Homepage Status</h2>
                    <p>Enable or disable the main homepage areas.</p>
                </div>
            </div>

            <div class="general-settings-grid">
                @foreach([
                    ['home_enabled', 'Home Enabled', 'Enable the homepage.'],
                    ['home_hero_enabled', 'Hero Enabled', 'Show the hero/banner slider.'],
                    ['home_hero_autoplay', 'Hero Autoplay', 'Automatically change hero slides.'],
                    ['home_hero_text', 'Hero Text', 'Show text over hero banners.'],
                    ['home_trending_enabled', 'Trending Tours', 'Show the trending tours section.'],
                    ['home_categories_enabled', 'Categories', 'Show the categories section.'],
                    ['home_promotional_enabled', 'Promotional Banners', 'Show promotional banners.'],
                    ['home_style_spotlight_enabled', 'Style Spotlight', 'Show the style spotlight section.'],
                    ['home_section_hero', 'Section: Hero', 'Allow hero in homepage section layout.'],
                    ['home_section_trending', 'Section: Trending', 'Allow trending in homepage section layout.'],
                    ['home_section_categories', 'Section: Categories', 'Allow categories in homepage section layout.'],
                    ['home_section_promotional', 'Section: Promotional', 'Allow promotional section.'],
                    ['home_section_style_spotlight', 'Section: Spotlight', 'Allow style spotlight section.'],
                    ['home_section_newsletter', 'Section: Newsletter', 'Allow newsletter section.'],
                ] as [$key, $label, $help])
                    <div class="settings-toggle-field">
                        <div>
                            <strong>{{ $label }}</strong>
                            <small>{{ $help }}</small>
                        </div>
                        <label class="switch">
                            <input type="hidden" name="{{ $key }}" value="0">
                            <input type="checkbox" name="{{ $key }}" value="1" @checked((bool) old($key, $settings[str_replace('_', '.', preg_replace('/^home_/', 'home.', $key))] ?? false))>
                            <span></span>
                        </label>
                    </div>
                @endforeach

                <div class="general-settings-field">
                    <label for="home_hero_interval">Hero Interval (ms)</label>
                    <input id="home_hero_interval" type="number" name="home_hero_interval"
                           value="{{ old('home_hero_interval', $settings['home.hero_interval'] ?? 7000) }}"
                           min="1000" max="30000">
                    <small>Example: 5000 = 5 seconds.</small>
                </div>
            </div>
        </div>

        <div class="general-settings-card">
            <div class="general-settings-card-header">
                <div class="general-settings-card-icon">T</div>
                <div><h2>Homepage Titles</h2><p>Change headings displayed on homepage sections.</p></div>
            </div>
            <div class="general-settings-grid">
                @foreach([
                    ['home_categories_title', 'Categories Title', 'Explore Our Tours'],
                    ['home_category_title', 'Category Section Title', 'Explore Our Tours'],
                    ['home_trending_title', 'Trending Title', 'Trending Tours'],
                    ['home_promotional_title', 'Promotional Title', 'Featured Offers'],
                    ['home_spotlight_title', 'Spotlight Title', 'Style Spotlight'],
                ] as [$key, $label, $default])
                    <div class="general-settings-field">
                        <label>{{ $label }}</label>
                        <input type="text" name="{{ $key }}" value="{{ old($key, $settings['home.'.str_replace('home_', '', $key)] ?? $default) }}">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="settings-section-title">
            <span>FOOTER MANAGEMENT</span>
            <p>Everything below controls the static footer you shared. Admin can add, edit, enable, disable and remove items.</p>
        </div>

        <div class="general-settings-card">
            <div class="general-settings-card-header">
                <div class="general-settings-card-icon">F</div>
                <div><h2>Footer Basic</h2><p>Logo, description and footer visibility.</p></div>
            </div>

            <div class="general-settings-grid">
                <div class="settings-toggle-field">
                    <div><strong>Footer Enabled</strong><small>Show the complete footer on the user side.</small></div>
                    <label class="switch">
                        <input type="hidden" name="footer_enabled" value="0">
                        <input type="checkbox" name="footer_enabled" value="1" @checked((bool) old('footer_enabled', $settings['footer.enabled'] ?? true))>
                        <span></span>
                    </label>
                </div>

                <div class="general-settings-field">
                    <label>Footer Logo</label>
                    <input type="file" name="footer_logo" accept=".jpg,.jpeg,.png,.webp">
                    @if(!empty($settings['footer.logo']))
                        <div class="current-file">
                            <img src="{{ filter_var($settings['footer.logo'], FILTER_VALIDATE_URL) ? $settings['footer.logo'] : asset('storage/'.ltrim($settings['footer.logo'], '/')) }}" alt="">
                            <label><input type="checkbox" name="remove_footer_logo" value="1"> Remove current logo</label>
                        </div>
                    @endif
                </div>

                <div class="general-settings-field">
                    <label>Logo Alt Text</label>
                    <input type="text" name="footer_logo_alt" value="{{ old('footer_logo_alt', $settings['footer.logo_alt'] ?? $settings['site.name'] ?? '') }}">
                </div>

                <div class="general-settings-field general-settings-field-full">
                    <label>Footer Description</label>
                    <textarea name="footer_description" rows="4">{{ old('footer_description', $settings['footer.description'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- CTA --}}
        <div class="general-settings-card">
            <div class="general-settings-card-header">
                <div class="general-settings-card-icon">↗</div>
                <div><h2>Footer CTA</h2><p>Edit the “Ready to Travel With Faith?” area and its badges/buttons.</p></div>
            </div>

            <div class="general-settings-grid">
                <div class="settings-toggle-field">
                    <div><strong>CTA Enabled</strong><small>Show the CTA strip above the footer.</small></div>
                    <label class="switch">
                        <input type="hidden" name="footer_cta_enabled" value="0">
                        <input type="checkbox" name="footer_cta_enabled" value="1" @checked((bool) old('footer_cta_enabled', $settings['footer.cta_enabled'] ?? false))>
                        <span></span>
                    </label>
                </div>
                <div></div>

                <div class="general-settings-field">
                    <label>CTA Title</label>
                    <input type="text" name="footer_cta_title" value="{{ old('footer_cta_title', $settings['footer.cta_title'] ?? 'Ready to Travel With Faith?') }}">
                </div>
                <div class="general-settings-field">
                    <label>CTA Button Text</label>
                    <input type="text" name="footer_cta_button_text" value="{{ old('footer_cta_button_text', $settings['footer.cta_button_text'] ?? 'Plan My Trip') }}">
                </div>
                <div class="general-settings-field">
                    <label>CTA Button URL</label>
                    <input type="text" name="footer_cta_button_url" value="{{ old('footer_cta_button_url', $settings['footer.cta_button_url'] ?? route('contact')) }}">
                </div>
                <div class="general-settings-field general-settings-field-full">
                    <label>CTA Description</label>
                    <textarea name="footer_cta_description" rows="3">{{ old('footer_cta_description', $settings['footer.cta_description'] ?? 'Tell us your destination and dates – we will craft the perfect journey for you.') }}</textarea>
                </div>
            </div>

            <div class="repeater-wrap">
                <div class="repeater-heading"><h3>CTA Badges</h3><button type="button" class="add-btn" data-add="cta-badge">+ Add Badge</button></div>
                <div id="cta-badges">
                    @foreach($footerCtaBadges as $i => $item)
                        <div class="repeater-row">
                            <input type="hidden" name="footer_cta_badges[{{ $i }}][enabled]" value="0">
                            <label class="mini-toggle"><input type="checkbox" name="footer_cta_badges[{{ $i }}][enabled]" value="1" @checked(!empty($item['enabled']))> Enabled</label>
                            <input type="text" name="footer_cta_badges[{{ $i }}][icon]" value="{{ $item['icon'] ?? '✓' }}" placeholder="Icon">
                            <input type="text" name="footer_cta_badges[{{ $i }}][text]" value="{{ $item['text'] ?? '' }}" placeholder="Badge text">
                            <button type="button" class="remove-btn">Remove</button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- SOCIALS --}}
        <div class="general-settings-card">
            <div class="general-settings-card-header">
                <div class="general-settings-card-icon">●</div>
                <div><h2>Social Media</h2><p>Add unlimited social platforms. Facebook, Instagram, YouTube, WhatsApp or any custom platform.</p></div>
            </div>
            <div class="repeater-wrap">
                <div class="repeater-heading"><h3>Social Links</h3><button type="button" class="add-btn" data-add="social">+ Add Social</button></div>
                <div id="socials">
                    @foreach($footerSocials as $i => $item)
                        <div class="repeater-row">
                            <input type="hidden" name="footer_socials[{{ $i }}][enabled]" value="0">
                            <label class="mini-toggle"><input type="checkbox" name="footer_socials[{{ $i }}][enabled]" value="1" @checked(!empty($item['enabled']))> Enabled</label>
                            <input type="text" name="footer_socials[{{ $i }}][name]" value="{{ $item['name'] ?? $item['label'] ?? '' }}" placeholder="Platform name">
                            <input type="text" name="footer_socials[{{ $i }}][icon]" value="{{ $item['icon'] ?? '↗' }}" placeholder="Icon">
                            <input class="grow" type="url" name="footer_socials[{{ $i }}][url]" value="{{ $item['url'] ?? '' }}" placeholder="https://...">
                            <button type="button" class="remove-btn">Remove</button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- COLUMNS --}}
        <div class="general-settings-card">
            <div class="general-settings-card-header">
                <div class="general-settings-card-icon">≡</div>
                <div><h2>Footer Link Columns</h2><p>Create unlimited columns and unlimited links inside every column.</p></div>
            </div>
            <div class="repeater-wrap">
                <div class="repeater-heading"><h3>Columns</h3><button type="button" class="add-btn" data-add="column">+ Add Column</button></div>
                <div id="columns">
                    @foreach($footerColumns as $i => $column)
                        <div class="nested-card" data-column>
                            <div class="repeater-row">
                                <input type="hidden" name="footer_columns[{{ $i }}][enabled]" value="0">
                                <label class="mini-toggle"><input type="checkbox" name="footer_columns[{{ $i }}][enabled]" value="1" @checked(!empty($column['enabled']))> Enabled</label>
                                <input class="grow" type="text" name="footer_columns[{{ $i }}][title]" value="{{ $column['title'] ?? '' }}" placeholder="Column title">
                                <button type="button" class="remove-btn">Remove Column</button>
                            </div>
                            <div class="nested-links">
                                @foreach(($column['links'] ?? []) as $j => $link)
                                    <div class="repeater-row compact">
                                        <input type="hidden" name="footer_columns[{{ $i }}][links][{{ $j }}][enabled]" value="0">
                                        <label class="mini-toggle"><input type="checkbox" name="footer_columns[{{ $i }}][links][{{ $j }}][enabled]" value="1" @checked(!empty($link['enabled']))> On</label>
                                        <input type="text" name="footer_columns[{{ $i }}][links][{{ $j }}][icon]" value="{{ $link['icon'] ?? '' }}" placeholder="Icon">
                                        <input type="text" class="grow" name="footer_columns[{{ $i }}][links][{{ $j }}][label]" value="{{ $link['label'] ?? $link['name'] ?? '' }}" placeholder="Link label">
                                        <input type="text" class="grow" name="footer_columns[{{ $i }}][links][{{ $j }}][url]" value="{{ $link['url'] ?? '' }}" placeholder="/page or https://...">
                                        <button type="button" class="remove-btn">×</button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="add-nested-link">+ Add Link</button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- CONTACT --}}
        <div class="general-settings-card">
            <div class="general-settings-card-header">
                <div class="general-settings-card-icon">☎</div>
                <div><h2>Footer Contact</h2><p>Every contact row is editable and can be linked with tel:, mailto: or a normal URL.</p></div>
            </div>
            <div class="general-settings-grid">
                <div class="general-settings-field">
                    <label>Contact Section Title</label>
                    <input type="text" name="footer_contact_title" value="{{ old('footer_contact_title', $settings['footer.contact_title'] ?? 'GET IN TOUCH') }}">
                </div>
            </div>
            <div class="repeater-wrap">
                <div class="repeater-heading"><h3>Contact Items</h3><button type="button" class="add-btn" data-add="contact">+ Add Contact</button></div>
                <div id="contacts">
                    @foreach($footerContacts as $i => $item)
                        <div class="repeater-row">
                            <input type="hidden" name="footer_contacts[{{ $i }}][enabled]" value="0">
                            <label class="mini-toggle"><input type="checkbox" name="footer_contacts[{{ $i }}][enabled]" value="1" @checked(!empty($item['enabled']))> Enabled</label>
                            <input type="text" name="footer_contacts[{{ $i }}][icon]" value="{{ $item['icon'] ?? '●' }}" placeholder="Icon">
                            <input type="text" name="footer_contacts[{{ $i }}][label]" value="{{ $item['label'] ?? '' }}" placeholder="Label">
                            <input type="text" class="grow" name="footer_contacts[{{ $i }}][value]" value="{{ $item['value'] ?? '' }}" placeholder="Contact value">
                            <input type="text" class="grow" name="footer_contacts[{{ $i }}][url]" value="{{ $item['url'] ?? '' }}" placeholder="tel:+91... / mailto:...">
                            <button type="button" class="remove-btn">Remove</button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- TRUST --}}
        <div class="general-settings-card">
            <div class="general-settings-card-header">
                <div class="general-settings-card-icon">★</div>
                <div><h2>Trust Badges</h2><p>Manage the badges shown below company information.</p></div>
            </div>
            <div class="repeater-wrap">
                <div class="repeater-heading"><h3>Badges</h3><button type="button" class="add-btn" data-add="trust">+ Add Badge</button></div>
                <div id="trust-badges">
                    @foreach($footerBadges as $i => $item)
                        <div class="repeater-row">
                            <input type="hidden" name="footer_trust_badges[{{ $i }}][enabled]" value="0">
                            <label class="mini-toggle"><input type="checkbox" name="footer_trust_badges[{{ $i }}][enabled]" value="1" @checked(!empty($item['enabled']))> Enabled</label>
                            <input type="text" name="footer_trust_badges[{{ $i }}][icon]" value="{{ $item['icon'] ?? '✓' }}" placeholder="Icon">
                            <input class="grow" type="text" name="footer_trust_badges[{{ $i }}][title]" value="{{ $item['title'] ?? '' }}" placeholder="Title">
                            <input class="grow" type="text" name="footer_trust_badges[{{ $i }}][subtitle]" value="{{ $item['subtitle'] ?? '' }}" placeholder="Subtitle (optional)">
                            <button type="button" class="remove-btn">Remove</button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- DESTINATIONS --}}
        <div class="general-settings-card">
            <div class="general-settings-card-header">
                <div class="general-settings-card-icon">●</div>
                <div><h2>Popular Destinations</h2><p>Add unlimited destinations and links.</p></div>
            </div>
            <div class="general-settings-grid">
                <div class="general-settings-field">
                    <label>Destination Section Title</label>
                    <input type="text" name="footer_destinations_title" value="{{ old('footer_destinations_title', $settings['footer.destinations_title'] ?? 'POPULAR DESTINATIONS') }}">
                </div>
            </div>
            <div class="repeater-wrap">
                <div class="repeater-heading"><h3>Destinations</h3><button type="button" class="add-btn" data-add="destination">+ Add Destination</button></div>
                <div id="destinations">
                    @foreach($footerDestinations as $i => $item)
                        <div class="repeater-row">
                            <input type="hidden" name="footer_destinations[{{ $i }}][enabled]" value="0">
                            <label class="mini-toggle"><input type="checkbox" name="footer_destinations[{{ $i }}][enabled]" value="1" @checked(!empty($item['enabled']))> Enabled</label>
                            <input type="text" name="footer_destinations[{{ $i }}][icon]" value="{{ $item['icon'] ?? '' }}" placeholder="Icon">
                            <input class="grow" type="text" name="footer_destinations[{{ $i }}][label]" value="{{ $item['label'] ?? $item['name'] ?? '' }}" placeholder="Destination name">
                            <input class="grow" type="text" name="footer_destinations[{{ $i }}][url]" value="{{ $item['url'] ?? '' }}" placeholder="/destination or https://...">
                            <button type="button" class="remove-btn">Remove</button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- BOTTOM --}}
        <div class="general-settings-card">
            <div class="general-settings-card-header">
                <div class="general-settings-card-icon">©</div>
                <div><h2>Footer Bottom</h2><p>Copyright, legal links and crafted text.</p></div>
            </div>
            <div class="general-settings-grid">
                <div class="general-settings-field general-settings-field-full">
                    <label>Copyright Text</label>
                    <input type="text" name="footer_copyright_text" value="{{ old('footer_copyright_text', $settings['footer.copyright_text'] ?? '© '.date('Y').' travels · Tourism With Faith · All rights reserved.') }}">
                </div>
                <div class="settings-toggle-field">
                    <div><strong>Show Crafted Text</strong><small>Show the small “Crafted with …” text.</small></div>
                    <label class="switch">
                        <input type="hidden" name="footer_show_crafted" value="0">
                        <input type="checkbox" name="footer_show_crafted" value="1" @checked((bool) old('footer_show_crafted', $settings['footer.show_crafted'] ?? true))>
                        <span></span>
                    </label>
                </div>
                <div class="general-settings-field">
                    <label>Crafted Text</label>
                    <input type="text" name="footer_crafted_text" value="{{ old('footer_crafted_text', $settings['footer.crafted_text'] ?? 'Crafted with ♥ in India') }}">
                </div>
            </div>

            <div class="repeater-wrap">
                <div class="repeater-heading"><h3>Bottom Links</h3><button type="button" class="add-btn" data-add="bottom-link">+ Add Link</button></div>
                <div id="bottom-links">
                    @foreach($footerBottomLinks as $i => $item)
                        <div class="repeater-row">
                            <input type="hidden" name="footer_bottom_links[{{ $i }}][enabled]" value="0">
                            <label class="mini-toggle"><input type="checkbox" name="footer_bottom_links[{{ $i }}][enabled]" value="1" @checked(!empty($item['enabled']))> Enabled</label>
                            <input class="grow" type="text" name="footer_bottom_links[{{ $i }}][label]" value="{{ $item['label'] ?? '' }}" placeholder="Terms & Conditions">
                            <input class="grow" type="text" name="footer_bottom_links[{{ $i }}][url]" value="{{ $item['url'] ?? '' }}" placeholder="/terms">
                            <button type="button" class="remove-btn">Remove</button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="general-settings-save-bar">
            <button type="submit" class="general-settings-save-button">Save Home & Footer Settings</button>
        </div>
    </form>
</div>

<style>
.home-settings .settings-section-title{margin:28px 0 12px;padding:0 4px}
.home-settings .settings-section-title span{font-size:12px;font-weight:800;letter-spacing:1px}
.home-settings .settings-section-title p{margin:5px 0 0;color:#738198;font-size:13px}
.home-settings .settings-toggle-field{display:flex;align-items:center;justify-content:space-between;gap:20px;border:1px solid #e8edf3;border-radius:12px;padding:14px 16px;background:#fff}
.home-settings .settings-toggle-field strong{display:block;font-size:13px;color:#1d2a3a}
.home-settings .settings-toggle-field small{display:block;margin-top:4px;color:#7b899c;font-size:11px;line-height:1.5}
.home-settings .switch{position:relative;width:44px;height:24px;display:inline-block;flex:0 0 auto}
.home-settings .switch input{opacity:0;width:0;height:0}
.home-settings .switch span{position:absolute;inset:0;border-radius:30px;background:#cbd4df;cursor:pointer;transition:.2s}
.home-settings .switch span:before{content:"";position:absolute;width:18px;height:18px;left:3px;top:3px;background:#fff;border-radius:50%;transition:.2s;box-shadow:0 1px 3px rgba(0,0,0,.15)}
.home-settings .switch input:checked+span{background:#35bd78}
.home-settings .switch input:checked+span:before{transform:translateX(20px)}
.home-settings .repeater-wrap{padding:0 24px 24px}
.home-settings .repeater-heading{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
.home-settings .repeater-heading h3{margin:0;font-size:14px}
.home-settings .add-btn,.home-settings .add-nested-link{border:1px solid #dbe3ec;background:#f7fafc;border-radius:8px;padding:8px 12px;cursor:pointer;font-weight:700}
.home-settings .add-btn:hover,.home-settings .add-nested-link:hover{background:#eef4f8}
.home-settings .repeater-row{display:flex;align-items:center;gap:8px;padding:10px;border:1px solid #e7edf3;border-radius:10px;background:#fbfcfd;margin-bottom:8px;flex-wrap:wrap}
.home-settings .repeater-row input[type=text],.home-settings .repeater-row input[type=url]{min-width:130px;flex:0 1 180px}
.home-settings .repeater-row .grow{flex:1 1 230px}
.home-settings .mini-toggle{font-size:11px;white-space:nowrap;color:#66758a}
.home-settings .remove-btn{border:0;background:#fff0f0;color:#c43f3f;border-radius:7px;padding:8px 10px;cursor:pointer;font-weight:700}
.home-settings .nested-card{border:1px solid #e2e9f0;border-radius:12px;padding:12px;margin-bottom:12px;background:#fff}
.home-settings .nested-links{padding:6px 0 4px 20px}
.home-settings .compact{background:#f8fafc}
.home-settings .current-file{display:flex;align-items:center;gap:12px;margin-top:8px;font-size:11px;color:#69778a}
.home-settings .current-file img{width:90px;height:45px;object-fit:contain;border:1px solid #e3e8ee;border-radius:6px;background:#fff}
@media(max-width:767px){.home-settings .repeater-row{align-items:stretch}.home-settings .repeater-row input{width:100%;flex:1 1 100%}.home-settings .mini-toggle{width:100%}}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let socialIndex = {{ count($footerSocials) }};
    let ctaBadgeIndex = {{ count($footerCtaBadges) }};
    let columnIndex = {{ count($footerColumns) }};
    let contactIndex = {{ count($footerContacts) }};
    let trustIndex = {{ count($footerBadges) }};
    let destinationIndex = {{ count($footerDestinations) }};
    let bottomIndex = {{ count($footerBottomLinks) }};

    const esc = value => String(value ?? '').replaceAll('"', '&quot;');

    const row = (html) => {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();
        return wrapper.firstElementChild;
    };

    document.querySelectorAll('[data-add]').forEach(button => {
        button.addEventListener('click', function () {
            const type = this.dataset.add;
            let html = '';

            if (type === 'social') {
                const i = socialIndex++;
                html = `<div class="repeater-row">
                    <input type="hidden" name="footer_socials[${i}][enabled]" value="0">
                    <label class="mini-toggle"><input type="checkbox" name="footer_socials[${i}][enabled]" value="1" checked> Enabled</label>
                    <input type="text" name="footer_socials[${i}][name]" placeholder="Platform name">
                    <input type="text" name="footer_socials[${i}][icon]" value="↗" placeholder="Icon">
                    <input class="grow" type="url" name="footer_socials[${i}][url]" placeholder="https://...">
                    <button type="button" class="remove-btn">Remove</button>
                </div>`;
                document.querySelector('#socials').appendChild(row(html));
            }

            if (type === 'cta-badge') {
                const i = ctaBadgeIndex++;
                html = `<div class="repeater-row">
                    <input type="hidden" name="footer_cta_badges[${i}][enabled]" value="0">
                    <label class="mini-toggle"><input type="checkbox" name="footer_cta_badges[${i}][enabled]" value="1" checked> Enabled</label>
                    <input type="text" name="footer_cta_badges[${i}][icon]" value="✓" placeholder="Icon">
                    <input class="grow" type="text" name="footer_cta_badges[${i}][text]" placeholder="Badge text">
                    <button type="button" class="remove-btn">Remove</button>
                </div>`;
                document.querySelector('#cta-badges').appendChild(row(html));
            }

            if (type === 'contact') {
                const i = contactIndex++;
                html = `<div class="repeater-row">
                    <input type="hidden" name="footer_contacts[${i}][enabled]" value="0">
                    <label class="mini-toggle"><input type="checkbox" name="footer_contacts[${i}][enabled]" value="1" checked> Enabled</label>
                    <input type="text" name="footer_contacts[${i}][icon]" value="●" placeholder="Icon">
                    <input type="text" name="footer_contacts[${i}][label]" placeholder="PHONE / WHATSAPP">
                    <input class="grow" type="text" name="footer_contacts[${i}][value]" placeholder="+91...">
                    <input class="grow" type="text" name="footer_contacts[${i}][url]" placeholder="tel:+91...">
                    <button type="button" class="remove-btn">Remove</button>
                </div>`;
                document.querySelector('#contacts').appendChild(row(html));
            }

            if (type === 'trust') {
                const i = trustIndex++;
                html = `<div class="repeater-row">
                    <input type="hidden" name="footer_trust_badges[${i}][enabled]" value="0">
                    <label class="mini-toggle"><input type="checkbox" name="footer_trust_badges[${i}][enabled]" value="1" checked> Enabled</label>
                    <input type="text" name="footer_trust_badges[${i}][icon]" value="✓" placeholder="Icon">
                    <input class="grow" type="text" name="footer_trust_badges[${i}][title]" placeholder="Title">
                    <input class="grow" type="text" name="footer_trust_badges[${i}][subtitle]" placeholder="Subtitle">
                    <button type="button" class="remove-btn">Remove</button>
                </div>`;
                document.querySelector('#trust-badges').appendChild(row(html));
            }

            if (type === 'destination') {
                const i = destinationIndex++;
                html = `<div class="repeater-row">
                    <input type="hidden" name="footer_destinations[${i}][enabled]" value="0">
                    <label class="mini-toggle"><input type="checkbox" name="footer_destinations[${i}][enabled]" value="1" checked> Enabled</label>
                    <input type="text" name="footer_destinations[${i}][icon]" placeholder="Icon">
                    <input class="grow" type="text" name="footer_destinations[${i}][label]" placeholder="Destination name">
                    <input class="grow" type="text" name="footer_destinations[${i}][url]" placeholder="/destination or https://...">
                    <button type="button" class="remove-btn">Remove</button>
                </div>`;
                document.querySelector('#destinations').appendChild(row(html));
            }

            if (type === 'bottom-link') {
                const i = bottomIndex++;
                html = `<div class="repeater-row">
                    <input type="hidden" name="footer_bottom_links[${i}][enabled]" value="0">
                    <label class="mini-toggle"><input type="checkbox" name="footer_bottom_links[${i}][enabled]" value="1" checked> Enabled</label>
                    <input class="grow" type="text" name="footer_bottom_links[${i}][label]" placeholder="Terms & Conditions">
                    <input class="grow" type="text" name="footer_bottom_links[${i}][url]" placeholder="/terms">
                    <button type="button" class="remove-btn">Remove</button>
                </div>`;
                document.querySelector('#bottom-links').appendChild(row(html));
            }

            if (type === 'column') {
                const i = columnIndex++;
                html = `<div class="nested-card" data-column>
                    <div class="repeater-row">
                        <input type="hidden" name="footer_columns[${i}][enabled]" value="0">
                        <label class="mini-toggle"><input type="checkbox" name="footer_columns[${i}][enabled]" value="1" checked> Enabled</label>
                        <input class="grow" type="text" name="footer_columns[${i}][title]" placeholder="Column title">
                        <button type="button" class="remove-btn">Remove Column</button>
                    </div>
                    <div class="nested-links"></div>
                    <button type="button" class="add-nested-link">+ Add Link</button>
                </div>`;
                document.querySelector('#columns').appendChild(row(html));
            }
        });
    });

    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-btn')) {
            const target = event.target.closest('.repeater-row, .nested-card');
            if (target) target.remove();
        }

        if (event.target.classList.contains('add-nested-link')) {
            const column = event.target.closest('[data-column]');
            const links = column.querySelector('.nested-links');
            const columnTitle = column.querySelector('input[name*="[title]"]');
            const match = columnTitle ? columnTitle.name.match(/footer_columns\[(\d+)\]/) : null;
            if (!match) return;

            const i = match[1];
            const existing = links.querySelectorAll('.repeater-row').length;
            const j = existing + Date.now() % 1000;

            links.appendChild(row(`<div class="repeater-row compact">
                <input type="hidden" name="footer_columns[${i}][links][${j}][enabled]" value="0">
                <label class="mini-toggle"><input type="checkbox" name="footer_columns[${i}][links][${j}][enabled]" value="1" checked> On</label>
                <input type="text" name="footer_columns[${i}][links][${j}][icon]" placeholder="Icon">
                <input type="text" class="grow" name="footer_columns[${i}][links][${j}][label]" placeholder="Link label">
                <input type="text" class="grow" name="footer_columns[${i}][links][${j}][url]" placeholder="/page or https://...">
                <button type="button" class="remove-btn">×</button>
            </div>`));
        }
    });
});
</script>
@endsection
