@extends('front.layout.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'ScribbyDiary')
@section('meta_tags')
    {!! \Artesaos\SEOTools\Facades\SEOTools::generate() !!}
@endsection
@push('stylesheets')
    <link rel="stylesheet" type="text/css" href="{{ asset('back/vendors/styles/icon-font.min.css') }}" />
@endpush
@section('content')

    <!-- Hero Section -->
    <section class="hero-contact">
        <div class="container">
            <h1>About {{ settings()->site_title }}</h1>
            <p>Empowering creators and readers with insights that matter, stories that inspire, and ideas that shape the
                future.</p>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="about-section">
        <div class="section">
            <div class="about-grid">
                <div class="about-image">
                    <div class="about-image-inner">📝</div>
                </div>
                <div class="about-content">
                    <h2>Our Story</h2>
                    <p>{{ settings()->site_title }} was founded in 2020 with a simple mission: to make quality content
                        accessible to everyone.
                        What started as a small team of tech enthusiasts sharing their discoveries has grown into a thriving
                        community of writers, designers, developers, and curious minds.</p>
                    <p>We believe that great ideas deserve great platforms. Every article, every tutorial, and every opinion
                        piece on {{ settings()->site_title }} goes through careful curation to ensure it provides real value to our readers.</p>
                    <p>Today, we're proud to host content from over 200 contributors, serving millions of readers across the
                        globe. But no matter how big we grow, our commitment to quality remains the same.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Stats Section -->
    <section class="stats-section">
        <div class="section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">2M+</div>
                    <div class="stat-label">Monthly Readers</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Published Articles</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">200+</div>
                    <div class="stat-label">Expert Writers</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Countries Reached</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section">
        <div class="section">
            <div class="section-header">
                <h2>Our Core Values</h2>
                <p>The principles that guide everything we do at {{ settings()->site_title }}</p>
            </div>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">✨</div>
                    <h3>Quality First</h3>
                    <p>We never compromise on quality. Every piece of content is carefully researched, well-written, and
                        edited to perfection before publication.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🎯</div>
                    <h3>Reader Focused</h3>
                    <p>Our readers are our priority. We create content that educates, inspires, and provides real solutions
                        to real problems.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🌍</div>
                    <h3>Accessibility</h3>
                    <p>Knowledge should be free and accessible. We work to make complex topics understandable for everyone,
                        regardless of background.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="about-team-section">
        <div class="section">
            <div class="section-header">
                <h2>Meet Our Team</h2>
                <p>The passionate people behind {{ settings()->site_title }}'s success</p>
            </div>
            <div class="team-grid">
                <div class="team-card">
                    <div class="team-image">
                        <div class="team-avatar">👩‍💼</div>
                    </div>
                    <div class="team-info">
                        <h3>Sarah Chen</h3>
                        <p class="role">Founder & Editor-in-Chief</p>
                        <p>Former tech journalist with 10+ years experience in digital media and content strategy.</p>
                        <div class="team-social">
                            <a href="#">𝕏</a>
                            <a href="#">in</a>
                        </div>
                    </div>
                </div>
                <div class="team-card">
                    <div class="team-image">
                        <div class="team-avatar">👨‍💻</div>
                    </div>
                    <div class="team-info">
                        <h3>Marcus Rivera</h3>
                        <p class="role">Head of Technology</p>
                        <p>Full-stack developer and open source advocate. Loves breaking down complex tech concepts.</p>
                        <div class="team-social">
                            <a href="#">𝕏</a>
                            <a href="#">in</a>
                        </div>
                    </div>
                </div>
                <div class="team-card">
                    <div class="team-image">
                        <div class="team-avatar">👩‍🎨</div>
                    </div>
                    <div class="team-info">
                        <h3>Emily Watson</h3>
                        <p class="role">Design Director</p>
                        <p>Award-winning designer who believes great design should be both beautiful and functional.</p>
                        <div class="team-social">
                            <a href="#">𝕏</a>
                            <a href="#">in</a>
                        </div>
                    </div>
                </div>
                <div class="team-card">
                    <div class="team-image">
                        <div class="team-avatar">👨‍🎓</div>
                    </div>
                    <div class="team-info">
                        <h3>James Park</h3>
                        <p class="role">Community Manager</p>
                        <p>Builds bridges between writers and readers. Passionate about fostering meaningful discussions.
                        </p>
                        <div class="team-social">
                            <a href="#">𝕏</a>
                            <a href="#">in</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="about-cta-section">
        <div class="section">
            <h2 style="font-size: 2.5rem;font-weight: 700;margin-bottom: 16px;">Want to Join Our Journey?</h2>
            <p
                style="font-size: 1.1rem;opacity: 0.95;margin-bottom: 32px;max-width: 500px;margin-left: auto;margin-right: auto;">
                We're always looking for talented writers and creative minds to contribute to {{ settings()->site_title }}.
            </p>
            <div class="cta-buttons">
                <a href="{{ route('blog.contact') }}" class="cta-btn cta-btn-primary">Get in Touch</a>
                <a href="{{ route('blog.posts') }}" class="cta-btn cta-btn-secondary">Read Our Articles</a>
            </div>
        </div>
    </section>


@endsection