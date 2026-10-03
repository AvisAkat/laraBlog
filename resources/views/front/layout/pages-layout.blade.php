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

    <!-- CSS -->
    {{--
    <link rel="stylesheet" type="text/css" href="{{ asset('front/bootstrap/bootstrap.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('front/bootstrap/bootstrap.js') }}" /> --}}
    {{--
    <link rel="stylesheet" type="text/css" href="{{ asset('front/bootstrap/styles/style.css') }}" /> --}}

    <!-- frontend css -->
    <link rel="stylesheet" href="{{ asset('front/css/style.css') }}">
    @stack('stylesheets')
</head>

<body>
    {{-- Toasts --}}
    <x-notify-alerts></x-notify-alerts>

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
                    @if (site_social_links()->x_url)
                        <a href="{{ site_social_links()->x_url }}" target="_blank" aria-label="X" title="X (Twitter)">
                            𝕏
                        </a>
                    @endif
                    @if (site_social_links()->facebook_url)
                        <a href="{{ site_social_links()->facebook_url }}" target="_blank" aria-label="Facebook"
                            title="Facebook">
                            <i class="ti-facebook"></i>
                        </a>
                    @endif
                    @if (site_social_links()->instagram_url)
                        <a href="{{ site_social_links()->instagram_url }}" target="_blank" aria-label="Instagram"
                            title="Instagram">
                            <i class="ti-instagram"></i>
                        </a>
                    @endif
                    @if (site_social_links()->linkedin_url)
                        <a href="{{ site_social_links()->linkedin_url }}" target="_blank" aria-label="LinkedIn"
                            title="LinkedIn">
                            <i class="ti-linkedin"></i>
                        </a>
                    @endif
                    @if (site_social_links()->youtube_url)
                        <a href="{{ site_social_links()->youtube_url }}" target="_blank" aria-label="YouTube"
                            title="Youtube">
                            <i class="ti-youtube"></i>
                        </a>
                    @endif
                </div>
            </div>
            <div>
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="/">Home</a></li>
                    <li><a href="{{ route('blog.posts') }}">All Articles</a></li>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="{{ route('blog.contact') }}">Contact</a></li>
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
    <script src="{{ asset('front/js/core.js') }}"></script>
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
    <script>
        // Script for the toast notification to lostern for the showAlert event (livewire))
        document.addEventListener('livewire:init', () => {

            Livewire.on('showAlert', (data) => {
                data = data[0];

                const toastEl = document.getElementById('liveToast');
                const toastMessage = document.getElementById('toast-message');

                // Reset background classes
                toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info');

                // Set background color based on type
                switch (data.type) {
                    case 'success':
                        toastEl.classList.add('bg-success');
                        break;
                    case 'error':
                        toastEl.classList.add('bg-danger');
                        break;
                    case 'warning':
                        toastEl.classList.add('bg-warning');
                        break;
                    default:
                        toastEl.classList.add('bg-info');
                }

                // Set message
                toastMessage.innerText = data.message;

                // Show toast
                const toast = new bootstrap.Toast(toastEl, {
                    delay: 5000
                });

                toast.show();
            });

        });
    </script>
    @stack('scripts')

</body>

</html>