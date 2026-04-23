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
        <span class="hero-badge">✨ Welcome to ScribbleDiary</span>
        <h1>Discover Ideas That<br><span class="gradient-text">Inspire & Innovate</span></h1>
        <p>Explore thought-provoking articles on technology, design, business, and creative thinking from writers around the
            world.</p>
        <div class="hero-buttons">
            <a href="articles.html" class="btn btn-primary">Browse Articles →</a>
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
                <img class="featured-card-img"
                    {{-- src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=800&h=500&fit=crop" --}}
                    src="{{ asset('images/posts') . '/' . $featuredPost->featured_image }}"
                    alt="Team working on technology">
                <div class="featured-card-body">
                    <div class="article-card-meta">
                        <a href="{{ route('blog.category_posts', $featuredPost->post_category->slug) }}">
                            <span class="tag">
                                {{ $featuredPost->post_category->name }}
                            </span>
                        </a>
                        <a href="{{ route('blog.category_posts', $featuredPost->post_category->parent_category->slug) }}">
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

    <!-- ===== LATEST ARTICLES ===== -->
    <section class="section">
        <div class="section-header">
            <h2>Latest Articles</h2>
            <p>Fresh perspectives and insights from our contributors</p>
            <div class="line"></div>
        </div>

        <div class="categories">
            <span class="category-pill active">All</span>
            <span class="category-pill">Technology</span>
            <span class="category-pill">Design</span>
            <span class="category-pill">Business</span>
            <span class="category-pill">Lifestyle</span>
            <span class="category-pill">Science</span>
        </div>

        <div class="articles-grid-home">

            <!-- Card 1 -->
            <article class="article-card">
                <img class="article-card-img"
                    src="https://images.unsplash.com/photo-1559028012-481c04fa702d?w=600&h=400&fit=crop"
                    alt="Design workspace">
                <div class="article-card-body">
                    <div class="article-card-meta">
                        <span class="tag">Design</span>
                        <span class="date">Dec 12, 2025</span>
                    </div>
                    <h3><a href="article.html">Mastering UI Design: Principles Every Designer Should Know</a></h3>
                    <p>Learn the fundamental principles of UI design that will elevate your work from good to exceptional.
                        From color theory to typography, we cover it all.</p>
                    <a href="article.html" class="read-more">Read More <span class="arrow">→</span></a>
                </div>
            </article>

            <!-- Card 2 -->
            <article class="article-card">
                <img class="article-card-img"
                    src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&h=400&fit=crop"
                    alt="Business analytics">
                <div class="article-card-body">
                    <div class="article-card-meta">
                        <span class="tag tag-accent">Business</span>
                        <span class="date">Dec 10, 2025</span>
                    </div>
                    <h3><a href="article.html">Building a Successful Startup: Lessons From Silicon Valley</a></h3>
                    <p>What separates successful startups from the rest? We interviewed 50 founders to uncover the patterns
                        and strategies that lead to sustainable growth.</p>
                    <a href="article.html" class="read-more">Read More <span class="arrow">→</span></a>
                </div>
            </article>

            <!-- Card 3 -->
            <article class="article-card">
                <img class="article-card-img"
                    src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=600&h=400&fit=crop"
                    alt="Coding workspace">
                <div class="article-card-body">
                    <div class="article-card-meta">
                        <span class="tag">Technology</span>
                        <span class="date">Dec 8, 2025</span>
                    </div>
                    <h3><a href="article.html">The Rise of WebAssembly: A New Era for Web Development</a></h3>
                    <p>WebAssembly is changing how we think about web applications. Explore how this technology enables
                        near-native performance in the browser.</p>
                    <a href="article.html" class="read-more">Read More <span class="arrow">→</span></a>
                </div>
            </article>

            <!-- Card 4 -->
            <article class="article-card">
                <img class="article-card-img"
                    src="https://images.unsplash.com/photo-1545239351-ef35f43d514b?w=600&h=400&fit=crop"
                    alt="Nature landscape">
                <div class="article-card-body">
                    <div class="article-card-meta">
                        <span class="tag tag-accent">Science</span>
                        <span class="date">Dec 5, 2025</span>
                    </div>
                    <h3><a href="article.html">Climate Tech Innovations That Could Save Our Planet</a></h3>
                    <p>From carbon capture to renewable energy breakthroughs, discover the technologies that give us hope in
                        the fight against climate change.</p>
                    <a href="article.html" class="read-more">Read More <span class="arrow">→</span></a>
                </div>
            </article>

            <!-- Card 5 -->
            <article class="article-card">
                <img class="article-card-img"
                    src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=600&h=400&fit=crop"
                    alt="Team collaboration">
                <div class="article-card-body">
                    <div class="article-card-meta">
                        <span class="tag">Lifestyle</span>
                        <span class="date">Dec 3, 2025</span>
                    </div>
                    <h3><a href="article.html">Remote Work Revolution: How to Stay Productive Anywhere</a></h3>
                    <p>The remote work era is here to stay. Learn proven strategies for maintaining focus, work-life
                        balance, and collaboration from anywhere in the world.</p>
                    <a href="article.html" class="read-more">Read More <span class="arrow">→</span></a>
                </div>
            </article>

            <!-- Card 6 -->
            <article class="article-card">
                <img class="article-card-img"
                    src="https://images.unsplash.com/photo-1551434678-e076c223a692?w=600&h=400&fit=crop"
                    alt="Developer working">
                <div class="article-card-body">
                    <div class="article-card-meta">
                        <span class="tag">Technology</span>
                        <span class="date">Dec 1, 2025</span>
                    </div>
                    <h3><a href="article.html">Understanding Modern CSS: Grid, Flexbox, and Beyond</a></h3>
                    <p>CSS has evolved dramatically. This comprehensive guide covers modern layout techniques, animations,
                        and responsive design patterns you need to know.</p>
                    <a href="article.html" class="read-more">Read More <span class="arrow">→</span></a>
                </div>
            </article>

        </div>

        <div style="text-align: center; margin-top: 48px;">
            <a href="articles.html" class="btn btn-outline">View All Articles →</a>
        </div>
    </section>

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

@endsection