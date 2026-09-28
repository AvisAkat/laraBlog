<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('pageTitle')</title>
    @yield('meta_tags')

    <!-- Site favicon -->
    <link rel="icon" type="image/png" sizes="16x16"
        href="/images/site/{{ isset(settings()->site_favicon) ? settings()->site_favicon : ''  }}" />

    {{-- Prevent the page from flashing before the theme is applied --}}
    <script data-theme-init>
        (function () {
            try {
                var t = localStorage.getItem("theme");
                if (!t && window.matchMedia("(prefers-color-scheme: dark)").matches) t = "dark";
                if (t) document.documentElement.setAttribute("data-theme", t);
            } catch (e) { }
        })();
    </script>

    <!-- frontend css -->
    <link rel="stylesheet" href="{{ asset('front/css/style.css') }}">
    @stack('stylesheets')
</head>

<body>

    <!-- ===== HEADER ===== -->
    <header class="header">
        <div class="header-inner">
            {{-- <a href="/" class="logo">BlogVerse</a> --}}
            <div class="brand-logo">
                <a href="/">
                    <img src="/images/site/{{ isset(settings()->site_logo) ? settings()->site_logo : '' }}" alt=""
                        class="dark-logo site_logo" />
                    <img src="/images/site/{{ isset(settings()->site_logo) ? settings()->site_logo : '' }}" alt=""
                        class="light-logo site_logo" />
                </a>
            </div>
            <button class="menu-toggle" onclick="document.querySelector('.nav').classList.toggle('open')">☰</button>
            <nav class="nav">
                <a href="/" class="{{ Route::Is('blog.home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('blog.posts') }}"
                    class="{{ Route::Is('blog.posts') || Route::Is('blog.category_posts') ? 'active' : '' }}">Articles</a>
                <a href="about.html">About Us</a>
                <a href="{{ route('blog.contact') }}">Contact</a>
                {{-- <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle theme">
                    <span class="theme-toggle-label"></span>
                </button> --}}

            </nav>

            <div class="user-settings">
                <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle theme">
                    <span class="theme-toggle-label"></span>
                </button>

                @auth
                    {{-- User Deatils + Dropdown --}}
                    <dic class="user-details">
                        <img src="{{ auth()->user()->picture }}" alt="user-avater">
                        <div class="user-details-dropdown">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="ti-dashboard"></i> Dashboard
                            </a>
                            <a href="{{ route('admin.profile') }}">
                                <i class="ti-user"></i> Profile
                            </a>
                            @if (auth()->user()->type == App\UserType::SuperAdmin)
                                <a href="{{ route('admin.settings') }}">
                                    <i class="ti-settings"></i> Settings
                                </a>
                            @endif
                            <form id="front-logout-form" action="{{ route('admin.logout', ['source' => 'front']) }}"
                                method="POST" style="display: none">
                                @csrf
                            </form>
                            <a href="javascript:;"
                                onclick="event.preventDefault();document.getElementById('front-logout-form').submit();">
                                <i class="ti-power-off"></i> Logout
                            </a>
                        </div>
                    </dic>
                @endauth
            </div>
        </div>
    </header>

    {{-- ======== HEADER END ========== --}}

    {{-- ========= PAGE CONTENT ========= --}}
    <main>
        @yield('content')
    </main>
    {{-- ========== PAGE CONTENT END ========= --}}
    <!-- ===== FOOTER ===== -->
    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="brand-logo">
                    <a href="/">
                        <img src="/images/site/{{ isset(settings()->site_logo) ? settings()->site_logo : '' }}"
                            alt="{{ isset(settings()->site_title) ? settings()->site_title : '' }}"
                            class="dark-logo site_logo" />
                        <img src="/images/site/{{ isset(settings()->site_logo) ? settings()->site_logo : '' }}"
                            alt="{{ isset(settings()->site_title) ? settings()->site_title : '' }}"
                            class="light-logo site_logo" />
                    </a>
                </div>
                <p>
                    {{ isset(settings()->site_meta_description) ? settings()->site_meta_description : '' }}
                </p>
                <div class="social-links" style="margin-top: 20px;">
                    <a href="#" aria-label="Twitter">𝕏</a>
                    <a href="#" aria-label="GitHub">⌨</a>
                    <a href="#" aria-label="LinkedIn">in</a>
                    <a href="#" aria-label="RSS">⊕</a>
                </div>
            </div>
            <div>
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="/">Home</a></li>
                    <li><a href="{{ route('blog.posts') }}">All Articles</a></li>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4>Categories</h4>
                <ul class="footer-links">
                    <li><a href="#">Technology</a></li>
                    <li><a href="#">Design</a></li>
                    <li><a href="#">Business</a></li>
                    <li><a href="#">Science</a></li>
                </ul>
            </div>
            <div>
                <h4>Resources</h4>
                <ul class="footer-links">
                    <li><a href="#">Write for Us</a></li>
                    <li><a href="#">Style Guide</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© 2026 ScribbleDiary. All rights reserved.</span>
            <span>Designed by AvisAkat</span>
        </div>
    </footer>

    <!-- ===== THEME JS ===== -->
    <script src="{{ asset('front/js/main.js') }}"></script>
    <script src="{{ asset('extra-assets/jquery-ui/jquery-ui.min.js') }}"></script> {{-- jquery version is 1.14.0 --}}
    <script>
        //Toggle User Details Dropdown Menu
        document.querySelector('.user-settings .user-details').addEventListener('click', function () {
            this.classList.toggle('active');
        });
        //Close Dropdown if user clicks outside
        document.addEventListener('click', function (e) {
            const userDetails = document.querySelector('.user-settings .user-details');
            if (!userDetails.contains(e.target)) {
                userDetails.classList.remove('active');
            }
        })
    </script>
    @stack('scripts')

</body>

</html>