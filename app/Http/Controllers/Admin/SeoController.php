<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\TourPackage;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class SeoController extends Controller
{
    public function __construct(
        protected SettingsService $settings
    ) {
    }

    /**
     * SEO Tools page.
     */
    public function index(): View
    {
        return view('admin.settings.seo', [
            'settings' => $this->settings->all(),
        ]);
    }

    /**
     * Save SEO settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'settings_language' => [
                'required',
                'string',
                'max:20',
            ],

            'site_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'home_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'site_description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'keywords' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'google_analytics' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'sitemap_frequency' => [
                'required',
                'in:always,hourly,daily,weekly,monthly,yearly,never',
            ],

            'sitemap_lastmod' => [
                'required',
                'in:none,server',
            ],

            'sitemap_priority' => [
                'required',
                'in:none,automatic',
            ],
        ]);

        $this->settings->setMany([

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            'seo.settings_language' => $this->setting(
                $validated['settings_language'],
                'seo'
            ),

            'seo.title' => $this->setting(
                $validated['site_title'] ?? null,
                'seo'
            ),

            'seo.home_title' => $this->setting(
                $validated['home_title'] ?? null,
                'seo'
            ),

            'seo.description' => $this->setting(
                $validated['site_description'] ?? null,
                'seo'
            ),

            'seo.keywords' => $this->setting(
                $validated['keywords'] ?? null,
                'seo'
            ),

            'seo.analytics_head' => $this->setting(
                $validated['google_analytics'] ?? null,
                'seo'
            ),

            /*
            |--------------------------------------------------------------------------
            | SITEMAP
            |--------------------------------------------------------------------------
            */

            'sitemap.frequency' => $this->setting(
                $validated['sitemap_frequency'],
                'sitemap'
            ),

            'sitemap.lastmod' => $this->setting(
                $validated['sitemap_lastmod'],
                'sitemap'
            ),

            'sitemap.priority' => $this->setting(
                $validated['sitemap_priority'],
                'sitemap'
            ),
        ]);

        $this->settings->clearCache();

        /*
        |--------------------------------------------------------------------------
        | Automatically regenerate sitemap after settings save
        |--------------------------------------------------------------------------
        */

        $this->generateSitemapFile();

        return redirect()
            ->route('admin.settings.seo')
            ->with(
                'success',
                'SEO settings updated successfully.'
            );
    }

    /**
     * Generate sitemap manually from admin.
     */
    public function generateSitemap(): RedirectResponse
    {
        $this->generateSitemapFile();

        return redirect()
            ->route('admin.settings.seo')
            ->with(
                'success',
                'Sitemap generated successfully.'
            );
    }

    /**
     * Cron endpoint.
     *
     * This can be called automatically by server cron.
     */
    public function cronUpdateSitemap(): Response
    {
        $this->generateSitemapFile();

        return response(
            'Sitemap updated successfully.',
            200,
            [
                'Content-Type' => 'text/plain; charset=UTF-8',
            ]
        );
    }

    /**
     * Generate sitemap.xml.
     */
    protected function generateSitemapFile(): void
    {
        $settings = $this->settings->all();

        $frequency = $settings['sitemap.frequency']
            ?? 'daily';

        $lastmodMode = $settings['sitemap.lastmod']
            ?? 'server';

        $priorityMode = $settings['sitemap.priority']
            ?? 'automatic';

        $urls = [];

        /*
        |--------------------------------------------------------------------------
        | Static pages
        |--------------------------------------------------------------------------
        */

        $this->addUrl(
            $urls,
            route('home'),
            null,
            '1.0',
            $frequency,
            $lastmodMode,
            $priorityMode,
            0
        );

        $this->addUrl(
            $urls,
            route('about'),
            null,
            '0.8',
            $frequency,
            $lastmodMode,
            $priorityMode,
            1
        );

        $this->addUrl(
            $urls,
            route('tours.index'),
            null,
            '0.9',
            $frequency,
            $lastmodMode,
            $priorityMode,
            1
        );

        $this->addUrl(
            $urls,
            route('destinations.index'),
            null,
            '0.8',
            $frequency,
            $lastmodMode,
            $priorityMode,
            1
        );

        $this->addUrl(
            $urls,
            route('blog.index'),
            null,
            '0.8',
            $frequency,
            $lastmodMode,
            $priorityMode,
            1
        );

        $this->addUrl(
            $urls,
            route('contact'),
            null,
            '0.6',
            $frequency,
            $lastmodMode,
            $priorityMode,
            1
        );

        /*
        |--------------------------------------------------------------------------
        | Published Tour Packages
        |--------------------------------------------------------------------------
        */

        TourPackage::query()
            ->where(
                'status',
                TourPackage::STATUS_PUBLISHED
            )
            ->whereNotNull('slug')
            ->orderBy('id')
            ->chunkById(
                500,
                function ($tours) use (
                    &$urls,
                    $frequency,
                    $lastmodMode,
                    $priorityMode
                ) {
                    foreach ($tours as $tour) {
                        $this->addUrl(
                            $urls,
                            route(
                                'tours.show',
                                [
                                    'tour' => $tour->slug,
                                ]
                            ),
                            $tour->updated_at,
                            '0.7',
                            $frequency,
                            $lastmodMode,
                            $priorityMode,
                            2
                        );
                    }
                }
            );

        /*
        |--------------------------------------------------------------------------
        | Published Blog Posts
        |--------------------------------------------------------------------------
        */

        Blog::query()
            ->where(
                'status',
                Blog::STATUS_PUBLISHED
            )
            ->whereNotNull('slug')
            ->orderBy('id')
            ->chunkById(
                500,
                function ($blogs) use (
                    &$urls,
                    $frequency,
                    $lastmodMode,
                    $priorityMode
                ) {
                    foreach ($blogs as $blog) {
                        $this->addUrl(
                            $urls,
                            route(
                                'blog.show',
                                [
                                    'slug' => $blog->slug,
                                ]
                            ),
                            $blog->updated_at,
                            '0.6',
                            $frequency,
                            $lastmodMode,
                            $priorityMode,
                            2
                        );
                    }
                }
            );

        /*
        |--------------------------------------------------------------------------
        | Make sure public directory exists
        |--------------------------------------------------------------------------
        */

        $publicPath = public_path();

        if (!File::exists($publicPath)) {
            File::makeDirectory(
                $publicPath,
                0755,
                true
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 50,000 URL limit
        |--------------------------------------------------------------------------
        */

        $chunks = array_chunk(
            $urls,
            50000
        );

        /*
        |--------------------------------------------------------------------------
        | Single sitemap
        |--------------------------------------------------------------------------
        */

        if (count($chunks) === 1) {
            File::put(
                public_path('sitemap.xml'),
                $this->buildUrlSet($chunks[0])
            );

            /*
            |--------------------------------------------------------------------------
            | Remove old sitemap parts
            |--------------------------------------------------------------------------
            */

            $this->deleteOldSitemapParts();

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Multiple sitemap files
        |--------------------------------------------------------------------------
        */

        $sitemapFiles = [];

        foreach ($chunks as $index => $chunk) {
            $fileNumber = $index + 1;

            $fileName = 'sitemap-' . $fileNumber . '.xml';

            File::put(
                public_path($fileName),
                $this->buildUrlSet($chunk)
            );

            $sitemapFiles[] = url($fileName);
        }

        /*
        |--------------------------------------------------------------------------
        | Sitemap index
        |--------------------------------------------------------------------------
        */

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $xml .= "\n";

        foreach ($sitemapFiles as $file) {
            $xml .= '    <sitemap>';
            $xml .= "\n";
            $xml .= '        <loc>';
            $xml .= htmlspecialchars(
                $file,
                ENT_XML1 | ENT_COMPAT,
                'UTF-8'
            );
            $xml .= '</loc>';
            $xml .= "\n";
            $xml .= '    </sitemap>';
            $xml .= "\n";
        }

        $xml .= '</sitemapindex>';
        $xml .= "\n";

        File::put(
            public_path('sitemap.xml'),
            $xml
        );
    }

    /**
     * Add URL to sitemap collection.
     */
    protected function addUrl(
        array &$urls,
        string $location,
        $lastModified = null,
        string $defaultPriority = '0.5',
        string $frequency = 'daily',
        string $lastmodMode = 'server',
        string $priorityMode = 'automatic',
        int $depth = 1
    ): void {
        $item = [
            'loc' => $location,
            'changefreq' => $frequency,
        ];

        /*
        |--------------------------------------------------------------------------
        | Last modification
        |--------------------------------------------------------------------------
        */

        if ($lastmodMode === 'server') {
            $date = $lastModified;

            if ($date) {
                $item['lastmod'] = $date->toAtomString();
            } else {
                $item['lastmod'] = now()->toAtomString();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Priority
        |--------------------------------------------------------------------------
        */

        if ($priorityMode === 'automatic') {
            if ($depth === 0) {
                $item['priority'] = '1.0';
            } elseif ($depth === 1) {
                $item['priority'] = $defaultPriority;
            } else {
                $item['priority'] = '0.6';
            }
        }

        $urls[] = $item;
    }

    /**
     * Build sitemap URL set.
     */
    protected function buildUrlSet(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= "\n";

        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $xml .= "\n";

        foreach ($urls as $url) {
            $xml .= "    <url>\n";

            $xml .= '        <loc>';
            $xml .= htmlspecialchars(
                $url['loc'],
                ENT_XML1 | ENT_COMPAT,
                'UTF-8'
            );
            $xml .= '</loc>';
            $xml .= "\n";

            if (!empty($url['lastmod'])) {
                $xml .= '        <lastmod>';
                $xml .= htmlspecialchars(
                    $url['lastmod'],
                    ENT_XML1 | ENT_COMPAT,
                    'UTF-8'
                );
                $xml .= '</lastmod>';
                $xml .= "\n";
            }

            if (
                !empty($url['changefreq']) &&
                $url['changefreq'] !== 'never'
            ) {
                $xml .= '        <changefreq>';
                $xml .= htmlspecialchars(
                    $url['changefreq'],
                    ENT_XML1 | ENT_COMPAT,
                    'UTF-8'
                );
                $xml .= '</changefreq>';
                $xml .= "\n";
            }

            if (!empty($url['priority'])) {
                $xml .= '        <priority>';
                $xml .= htmlspecialchars(
                    $url['priority'],
                    ENT_XML1 | ENT_COMPAT,
                    'UTF-8'
                );
                $xml .= '</priority>';
                $xml .= "\n";
            }

            $xml .= "    </url>\n";
        }

        $xml .= '</urlset>';
        $xml .= "\n";

        return $xml;
    }

    /**
     * Remove old sitemap-N.xml files.
     */
    protected function deleteOldSitemapParts(): void
    {
        foreach (
            File::glob(
                public_path('sitemap-*.xml')
            ) as $file
        ) {
            File::delete($file);
        }
    }

    /**
     * Settings definition helper.
     */
    protected function setting(
        mixed $value,
        string $group = 'seo',
        string $type = 'text'
    ): array {
        return [
            'value' => $value,
            'group' => $group,
            'type' => $type,
            'is_public' => true,
        ];
    }
}