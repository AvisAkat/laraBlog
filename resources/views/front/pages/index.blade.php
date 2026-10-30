@extends('front.layout.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : '')
@section('meta_tags')
    {!! \Artesaos\SEOTools\Facades\SEOTools::generate() !!}
@endsection
@push('stylesheets')
    <link rel="stylesheet" type="text/css" href="{{ asset('back/vendors/styles/icon-font.min.css') }}" />
@endpush
@section('content')

    <!-- ===== HERO ===== -->
    <section class="hero">
        <span class="hero-badge">✨ Welcome to {{ isset(settings()->site_title) ? settings()->site_title : '' }}</span>
        <h1>Discover Ideas That<br><span class="gradient-text">Inspire & Innovate</span></h1>
        <p>Explore thought-provoking articles on technology, design, business, and creative thinking from writers around the
            world.</p>
        <div class="hero-buttons">
            <a href="{{ route('blog.posts') }}" class="btn btn-primary">Browse Articles →</a>
            <a href="#featured" class="btn btn-outline">Featured Stories</a>
        </div>
    </section>

    <!-- ===== FEATURED ARTICLE ===== -->
    <section class="section" id="featured">
        <div class="section-header">
            <h2>Featured Article</h2>
            <p>Our editor's pick of the week</p>
            <div class="line"></div>
        </div>

        @if (!empty($featuredPost))

            <div class="featured-card">
                <img class="featured-card-img" src="{{ asset('images/posts/' . $featuredPost->featured_image) }}"
                    alt="Team working on technology">
                <div class="featured-card-body">
                    <div class="article-card-meta">
                        <a href="{{ route('blog.category_posts', $featuredPost->post_category->slug) }}">
                            <span class="tag">
                                {{ $featuredPost->post_category->name }}
                            </span>
                        </a>
                        <a href="javascript:;">
                            <span class="tag tag-accent">
                                {{ $featuredPost->post_category->parent_category->name }}
                            </span>
                        </a>
                        <span class="date"><i class="icon-copy ion-calendar"></i>
                            {{ date_formatter($featuredPost->created_at) }}
                        </span>
                        <span class="read-time">|&nbsp;&nbsp;&nbsp;
                            <i class="icon-copy ion-clock"></i>
                            {{ readingDuration($featuredPost->title, $featuredPost->content) }}
                            @choice('min|mins', readingDuration($featuredPost->title, $featuredPost->content)) read
                        </span>
                    </div>
                    <h3>
                        <a href="{{ route('blog.read_post', $featuredPost->slug) }}">{{ $featuredPost->title }}</a>
                    </h3>
                    <p>
                        {!!Str::ucfirst(strip_words($featuredPost->content, 45)) !!}
                    </p>
                    <div style="margin-top: -20px;">
                        <a href="{{ route('blog.author_posts', $featuredPost->author->username) }}">
                            <span class="author-tag">{{ $featuredPost->author->name }}</span>
                        </a>
                    </div>
                    </p>

                    <div>
                        <a href="{{ route('blog.read_post', $featuredPost->slug) }}" class="btn btn-primary">Read Full Article
                            →</a>
                    </div>
                </div>
            </div>

        @endif
    </section>

    <!-- ===== POPULAR THIS MONTH ===== -->
    @if (!empty($popularPosts) && $popularPosts->isNotEmpty())
        <section class="section popular-month-section">
            <div class="section-header">
                <h2>Popular This Month</h2>
                <p>Reader favorites and stories worth discovering</p>
                <div class="line"></div>
            </div>

            <div class="popular-month-grid">
                @foreach ($popularPosts as $post)
                    <article class="popular-month-card">
                        <a class="popular-month-image" href="{{ route('blog.read_post', $post->slug) }}">
                            <img src="{{ asset('images/posts/' . $post->featured_image) }}" alt="{{ $post->title }}">
                            <span class="popular-month-rank">#{{ $loop->iteration }}</span>
                        </a>
                        <div class="popular-month-body">
                            <span class="popular-month-category">
                                {{ $post->post_category->name ?? 'Article' }}
                            </span>
                            <h3>
                                <a href="{{ route('blog.read_post', $post->slug) }}">{{ $post->title }}</a>
                            </h3>
                            <p>{!! Str::ucfirst(strip_words($post->content, 20)) !!}</p>
                            <a class="popular-month-link" href="{{ route('blog.read_post', $post->slug) }}">
                                Read Article <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <!-- ===== LATEST ARTICLES ===== -->
    <section class="section">
        <div class="section-header">
            <h2>Latest Articles</h2>
            <p>Fresh perspectives and insights from our contributors</p>
            <div class="line"></div>
        </div>

        <div class="categories">
            <span class="category-pill active">
                <a href="{{ route('blog.posts') }}">
                    All
                </a>
            </span>
            @foreach ($postCategories as $category)
                <span class="category-pill">
                    <a href="{{ route('blog.category_posts', $category->slug) }}">
                        {{ $category->name }}
                    </a>
                </span>
            @endforeach
        </div>

        <div class="articles-grid-home">

            @if (!empty($latestPost))
                @foreach ($latestPost as $post)
                    <article class="article-card">
                        <a href="{{ route('blog.read_post', $post->slug) }}">
                            <img class="article-card-img" src="{{ asset('/images/posts/' . $post->featured_image) }}"
                                alt="Design workspace">
                        </a>
                        <div class="article-card-body">
                            <div class="article-card-meta">
                                <span class="tag">
                                    <a href="{{ route('blog.category_posts', $post->post_category->slug ?? '.') }}">
                                        {{ $post->post_category->name ?? '.' }}
                                    </a>
                                </span>
                                <span class="date"><i class="icon-copy ion-calendar"></i>
                                    {{ date_formatter($post->created_at, 'short') }}</span>
                                <span class="read-time">|&nbsp;&nbsp;
                                    <i class="icon-copy ion-clock"></i>
                                    {{ readingDuration($post->title, $post->content) }}
                                    @choice('min|mins', readingDuration($post->title, $post->content))
                                </span>
                            </div>
                            <h3>
                                <a href="{{ route('blog.read_post', $post->slug) }}">
                                    {{ $post->title }}
                                </a>
                            </h3>
                            <p>
                                {!!Str::ucfirst(strip_words($post->content, 21)) !!}
                            </p>
                            <div style="margin-top: -16px;margin-bottom: 5px">
                                <a href="{{ route('blog.author_posts', $post->author->username) }}">
                                    <span class="author-tag">{{ $post->author->name }}</span>
                                </a>
                            </div>
                            <a href="{{ route('blog.read_post', $post->slug) }}" class="read-more">Read More <span
                                    class="arrow">→</span></a>
                        </div>
                    </article>

                @endforeach
            @endif


        </div>

        <div style="text-align: center; margin-top: 48px;">
            <a href="{{ route('blog.posts') }}" class="btn btn-outline">View All Articles →</a>
        </div>
    </section>

    <!-- ===== STATS ===== -->
    @livewire('aboutus-stats')

    <!-- ===== NEWSLETTER ===== -->
    <section class="section">
        <div class="newsletter">
            <h2>Stay in the Loop ✉️</h2>
            <p>Get the latest articles delivered straight to your inbox. No spam, just great content.</p>
            <div class="newsletter-form">
                <input type="email" placeholder="Enter your email address">
                <button class="btn-white">Subscribe</button>
            </div>
        </div>
    </section>

    <!-- ===== RANDOM ARTICLES ===== -->
    {{-- @php($randomArticles = collect($randomPosts ?? [])->shuffle()) --}}
    @if ($randomPosts->isNotEmpty())
        <section class="section random-articles-section" aria-labelledby="random-articles-title">
            <div class="random-articles-heading">
                <div class="section-header">
                    <h2 id="random-articles-title">A Little More to Explore</h2>
                    <p>Discover something new from our stories</p>
                    <div class="line"></div>
                </div>
                @if ($randomPosts->count() > 3)
                    <div class="random-articles-controls" aria-label="Article carousel controls">
                        <button type="button" class="random-articles-control" data-random-articles-prev
                            aria-label="Show previous articles" disabled>
                            <i class="icon-copy ion-ios-arrow-back" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="random-articles-control" data-random-articles-next
                            aria-label="Show next articles">
                            <i class="icon-copy ion-ios-arrow-forward" aria-hidden="true"></i>
                        </button>
                    </div>
                @endif
            </div>
            <div class="random-articles-viewport">
                <div class="random-articles-track" data-random-articles role="region"
                    aria-label="Random articles carousel" tabindex="0">
                    @foreach ($randomPosts as $post)
                        <article class="random-article-card">
                            <a class="random-article-image" href="{{ route('blog.read_post', $post->slug) }}">
                                <img src="{{ asset('images/posts/' . $post->featured_image) }}"
                                    alt="{{ $post->title }}" loading="lazy">
                            </a>
                            <div class="random-article-body">
                                <span class="popular-month-category">
                                    {{ $post->post_category->name ?? 'Article' }}
                                </span>
                                <h3>
                                    <a href="{{ route('blog.read_post', $post->slug) }}">{{ $post->title }}</a>
                                </h3>
                                <p>{!! Str::ucfirst(strip_words($post->content, 20)) !!}</p>
                                <a class="popular-month-link" href="{{ route('blog.read_post', $post->slug) }}">
                                    Read Article <span aria-hidden="true">→</span>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-random-articles]').forEach((carousel) => {
            const section = carousel.closest('.random-articles-section');
            const previousButton = section.querySelector('[data-random-articles-prev]');
            const nextButton = section.querySelector('[data-random-articles-next]');
            const cards = Array.from(carousel.children);
            let currentIndex = 0;
            let autoplay;

            const getVisibleCards = () => {
                if (window.matchMedia('(max-width: 640px)').matches) {
                    return 1;
                }
                return window.matchMedia('(max-width: 900px)').matches ? 2 : 3;
            };

            const getMaxIndex = () => Math.max(0, cards.length - getVisibleCards());

            const updateCarousel = () => {
                const gap = parseFloat(window.getComputedStyle(carousel).columnGap) || 0;
                const cardWidth = cards[0].getBoundingClientRect().width;
                const maxIndex = getMaxIndex();
                currentIndex = Math.min(currentIndex, maxIndex);
                carousel.style.transform = `translateX(-${currentIndex * (cardWidth + gap)}px)`;

                if (previousButton && nextButton) {
                    previousButton.disabled = currentIndex === 0;
                    nextButton.disabled = currentIndex === maxIndex;
                }
            };

            const startAutoplay = () => {
                window.clearInterval(autoplay);
                if (getMaxIndex() > 0 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    autoplay = window.setInterval(() => {
                        currentIndex = currentIndex >= getMaxIndex() ? 0 : currentIndex + 1;
                        updateCarousel();
                    }, 5000);
                }
            };

            if (previousButton && nextButton) {
                previousButton.addEventListener('click', () => {
                    currentIndex = Math.max(0, currentIndex - 1);
                    updateCarousel();
                    startAutoplay();
                });
                nextButton.addEventListener('click', () => {
                    currentIndex = Math.min(getMaxIndex(), currentIndex + 1);
                    updateCarousel();
                    startAutoplay();
                });
            }

            section.addEventListener('mouseenter', () => window.clearInterval(autoplay));
            section.addEventListener('mouseleave', startAutoplay);
            section.addEventListener('focusin', () => window.clearInterval(autoplay));
            section.addEventListener('focusout', (event) => {
                if (!section.contains(event.relatedTarget)) {
                    startAutoplay();
                }
            });
            window.addEventListener('resize', () => {
                updateCarousel();
                startAutoplay();
            });
            updateCarousel();
            startAutoplay();
        });
    </script>
@endpush