@extends('front.layout.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : '')
@section('meta_tags')
    {!! \Artesaos\SEOTools\Facades\SEOTools::generate() !!}
@endsection
@push('stylesheets')
    <link rel="stylesheet" type="text/css" href="{{ asset('back/vendors/styles/icon-font.min.css') }}" />
@endpush
@section('content')


    <!-- ===== PAGE BANNER ===== -->
    <div class="page-banner">
        @isset($author)
            <div class="author-banner">
                <img class="author-avatar" src="{{ asset($author->picture) }}" alt="Author {{ $author->name }}">
                <h2 class="gradient-text">{{ $author->name }}</h2>
                <p style="text-transform: capitalize; margin-top: -10px;">{{ $author->username }}</p>
                <p class="author-bio-text">{{ $author->bio }}</p>

                @if ($author->social_links)
                    <div class="social-links">
                        @if ($author->social_links->facebook_url)
                            <a href="{{ $author->social_links->facebook_url }}" target="_blank">
                                <button class="share-btn" aria-label="Share on Facebook"><i class="ti-facebook"></i></button>
                            </a>
                        @endif
                        @if ($author->social_links->instagram_url)
                            <a href="{{ $author->social_links->instagram_url }}" target="_blank">
                                <button class="share-btn" aria-label="Share on Instagram"><i class="ti-instagram"></i></button>
                            </a>
                        @endif
                        @if ($author->social_links->youtube_url)
                            <a href="{{ $author->social_links->youtube_url }}" target="_blank">
                                <button class="share-btn" aria-label="Share on YouTube"><i class="ti-youtube"></i></button>
                            </a>
                        @endif
                        @if ($author->social_links->linkedin_url)
                            <a href="{{ $author->social_links->linkedin_url }}" target="_blank">
                                <button class="share-btn" aria-label="Share on LinkedIn"><i class="ti-linkedin"></i></button>
                            </a>
                        @endif
                        @if ($author->social_links->github_url)
                            <a href="{{ $author->social_links->github_url }}" target="_blank">
                                <button class="share-btn" aria-label="Share on GitHub"><i class="ti-github"></i></button>
                            </a>
                        @endif
                        @if ($author->social_links->x_url)
                            <a href="{{ $author->social_links->x_url }}" target="_blank">
                                <button class="share-btn" aria-label="Share on X">𝕏</button>
                            </a>
                        @endif

                    </div>

                @endif
            </div>

        @else
            @if (!$bannerInfo)
                <h1>All <span class="gradient-text">Articles</span></h1>
            @else
                <h1><span class="gradient-text" style="text-transform: capitalize">{{ $bannerInfo ? $bannerInfo : '' }}</span></h1>
            @endif
            <p>Browse our complete collection of articles, tutorials, and stories
                {{ $bannerInfo ? 'in ' . $bannerInfo . ' category' : '' }}
            </p>
        @endisset

    </div>

    <!-- ===== MAIN CONTENT ===== -->
    <section class="articles-section">

        <!-- Search -->
        <div class="search-bar">
            <input type="text" placeholder="Search articles...">
            <button>Search</button>
        </div>

        <!-- Categories -->
        <div class="categories">
            <span class="category-pill active">
                <a href="{{ route('blog.posts') }}">
                    All
                </a>
            </span>
            @foreach ($postCategories as $category)
                @if ($category->name != $bannerInfo)
                    <span class="category-pill">
                        <a href="{{ route('blog.category_posts', $category->slug) }}">
                            {{ $category->name }}({{ $category->posts_count }})
                        </a>
                    </span>
                @endif
            @endforeach
        </div>

        <!-- Layout: Articles + Sidebar -->
        <div class="articles-layout">

            <!-- Articles List -->
            <div>
                <div class="articles-grid">
                    @forelse ($allPosts as $post)

                        <!-- Article -->
                        <article class="article-card-horizontal">
                            <div>
                                <a href="{{ route('blog.read_post', $post->slug) }}">
                                    <img class="article-card-horizontal-img"
                                        src="{{ asset('images/posts/resized/thumb_') . $post->featured_image }}"
                                        alt="{{ $post->title }}">
                                </a>
                            </div>
                            <div class="article-card-horizontal-body">
                                <div class="article-card-meta">
                                    <span class="tag">
                                        <a href="{{ route('blog.category_posts', $post->post_category->slug) }}">
                                            {{ $post->post_category->name }}
                                        </a>
                                    </span>
                                    {{-- <span class="tag tag-accent">Featured</span> --}}
                                    <span class="date">{{ date_formatter($post->created_at) }}</span>
                                </div>
                                <div class="author-row">
                                    <img class="author-avatar" src="{{ asset($post->author->picture) }}"
                                        alt="Author {{ $post->author->name }}">
                                    <span class="author-name">
                                        <a href="{{ route('blog.author_posts', $post->author->username) }}">
                                            {{ $post->author->name }}
                                        </a>
                                    </span>
                                    <span class="read-time">
                                        . {{ readingDuration($post->title, $post->content) }}
                                        @choice('min|mins', readingDuration($post->title, $post->content))
                                        read
                                    </span>
                                </div>
                                <h3>
                                    <a href="{{ route('blog.read_post', $post->slug) }}">{{ $post->title }}</a>
                                </h3>
                                <p>
                                    {!!Str::ucfirst(strip_words($post->content, 25)) !!}
                                </p>
                                <a href="{{ route('blog.read_post', $post->slug) }}" class="read-more">Read Full Article <span
                                        class="arrow">→</span></a>
                            </div>
                        </article>
                    @empty
                        <hr style="margin: 20px 0;border: none;border-top: 1px solid #6b7280;">
                        <h4 style="text-align: center">No posts found</h4>
                    @endforelse

                </div>

                <!-- Pagination -->
                {{ $allPosts->appends(request()->input())->links('components.pagination') }}

            </div>

            <!-- Sidebar -->
            <aside class="sidebar">

                <!-- Popular Posts -->
                <div class="sidebar-card">
                    <h3>🔥 Popular Posts</h3>
                    @foreach ($popularPosts as $post)
                        <div class="popular-item">
                            <span class="popular-number">{{ $loop->iteration }}</span>
                            <div>
                                <h4><a href="{{ route('blog.read_post', $post->slug) }}">{{ $post->title }}</a></h4>
                                <span>12.4k views</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Tags -->
                <div class="sidebar-card">
                    <h3>🏷️ Tags</h3>
                    <div class="tags-cloud">
                        @foreach ($allTags as $tag)
                            <span class="tag {{ $loop->odd ? 'tag-accent' : '' }}">
                                <a href="{{ route('blog.tag_posts', urlencode($tag)) }}">{{ $tag }}</a>
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Newsletter -->
                <div class="sidebar-newsletter">
                    <h3>📬 Newsletter</h3>
                    <p>Get weekly insights delivered to your inbox.</p>
                    <input type="email" placeholder="Your email">
                    <button>Subscribe</button>
                </div>

            </aside>
        </div>
    </section>

@endsection