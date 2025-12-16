<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>WOPANCO - Woman Painter Community</title>
        
        {{-- FAVICON --}}
        <link rel="icon" sizes="96x96" type="image/png" href="{{ asset('images/wopanco2.png') }}">
        
        {{-- STYLESHEETS --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Open+Sans&display=swap" rel="stylesheet">
                
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/bootstrap-icons.css') }}" rel="stylesheet">
        <link href="{{ asset('css/templatemo-topic-listing.css') }}" rel="stylesheet">      

        {{-- === CUSTOM NAVBAR STYLES === --}}
        <style>
            /* 1. TRANSITION SETUP */
            .navbar {
                background-color: transparent; 
                transition: background-color 0.3s ease, box-shadow 0.3s ease;
            }
            .navbar .nav-link,
            .navbar .navbar-brand span,
            .navbar .bi-cart3,
            .navbar .navbar-toggler-icon,
            .navbar .user-name-text {
                color: var(--white-color); 
                transition: color 0.3s ease;
            }
            .navbar .bi-person {
                color: #000000; 
                transition: color 0.3s ease;
            }
            
            /* Toggler Default (White) */
            .navbar-toggler {
                border-color: rgba(255,255,255,0.8);
            }
            .navbar-toggler-icon {
                filter: brightness(0) invert(1);
            }

            /* ========================================= */
            /* PC/DESKTOP ALIGNMENT FIX (min-width: 992px) */
            /* ========================================= */
            @media (min-width: 992px) {
                /* This centers the List Items vertically in the bar */
                .navbar-nav {
                    align-items: center; 
                }

                /* This centers the Text inside the Link */
                .navbar .nav-link {
                    display: flex;
                    align-items: center;      /* Vertical Center */
                    justify-content: center;  /* Horizontal Center */
                    text-align: center;
                }
            }

            /* 2. ACTIVE STATES (Scrolled Down OR Mobile Menu Open) */
            .sticky-wrapper.is-sticky .navbar,
            .navbar.mobile-menu-open {
                background-color: var(--white-color) !important;
                opacity: 0.95;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }

            /* Change TEXT to Secondary Color */
            .sticky-wrapper.is-sticky .navbar .nav-link,
            .sticky-wrapper.is-sticky .navbar .navbar-brand span,
            .sticky-wrapper.is-sticky .navbar .bi-cart3,
            .sticky-wrapper.is-sticky .navbar .user-name-text,
            .navbar.mobile-menu-open .nav-link,
            .navbar.mobile-menu-open .navbar-brand span,
            .navbar.mobile-menu-open .bi-cart3,
            .navbar.mobile-menu-open .user-name-text {
                color: var(--secondary-color) !important;
            }

            /* Change ICONS to Secondary Color */
            .sticky-wrapper.is-sticky .navbar .bi-person,
            .navbar.mobile-menu-open .bi-person {
                color: var(--secondary-color) !important;
            }

            /* Change TOGGLER to Secondary Color */
            .sticky-wrapper.is-sticky .navbar-toggler,
            .navbar.mobile-menu-open .navbar-toggler {
                border-color: var(--secondary-color) !important;
            }
            .sticky-wrapper.is-sticky .navbar-toggler-icon,
            .navbar.mobile-menu-open .navbar-toggler-icon {
                filter: none; 
                filter: brightness(0) saturate(100%) invert(36%) sepia(88%) saturate(680%) hue-rotate(200deg) brightness(92%) contrast(92%);
            }

            /* Active Link / Hover State */
            .sticky-wrapper.is-sticky .navbar .nav-link.active,
            .sticky-wrapper.is-sticky .navbar .nav-link:hover,
            .navbar.mobile-menu-open .nav-link.active,
            .navbar.mobile-menu-open .nav-link:hover {
                color: var(--primary-color) !important;
            }

            /* ---------- FLOATING BOTTOM NAV ---------- */
            .bottom-nav {
                position: fixed;
                left: 50%;
                transform: translateX(-50%);
                bottom: 20px;
                z-index: 9999;
                display: flex;
                gap: 5px;
                background: rgba(30, 30, 30, 0.85);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                box-shadow: 0 8px 30px rgba(0,0,0,0.3);
                padding: 10px 15px;
                border-radius: 50px;
                min-width: 320px;
                justify-content: space-around;
                /* SMOOTH TRANSITION FOR HIDING */
                transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
                opacity: 1;
            }

            /* Class to hide it */
            .bottom-nav.nav-hidden {
                transform: translateX(-50%) translateY(150%);
                opacity: 0;
            }

            /* Buttons */
            .bn-btn {
                display: flex; 
                flex-direction: column; 
                align-items: center; 
                justify-content: center;
                color: rgba(255,255,255,0.6); /* Dimmed white */
                background: transparent; 
                border: none; 
                padding: 5px 10px; 
                font-size: 0.75rem;
                transition: all 0.2s ease;
            }
            
            .bn-btn .bi { 
                font-size: 1.3rem; 
                margin-bottom: 2px;
            }
            
            /* Active State (Highlighted) */
            .bn-btn.active { 
                color: var(--white-color); 
                background: var(--secondary-color); /* Teal Background */
                box-shadow: 0 4px 15px rgba(75, 114, 109, 0.4);
                border-radius: 15px; 
            }

            /* Hide on PC */
            @media (min-width: 992px) {
                .bottom-nav { display: none; }
            }
        </style>
    </head>
    
    <body id="top">

        <main>

            <nav class="navbar navbar-expand-lg">
                <div class="container">
                    
                    {{-- 1. BRAND LOGO --}}
                    <a class="navbar-brand" href="/">
                        <img src="{{ asset('images/wopanco2.png') }}" style="max-width:35px">
                        <span>WOPANCO</span>
                    </a>

                    {{-- 2. MOBILE CART ICON --}}
                    <a href="{{ route('marketplace.index') }}" class="d-lg-none ms-auto me-3 text-dark position-relative">
                        <i class="bi-cart3 fs-3"></i> 
                    </a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-lg-5 me-lg-auto">
                            <li class="nav-item">
                                <a class="nav-link click-scroll" href="{{ route('home') }}/#section_1">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link click-scroll" href="{{ route('home') }}/#section_2">Profil Pelukis</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link click-scroll" href="{{ route('home') }}/#section_3">Event</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link click-scroll" href="{{ route('creative') }}">Creative</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link click-scroll" href="{{ route('home') }}/#section_5">Contact</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('about') }}">About us</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}" href="{{ route('news.index') }}">News</a>
                            </li>

                            {{-- MOBILE AUTH LINKS --}}
                            @auth
                                <li class="nav-item d-lg-none"><hr class="my-2"></li>
                                @if(Auth::user()->is_artist)
                                    <li class="nav-item d-lg-none">
                                        <a class="nav-link" href="{{ route('artist.dashboard') }}">Artist Dashboard</a>
                                    </li>
                                @endif
                                <li class="nav-item d-lg-none">
                                    <a class="nav-link" href="{{ route('profile.user.show') }}">My Profile</a>
                                </li>
                                @if (Auth::user()->is_admin)
                                    <li class="nav-item d-lg-none">
                                        <a class="nav-link" href="{{ route('dashboard') }}">Admin Dashboard</a>
                                    </li>
                                @endif
                                <li class="nav-item d-lg-none">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </a>
                                    </form>
                                </li>
                            @else
                                <li class="nav-item d-lg-none"><hr class="my-2"></li>
                                <li class="nav-item d-lg-none">
                                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                                </li>
                            @endauth
                        </ul>

                        {{-- DESKTOP RIGHT SECTION --}}
                        <div class="d-none d-lg-flex align-items-center ms-auto">
                            
                            {{-- Marketplace Button --}}
                            <a href="{{ route('marketplace.index') }}" class="btn custom-btn btn-sm me-3 d-flex align-items-center">
                                <i class="bi-cart-fill me-2"></i> Marketplace
                            </a>

                            @auth
                                {{-- Notification Bell --}}
                                @if(auth()->user()->is_artist)
                                    @php
                                        $notificationCount = auth()->user()->artworks()->where('reserved_stock', '>', 0)->count();
                                    @endphp
                                    <a href="{{ route('artist.dashboard') }}" class="btn position-relative me-3 p-0" title="Artist Dashboard" style="border:none; background:transparent;">
                                        <i class="bi-bell fs-4"></i>
                                        @if($notificationCount > 0)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                {{ $notificationCount }}
                                            </span>
                                        @endif
                                    </a>
                                @endif

                                {{-- User Dropdown --}}
                                <div class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="navbar-icon dropdown-item bi-person smoothscroll me-2"></i>
                                        <span class="user-name-text" style="font-family:Montserrat, sans-serif; font-size:15px;">
                                            Hai, {{ explode(' ', Auth::user()->name)[0] }}
                                        </span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-light dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                                        <li><a class="dropdown-item" href="{{ route('profile.user.show') }}">My Profile</a></li>
                                        @if (Auth::user()->is_artist)
                                            <li><a class="dropdown-item" href="{{ route('artist.dashboard') }}">Artist Dashboard</a></li>
                                        @endif
                                        @if (Auth::user()->is_admin)
                                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">Admin Dashboard</a></li>
                                        @endif
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                                    {{ __('Log Out') }}
                                                </a>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                {{-- Guest Login Icon --}}
                                <a href="{{ route('login') }}" class="navbar-icon bi-person smoothscroll ms-3"></a>
                            @endauth
                        </div>

                    </div>
                </div>
            </nav>    
        
            @yield('content')
            
        </main>

        <footer class="site-footer section-padding">
            <div class="container">
                <div class="row">

                    {{-- 1. BRAND --}}
                    <div class="col-lg-3 col-12 mb-4 pb-2">
                        <a class="navbar-brand mb-2" href="/">
                            <img src="{{ asset('images/wopanco2.png') }}" style="max-width:35px">
                            <span>WOPANCO</span>
                        </a>
                        <p class="mt-4 mb-0" style="font-size: 0.8rem; opacity: 0.85; line-height: 1.5;">
                            {{ __('messages.footer_grant') }}
                        </p>
                    </div>

                    {{-- 2. SPONSORS --}}
                    <div class="col-lg-3 col-md-4 col-6">
                        <h6 class="site-footer-title mb-3">Supported By</h6>
                        <div class="d-flex gap-2">
                            <div class="bg-white rounded p-2 d-flex align-items-center justify-content-center" style="width: fit-content;">
                                <img src="{{ asset('Sponsors/logo diktisaintek.png') }}" class="img-fluid" alt="Diktisaintek" style="max-height: 35px; width: auto;">
                            </div>
                        </div>
                        <div class="bg-white rounded p-2 mb-2 mt-2 d-inline-block" style="width: fit-content;">
                            <img src="{{ asset('Sponsors/logo SCU.png') }}" class="img-fluid" alt="SCU" style="max-height: 40px; width: auto;">
                        </div>
                    </div>
                    
                    {{-- 3. INFO LINKS --}}
                    <div class="col-lg-3 col-md-4 col-6 mb-4 mb-lg-0">
                        <h6 class="site-footer-title mb-3">Information</h6>
                        <p class="text-white d-flex mb-2 align-items-start">
                            <i class="bi-whatsapp me-2" style="color: #9ca3af;"></i>
                            <a href="https://wa.me/6289668411463" class="site-footer-link">
                                {{ __('messages.contact_admin') }}
                            </a>
                        </p>
                        <p class="text-white d-flex mb-2 align-items-start">
                            <i class="bi-envelope me-2" style="color: #9ca3af;"></i>
                            <a href="mailto:{{ __('messages.contact_email') }}" class="site-footer-link text-break">
                                {{ __('messages.contact_email') }}
                            </a>
                        </p>
                        <p class="text-white d-flex mb-2 align-items-start">
                            <i class="bi-instagram me-2" style="color: #9ca3af;"></i>
                            <a href="https://instagram.com/wopanco.indonesia" target="_blank" class="site-footer-link">
                                {{ __('messages.contact_ig') }}
                            </a>
                        </p>
                    </div>

                    {{-- 4. LANGUAGE & COPYRIGHT --}}
                    <div class="col-lg-3 col-md-4 col-12 mt-4 mt-lg-0 ms-auto">
                        <div class="mb-4">
                            <h6 class="text-white mb-2" style="font-size: 0.9rem;">Language / Bahasa</h6>
                            <div class="btn-group" role="group">
                                <a href="{{ route('lang.switch', 'id') }}" class="btn btn-sm {{ app()->getLocale() == 'id' ? 'btn-light' : 'btn-outline-light' }}">
                                    <img src="https://flagcdn.com/16x12/id.png" alt="ID" class="me-1"> ID
                                </a>
                                <a href="{{ route('lang.switch', 'en') }}" class="btn btn-sm {{ app()->getLocale() == 'en' ? 'btn-light' : 'btn-outline-light' }}">
                                    <img src="https://flagcdn.com/16x12/gb.png" alt="EN" class="me-1"> EN
                                </a>
                            </div>
                        </div>
                        <p class="copyright-text">Copyright © 2025 Woman Painter Community.<br><br>Design: <a style="color: var(--secondary-color) !important;" rel="nofollow" href="https://templatemo.com" target="_blank">TemplateMo</a> & <a style="color: var(--secondary-color) !important;" rel="nofollow" href="https://github.com/Corneliox" target="_blank">Corneliox</a></p>
                    </div>

                </div>
            </div>
        </footer>

        {{-- FLOATING BOTTOM NAV (Placed BEFORE Scripts) --}}
        <nav id="wopanco-bottom-nav" class="bottom-nav d-lg-none">
            {{-- HOME --}}
            <a href="{{ route('home') }}" class="bn-btn text-decoration-none {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="bi-house-door-fill"></i>
                <small>Home</small>
            </a>
            {{-- ABOUT --}}
            <a href="{{ route('about') }}" class="bn-btn text-decoration-none {{ request()->routeIs('about') ? 'active' : '' }}">
                <i class="bi-list-ul"></i>
                <small>About</small>
            </a>
            {{-- MARKETPLACE --}}
            <a href="{{ route('marketplace.index') }}" class="bn-btn text-decoration-none {{ request()->routeIs('marketplace.index') ? 'active' : '' }}">
                <i class="bi-shop"></i>
                <small>Shop</small>
            </a>
            {{-- PROFILE / LOGIN --}}
            @auth
                <a href="{{ route('profile.user.show') }}" class="bn-btn text-decoration-none {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="bi-person-circle"></i>
                    <small>Profile</small>
                </a>
            @else
                <a href="{{ route('login') }}" class="bn-btn text-decoration-none {{ request()->routeIs('login') ? 'active' : '' }}">
                    <i class="bi-box-arrow-in-right"></i>
                    <small>Login</small>
                </a>
            @endauth
        </nav>

        {{-- SCRIPTS --}}
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('js/jquery.sticky.js') }}"></script>
        <script src="{{ asset('js/custom.js') }}"></script>
        <script src="https://cdn.tiny.cloud/1/w0mxt01iygm8l26kqy3w3okjhxfjp66y9mpfory164br98jq/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        @stack('scripts')

        {{-- SCRIPT FOR MOBILE NAVBAR & SCROLL --}}
        <script>
            // 1. Mobile Menu Logic
            const navbar = document.querySelector('.navbar');
            const navbarCollapse = document.querySelector('#navbarNav');

            if (navbar && navbarCollapse) {
                navbarCollapse.addEventListener('show.bs.collapse', function () {
                    navbar.classList.add('mobile-menu-open');
                });
                navbarCollapse.addEventListener('hide.bs.collapse', function () {
                    navbar.classList.remove('mobile-menu-open');
                });
            }

            // 2. BOTTOM NAV SCROLL LOGIC
            document.addEventListener('DOMContentLoaded', function() {
                const bottomNav = document.getElementById('wopanco-bottom-nav');
                let lastScrollTop = 0;
                
                if (bottomNav) {
                    window.addEventListener('scroll', function() {
                        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                        
                        // Prevent negative scrolling (iOS)
                        if (scrollTop < 0) return;

                        // Check Scroll Direction
                        if (scrollTop > lastScrollTop && scrollTop > 100) {
                            // Scrolling DOWN -> Hide
                            bottomNav.classList.add('nav-hidden');
                        } else {
                            // Scrolling UP -> Show
                            bottomNav.classList.remove('nav-hidden');
                        }
                        
                        lastScrollTop = scrollTop;
                    });
                }
            });
        </script>

        <script>
            tinymce.init({
                selector: '.rich-editor', // We will add this class to your textareas
                plugins: 'lists link image preview',
                toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link',
                menubar: false,
                statusbar: false
            });
        </script>

    </body>
</html>