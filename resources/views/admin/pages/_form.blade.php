@php
    $isEdit = isset($page) && $page;

    $value = function (
        string $key,
        mixed $default = ''
    ) use ($page) {
        return old(
            $key,
            $page?->{$key} ?? $default
        );
    };

    $seo = $page?->seo;

    $seoValue = function (
        string $key,
        mixed $default = ''
    ) use ($seo) {
        return old(
            $key,
            $seo?->{$key} ?? $default
        );
    };

    $selectedTemplate = $value('template', 'default');
    $selectedStatus = $value('status', 'draft');
    $selectedLocation = $value('menu_location', 'none');
@endphp

<style>
/* ================================================================
   PAGE CMS — SENIOR ADMIN FORM
================================================================ */

.page-cms {
    --pc-ink: #102a35;
    --pc-muted: #64748b;
    --pc-border: #dbe4e8;
    --pc-border-soft: #e8eef1;
    --pc-bg: #f5f8f9;
    --pc-card: #ffffff;
    --pc-primary: #0f766e;
    --pc-primary-dark: #115e59;
    --pc-accent: #f2741b;
    --pc-success: #047857;
    --pc-danger: #b91c1c;
    --pc-warning: #b45309;
    max-width: 1400px;
    margin: 0 auto;
    padding: 28px 28px 100px;
    color: var(--pc-ink);
}

.page-cms * {
    box-sizing: border-box;
}

.page-cms__intro {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 24px;
    margin-bottom: 24px;
}

.page-cms__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    color: var(--pc-accent);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .14em;
    text-transform: uppercase;
}

.page-cms__eyebrow::before {
    content: "";
    width: 24px;
    height: 2px;
    border-radius: 999px;
    background: currentColor;
}

.page-cms__title {
    margin: 0;
    color: var(--pc-ink);
    font-size: clamp(28px, 3vw, 38px);
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: -.025em;
}

.page-cms__subtitle {
    margin: 8px 0 0;
    max-width: 780px;
    color: var(--pc-muted);
    font-size: 14px;
    line-height: 1.7;
}

.page-cms__back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 42px;
    padding: 0 15px;
    border: 1px solid var(--pc-border);
    border-radius: 10px;
    background: #fff;
    color: #334155;
    text-decoration: none;
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
    transition: .2s ease;
}

.page-cms__back:hover {
    border-color: #c6d3d8;
    background: #f8fafb;
}

.page-cms__alert {
    margin-bottom: 20px;
    padding: 14px 16px;
    border-radius: 12px;
    font-size: 13px;
    line-height: 1.6;
}

.page-cms__alert--success {
    border: 1px solid #a7f3d0;
    background: #ecfdf5;
    color: #065f46;
}

.page-cms__alert--error {
    border: 1px solid #fecaca;
    background: #fef2f2;
    color: #991b1b;
}

.page-cms__alert--error ul {
    margin: 8px 0 0;
    padding-left: 18px;
}

.page-cms__layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 340px;
    gap: 22px;
    align-items: start;
}

.page-cms__main,
.page-cms__side {
    min-width: 0;
}

.page-cms__card {
    margin-bottom: 20px;
    overflow: hidden;
    border: 1px solid var(--pc-border-soft);
    border-radius: 16px;
    background: var(--pc-card);
    box-shadow: 0 8px 28px rgba(16, 42, 53, .045);
}

.page-cms__card:last-child {
    margin-bottom: 0;
}

.page-cms__card-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    padding: 18px 20px;
    border-bottom: 1px solid var(--pc-border-soft);
    background: linear-gradient(180deg, #ffffff 0%, #fbfdfd 100%);
}

.page-cms__card-title {
    margin: 0;
    color: var(--pc-ink);
    font-size: 16px;
    font-weight: 800;
}

.page-cms__card-help {
    margin: 4px 0 0;
    color: var(--pc-muted);
    font-size: 12px;
    line-height: 1.55;
}

.page-cms__card-body {
    padding: 20px;
}

.page-cms__grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
}

.page-cms__field {
    min-width: 0;
}

.page-cms__field--full {
    grid-column: 1 / -1;
}

.page-cms__label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 7px;
    color: #334155;
    font-size: 12px;
    font-weight: 800;
}

.page-cms__required {
    color: var(--pc-danger);
}

.page-cms__counter {
    color: #94a3b8;
    font-size: 11px;
    font-weight: 600;
}

.page-cms__input,
.page-cms__select,
.page-cms__textarea {
    width: 100%;
    border: 1px solid #cfdbe0;
    border-radius: 10px;
    background: #fff;
    color: #132b35;
    font: inherit;
    font-size: 13px;
    outline: none;
    transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
}

.page-cms__input,
.page-cms__select {
    min-height: 44px;
    padding: 0 12px;
}

.page-cms__textarea {
    min-height: 120px;
    padding: 12px;
    resize: vertical;
    line-height: 1.65;
}

.page-cms__input:focus,
.page-cms__select:focus,
.page-cms__textarea:focus {
    border-color: #78aaa5;
    box-shadow: 0 0 0 4px rgba(15,118,110,.09);
}

.page-cms__input::placeholder,
.page-cms__textarea::placeholder {
    color: #a0adb5;
}

.page-cms__hint {
    margin-top: 6px;
    color: #7b8a95;
    font-size: 11px;
    line-height: 1.55;
}

.page-cms__slug-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 8px;
}

.page-cms__tiny-btn {
    min-height: 44px;
    padding: 0 11px;
    border: 1px solid var(--pc-border);
    border-radius: 10px;
    background: #f8fafb;
    color: #465862;
    font-size: 11px;
    font-weight: 800;
    cursor: pointer;
}

.page-cms__tiny-btn:hover {
    background: #f1f5f6;
}

.page-cms__content-tabs {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 10px;
}

.page-cms__tab {
    min-height: 34px;
    padding: 0 11px;
    border: 1px solid var(--pc-border);
    border-radius: 9px;
    background: #fff;
    color: #64748b;
    font-size: 11px;
    font-weight: 800;
    cursor: pointer;
}

.page-cms__tab.is-active {
    border-color: #9fc4c0;
    background: #edf8f6;
    color: var(--pc-primary-dark);
}

.page-cms__editor-wrap {
    position: relative;
}

.page-cms__code {
    min-height: 370px;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 12px;
    line-height: 1.75;
}

.page-cms__preview {
    display: none;
    min-height: 370px;
    overflow: auto;
    padding: 20px;
    border: 1px solid #cfdbe0;
    border-radius: 10px;
    background: #fbfdfd;
}

.page-cms__preview.is-visible {
    display: block;
}

.page-cms__preview h1,
.page-cms__preview h2,
.page-cms__preview h3,
.page-cms__preview h4 {
    color: #132b35;
    line-height: 1.3;
    margin: 1.4em 0 .65em;
}

.page-cms__preview p {
    margin: 0 0 1em;
    color: #425662;
    line-height: 1.8;
}

.page-cms__preview a {
    color: var(--pc-accent);
}

.page-cms__preview img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
}

.page-cms__toolbar {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 5px;
    padding: 8px;
    margin-bottom: 8px;
    border: 1px solid var(--pc-border);
    border-radius: 10px;
    background: #f8fafb;
}

.page-cms__tool {
    min-width: 31px;
    height: 31px;
    padding: 0 8px;
    border: 1px solid transparent;
    border-radius: 7px;
    background: transparent;
    color: #41535c;
    font-size: 11px;
    font-weight: 800;
    cursor: pointer;
}

.page-cms__tool:hover {
    border-color: var(--pc-border);
    background: #fff;
}

.page-cms__divider {
    width: 1px;
    height: 21px;
    margin: 0 2px;
    background: var(--pc-border);
}

.page-cms__template-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
}

.page-cms__template {
    position: relative;
}

.page-cms__template input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.page-cms__template label {
    display: block;
    padding: 12px;
    border: 1px solid var(--pc-border);
    border-radius: 11px;
    background: #fff;
    cursor: pointer;
    transition: .18s ease;
}

.page-cms__template label:hover {
    border-color: #b4c6cb;
}

.page-cms__template input:checked + label {
    border-color: #88b9b3;
    background: #f1faf8;
    box-shadow: 0 0 0 3px rgba(15,118,110,.07);
}

.page-cms__template-name {
    display: block;
    color: #19333d;
    font-size: 12px;
    font-weight: 800;
}

.page-cms__template-desc {
    display: block;
    margin-top: 4px;
    color: #7a8992;
    font-size: 11px;
    line-height: 1.45;
}

.page-cms__status-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.page-cms__status {
    position: relative;
}

.page-cms__status input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.page-cms__status label {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 44px;
    padding: 0 10px;
    border: 1px solid var(--pc-border);
    border-radius: 10px;
    background: #fff;
    color: #53646d;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
}

.page-cms__status input:checked + label {
    border-color: #92b8b3;
    background: #eef8f6;
    color: var(--pc-primary-dark);
}

.page-cms__location-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}

.page-cms__location {
    position: relative;
}

.page-cms__location input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.page-cms__location label {
    display: block;
    padding: 12px;
    border: 1px solid var(--pc-border);
    border-radius: 10px;
    background: #fff;
    cursor: pointer;
}

.page-cms__location input:checked + label {
    border-color: #92b8b3;
    background: #eef8f6;
}

.page-cms__location-name {
    display: block;
    color: #1d3842;
    font-size: 12px;
    font-weight: 800;
}

.page-cms__location-desc {
    display: block;
    margin-top: 4px;
    color: #80909a;
    font-size: 10px;
    line-height: 1.45;
}

.page-cms__conditional {
    display: none;
}

.page-cms__conditional.is-visible {
    display: block;
}

.page-cms__image-upload {
    display: grid;
    gap: 10px;
}

.page-cms__file {
    width: 100%;
    padding: 10px;
    border: 1px dashed #cbd7dc;
    border-radius: 10px;
    background: #f9fbfb;
    font-size: 12px;
}

.page-cms__preview-image {
    display: none;
    width: 100%;
    max-height: 190px;
    object-fit: cover;
    border: 1px solid var(--pc-border);
    border-radius: 11px;
}

.page-cms__preview-image.is-visible {
    display: block;
}

.page-cms__seo-preview {
    padding: 14px;
    border: 1px solid var(--pc-border-soft);
    border-radius: 12px;
    background: #fbfdfd;
}

.page-cms__seo-url {
    margin-bottom: 5px;
    color: #188038;
    font-size: 11px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.page-cms__seo-title {
    color: #1a0dab;
    font-size: 17px;
    line-height: 1.3;
    word-break: break-word;
}

.page-cms__seo-description {
    margin-top: 5px;
    color: #4d5156;
    font-size: 12px;
    line-height: 1.55;
}

.page-cms__score {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 10px;
    align-items: center;
    margin-bottom: 14px;
}

.page-cms__score-track {
    height: 7px;
    overflow: hidden;
    border-radius: 999px;
    background: #e7eef0;
}

.page-cms__score-fill {
    width: 0;
    height: 100%;
    border-radius: inherit;
    background: var(--pc-accent);
    transition: width .25s ease;
}

.page-cms__score-value {
    min-width: 28px;
    color: #475a64;
    font-size: 11px;
    font-weight: 800;
    text-align: right;
}

.page-cms__checklist {
    display: grid;
    gap: 8px;
}

.page-cms__check {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    color: #65757e;
    font-size: 11px;
    line-height: 1.45;
}

.page-cms__check-icon {
    width: 18px;
    height: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    border-radius: 50%;
    background: #edf1f2;
    color: #94a3ad;
    font-size: 10px;
    font-weight: 800;
}

.page-cms__check.is-good .page-cms__check-icon {
    background: #dcfce7;
    color: #15803d;
}

.page-cms__sticky {
    position: sticky;
    bottom: 14px;
    z-index: 30;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-top: 20px;
    padding: 12px;
    border: 1px solid rgba(203, 215, 220, .92);
    border-radius: 14px;
    background: rgba(255, 255, 255, .96);
    box-shadow: 0 12px 35px rgba(15, 45, 55, .13);
    backdrop-filter: blur(10px);
}

.page-cms__sticky-status {
    color: #71808a;
    font-size: 11px;
    font-weight: 700;
}

.page-cms__actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.page-cms__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 42px;
    padding: 0 16px;
    border: 1px solid transparent;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    transition: .18s ease;
}

.page-cms__btn--secondary {
    border-color: var(--pc-border);
    background: #fff;
    color: #475569;
}

.page-cms__btn--secondary:hover {
    background: #f8fafb;
}

.page-cms__btn--primary {
    background: var(--pc-primary);
    color: #fff;
    box-shadow: 0 7px 18px rgba(15,118,110,.18);
}

.page-cms__btn--primary:hover {
    background: var(--pc-primary-dark);
    transform: translateY(-1px);
}

.page-cms__side-note {
    padding: 13px;
    border: 1px solid #e1e9eb;
    border-radius: 11px;
    background: #f9fbfb;
    color: #6c7b84;
    font-size: 11px;
    line-height: 1.6;
}

.page-cms__field-error {
    margin-top: 6px;
    color: var(--pc-danger);
    font-size: 11px;
    font-weight: 700;
}

@media (max-width: 1100px) {
    .page-cms__layout {
        grid-template-columns: 1fr;
    }

    .page-cms__side {
        order: 2;
    }
}

@media (max-width: 720px) {
    .page-cms {
        padding: 18px 14px 90px;
    }

    .page-cms__intro {
        flex-direction: column;
    }

    .page-cms__back {
        width: 100%;
    }

    .page-cms__grid {
        grid-template-columns: 1fr;
    }

    .page-cms__field--full {
        grid-column: auto;
    }

    .page-cms__location-grid,
    .page-cms__status-row {
        grid-template-columns: 1fr;
    }

    .page-cms__card-body {
        padding: 15px;
    }

    .page-cms__card-head {
        padding: 15px;
    }

    .page-cms__sticky {
        align-items: stretch;
        flex-direction: column;
    }

    .page-cms__actions {
        width: 100%;
    }

    .page-cms__btn {
        flex: 1;
    }
}
</style>

<div class="page-cms">

    <div class="page-cms__intro">
        <div>
            <div class="page-cms__eyebrow">Content Management / Pages</div>
            <h1 class="page-cms__title">
                {{ $isEdit ? 'Edit Website Page' : 'Create Website Page' }}
            </h1>
            <p class="page-cms__subtitle">
                Build a production-ready website page with structured content,
                responsive media, navigation placement and complete SEO/social metadata.
            </p>
        </div>

        <a
            href="{{ route('admin.pages.index') }}"
            class="page-cms__back"
        >
            ← Back to Pages
        </a>
    </div>

    @if(session('success'))
        <div class="page-cms__alert page-cms__alert--success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="page-cms__alert page-cms__alert--error">
            <strong>Please review the highlighted page settings.</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="page-cms__layout">

        <div class="page-cms__main">

            {{-- BASIC PAGE INFORMATION --}}
            <section class="page-cms__card">
                <div class="page-cms__card-head">
                    <div>
                        <h2 class="page-cms__card-title">01. Page Information</h2>
                        <p class="page-cms__card-help">
                            Define the page identity, URL and search-friendly summary.
                        </p>
                    </div>
                </div>

                <div class="page-cms__card-body">
                    <div class="page-cms__grid">

                        <div class="page-cms__field page-cms__field--full">
                            <label class="page-cms__label" for="title">
                                <span>Page Title <span class="page-cms__required">*</span></span>
                                <span class="page-cms__counter" data-counter-for="title"></span>
                            </label>

                            <input
                                class="page-cms__input"
                                type="text"
                                id="title"
                                name="title"
                                value="{{ $value('title') }}"
                                maxlength="255"
                                placeholder="Example: About travels"
                                required
                                autofocus
                            >

                            @error('title')
                                <div class="page-cms__field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="page-cms__field">
                            <label class="page-cms__label" for="slug">
                                <span>Page URL / Slug <span class="page-cms__required">*</span></span>
                            </label>

                            <div class="page-cms__slug-row">
                                <input
                                    class="page-cms__input"
                                    type="text"
                                    id="slug"
                                    name="slug"
                                    value="{{ $value('slug') }}"
                                    maxlength="500"
                                    placeholder="about-ssb-travelz"
                                    required
                                    spellcheck="false"
                                    autocomplete="off"
                                >

                                <button
                                    type="button"
                                    class="page-cms__tiny-btn"
                                    id="generateSlug"
                                >
                                    Generate
                                </button>
                            </div>

                            <div class="page-cms__hint">
                                Use lowercase words separated by hyphens. Nested URLs are supported.
                                Example: <strong>company/about-us</strong>.
                            </div>

                            @error('slug')
                                <div class="page-cms__field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="page-cms__field">
                            <label class="page-cms__label" for="excerpt">
                                <span>Short Description</span>
                                <span class="page-cms__counter" data-counter-for="excerpt"></span>
                            </label>

                            <textarea
                                class="page-cms__textarea"
                                id="excerpt"
                                name="excerpt"
                                rows="4"
                                maxlength="1000"
                                placeholder="A concise summary shown near the page title and useful for previews."
                            >{{ $value('excerpt') }}</textarea>

                            <div class="page-cms__hint">
                                Keep this clear and useful. Avoid repeating the title word-for-word.
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            {{-- CONTENT EDITOR --}}
            <section class="page-cms__card">
                <div class="page-cms__card-head">
                    <div>
                        <h2 class="page-cms__card-title">02. Content Editor</h2>
                        <p class="page-cms__card-help">
                            Build the page body using HTML. The toolbar inserts clean, reusable markup.
                        </p>
                    </div>
                </div>

                <div class="page-cms__card-body">

                    <div class="page-cms__content-tabs">
                        <button
                            type="button"
                            class="page-cms__tab is-active"
                            data-content-tab="source"
                        >
                            Source
                        </button>

                        <button
                            type="button"
                            class="page-cms__tab"
                            data-content-tab="preview"
                        >
                            Live Preview
                        </button>
                    </div>

                    <div id="sourceEditor">

                        <div class="page-cms__toolbar">
                            <button type="button" class="page-cms__tool" data-wrap="<h2>|</h2>">H2</button>
                            <button type="button" class="page-cms__tool" data-wrap="<h3>|</h3>">H3</button>

                            <span class="page-cms__divider"></span>

                            <button type="button" class="page-cms__tool" data-wrap="<strong>|</strong>">Bold</button>
                            <button type="button" class="page-cms__tool" data-wrap="<em>|</em>">Italic</button>

                            <span class="page-cms__divider"></span>

                            <button type="button" class="page-cms__tool" data-wrap="<p>|</p>">P</button>
                            <button type="button" class="page-cms__tool" data-wrap="<blockquote>|</blockquote>">Quote</button>
                            <button type="button" class="page-cms__tool" data-wrap="<ul>\n<li>|</li>\n</ul>">List</button>
                            <button type="button" class="page-cms__tool" data-wrap="<a href=&quot;https://example.com&quot;>|</a>">Link</button>
                        </div>

                        <div class="page-cms__editor-wrap">
                            <textarea
                                class="page-cms__textarea page-cms__code"
                                id="content"
                                name="content"
                                rows="22"
                                placeholder="<h2>About our travel platform</h2>
<p>Write the main page content here...</p>"
                            >{{ $value('content') }}</textarea>
                        </div>

                    </div>

                    <div
                        class="page-cms__preview"
                        id="contentPreview"
                    ></div>

                    <div class="page-cms__hint">
                        HTML is supported. Avoid adding scripts or page-level CSS here; keep presentation in your application styles.
                    </div>
                </div>
            </section>

            {{-- FEATURED MEDIA --}}
            <section class="page-cms__card">
                <div class="page-cms__card-head">
                    <div>
                        <h2 class="page-cms__card-title">03. Featured Media</h2>
                        <p class="page-cms__card-help">
                            Use a strong image that represents the page and works well on desktop and mobile.
                        </p>
                    </div>
                </div>

                <div class="page-cms__card-body">

                    <div class="page-cms__grid">

                        <div class="page-cms__field">
                            <label class="page-cms__label" for="featured_image">
                                <span>Featured Image</span>
                            </label>

                            <div class="page-cms__image-upload">
                                <input
                                    class="page-cms__file"
                                    type="file"
                                    id="featured_image"
                                    name="featured_image"
                                    accept=".jpg,.jpeg,.png,.webp,image/*"
                                >

                                <img
                                    id="featuredImagePreview"
                                    class="page-cms__preview-image {{ $page?->featured_image ? 'is-visible' : '' }}"
                                    src="{{ $page?->featured_image ? asset('storage/' . $page->featured_image) : '' }}"
                                    alt="{{ $page?->image_alt ?: 'Featured image preview' }}"
                                >
                            </div>

                            <div class="page-cms__hint">
                                JPG, JPEG, PNG or WEBP. Maximum 5MB.
                            </div>
                        </div>

                        <div class="page-cms__field">
                            <label class="page-cms__label" for="image_alt">
                                <span>Image Alt Text</span>
                                <span class="page-cms__counter" data-counter-for="image_alt"></span>
                            </label>

                            <input
                                class="page-cms__input"
                                type="text"
                                id="image_alt"
                                name="image_alt"
                                value="{{ $value('image_alt') }}"
                                maxlength="255"
                                placeholder="People enjoying a guided mountain tour"
                            >

                            <div class="page-cms__hint">
                                Describe what the image communicates. This helps accessibility and image search.
                            </div>

                            @if($page?->featured_image)
                                <label class="page-cms__label" style="justify-content:flex-start;margin-top:16px;">
                                    <input
                                        type="checkbox"
                                        name="remove_featured_image"
                                        value="1"
                                        style="margin-right:7px;"
                                    >
                                    Remove current featured image
                                </label>
                            @endif
                        </div>

                    </div>
                </div>
            </section>

            {{-- NAVIGATION / INFORMATION ARCHITECTURE --}}
            <section class="page-cms__card">
                <div class="page-cms__card-head">
                    <div>
                        <h2 class="page-cms__card-title">04. Navigation & Information Architecture</h2>
                        <p class="page-cms__card-help">
                            Control where the page appears and how it is ordered in your website navigation.
                        </p>
                    </div>
                </div>

                <div class="page-cms__card-body">

                    <div class="page-cms__field">
                        <label class="page-cms__label">
                            <span>Menu Placement <span class="page-cms__required">*</span></span>
                        </label>

                        <div class="page-cms__location-grid">

                            @foreach([
                                'none' => [
                                    'title' => 'No Menu',
                                    'desc' => 'Keep the page accessible only by URL or internal links.',
                                ],
                                'header' => [
                                    'title' => 'Header',
                                    'desc' => 'Show this page in the primary website navigation.',
                                ],
                                'footer' => [
                                    'title' => 'Footer',
                                    'desc' => 'Show this page in a footer navigation group.',
                                ],
                                'both' => [
                                    'title' => 'Header + Footer',
                                    'desc' => 'Expose the page in both navigation areas.',
                                ],
                            ] as $location => $meta)

                                <div class="page-cms__location">
                                    <input
                                        type="radio"
                                        id="menu_{{ $location }}"
                                        name="menu_location"
                                        value="{{ $location }}"
                                        @checked($selectedLocation === $location)
                                    >

                                    <label for="menu_{{ $location }}">
                                        <span class="page-cms__location-name">
                                            {{ $meta['title'] }}
                                        </span>

                                        <span class="page-cms__location-desc">
                                            {{ $meta['desc'] }}
                                        </span>
                                    </label>
                                </div>

                            @endforeach

                        </div>

                        @error('menu_location')
                            <div class="page-cms__field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="height:18px;"></div>

                    <div
                        class="page-cms__conditional"
                        id="headerSettings"
                    >
                        <div class="page-cms__grid">

                            <div class="page-cms__field">
                                <label class="page-cms__label" for="header_parent_id">
                                    <span>Header Parent Page</span>
                                </label>

                                <select
                                    class="page-cms__select"
                                    id="header_parent_id"
                                    name="header_parent_id"
                                >
                                    <option value="">Top Level / No Parent</option>

                                    @foreach($parentPages as $parent)
                                        <option
                                            value="{{ $parent->id }}"
                                            @selected(
                                                (string) $value('header_parent_id') ===
                                                (string) $parent->id
                                            )
                                        >
                                            {{ $parent->title }}
                                        </option>
                                    @endforeach
                                </select>

                                <div class="page-cms__hint">
                                    Use a parent only when this page belongs inside a navigation hierarchy.
                                </div>
                            </div>

                            <div class="page-cms__field">
                                <label class="page-cms__label" for="header_position">
                                    <span>Header Position</span>
                                </label>

                                <input
                                    class="page-cms__input"
                                    type="number"
                                    id="header_position"
                                    name="header_position"
                                    value="{{ $value('header_position', 0) }}"
                                    min="0"
                                    step="1"
                                >

                                <div class="page-cms__hint">
                                    Lower numbers appear earlier in the menu.
                                </div>
                            </div>

                        </div>

                        <div style="height:18px;"></div>
                    </div>

                    <div
                        class="page-cms__conditional"
                        id="footerSettings"
                    >
                        <div class="page-cms__grid">

                            <div class="page-cms__field">
                                <label class="page-cms__label" for="footer_column">
                                    <span>Footer Column / Group</span>
                                </label>

                                <input
                                    class="page-cms__input"
                                    type="text"
                                    id="footer_column"
                                    name="footer_column"
                                    value="{{ $value('footer_column') }}"
                                    maxlength="100"
                                    placeholder="Quick Links"
                                >

                                <div class="page-cms__hint">
                                    Match the footer group name used by your site's footer navigation.
                                </div>
                            </div>

                            <div class="page-cms__field">
                                <label class="page-cms__label" for="footer_position">
                                    <span>Footer Position</span>
                                </label>

                                <input
                                    class="page-cms__input"
                                    type="number"
                                    id="footer_position"
                                    name="footer_position"
                                    value="{{ $value('footer_position', 0) }}"
                                    min="0"
                                    step="1"
                                >

                                <div class="page-cms__hint">
                                    Lower numbers appear first in the footer group.
                                </div>
                            </div>

                        </div>

                        <div style="height:18px;"></div>
                    </div>

                    <div class="page-cms__grid">
                        <div class="page-cms__field">
                            <label class="page-cms__label" for="sort_order">
                                <span>General Sort Order</span>
                            </label>

                            <input
                                class="page-cms__input"
                                type="number"
                                id="sort_order"
                                name="sort_order"
                                value="{{ $value('sort_order', 0) }}"
                                min="0"
                                step="1"
                            >

                            <div class="page-cms__hint">
                                Useful for global ordering where your application reads sort_order.
                            </div>
                        </div>

                        <div class="page-cms__field">
                            <label class="page-cms__label">
                                <span>Page Template</span>
                            </label>

                            <div class="page-cms__template-grid">

                                @foreach([
                                    'default' => [
                                        'name' => 'Default',
                                        'desc' => 'Standard content layout with normal page spacing.',
                                    ],
                                    'full-width' => [
                                        'name' => 'Full Width',
                                        'desc' => 'Use when the page needs wider content or media.',
                                    ],
                                    'landing' => [
                                        'name' => 'Landing Page',
                                        'desc' => 'Designed for campaign-style or high-impact pages.',
                                    ],
                                ] as $template => $meta)

                                    <div class="page-cms__template">
                                        <input
                                            type="radio"
                                            id="template_{{ $template }}"
                                            name="template"
                                            value="{{ $template }}"
                                            @checked($selectedTemplate === $template)
                                        >

                                        <label for="template_{{ $template }}">
                                            <span class="page-cms__template-name">
                                                {{ $meta['name'] }}
                                            </span>

                                            <span class="page-cms__template-desc">
                                                {{ $meta['desc'] }}
                                            </span>
                                        </label>
                                    </div>

                                @endforeach

                            </div>
                        </div>
                    </div>

                </div>
            </section>

            {{-- SEO --}}
            <section class="page-cms__card">
                <div class="page-cms__card-head">
                    <div>
                        <h2 class="page-cms__card-title">05. SEO & Social Metadata</h2>
                        <p class="page-cms__card-help">
                            Set search-engine metadata, canonical URL and social sharing content.
                        </p>
                    </div>
                </div>

                <div class="page-cms__card-body">

                    <div class="page-cms__grid">

                        <div class="page-cms__field page-cms__field--full">
                            <label class="page-cms__label" for="meta_title">
                                <span>Meta Title</span>
                                <span class="page-cms__counter" data-counter-for="meta_title"></span>
                            </label>

                            <input
                                class="page-cms__input"
                                type="text"
                                id="meta_title"
                                name="meta_title"
                                value="{{ $seoValue('meta_title') }}"
                                maxlength="255"
                                placeholder="About travels | Travel Experiences & Tours"
                            >
                        </div>

                        <div class="page-cms__field page-cms__field--full">
                            <label class="page-cms__label" for="meta_description">
                                <span>Meta Description</span>
                                <span class="page-cms__counter" data-counter-for="meta_description"></span>
                            </label>

                            <textarea
                                class="page-cms__textarea"
                                id="meta_description"
                                name="meta_description"
                                rows="4"
                                maxlength="5000"
                                placeholder="Write a useful search-engine description for this page."
                            >{{ $seoValue('meta_description') }}</textarea>
                        </div>

                        <div class="page-cms__field page-cms__field--full">
                            <label class="page-cms__label" for="keywords">
                                <span>Keywords</span>
                                <span class="page-cms__counter" data-counter-for="keywords"></span>
                            </label>

                            <textarea
                                class="page-cms__textarea"
                                id="keywords"
                                name="keywords"
                                rows="3"
                                maxlength="5000"
                                placeholder="travel agency, tours, travel packages, India travel"
                            >{{ $seoValue('keywords') }}</textarea>
                        </div>

                        <div class="page-cms__field">
                            <label class="page-cms__label" for="canonical_url">
                                <span>Canonical URL</span>
                            </label>

                            <input
                                class="page-cms__input"
                                type="url"
                                id="canonical_url"
                                name="canonical_url"
                                value="{{ $seoValue('canonical_url') }}"
                                maxlength="1000"
                                placeholder="https://example.com/about-us"
                            >
                        </div>

                        <div class="page-cms__field">
                            <label class="page-cms__label" for="robots">
                                <span>Robots</span>
                            </label>

                            <select
                                class="page-cms__select"
                                id="robots"
                                name="robots"
                            >
                                @foreach([
                                    'index,follow' => 'Index, Follow',
                                    'index,nofollow' => 'Index, Nofollow',
                                    'noindex,follow' => 'Noindex, Follow',
                                    'noindex,nofollow' => 'Noindex, Nofollow',
                                ] as $robotValue => $robotLabel)
                                    <option
                                        value="{{ $robotValue }}"
                                        @selected(
                                            $seoValue('robots', 'index,follow') === $robotValue
                                        )
                                    >
                                        {{ $robotLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="page-cms__field page-cms__field--full">
                            <label class="page-cms__label" for="og_title">
                                <span>Open Graph Title</span>
                                <span class="page-cms__counter" data-counter-for="og_title"></span>
                            </label>

                            <input
                                class="page-cms__input"
                                type="text"
                                id="og_title"
                                name="og_title"
                                value="{{ $seoValue('og_title') }}"
                                maxlength="255"
                                placeholder="Title shown when this page is shared"
                            >
                        </div>

                        <div class="page-cms__field page-cms__field--full">
                            <label class="page-cms__label" for="og_description">
                                <span>Open Graph Description</span>
                                <span class="page-cms__counter" data-counter-for="og_description"></span>
                            </label>

                            <textarea
                                class="page-cms__textarea"
                                id="og_description"
                                name="og_description"
                                rows="4"
                                maxlength="5000"
                                placeholder="Description used by social sharing platforms."
                            >{{ $seoValue('og_description') }}</textarea>
                        </div>

                        <div class="page-cms__field">
                            <label class="page-cms__label" for="og_image">
                                <span>Open Graph Image</span>
                            </label>

                            <div class="page-cms__image-upload">
                                <input
                                    class="page-cms__file"
                                    type="file"
                                    id="og_image"
                                    name="og_image"
                                    accept=".jpg,.jpeg,.png,.webp,image/*"
                                >

                                <img
                                    id="ogImagePreview"
                                    class="page-cms__preview-image {{ $seo?->og_image ? 'is-visible' : '' }}"
                                    src="{{ $seo?->og_image ? asset('storage/' . $seo->og_image) : '' }}"
                                    alt="Open Graph preview"
                                >
                            </div>

                            <div class="page-cms__hint">
                                Recommended for social cards. Maximum 5MB.
                            </div>
                        </div>

                        <div class="page-cms__field">
                            <label class="page-cms__label" for="twitter_title">
                                <span>Twitter/X Title</span>
                                <span class="page-cms__counter" data-counter-for="twitter_title"></span>
                            </label>

                            <input
                                class="page-cms__input"
                                type="text"
                                id="twitter_title"
                                name="twitter_title"
                                value="{{ $seoValue('twitter_title') }}"
                                maxlength="255"
                                placeholder="Social title"
                            >
                        </div>

                        <div class="page-cms__field">
                            <label class="page-cms__label" for="twitter_description">
                                <span>Twitter/X Description</span>
                                <span class="page-cms__counter" data-counter-for="twitter_description"></span>
                            </label>

                            <textarea
                                class="page-cms__textarea"
                                id="twitter_description"
                                name="twitter_description"
                                rows="4"
                                maxlength="5000"
                                placeholder="Social description"
                            >{{ $seoValue('twitter_description') }}</textarea>
                        </div>

                        <div class="page-cms__field">
                            <label class="page-cms__label" for="twitter_image">
                                <span>Twitter/X Image</span>
                            </label>

                            <div class="page-cms__image-upload">
                                <input
                                    class="page-cms__file"
                                    type="file"
                                    id="twitter_image"
                                    name="twitter_image"
                                    accept=".jpg,.jpeg,.png,.webp,image/*"
                                >

                                <img
                                    id="twitterImagePreview"
                                    class="page-cms__preview-image {{ $seo?->twitter_image ? 'is-visible' : '' }}"
                                    src="{{ $seo?->twitter_image ? asset('storage/' . $seo->twitter_image) : '' }}"
                                    alt="Twitter preview"
                                >
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            {{-- PUBLISHING --}}
            <section class="page-cms__card">
                <div class="page-cms__card-head">
                    <div>
                        <h2 class="page-cms__card-title">06. Publishing</h2>
                        <p class="page-cms__card-help">
                            Save this page as a draft or make it live immediately.
                        </p>
                    </div>
                </div>

                <div class="page-cms__card-body">

                    <div class="page-cms__field">
                        <label class="page-cms__label">
                            <span>Publishing Status <span class="page-cms__required">*</span></span>
                        </label>

                        <div class="page-cms__status-row">

                            <div class="page-cms__status">
                                <input
                                    type="radio"
                                    id="status_draft"
                                    name="status"
                                    value="draft"
                                    @checked($selectedStatus === 'draft')
                                >

                                <label for="status_draft">
                                    Save as Draft
                                </label>
                            </div>

                            <div class="page-cms__status">
                                <input
                                    type="radio"
                                    id="status_published"
                                    name="status"
                                    value="published"
                                    @checked($selectedStatus === 'published')
                                >

                                <label for="status_published">
                                    Publish Now
                                </label>
                            </div>

                        </div>
                    </div>

                </div>
            </section>

        </div>

        <aside class="page-cms__side">

            {{-- SEO SCORE --}}
            <section class="page-cms__card">
                <div class="page-cms__card-head">
                    <div>
                        <h2 class="page-cms__card-title">Page Quality</h2>
                        <p class="page-cms__card-help">
                            Live checks while you build the page.
                        </p>
                    </div>
                </div>

                <div class="page-cms__card-body">

                    <div class="page-cms__score">
                        <strong style="font-size:12px;">SEO</strong>

                        <div class="page-cms__score-track">
                            <div
                                class="page-cms__score-fill"
                                id="seoScoreFill"
                            ></div>
                        </div>

                        <span
                            class="page-cms__score-value"
                            id="seoScoreValue"
                        >
                            0%
                        </span>
                    </div>

                    <div class="page-cms__checklist">

                        <div class="page-cms__check" data-seo-check="title">
                            <span class="page-cms__check-icon">•</span>
                            <span>Descriptive page title</span>
                        </div>

                        <div class="page-cms__check" data-seo-check="meta_title">
                            <span class="page-cms__check-icon">•</span>
                            <span>Meta title provided</span>
                        </div>

                        <div class="page-cms__check" data-seo-check="meta_description">
                            <span class="page-cms__check-icon">•</span>
                            <span>Meta description provided</span>
                        </div>

                        <div class="page-cms__check" data-seo-check="slug">
                            <span class="page-cms__check-icon">•</span>
                            <span>Clean URL slug</span>
                        </div>

                        <div class="page-cms__check" data-seo-check="content">
                            <span class="page-cms__check-icon">•</span>
                            <span>Useful page content</span>
                        </div>

                        <div class="page-cms__check" data-seo-check="image_alt">
                            <span class="page-cms__check-icon">•</span>
                            <span>Image alt text</span>
                        </div>

                    </div>

                </div>
            </section>

            {{-- SEARCH PREVIEW --}}
            <section class="page-cms__card">
                <div class="page-cms__card-head">
                    <div>
                        <h2 class="page-cms__card-title">Search Preview</h2>
                        <p class="page-cms__card-help">
                            Approximate preview of how the page may appear in search results.
                        </p>
                    </div>
                </div>

                <div class="page-cms__card-body">
                    <div class="page-cms__seo-preview">
                        <div
                            class="page-cms__seo-url"
                            id="seoPreviewUrl"
                        >
                            https://example.com/
                        </div>

                        <div
                            class="page-cms__seo-title"
                            id="seoPreviewTitle"
                        >
                            Your page title
                        </div>

                        <div
                            class="page-cms__seo-description"
                            id="seoPreviewDescription"
                        >
                            Add a meta description to see a better preview.
                        </div>
                    </div>
                </div>
            </section>

            {{-- EDITOR GUIDANCE --}}
            <section class="page-cms__card">
                <div class="page-cms__card-head">
                    <div>
                        <h2 class="page-cms__card-title">Publishing Checklist</h2>
                    </div>
                </div>

                <div class="page-cms__card-body">
                    <div class="page-cms__side-note">
                        Before publishing, verify the URL, page title, featured image,
                        alt text, navigation placement and SEO description. Then open the
                        public page and check desktop + mobile layouts.
                    </div>
                </div>
            </section>

        </aside>

    </div>

    <div class="page-cms__sticky">
        <div class="page-cms__sticky-status">
            {{ $isEdit ? 'Changes are saved when you submit the form.' : 'Review your page before publishing.' }}
        </div>

        <div class="page-cms__actions">
            <a
                href="{{ route('admin.pages.index') }}"
                class="page-cms__btn page-cms__btn--secondary"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="page-cms__btn page-cms__btn--primary"
            >
                {{ $isEdit ? 'Save Page Changes' : 'Create Page' }}
            </button>
        </div>
    </div>

</div>

<script>
(function () {
    'use strict';

    const title = document.getElementById('title');
    const slug = document.getElementById('slug');
    const excerpt = document.getElementById('excerpt');
    const content = document.getElementById('content');
    const metaTitle = document.getElementById('meta_title');
    const metaDescription = document.getElementById('meta_description');
    const metaKeywords = document.getElementById('keywords');
    const imageAlt = document.getElementById('image_alt');
    const seoPreviewUrl = document.getElementById('seoPreviewUrl');
    const seoPreviewTitle = document.getElementById('seoPreviewTitle');
    const seoPreviewDescription = document.getElementById('seoPreviewDescription');
    const seoScoreFill = document.getElementById('seoScoreFill');
    const seoScoreValue = document.getElementById('seoScoreValue');
    const contentPreview = document.getElementById('contentPreview');
    const sourceEditor = document.getElementById('sourceEditor');

    function slugify(value) {
        return value
            .toString()
            .trim()
            .toLowerCase()
            .replace(/['"]/g, '')
            .replace(/[^a-z0-9\/\s_-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/\/+/g, '/')
            .replace(/(^[\/-]+|[\/-]+$)/g, '');
    }

    let slugTouched = !!(slug && slug.value.trim());

    title?.addEventListener('input', function () {
        if (!slugTouched) {
            slug.value = slugify(title.value);
        }

        updateAll();
    });

    slug?.addEventListener('input', function () {
        slugTouched = true;
        slug.value = slugify(slug.value);
        updateAll();
    });

    document.getElementById('generateSlug')?.addEventListener('click', function () {
        if (title && slug) {
            slug.value = slugify(title.value);
            slugTouched = true;
            updateAll();
        }
    });

    document.querySelectorAll('[data-counter-for]').forEach(function (counter) {
        const fieldId = counter.getAttribute('data-counter-for');
        const field = document.getElementById(fieldId);

        if (!field) {
            return;
        }

        const updateCounter = function () {
            const max = field.getAttribute('maxlength');

            if (max) {
                counter.textContent = field.value.length + ' / ' + max;
            } else {
                counter.textContent = field.value.length + ' chars';
            }
        };

        field.addEventListener('input', updateCounter);
        updateCounter();
    });

    [excerpt, metaTitle, metaDescription, metaKeywords, imageAlt].forEach(function (field) {
        field?.addEventListener('input', updateAll);
    });

    content?.addEventListener('input', updateAll);

    document.querySelectorAll('[data-wrap]').forEach(function (button) {
        button.addEventListener('click', function () {
            if (!content) {
                return;
            }

            const value = button.getAttribute('data-wrap') || '';
            const parts = value.split('|');
            const start = parts[0] || '';
            const end = parts[1] || '';
            const selectionStart = content.selectionStart;
            const selectionEnd = content.selectionEnd;
            const selected = content.value.substring(selectionStart, selectionEnd);

            content.focus();

            content.setRangeText(
                start + selected + end,
                selectionStart,
                selectionEnd,
                'select'
            );

            updateAll();
        });
    });

    document.querySelectorAll('[data-content-tab]').forEach(function (tab) {
        tab.addEventListener('click', function () {
            document.querySelectorAll('[data-content-tab]').forEach(function (item) {
                item.classList.remove('is-active');
            });

            tab.classList.add('is-active');

            const mode = tab.getAttribute('data-content-tab');

            if (mode === 'preview') {
                sourceEditor.style.display = 'none';
                contentPreview.classList.add('is-visible');

                const html = (content?.value || '')
                    .replace(/<script\b[^>]*>[\s\S]*?<\/script>/gi, '')
                    .replace(/<style\b[^>]*>[\s\S]*?<\/style>/gi, '');

                contentPreview.innerHTML = html || '<p style="color:#94a3ad;">Nothing to preview yet.</p>';
            } else {
                sourceEditor.style.display = '';
                contentPreview.classList.remove('is-visible');
            }
        });
    });

    function imagePreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);

        if (!input || !preview) {
            return;
        }

        input.addEventListener('change', function () {
            const file = input.files && input.files[0];

            if (!file) {
                return;
            }

            if (!file.type.startsWith('image/')) {
                return;
            }

            const reader = new FileReader();

            reader.onload = function (event) {
                preview.src = event.target.result;
                preview.classList.add('is-visible');
            };

            reader.readAsDataURL(file);
        });
    }

    imagePreview('featured_image', 'featuredImagePreview');
    imagePreview('og_image', 'ogImagePreview');
    imagePreview('twitter_image', 'twitterImagePreview');

    document.querySelectorAll('input[name="menu_location"]').forEach(function (radio) {
        radio.addEventListener('change', updateMenuVisibility);
    });

    function updateMenuVisibility() {
        const selected = document.querySelector('input[name="menu_location"]:checked')?.value || 'none';
        document.getElementById('headerSettings')?.classList.toggle(
            'is-visible',
            selected === 'header' || selected === 'both'
        );

        document.getElementById('footerSettings')?.classList.toggle(
            'is-visible',
            selected === 'footer' || selected === 'both'
        );
    }

    function setCheck(key, good) {
        const item = document.querySelector('[data-seo-check="' + key + '"]');

        if (!item) {
            return;
        }

        item.classList.toggle('is-good', good);

        const icon = item.querySelector('.page-cms__check-icon');

        if (icon) {
            icon.textContent = good ? '✓' : '•';
        }
    }

    function updateSeo() {
        const titleValue = (title?.value || '').trim();
        const metaTitleValue = (metaTitle?.value || '').trim();
        const metaDescriptionValue = (metaDescription?.value || '').trim();
        const slugValue = (slug?.value || '').trim();
        const contentValue = (content?.value || '').trim();
        const imageAltValue = (imageAlt?.value || '').trim();

        const checks = {
            title: titleValue.length >= 8,
            meta_title: metaTitleValue.length >= 20,
            meta_description: metaDescriptionValue.length >= 80,
            slug: /^[a-z0-9]+(?:[\/_-][a-z0-9]+)*$/.test(slugValue),
            content: contentValue.replace(/<[^>]*>/g, ' ').trim().length >= 120,
            image_alt: imageAltValue.length >= 8
        };

        Object.entries(checks).forEach(function ([key, good]) {
            setCheck(key, good);
        });

        const score = Math.round(
            (Object.values(checks).filter(Boolean).length / Object.keys(checks).length) * 100
        );

        seoScoreFill.style.width = score + '%';
        seoScoreValue.textContent = score + '%';

        const baseUrl = window.location.origin + '/';
        seoPreviewUrl.textContent = baseUrl + slugValue;

        seoPreviewTitle.textContent =
            metaTitleValue ||
            titleValue ||
            'Your page title';

        seoPreviewDescription.textContent =
            metaDescriptionValue ||
            excerpt?.value?.trim() ||
            'Add a meta description to see a better preview.';
    }

    function updateAll() {
        updateSeo();

        if (
            contentPreview?.classList.contains('is-visible')
        ) {
            const html = (content?.value || '')
                .replace(/<script\b[^>]*>[\s\S]*?<\/script>/gi, '')
                .replace(/<style\b[^>]*>[\s\S]*?<\/style>/gi, '');

            contentPreview.innerHTML = html || '<p style="color:#94a3ad;">Nothing to preview yet.</p>';
        }
    }

    updateMenuVisibility();
    updateAll();
})();
</script>
