<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/FAIRALL_LOGO.svg') }}">
    <title>{{ $title }}</title>
    @stack('head-scripts')
</head>

<body class="font-body" style="font-family: var(--font-body1); background-color: white; overflow-x: hidden;">
    <x-loading-overlay />
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 shadow-md" id="header"
        style="background-color: var(--color-primary1);">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 py-3 sm:py-4 flex justify-between items-center">
            <!-- Logo -->
            <a href="/" class="flex items-center gap-2 group">
                <img src="{{ asset('images/FAIRALL_LOGO.png') }}" alt="FAIRALL Logo"
                    class="h-8 sm:h-10 md:h-12 w-auto transition-all duration-300 group-hover:scale-105">
                <div class="flex flex-col">
                    <span
                        class="text-lg sm:text-xl md:text-2xl font-bold tracking-wide text-white leading-tight logo-text"
                        style="font-family: var(--font-header1);">FAIRALL</span>
                    <span class="text-[8px] sm:text-sm text-white/50 tracking-wider hidden sm:block"
                        style="font-family: var(--font-body1);">FAIRPLAY FOR ALL FOUNDATION</span>
                </div>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-4 lg:gap-6">
                <a href="/about"
                    class="nav-link px-3 py-2 text-sm lg:text-base font-medium {{ request()->is('about') ? 'active' : '' }}"
                    style="font-family: var(--font-body1); color: {{ request()->is('about') ? 'var(--color-accent1)' : 'rgba(255,255,255,0.8)' }};">
                    About Us
                </a>
                <a href="/programs"
                    class="nav-link px-3 py-2 text-sm lg:text-base font-medium {{ request()->is('programs') ? 'active' : '' }}"
                    style="font-family: var(--font-body1); color: {{ request()->is('programs') ? 'var(--color-accent1)' : 'rgba(255,255,255,0.8)' }};">
                    Programs
                </a>
                <a href="/contact"
                    class="nav-link px-3 py-2 text-sm lg:text-base font-medium {{ request()->is('contact') ? 'active' : '' }}"
                    style="font-family: var(--font-body1); color: {{ request()->is('contact') ? 'var(--color-accent1)' : 'rgba(255,255,255,0.8)' }};">
                    Contact
                </a>
                <a href="/donate"
                    class="nav-link px-3 py-2 text-sm lg:text-base font-medium {{ request()->is('donate') ? 'active' : '' }}"
                    style="font-family: var(--font-body1); color: {{ request()->is('donate') ? 'var(--color-accent1)' : 'rgba(255,255,255,0.8)' }};">
                    Donate
                </a>
                <a href="/login" class="login-btn ml-2 px-5 py-2 text-sm font-semibold rounded-full"
                    style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                    Log In
                </a>
            </div>

            <!-- Hamburger Menu Button (Mobile) -->
            <button id="hamburger-btn"
                class="md:hidden flex flex-col gap-1.5 p-2 rounded-lg transition-all duration-300 hover:bg-white/10">
                <span class="w-6 h-0.5 rounded-full transition-all duration-300 bg-white"></span>
                <span class="w-6 h-0.5 rounded-full transition-all duration-300 bg-white"></span>
                <span class="w-6 h-0.5 rounded-full transition-all duration-300 bg-white"></span>
            </button>
        </nav>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden overflow-hidden transition-all duration-300"
            style="background-color: var(--color-primary1); border-top: 1px solid rgba(255,255,255,0.1);">
            <div class="flex flex-col py-4 px-4 space-y-1">
                <a href="/about"
                    class="mobile-nav-link px-4 py-3 rounded-lg font-medium transition-all duration-300 {{ request()->is('about') ? 'active' : '' }}"
                    style="font-family: var(--font-body1); color: {{ request()->is('about') ? 'var(--color-accent1)' : 'rgba(255,255,255,0.8)' }};">
                    About Us
                </a>
                <a href="/programs"
                    class="mobile-nav-link px-4 py-3 rounded-lg font-medium transition-all duration-300 {{ request()->is('programs') ? 'active' : '' }}"
                    style="font-family: var(--font-body1); color: {{ request()->is('programs') ? 'var(--color-accent1)' : 'rgba(255,255,255,0.8)' }};">
                    Programs
                </a>
                <a href="/contact"
                    class="mobile-nav-link px-4 py-3 rounded-lg font-medium transition-all duration-300 {{ request()->is('contact') ? 'active' : '' }}"
                    style="font-family: var(--font-body1); color: {{ request()->is('contact') ? 'var(--color-accent1)' : 'rgba(255,255,255,0.8)' }};">
                    Contact
                </a>
                <a href="/donate"
                    class="mobile-nav-link px-4 py-3 rounded-lg font-medium transition-all duration-300 {{ request()->is('donate') ? 'active' : '' }}"
                    style="font-family: var(--font-body1); color: {{ request()->is('donate') ? 'var(--color-accent1)' : 'rgba(255,255,255,0.8)' }};">
                    Donate
                </a>
                <a href="/login"
                    class="mobile-login-btn mt-3 mx-4 px-6 py-3 rounded-full font-semibold transition-all duration-300 text-center"
                    style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                    Log In
                </a>
            </div>
        </div>
    </header>

    <main class="pt-16 md:pt-20">
        <div class="w-full">
            {{ $slot }}
        </div>
    </main>

    <footer class="relative overflow-hidden" style="background-color: var(--color-primary1);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-16 relative z-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12 mb-10 lg:mb-12">
                <!-- Logo Column -->
                <div class="text-center sm:text-left">
                    <div class="flex items-center justify-center sm:justify-start gap-2 mb-3">
                        <img src="{{ asset('images/FAIRALL_LOGO.png') }}" alt="FAIRALL Logo"
                            class="h-8 sm:h-10 w-auto">
                        <div class="flex flex-col">
                            <span class="text-lg sm:text-xl font-bold tracking-wide text-white footer-logo-text"
                                style="font-family: var(--font-header1);">FAIRALL</span>
                            <span class="text-[8px] text-white/50 tracking-wider"
                                style="font-family: var(--font-body1);">FAIRPLAY FOR ALL FOUNDATION</span>
                        </div>
                    </div>
                    <p class="text-xs text-white/60 mt-2 ml-10" style="font-family: var(--font-body1);">LEVELING THE
                        PLAYING FIELD</p>
                </div>

                <!-- Quick Links Column -->
                <div class="text-center sm:text-left">
                    <h3 class="font-semibold mb-3 text-base text-white" style="font-family: var(--font-body1);">Quick
                        Links</h3>
                    <ul class="space-y-2">
                        <li><a href="/about"
                                class="footer-link text-sm text-white/60 hover:text-white transition-all duration-200">About
                                Us</a></li>
                        <li><a href="/programs"
                                class="footer-link text-sm text-white/60 hover:text-white transition-all duration-200">Programs</a>
                        </li>
                        <li><a href="/contact"
                                class="footer-link text-sm text-white/60 hover:text-white transition-all duration-200">Contact</a>
                        </li>
                    </ul>
                </div>

                <!-- Support Column -->
                <div class="text-center sm:text-left">
                    <h3 class="font-semibold mb-3 text-base text-white" style="font-family: var(--font-body1);">Support
                    </h3>
                    <ul class="space-y-2">
                        <li><a href="/donate"
                                class="footer-link text-sm text-white/60 hover:text-white transition-all duration-200">Get
                                Involved</a></li>
                        <li><a href="/donate"
                                class="footer-link text-sm text-white/60 hover:text-white transition-all duration-200">Donate</a>
                        </li>

                    </ul>
                </div>

                <!-- Connect Column -->
                <div class="text-center sm:text-left">
                    <h3 class="font-semibold mb-3 text-base text-white" style="font-family: var(--font-body1);">
                        Connect</h3>
                    <div class="flex justify-center sm:justify-start gap-3">
                        <a href="https://www.facebook.com/fairplayforallfoundation/photos"
                            class="footer-social-icon w-9 h-9 flex items-center justify-center rounded-full transition-all duration-300"
                            style="background-color: rgba(255,255,255,0.1);">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="footer-social-icon w-9 h-9 flex items-center justify-center rounded-full transition-all duration-300"
                            style="background-color: rgba(255,255,255,0.1);">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z" />
                            </svg>
                        </a>
                        <a href="https://www.youtube.com/@FairplayforAllFFA/featured"
                            class="footer-social-icon w-9 h-9 flex items-center justify-center rounded-full transition-all duration-300"
                            style="background-color: rgba(255,255,255,0.1);">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.376.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.376-.505a3.016 3.016 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t pt-6 text-center" style="border-color: rgba(255,255,255,0.1);">
                <p class="text-xs text-white/40" style="font-family: var(--font-body1);">&copy; {{ date('Y') }}
                    FAIRALL. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Hamburger menu toggle
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        let isMenuOpen = false;

        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', () => {
                isMenuOpen = !isMenuOpen;

                if (isMenuOpen) {
                    mobileMenu.classList.remove('hidden');
                    mobileMenu.style.maxHeight = mobileMenu.scrollHeight + 'px';
                    const spans = hamburgerBtn.querySelectorAll('span');
                    spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
                    spans[1].style.opacity = '0';
                    spans[2].style.transform = 'rotate(-45deg) translate(7px, -7px)';
                } else {
                    mobileMenu.style.maxHeight = '0';
                    setTimeout(() => {
                        mobileMenu.classList.add('hidden');
                    }, 300);
                    const spans = hamburgerBtn.querySelectorAll('span');
                    spans[0].style.transform = 'none';
                    spans[1].style.opacity = '1';
                    spans[2].style.transform = 'none';
                }
            });
        }

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.mobile-nav-link, .mobile-login-btn').forEach(link => {
            link.addEventListener('click', () => {
                isMenuOpen = false;
                mobileMenu.style.maxHeight = '0';
                setTimeout(() => {
                    mobileMenu.classList.add('hidden');
                }, 300);
                if (hamburgerBtn) {
                    const spans = hamburgerBtn.querySelectorAll('span');
                    spans[0].style.transform = 'none';
                    spans[1].style.opacity = '1';
                    spans[2].style.transform = 'none';
                }
            });
        });

        document.addEventListener('submit', (event) => {
            const form = event.target;

            if (!(form instanceof HTMLFormElement) || form.dataset.disableSubmit === 'false') {
                return;
            }

            const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            submitButtons.forEach((button) => {
                button.disabled = true;
                button.setAttribute('aria-disabled', 'true');
            });
        }, true);
    </script>
</body>

</html>
