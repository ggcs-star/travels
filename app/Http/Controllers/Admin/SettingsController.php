<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        protected SettingsService $settings,
    ) {
    }

    /**
     * Redirect the old settings entry point to General Settings.
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.settings.general');
    }

    /**
     * Display General Settings.
     */
    public function general(): View
    {
        return $this->settingsView('admin.settings.general');
    }

    /**
     * Display Visual Settings.
     */
    public function visual(): View
    {
        return $this->settingsView('admin.settings.visual');
    }

    /**
     * Display Font Settings.
     */
    public function fonts(): View
    {
        return $this->settingsView('admin.settings.fonts');
    }

    /**
     * Display Home Page Settings.
     */
    public function home(): View
    {
        return $this->settingsView('admin.settings.home');
    }

    /**
     * Update Home Page Settings only.
     */
    public function updateHome(Request $request): RedirectResponse
    {
        $validator = Validator::make(
            $request->all(),
            $this->homeRules(),
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $this->saveHome($request);
        $this->saveFooter($request);
        $this->handleHomeFiles($request);
        $this->settings->clearCache();

        return redirect()
            ->route('admin.settings.home')
            ->with('success', 'Home page settings updated successfully.');
    }

    /**
     * Display SEO Settings.
     */
    public function seo(): View
    {
        return $this->settingsView('admin.settings.seo');
    }

    /**
     * Update General Settings only.
     *
     * This is the endpoint used by the General Settings page.
     * Other settings groups are intentionally not touched here.
     */
    public function updateGeneral(Request $request): RedirectResponse
    {
        $validator = Validator::make(
            $request->all(),
            $this->generalRules(),
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $this->saveGeneral($request);
        $this->settings->clearCache();

        return redirect()
            ->route('admin.settings.general')
            ->with('success', 'General settings updated successfully.');
    }

    /**
     * Update Visual Settings only.
     */
    public function updateVisual(Request $request): RedirectResponse
    {
        $validator = Validator::make(
            $request->all(),
            $this->visualRules(),
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $this->saveVisual($request);
        $this->handleVisualFiles($request);
        $this->settings->clearCache();

        return redirect()
            ->route('admin.settings.visual')
            ->with('success', 'Visual settings updated successfully.');
    }

    /**
     * Legacy endpoint for the old single-page settings screen.
     *
     * Keep this temporarily so existing links/forms do not break while
     * the remaining settings sections are migrated to their own pages.
     */
    public function update(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $this->saveGeneral($request);
        $this->saveTopbar($request);
        $this->saveHeader($request);
        $this->saveFooter($request);
        $this->saveSeo($request);
        $this->saveSocialCompatibility($request);
        $this->handleFiles($request);

        $this->settings->clearCache();

        return back()->with(
            'success',
            'Website settings updated successfully.',
        );
    }

    /**
     * Shared data loader for settings pages.
     */
    protected function settingsView(string $view): View
    {
        return view($view, [
            'settings' => $this->settings->all(),
        ]);
    }

    /**
     * Validation rules for General Settings.
     */
    protected function generalRules(): array
    {
        return [
            'site_name' => ['nullable', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'site_description' => ['nullable', 'string', 'max:5000'],
            'site_email' => ['nullable', 'email', 'max:255'],
            'site_phone' => ['nullable', 'string', 'max:100'],
            'site_whatsapp' => ['nullable', 'string', 'max:100'],
            'site_address' => ['nullable', 'string', 'max:2000'],
            'site_working_hours' => ['nullable', 'string', 'max:500'],
            'site_registered_address' => ['nullable', 'string', 'max:2000'],
            'site_branch_address' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Validation rules for Visual Settings.
     */
    protected function visualRules(): array
    {
        return [
            'visual_primary_color' => [
                'nullable',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'visual_secondary_color' => [
                'nullable',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'visual_accent_color' => [
                'nullable',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'visual_text_color' => [
                'nullable',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'visual_background_color' => [
                'nullable',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],

            'visual_container_width' => [
                'nullable',
                'integer',
                'min:800',
                'max:2000',
            ],
            'visual_border_radius' => [
                'nullable',
                'integer',
                'min:0',
                'max:50',
            ],
            'visual_button_style' => [
                'nullable',
                'in:rounded,sharp,pill',
            ],
            'visual_card_style' => [
                'nullable',
                'in:flat,bordered,shadow',
            ],
            'visual_theme_mode' => [
                'nullable',
                'in:light,dark,system',
            ],

            'visual_page_loader' => [
                'nullable',
                'boolean',
            ],
            'visual_animations' => [
                'nullable',
                'boolean',
            ],
            'visual_back_to_top' => [
                'nullable',
                'boolean',
            ],

            'visual_logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'visual_favicon' => [
                'nullable',
                'file',
                'mimes:ico,png,jpg,jpeg,webp',
                'max:1024',
            ],
            'visual_login_logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'footer_logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'remove_footer_logo' => [
                'nullable',
                'boolean',
            ],

            'visual_body_font' => ['nullable', 'string', 'max:100'],
            'visual_heading_font' => ['nullable', 'string', 'max:100'],
            'visual_nav_font' => ['nullable', 'string', 'max:100'],
            'visual_button_font' => ['nullable', 'string', 'max:100'],
            'visual_body_weight' => ['nullable', 'integer', 'in:400,500,600,700'],
            'visual_heading_weight' => ['nullable', 'integer', 'in:400,500,600,700,800'],
            'visual_font_size' => ['nullable', 'numeric', 'min:12', 'max:24'],
            'visual_line_height' => ['nullable', 'numeric', 'min:1', 'max:2.5'],
        ];
    }

    /**
     * Validation rules for Home Page Settings.
     */
    protected function homeRules(): array
    {
        return [
            'home_enabled' => ['nullable', 'boolean'],
            'home_hero_enabled' => ['nullable', 'boolean'],
            'home_hero_autoplay' => ['nullable', 'boolean'],
            'home_hero_text' => ['nullable', 'boolean'],
            'home_hero_interval' => ['nullable', 'integer', 'min:1000', 'max:30000'],
            'home_trending_enabled' => ['nullable', 'boolean'],
            'home_categories_enabled' => ['nullable', 'boolean'],
            'home_categories_title' => ['nullable', 'string', 'max:255'],
            'home_promotional_enabled' => ['nullable', 'boolean'],
            'home_style_spotlight_enabled' => ['nullable', 'boolean'],
            'home_section_hero' => ['nullable', 'boolean'],
            'home_section_trending' => ['nullable', 'boolean'],
            'home_section_categories' => ['nullable', 'boolean'],
            'home_section_promotional' => ['nullable', 'boolean'],
            'home_section_style_spotlight' => ['nullable', 'boolean'],
            'home_section_newsletter' => ['nullable', 'boolean'],
            'home_trending_title' => ['nullable', 'string', 'max:255'],
            'home_promotional_title' => ['nullable', 'string', 'max:255'],
            'home_spotlight_title' => ['nullable', 'string', 'max:255'],
            'home_category_title' => ['nullable', 'string', 'max:255'],

            // Footer
            'footer_enabled' => ['nullable', 'boolean'],
            'footer_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_footer_logo' => ['nullable', 'boolean'],
            'footer_logo_alt' => ['nullable', 'string', 'max:255'],
            'footer_description' => ['nullable', 'string', 'max:5000'],
            'footer_cta_enabled' => ['nullable', 'boolean'],
            'footer_cta_title' => ['nullable', 'string', 'max:500'],
            'footer_cta_description' => ['nullable', 'string', 'max:3000'],
            'footer_cta_button_text' => ['nullable', 'string', 'max:255'],
            'footer_cta_button_url' => ['nullable', 'string', 'max:1000'],
            'footer_cta_badges' => ['nullable', 'array'],
            'footer_cta_badges.*.text' => ['nullable', 'string', 'max:255'],
            'footer_cta_badges.*.icon' => ['nullable', 'string', 'max:50'],
            'footer_cta_badges.*.enabled' => ['nullable', 'boolean'],

            'footer_socials' => ['nullable', 'array'],
            'footer_socials.*.name' => ['nullable', 'string', 'max:100'],
            'footer_socials.*.url' => ['nullable', 'string', 'max:1000'],
            'footer_socials.*.icon' => ['nullable', 'string', 'max:50'],
            'footer_socials.*.enabled' => ['nullable', 'boolean'],

            'footer_columns' => ['nullable', 'array'],
            'footer_columns.*.title' => ['nullable', 'string', 'max:255'],
            'footer_columns.*.enabled' => ['nullable', 'boolean'],
            'footer_columns.*.links' => ['nullable', 'array'],
            'footer_columns.*.links.*.label' => ['nullable', 'string', 'max:255'],
            'footer_columns.*.links.*.url' => ['nullable', 'string', 'max:1000'],
            'footer_columns.*.links.*.icon' => ['nullable', 'string', 'max:50'],
            'footer_columns.*.links.*.enabled' => ['nullable', 'boolean'],

            'footer_contacts' => ['nullable', 'array'],
            'footer_contacts.*.label' => ['nullable', 'string', 'max:255'],
            'footer_contacts.*.value' => ['nullable', 'string', 'max:2000'],
            'footer_contacts.*.url' => ['nullable', 'string', 'max:1000'],
            'footer_contacts.*.icon' => ['nullable', 'string', 'max:50'],
            'footer_contacts.*.enabled' => ['nullable', 'boolean'],

            'footer_trust_badges' => ['nullable', 'array'],
            'footer_trust_badges.*.title' => ['nullable', 'string', 'max:255'],
            'footer_trust_badges.*.subtitle' => ['nullable', 'string', 'max:255'],
            'footer_trust_badges.*.icon' => ['nullable', 'string', 'max:50'],
            'footer_trust_badges.*.enabled' => ['nullable', 'boolean'],

            'footer_destinations' => ['nullable', 'array'],
            'footer_destinations.*.label' => ['nullable', 'string', 'max:255'],
            'footer_destinations.*.url' => ['nullable', 'string', 'max:1000'],
            'footer_destinations.*.icon' => ['nullable', 'string', 'max:50'],
            'footer_destinations.*.enabled' => ['nullable', 'boolean'],

            'footer_contact_title' => ['nullable', 'string', 'max:255'],
            'footer_destinations_title' => ['nullable', 'string', 'max:255'],
            'footer_copyright_text' => ['nullable', 'string', 'max:1000'],
            'footer_show_crafted' => ['nullable', 'boolean'],
            'footer_crafted_text' => ['nullable', 'string', 'max:1000'],
            'footer_bottom_links' => ['nullable', 'array'],
            'footer_bottom_links.*.label' => ['nullable', 'string', 'max:255'],
            'footer_bottom_links.*.url' => ['nullable', 'string', 'max:1000'],
            'footer_bottom_links.*.enabled' => ['nullable', 'boolean'],
        ];
    }

    protected function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | General
            |--------------------------------------------------------------------------
            */

            'site_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'site_tagline' => [
                'nullable',
                'string',
                'max:255',
            ],

            'site_description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'site_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'site_phone' => [
                'nullable',
                'string',
                'max:100',
            ],

            'site_whatsapp' => [
                'nullable',
                'string',
                'max:100',
            ],

            'site_address' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'site_working_hours' => [
                'nullable',
                'string',
                'max:500',
            ],

            'site_registered_address' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'site_branch_address' => [
                'nullable',
                'string',
                'max:2000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Topbar
            |--------------------------------------------------------------------------
            */

            'topbar_phone_label' => [
                'nullable',
                'string',
                'max:100',
            ],

            'topbar_email_label' => [
                'nullable',
                'string',
                'max:100',
            ],

            'topbar_text' => [
                'nullable',
                'string',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Header
            |--------------------------------------------------------------------------
            */

            'header_logo_alt' => [
                'nullable',
                'string',
                'max:255',
            ],

            'header_cta_text' => [
                'nullable',
                'string',
                'max:255',
            ],

            'header_cta_url' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'header_navigation' => [
                'nullable',
                'array',
            ],

            'header_navigation.*.label' => [
                'nullable',
                'string',
                'max:255',
            ],

            'header_navigation.*.url' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'header_navigation.*.icon' => [
                'nullable',
                'string',
                'max:255',
            ],

            'header_navigation.*.target' => [
                'nullable',
                'in:_self,_blank',
            ],

            /*
            |--------------------------------------------------------------------------
            | Footer
            |--------------------------------------------------------------------------
            */

            'footer_description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'footer_cta_title' => [
                'nullable',
                'string',
                'max:500',
            ],

            'footer_cta_description' => [
                'nullable',
                'string',
                'max:3000',
            ],

            'footer_cta_button_text' => [
                'nullable',
                'string',
                'max:255',
            ],

            'footer_cta_button_url' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'footer_cta_badges' => [
                'nullable',
                'array',
            ],

            'footer_cta_badges.*.text' => [
                'nullable',
                'string',
                'max:255',
            ],

            'footer_cta_badges.*.icon' => [
                'nullable',
                'string',
                'max:255',
            ],

            'footer_socials' => [
                'nullable',
                'array',
            ],

            'footer_socials.*.name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'footer_socials.*.url' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'footer_socials.*.icon' => [
                'nullable',
                'string',
                'max:255',
            ],

            'footer_columns' => [
                'nullable',
                'array',
            ],

            'footer_columns.*.title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'footer_columns.*.links' => [
                'nullable',
                'array',
            ],

            'footer_columns.*.links.*.label' => [
                'nullable',
                'string',
                'max:255',
            ],

            'footer_columns.*.links.*.url' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'footer_columns.*.links.*.icon' => [
                'nullable',
                'string',
                'max:255',
            ],

            'footer_contacts' => [
                'nullable',
                'array',
            ],

            'footer_contacts.*.label' => [
                'nullable',
                'string',
                'max:255',
            ],

            'footer_contacts.*.value' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'footer_contacts.*.url' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'footer_contacts.*.icon' => [
                'nullable',
                'string',
                'max:255',
            ],

            'footer_trust_badges' => [
                'nullable',
                'array',
            ],

            'footer_trust_badges.*.title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'footer_trust_badges.*.subtitle' => [
                'nullable',
                'string',
                'max:255',
            ],

            'footer_trust_badges.*.icon' => [
                'nullable',
                'string',
                'max:255',
            ],

            'footer_destinations' => [
                'nullable',
                'array',
            ],

            'footer_destinations.*.label' => [
                'nullable',
                'string',
                'max:255',
            ],

            'footer_destinations.*.url' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'footer_bottom_links' => [
                'nullable',
                'array',
            ],

            'footer_bottom_links.*.label' => [
                'nullable',
                'string',
                'max:255',
            ],

            'footer_bottom_links.*.url' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'footer_contact_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'footer_destinations_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'footer_copyright_text' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'footer_crafted_text' => [
                'nullable',
                'string',
                'max:1000',
            ],

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            'seo_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'seo_description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'seo_keywords' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'seo_author' => [
                'nullable',
                'string',
                'max:255',
            ],

            'seo_robots' => [
                'nullable',
                'string',
                'max:255',
            ],

            'seo_canonical' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'seo_og_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'seo_og_description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'seo_twitter_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'seo_twitter_description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'seo_twitter_card' => [
                'nullable',
                'in:summary,summary_large_image,app,player',
            ],

            'seo_google_verification' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'seo_bing_verification' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'seo_schema' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'seo_analytics_head' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'seo_analytics_body' => [
                'nullable',
                'string',
                'max:20000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Images
            |--------------------------------------------------------------------------
            */

            'header_logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'favicon' => [
                'nullable',
                'file',
                'mimes:ico,png,jpg,jpeg,webp',
                'max:1024',
            ],

            'seo_og_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            /*
            |--------------------------------------------------------------------------
            | Legacy Social
            |--------------------------------------------------------------------------
            */

            'facebook_url' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'instagram_url' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'youtube_url' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'linkedin_url' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'twitter_url' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'whatsapp_url' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Visual settings.
     */
    protected function saveVisual(Request $request): void
    {
        $this->settings->setMany([
            'visual.primary_color' => $this->setting(
                $request->input('visual_primary_color'),
                'visual'
            ),
            'visual.secondary_color' => $this->setting(
                $request->input('visual_secondary_color'),
                'visual'
            ),
            'visual.accent_color' => $this->setting(
                $request->input('visual_accent_color'),
                'visual'
            ),
            'visual.text_color' => $this->setting(
                $request->input('visual_text_color'),
                'visual'
            ),
            'visual.background_color' => $this->setting(
                $request->input('visual_background_color'),
                'visual'
            ),

            'visual.container_width' => $this->setting(
                $request->input('visual_container_width', 1320),
                'visual',
                'integer'
            ),
            'visual.border_radius' => $this->setting(
                $request->input('visual_border_radius', 12),
                'visual',
                'integer'
            ),
            'visual.button_style' => $this->setting(
                $request->input('visual_button_style', 'rounded'),
                'visual'
            ),
            'visual.card_style' => $this->setting(
                $request->input('visual_card_style', 'shadow'),
                'visual'
            ),
            'visual.theme_mode' => $this->setting(
                $request->input('visual_theme_mode', 'light'),
                'visual'
            ),

            'visual.page_loader' => $this->setting(
                $request->boolean('visual_page_loader'),
                'visual',
                'boolean'
            ),
            'visual.animations' => $this->setting(
                $request->boolean('visual_animations'),
                'visual',
                'boolean'
            ),
            'visual.back_to_top' => $this->setting(
                $request->boolean('visual_back_to_top'),
                'visual',
                'boolean'
            ),

            'visual.body_font' => $this->setting(
                $request->input('visual_body_font', 'Inter'), 'visual'
            ),
            'visual.heading_font' => $this->setting(
                $request->input('visual_heading_font', 'Poppins'), 'visual'
            ),
            'visual.nav_font' => $this->setting(
                $request->input('visual_nav_font', 'Inter'), 'visual'
            ),
            'visual.button_font' => $this->setting(
                $request->input('visual_button_font', 'Inter'), 'visual'
            ),
            'visual.body_weight' => $this->setting(
                $request->input('visual_body_weight', 400), 'visual', 'integer'
            ),
            'visual.heading_weight' => $this->setting(
                $request->input('visual_heading_weight', 700), 'visual', 'integer'
            ),
            'visual.font_size' => $this->setting(
                $request->input('visual_font_size', 16), 'visual', 'text'
            ),
            'visual.line_height' => $this->setting(
                $request->input('visual_line_height', 1.6), 'visual', 'text'
            ),
        ]);
    }

    /**
     * Home page settings.
     */
    protected function saveHome(Request $request): void
    {
        $this->settings->setMany([
            'home.enabled' => $this->setting(
                $request->boolean('home_enabled'),
                'home',
                'boolean'
            ),
            'home.hero_enabled' => $this->setting(
                $request->boolean('home_hero_enabled'),
                'home',
                'boolean'
            ),
            'home.hero_autoplay' => $this->setting(
                $request->boolean('home_hero_autoplay'),
                'home',
                'boolean'
            ),
            'home.hero_text' => $this->setting(
                $request->boolean('home_hero_text'),
                'home',
                'boolean'
            ),
            'home.hero_interval' => $this->setting(
                $request->input('home_hero_interval', 7000),
                'home',
                'integer'
            ),
            'home.trending_enabled' => $this->setting(
                $request->boolean('home_trending_enabled'),
                'home',
                'boolean'
            ),
            'home.categories_enabled' => $this->setting(
                $request->boolean('home_categories_enabled'),
                'home',
                'boolean'
            ),
            'home.categories_title' => $this->setting(
                $request->input('home_categories_title', 'Explore Our Tours'),
                'home'
            ),
            'home.promotional_enabled' => $this->setting(
                $request->boolean('home_promotional_enabled'),
                'home',
                'boolean'
            ),
            'home.style_spotlight_enabled' => $this->setting(
                $request->boolean('home_style_spotlight_enabled'),
                'home',
                'boolean'
            ),
            'home.section_hero' => $this->setting(
                $request->boolean('home_section_hero'),
                'home',
                'boolean'
            ),
            'home.section_trending' => $this->setting(
                $request->boolean('home_section_trending'),
                'home',
                'boolean'
            ),
            'home.section_categories' => $this->setting(
                $request->boolean('home_section_categories'),
                'home',
                'boolean'
            ),
            'home.section_promotional' => $this->setting(
                $request->boolean('home_section_promotional'),
                'home',
                'boolean'
            ),
            'home.section_style_spotlight' => $this->setting(
                $request->boolean('home_section_style_spotlight'),
                'home',
                'boolean'
            ),
            'home.section_newsletter' => $this->setting(
                $request->boolean('home_section_newsletter'),
                'home',
                'boolean'
            ),
            'home.trending_title' => $this->setting(
                $request->input('home_trending_title', 'Trending Tours'),
                'home'
            ),
            'home.promotional_title' => $this->setting(
                $request->input('home_promotional_title', 'Featured Offers'),
                'home'
            ),
            'home.spotlight_title' => $this->setting(
                $request->input('home_spotlight_title', 'Style Spotlight'),
                'home'
            ),
            'home.category_title' => $this->setting(
                $request->input('home_category_title', 'Explore Our Tours'),
                'home'
            ),
        ]);
    }

    /**
     * Visual settings file uploads and removals.
     */
    protected function handleVisualFiles(Request $request): void
    {
        $this->handleImage(
            $request,
            'visual_logo',
            'visual.logo',
            'visual',
            'visual'
        );

        $this->handleImage(
            $request,
            'visual_favicon',
            'visual.favicon',
            'visual',
            'visual'
        );

        $this->handleImage(
            $request,
            'visual_login_logo',
            'visual.login_logo',
            'visual',
            'visual'
        );

        $this->handleImage(
            $request,
            'footer_logo',
            'footer.logo',
            'footer',
            'footer'
        );

        if ($request->boolean('remove_visual_logo')) {
            $this->removeFileSetting('visual.logo');
        }

        if ($request->boolean('remove_visual_favicon')) {
            $this->removeFileSetting('visual.favicon');
        }

        if ($request->boolean('remove_visual_login_logo')) {
            $this->removeFileSetting('visual.login_logo');
        }

        if ($request->boolean('remove_footer_logo')) {
            $this->removeFileSetting('footer.logo');
        }
    }

    /**
     * General settings.
     */
    protected function saveGeneral(Request $request): void
    {
        $this->settings->setMany([
            'site.name' => $this->setting(
                $request->input('site_name'),
                'general'
            ),

            'site.tagline' => $this->setting(
                $request->input('site_tagline'),
                'general'
            ),

            'site.description' => $this->setting(
                $request->input('site_description'),
                'general'
            ),

            'site.email' => $this->setting(
                $request->input('site_email'),
                'general'
            ),

            'site.phone' => $this->setting(
                $request->input('site_phone'),
                'general'
            ),

            'site.whatsapp' => $this->setting(
                $request->input('site_whatsapp'),
                'general'
            ),

            'site.address' => $this->setting(
                $request->input('site_address'),
                'general'
            ),

            'site.working_hours' => $this->setting(
                $request->input('site_working_hours'),
                'general'
            ),

            'site.registered_address' => $this->setting(
                $request->input('site_registered_address'),
                'general'
            ),

            'site.branch_address' => $this->setting(
                $request->input('site_branch_address'),
                'general'
            ),
        ]);
    }

    /**
     * Topbar settings.
     */
    protected function saveTopbar(Request $request): void
    {
        $this->settings->setMany([
            'topbar.enabled' => $this->setting(
                $request->boolean('topbar_enabled'),
                'topbar',
                'boolean'
            ),

            'topbar.phone_label' => $this->setting(
                $request->input('topbar_phone_label'),
                'topbar'
            ),

            'topbar.email_label' => $this->setting(
                $request->input('topbar_email_label'),
                'topbar'
            ),

            'topbar.text' => $this->setting(
                $request->input('topbar_text'),
                'topbar'
            ),
        ]);
    }

    /**
     * Header settings.
     */
    protected function saveHeader(Request $request): void
    {
        $this->settings->setMany([
            'header.enabled' => $this->setting(
                $request->boolean('header_enabled'),
                'header',
                'boolean'
            ),

            'header.logo_alt' => $this->setting(
                $request->input('header_logo_alt'),
                'header'
            ),

            'header.cta_text' => $this->setting(
                $request->input('header_cta_text'),
                'header'
            ),

            'header.cta_url' => $this->setting(
                $request->input('header_cta_url'),
                'header'
            ),

            'header.navigation' => $this->setting(
                $this->normalizeItems(
                    $request->input('header_navigation', [])
                ),
                'header',
                'json'
            ),
        ]);
    }

    /**
     * Footer settings.
     */
    protected function saveFooter(Request $request): void
    {
        $this->settings->setMany([
            'footer.logo_alt' => $this->setting(
                $request->input('footer_logo_alt'),
                'footer'
            ),

          'footer.enabled' => $this->setting(
    $request->has('footer_enabled')
        ? $request->boolean('footer_enabled')
        : true,
    'footer',
    'boolean'
),

            'footer.description' => $this->setting(
                $request->input('footer_description'),
                'footer'
            ),

            'footer.cta_enabled' => $this->setting(
                $request->boolean('footer_cta_enabled'),
                'footer',
                'boolean'
            ),

            'footer.cta_title' => $this->setting(
                $request->input('footer_cta_title'),
                'footer'
            ),

            'footer.cta_description' => $this->setting(
                $request->input('footer_cta_description'),
                'footer'
            ),

            'footer.cta_button_text' => $this->setting(
                $request->input('footer_cta_button_text'),
                'footer'
            ),

            'footer.cta_button_url' => $this->setting(
                $request->input('footer_cta_button_url'),
                'footer'
            ),

            'footer.cta_badges' => $this->setting(
                $this->normalizeItems(
                    $request->input('footer_cta_badges', [])
                ),
                'footer',
                'json'
            ),

            'footer.socials' => $this->setting(
                $this->normalizeItems(
                    $request->input('footer_socials', [])
                ),
                'footer',
                'json'
            ),

            'footer.columns' => $this->setting(
                $this->normalizeColumns(
                    $request->input('footer_columns', [])
                ),
                'footer',
                'json'
            ),

            'footer.contacts' => $this->setting(
                $this->normalizeItems(
                    $request->input('footer_contacts', [])
                ),
                'footer',
                'json'
            ),

            'footer.trust_badges' => $this->setting(
                $this->normalizeItems(
                    $request->input('footer_trust_badges', [])
                ),
                'footer',
                'json'
            ),

            'footer.destinations' => $this->setting(
                $this->normalizeItems(
                    $request->input('footer_destinations', [])
                ),
                'footer',
                'json'
            ),

            'footer.bottom_links' => $this->setting(
                $this->normalizeItems(
                    $request->input('footer_bottom_links', [])
                ),
                'footer',
                'json'
            ),

            'footer.contact_title' => $this->setting(
                $request->input('footer_contact_title'),
                'footer'
            ),

            'footer.destinations_title' => $this->setting(
                $request->input('footer_destinations_title'),
                'footer'
            ),

            'footer.copyright_text' => $this->setting(
                $request->input('footer_copyright_text'),
                'footer'
            ),

            'footer.show_crafted' => $this->setting(
                $request->boolean('footer_show_crafted'),
                'footer',
                'boolean'
            ),

            'footer.crafted_text' => $this->setting(
                $request->input('footer_crafted_text'),
                'footer'
            ),
        ]);
    }

    /**
     * SEO settings.
     */
    protected function saveSeo(Request $request): void
    {
        $this->settings->setMany([
            'seo.title' => $this->setting(
                $request->input('seo_title'),
                'seo'
            ),

            'seo.description' => $this->setting(
                $request->input('seo_description'),
                'seo'
            ),

            'seo.keywords' => $this->setting(
                $request->input('seo_keywords'),
                'seo'
            ),

            'seo.author' => $this->setting(
                $request->input('seo_author'),
                'seo'
            ),

            'seo.robots' => $this->setting(
                $request->input('seo_robots'),
                'seo'
            ),

            'seo.canonical' => $this->setting(
                $request->input('seo_canonical'),
                'seo'
            ),

            'seo.og_title' => $this->setting(
                $request->input('seo_og_title'),
                'seo'
            ),

            'seo.og_description' => $this->setting(
                $request->input('seo_og_description'),
                'seo'
            ),

            'seo.twitter_title' => $this->setting(
                $request->input('seo_twitter_title'),
                'seo'
            ),

            'seo.twitter_description' => $this->setting(
                $request->input('seo_twitter_description'),
                'seo'
            ),

            'seo.twitter_card' => $this->setting(
                $request->input('seo_twitter_card'),
                'seo'
            ),

            'seo.google_verification' => $this->setting(
                $request->input('seo_google_verification'),
                'seo'
            ),

            'seo.bing_verification' => $this->setting(
                $request->input('seo_bing_verification'),
                'seo'
            ),

            'seo.schema' => $this->setting(
                $request->input('seo_schema'),
                'seo'
            ),

            'seo.analytics_head' => $this->setting(
                $request->input('seo_analytics_head'),
                'seo'
            ),

            'seo.analytics_body' => $this->setting(
                $request->input('seo_analytics_body'),
                'seo'
            ),
        ]);
    }

    /**
     * Keep old social fields working.
     */
    protected function saveSocialCompatibility(Request $request): void
    {
        $this->settings->setMany([
            'social.facebook' => $this->setting(
                $request->input('facebook_url'),
                'social'
            ),

            'social.instagram' => $this->setting(
                $request->input('instagram_url'),
                'social'
            ),

            'social.youtube' => $this->setting(
                $request->input('youtube_url'),
                'social'
            ),

            'social.linkedin' => $this->setting(
                $request->input('linkedin_url'),
                'social'
            ),

            'social.twitter' => $this->setting(
                $request->input('twitter_url'),
                'social'
            ),

            'social.whatsapp' => $this->setting(
                $request->input('whatsapp_url'),
                'social'
            ),
        ]);
    }

    /**
     * Home page file uploads and removals.
     */
    protected function handleHomeFiles(Request $request): void
    {
        $this->handleImage(
            $request,
            'footer_logo',
            'footer.logo',
            'footer',
            'footer'
        );

        if ($request->boolean('remove_footer_logo')) {
            $this->removeFileSetting('footer.logo');
        }
    }

    /**
     * Handle all setting files.
     */
    protected function handleFiles(Request $request): void
    {
        $this->handleImage(
            $request,
            'header_logo',
            'header.logo',
            'header',
            'image'
        );

        $this->handleImage(
            $request,
            'favicon',
            'site.favicon',
            'favicon',
            'general'
        );

        $this->handleImage(
            $request,
            'seo_og_image',
            'seo.og_image',
            'seo',
            'seo'
        );

        if ($request->boolean('remove_header_logo')) {
            $this->removeFileSetting('header.logo');
        }

        if ($request->boolean('remove_favicon')) {
            $this->removeFileSetting('site.favicon');
        }

        if ($request->boolean('remove_seo_og_image')) {
            $this->removeFileSetting('seo.og_image');
        }
    }

    /**
     * Common image upload method.
     */
    protected function handleImage(
        Request $request,
        string $input,
        string $settingKey,
        string $directory,
        string $group
    ): void {
        if (!$request->hasFile($input)) {
            return;
        }

        $file = $request->file($input);

        if (!$file || !$file->isValid()) {
            return;
        }

        $oldPath = $this->settings->get($settingKey);

        $path = $file->store(
            'settings/' . $directory,
            'public'
        );

        $this->settings->set(
            key: $settingKey,
            value: $path,
            group: $group,
            type: 'text',
            isPublic: true
        );

        $this->deleteStoredFile($oldPath);
    }

    /**
     * Remove setting file.
     */
    protected function removeFileSetting(string $settingKey): void
    {
        $path = $this->settings->get($settingKey);

        $this->deleteStoredFile($path);

        $this->settings->set(
            key: $settingKey,
            value: null,
            group: $this->settingGroup($settingKey),
            type: 'text',
            isPublic: true
        );
    }

    /**
     * Delete stored file.
     */
    protected function deleteStoredFile(?string $path): void
    {
        if (!$path) {
            return;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return;
        }

        $path = ltrim($path, '/');

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Normalize repeater items.
     */
    protected function normalizeItems(array $items): array
    {
        return collect($items)
            ->map(function ($item) {
                if (!is_array($item)) {
                    return null;
                }

                $item = collect($item)
                    ->map(function ($value) {
                        return is_string($value)
                            ? trim($value)
                            : $value;
                    })
                    ->toArray();

                $item['enabled'] = !empty($item['enabled']);

                return $item;
            })
            ->filter(function ($item) {
                if (!$item) {
                    return false;
                }

                return collect($item)
                    ->except('enabled')
                    ->contains(
                        fn ($value) => filled($value)
                    );
            })
            ->values()
            ->toArray();
    }

    /**
     * Normalize footer columns and nested links.
     */
    protected function normalizeColumns(array $columns): array
    {
        return collect($columns)
            ->map(function ($column) {

                if (!is_array($column)) {
                    return null;
                }

                $links = $this->normalizeItems(
                    $column['links'] ?? []
                );

                return [
                    'title' => trim(
                        (string) ($column['title'] ?? '')
                    ),

                    'links' => $links,

                    'enabled' => !empty(
                        $column['enabled']
                    ),
                ];
            })
            ->filter(function ($column) {

                if (!$column) {
                    return false;
                }

                return filled($column['title'])
                    || !empty($column['links']);
            })
            ->values()
            ->toArray();
    }

    /**
     * Build a single setting definition.
     */
    protected function setting(
        mixed $value,
        string $group = 'general',
        string $type = 'text'
    ): array {
        return [
            'value' => $value,
            'group' => $group,
            'type' => $type,
            'is_public' => true,
        ];
    }

    /**
     * Resolve setting group.
     */
    protected function settingGroup(string $key): string
    {
        return match (true) {
            str_starts_with($key, 'site.') => 'general',
            str_starts_with($key, 'header.') => 'header',
            str_starts_with($key, 'topbar.') => 'topbar',
            str_starts_with($key, 'footer.') => 'footer',
            str_starts_with($key, 'seo.') => 'seo',
            str_starts_with($key, 'social.') => 'social',
            str_starts_with($key, 'visual.') => 'visual',
            str_starts_with($key, 'home.') => 'home',
            default => 'general',
        };
    }
}
