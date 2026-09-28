@extends('front.layout.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'ScribblyDiary')
@section('meta_tags')
    {!! \Artesaos\SEOTools\Facades\SEOTools::generate() !!}
@endsection
@push('stylesheets')
    <link rel="stylesheet" type="text/css" href="{{ asset('back/vendors/styles/icon-font.min.css') }}" />
    <style>
        .copy-tooltip {
            position: absolute;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: #fff;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 10;
        }
    </style>
@endpush
@section('content')

    <!-- Hero Section -->
    <section class="hero-contact">
        <div class="container">
            <h1>Get in Touch</h1>
            <p>Have questions, suggestions, or feedback? We'd love to hear from you. Reach out and let's start a
                conversation.</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Info -->
                <div class="contact-info">
                    <h2>Let's Connect</h2>
                    <p>Whether you have a question about our content, want to collaborate, or just want to say hello, we're
                        here for you.</p>

                    <div class="info-items">
                        <div class="info-item">
                            <div class="info-icon">📧</div>
                            <div class="info-content">
                                <h4>Email Us</h4>
                                <p><a href="mailto:scribblediary@gmail.com">scribblediary@gmail.com</a></p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">📍</div>
                            <div class="info-content">
                                <h4>Visit Us</h4>
                                <p>East Legon, Greater Accra</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">📞</div>
                            <div class="info-content">
                                <h4>Call Us</h4>
                                <p><a href="tel:+14155551234">+233 (0) 000 000 000</a></p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">⏰</div>
                            <div class="info-content">
                                <h4>Working Hours</h4>
                                <p>Monday - Friday: 9am - 6pm PST<br>Saturday - Sunday: Closed</p>
                            </div>
                        </div>
                    </div>

                    <div class="social-section">
                        <h4>Follow Us</h4>
                        <div class="social-links">
                            <a href="#" aria-label="Twitter">𝕏</a>
                            <a href="#" aria-label="Facebook"><i class="ti-facebook"></i></a>
                            <a href="#" aria-label="Instagram"><i class="ti-instagram"></i></a>
                            <a href="#" aria-label="LinkedIn"><i class="ti-linkedin"></i></a>
                            <a href="#" aria-label="YouTube"><i class="ti-youtube"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="contact-form-wrapper">
                    <h2>Send Us a Message</h2>
                    <x-form-alerts></x-form-alerts>
                    <form method="POST" action="{{ route('blog.send_email') }}">
                        @csrf
                        <div class="form-group">
                            <label for="name">First Name</label>
                            <input type="text" id="name" name="name" placeholder="John Quaye" value="{{ old('name') }}">
                            @error('name')
                                <span class="text-danger ml-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email Address <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" placeholder="johnq@example.com"
                                    value="{{ old('email') }}">
                                @error('email')
                                    <span class="text-danger ml-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" id="phone" name="phone" placeholder="+233 (0) 000 000 000"
                                    value="{{ old('phone') }}">
                                @error('phone')
                                    <span class="text-danger ml-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject <span class="text-danger">*</span></label>
                            <select id="subject" name="subject">
                                <option value="">Select a subject...</option>
                                <option value="general" {{ old('subject') == 'general' ? 'selected' : '' }}>General Inquiry
                                </option>
                                <option value="collaboration" {{ old('subject') == 'collaboration' ? 'selected' : '' }}>
                                    Collaboration Request</option>
                                <option value="feedback" {{ old('subject') == 'feedback' ? 'selected' : '' }}>Content Feedback
                                </option>
                                <option value="support" {{ old('subject') == 'support' ? 'selected' : '' }}>Technical Support
                                </option>
                                <option value="advertising" {{ old('subject') == 'advertising' ? 'selected' : '' }}>
                                    Advertising</option>
                                <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('subject')
                                <span class="text-danger ml-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="message">Message <span class="text-danger">*</span></label>
                            <textarea id="message" name="message"
                                placeholder="Tell us what's on your mind...">{{ old('message') }}</textarea>
                            @error('message')
                                <span class="text-danger ml-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="submit-btn">
                            <span>Send Message</span>
                            <span>→</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="section">
            <div class="section-header">
                <h2>Frequently Asked Questions</h2>
                <p>Quick answers to common questions about contacting and working with us</p>
            </div>
            <div class="faq-grid">
                <div class="faq-item">
                    <h4><span>Q.</span> How can I write for ByteBlog?</h4>
                    <p>We accept guest contributions from experienced writers. Send us your pitch via the contact form with
                        "Guest Post" as the subject. Include your topic idea, relevant experience, and any samples of your
                        previous work.</p>
                </div>
                <div class="faq-item">
                    <h4><span>Q.</span> Do you accept sponsored content?</h4>
                    <p>Yes, we partner with brands that align with our values and audience interests. Reach out to our
                        advertising team through the contact form for media kits and partnership opportunities.</p>
                </div>
                <div class="faq-item">
                    <h4><span>Q.</span> How long does it take to respond?</h4>
                    <p>We aim to respond to all inquiries within 24-48 business hours. During peak times, response times may
                        be slightly longer. Thank you for your patience!</p>
                </div>
                <div class="faq-item">
                    <h4><span>Q.</span> Can I republish your content?</h4>
                    <p>We offer syndication partnerships for select content. Please contact us with details about where the
                        content will be republished, and we'll get back to you with our terms.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="section">
            <div class="map-placeholder">
                <div class="content">
                    <div class="emoji">🗺️</div>
                    <h3>Find Us Here</h3>
                    <p>East Legon. Greater Accra, Ghana</p>
                </div>
            </div>
        </div>
    </section>

@endsection