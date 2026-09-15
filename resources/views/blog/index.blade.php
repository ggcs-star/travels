@extends('layouts.app')

@section('title', 'Travel Blog')

@section('content')

<div class="user-blog-page">

    {{-- HERO --}}

<section
    class="user-blog-hero"
    style="background-image: url('{{ asset('images/hero/blog.png') }}');"
>
        <div class="user-blog-container">

            <span class="user-blog-eyebrow">
                TRAVEL STORIES & GUIDES
            </span>

            <h1>
                Travel inspiration for your next journey
            </h1>

            <p>
                Discover destination guides, practical travel tips,
                itineraries and inspiring stories from around the world.
            </p>

        </div>

    </section>


    {{-- BLOGS --}}

    <section class="user-blog-list">

        <div class="user-blog-container">

            @if($blogs->count())

                <div class="user-blog-grid">

                    @foreach($blogs as $blog)

                        <article class="user-blog-card">

                            <a
                                href="{{ route('blog.show', $blog->slug) }}"
                                class="user-blog-card__image"
                            >

                                @if($blog->featured_image)

                                    <img
                                        src="{{ $blog->featured_image_url ?? \Illuminate\Support\Facades\Storage::url($blog->featured_image) }}"
                                        alt="{{ $blog->featured_image_alt ?: $blog->title }}"
                                        loading="lazy"
                                    >

                                @else

                                    <div class="user-blog-card__placeholder">
                                        Travel
                                    </div>

                                @endif

                            </a>


                            <div class="user-blog-card__body">

                                <div class="user-blog-card__meta">

                                    @if($blog->category)

                                        <span>
                                            {{ $blog->category->name }}
                                        </span>

                                    @endif

                                    @if($blog->reading_time)

                                        <span>
                                            {{ $blog->reading_time }} min read
                                        </span>

                                    @endif

                                </div>


                                <h2>

                                    <a
                                        href="{{ route('blog.show', $blog->slug) }}"
                                    >
                                        {{ $blog->title }}
                                    </a>

                                </h2>


                                @if($blog->excerpt)

                                    <p>
                                        {{ \Illuminate\Support\Str::limit(
                                            $blog->excerpt,
                                            145
                                        ) }}
                                    </p>

                                @endif


                                <div class="user-blog-card__footer">

                                    <span>
                                        {{ $blog->published_at
                                            ? $blog->published_at->format('d M Y')
                                            : ''
                                        }}
                                    </span>

                                    <a
                                        href="{{ route('blog.show', $blog->slug) }}"
                                    >
                                        Read article →
                                    </a>

                                </div>

                            </div>

                        </article>

                    @endforeach

                </div>


                @if($blogs->hasPages())

                    <div class="user-blog-pagination">
                        {{ $blogs->links() }}
                    </div>

                @endif

            @else

                <div class="user-blog-empty">

                    <span>
                        ✦
                    </span>

                    <h2>
                        No articles yet
                    </h2>

                    <p>
                        Travel guides and stories will appear here soon.
                    </p>

                </div>

            @endif

        </div>

    </section>

</div>

@endsection