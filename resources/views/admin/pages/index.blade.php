@extends('admin.layouts.app')

@section('title', 'Pages')

@section('content')

<style>

.pages-wrapper {
    padding: 30px;
}

.pages-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 22px;
}

.pages-header h1 {
    margin: 0;
    font-size: 28px;
    color: #0f172a;
}

.pages-header p {
    margin: 6px 0 0;
    color: #64748b;
}

.pages-add-button {
    background: var(--admin-primary);
    color: #fff;
    padding: 11px 17px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 700;
    font-size: 13px;
}

.pages-alert {
    padding: 14px 17px;
    border-radius: 9px;
    margin-bottom: 20px;
}

.pages-success {
    background: #ecfdf5;
    color: #047857;
}

.pages-filter {
    background: #fff;
    border: 1px solid #e2e8f0;
    padding: 17px;
    border-radius: 12px;
    margin-bottom: 20px;
}

.pages-filter form {
    display: grid;
    grid-template-columns: 1fr 180px 200px auto;
    gap: 10px;
}

.pages-filter input,
.pages-filter select {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 10px 12px;
}

.pages-filter button {
    border: 0;
    background: var(--admin-primary);
    color: #fff;
    border-radius: 8px;
    padding: 10px 16px;
    font-weight: 700;
    cursor: pointer;
}

.pages-table-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow-x: auto;
}

.pages-table {
    width: 100%;
    border-collapse: collapse;
}

.pages-table th,
.pages-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #e2e8f0;
    text-align: left;
    white-space: nowrap;
}

.pages-table th {
    background: #f8fafc;
    color: #475569;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .05em;
    text-transform: uppercase;
}

.pages-table td {
    font-size: 13px;
}

.pages-title {
    font-weight: 700;
    color: #0f172a;
}

.pages-slug {
    color: #64748b;
    font-size: 12px;
    margin-top: 3px;
}

.pages-badge {
    display: inline-flex;
    padding: 5px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
}

.pages-published {
    background: #dcfce7;
    color: #166534;
}

.pages-draft {
    background: #fef3c7;
    color: #92400e;
}

.pages-location {
    background: #eff6ff;
    color: #1d4ed8;
}

.pages-actions {
    display: flex;
    align-items: center;
    gap: 7px;
}

.pages-action {
    border: 0;
    border-radius: 7px;
    padding: 7px 10px;
    font-size: 12px;
    text-decoration: none;
    cursor: pointer;
    font-weight: 700;
}

.pages-edit {
    background: var(--admin-primary-soft);
    color: var(--admin-primary-dark);
}

.pages-view {
    background: #f1f5f9;
    color: #334155;
}

.pages-status {
    background: #ecfdf5;
    color: #047857;
}

.pages-delete {
    background: #fef2f2;
    color: #b91c1c;
}

.pages-pagination {
    padding: 18px;
}

@media(max-width:900px) {

    .pages-wrapper {
        padding: 18px;
    }

    .pages-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .pages-filter form {
        grid-template-columns: 1fr;
    }

}

</style>


<div class="pages-wrapper">

    <div class="pages-header">

        <div>

            <h1>
                Pages
            </h1>

            <p>
                Manage website pages and their SEO settings.
            </p>

        </div>

        <a
            href="{{ route('admin.pages.create') }}"
            class="pages-add-button"
        >
            + Add Page
        </a>

    </div>


    @if(session('success'))

        <div class="pages-alert pages-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- FILTER --}}

    <div class="pages-filter">

        <form
            action="{{ route('admin.pages.index') }}"
            method="GET"
        >

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search by page title or slug..."
            >


            <select name="status">

                <option value="">
                    All Status
                </option>

                <option
                    value="published"
                    @selected(
                        request('status') === 'published'
                    )
                >
                    Published
                </option>

                <option
                    value="draft"
                    @selected(
                        request('status') === 'draft'
                    )
                >
                    Draft
                </option>

            </select>


            <select name="menu_location">

                <option value="">
                    All Locations
                </option>

                <option
                    value="none"
                    @selected(
                        request('menu_location') === 'none'
                    )
                >
                    No Menu
                </option>

                <option
                    value="header"
                    @selected(
                        request('menu_location') === 'header'
                    )
                >
                    Header
                </option>

                <option
                    value="footer"
                    @selected(
                        request('menu_location') === 'footer'
                    )
                >
                    Footer
                </option>

                <option
                    value="both"
                    @selected(
                        request('menu_location') === 'both'
                    )
                >
                    Header + Footer
                </option>

            </select>


            <button type="submit">
                Filter
            </button>

        </form>

    </div>


    {{-- TABLE --}}

    <div class="pages-table-card">

        <table class="pages-table">

            <thead>

                <tr>

                    <th>
                        Page
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Location
                    </th>

                    <th>
                        Template
                    </th>

                    <th>
                        Updated
                    </th>

                    <th>
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse(
                    $pages as $page
                )

                    <tr>

                        <td>

                            <div class="pages-title">
                                {{ $page->title }}
                            </div>

                            <div class="pages-slug">
                                /{{ ltrim($page->slug, '/') }}
                            </div>

                        </td>


                        <td>

                            <span
                                class="pages-badge
                                {{ $page->isPublished()
                                    ? 'pages-published'
                                    : 'pages-draft'
                                }}"
                            >
                                {{ ucfirst($page->status) }}
                            </span>

                        </td>


                        <td>

                            <span class="pages-badge pages-location">

                                @switch($page->menu_location)

                                    @case('header')
                                        Header
                                        @break

                                    @case('footer')
                                        Footer
                                        @break

                                    @case('both')
                                        Header + Footer
                                        @break

                                    @default
                                        No Menu

                                @endswitch

                            </span>

                        </td>


                        <td>
                            {{ ucwords(
                                str_replace(
                                    '-',
                                    ' ',
                                    $page->template
                                )
                            ) }}
                        </td>


                        <td>
                            {{ $page->updated_at?->format('d M Y, h:i A') }}
                        </td>


                        <td>

                            <div class="pages-actions">

                                <a
                                    href="{{ route(
                                        'admin.pages.edit',
                                        $page
                                    ) }}"
                                    class="pages-action pages-edit"
                                >
                                    Edit
                                </a>


                                @if($page->isPublished())

                                    <a
                                        href="{{ $page->url }}"
                                        target="_blank"
                                        class="pages-action pages-view"
                                    >
                                        View
                                    </a>

                                @endif


                                <form
                                    action="{{ route(
                                        'admin.pages.status',
                                        $page
                                    ) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="pages-action pages-status"
                                    >
                                        {{ $page->isPublished()
                                            ? 'Draft'
                                            : 'Publish'
                                        }}
                                    </button>

                                </form>


                                <form
                                    action="{{ route(
                                        'admin.pages.destroy',
                                        $page
                                    ) }}"
                                    method="POST"
                                    onsubmit="return confirm('Delete this page permanently?');"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="pages-action pages-delete"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            style="text-align:center;padding:50px;color:#64748b;"
                        >
                            No pages found.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>


        <div class="pages-pagination">
            {{ $pages->links() }}
        </div>

    </div>

</div>

@endsection