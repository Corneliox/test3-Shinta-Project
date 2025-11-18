<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>WOPANCO - Woman Painter Community</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Open+Sans&display=swap" rel="stylesheet">
                
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/bootstrap-icons.css') }}" rel="stylesheet">
        <link href="{{ asset('css/templatemo-topic-listing.css') }}" rel="stylesheet">      
    </head>
    
    <body id="top">

        <main>

            <nav class="navbar navbar-expand-lg">
                <div class="container">
                    <a class="navbar-brand" href="/">
                        <img src="{{ asset('images/wopanco2.png') }}" style="max-width:35px">
                        <span>WOPANCO</span>
                    </a>

                    {{-- =============================================== --}}
                    {{-- 1. DELETE THE BROKEN MOBILE DROPDOWN          --}}
                    {{-- We are removing the d-lg-none dropdown block  --}}
                    {{-- that was here.                                --}}
                    {{-- =============================================== --}}


                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-lg-5 me-lg-auto">
                            <li class="nav-item">
                                <a class="nav-link click-scroll" style="text-align:center" href="/#section_1">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link click-scroll" style="text-align:center" href="/#section_2">Profil Pelukis</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link click-scroll" style="text-align:center" href="/#section_3">Event</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link click-scroll" style="text-align:center" href="/#section_4">Creative</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link click-scroll" style="text-align:center" href="/#section_5">Contact</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarLightDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">Pages</a>
                                <ul class="dropdown-menu dropdown-menu-light" aria-labelledby="navbarLightDropdownMenuLink">
                                    <li><a class="dropdown-item" href="{{ route('event') }}">Topics Listing</a></li>
                                    <li><a class="dropdown-item" href="{{ route('contact') }}">Contact Form</a></li>
                                </ul>
                            </li>

                            {{-- =============================================== --}}
                            {{-- 2. ADD STATIC AUTH LINKS FOR MOBILE           --}}
                            {{-- These will appear inside the hamburger menu   --}}
                            {{-- and be hidden on desktop (d-lg-none)        --}}
                            {{-- =============================================== --}}
                            @auth
                                {{-- Mobile-only Divider --}}
                                <li class="nav-item d-lg-none"><hr class="my-2"></li>

                                {{-- Mobile Profile Link --}}
                                <li class="nav-item d-lg-none">
                                    @if (Auth::user()->is_admin)
                                        <a class="nav-link" href="{{ route('profile.edit') }}">Profile</a>
                                    @else
                                        <a class="nav-link" href="{{ route('profile.user.show') }}">Profile</a>
                                    @endif
                                </li>

                                {{-- Mobile Dashboard Link --}}
                                @if (Auth::user()->is_admin)
                                    <li class="nav-item d-lg-none">
                                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                                    </li>
                                @endif

                                {{-- Mobile Logout Link --}}
                                <li class="nav-item d-lg-none">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a class="nav-link" href="{{ route('logout') }}"
                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </a>
                                    </form>
                                </li>
                            @else
                                {{-- Mobile-only Divider --}}
                                <li class="nav-item d-lg-none"><hr class="my-2"></li>

                                {{-- Mobile Login Link --}}
                                <li class="nav-item d-lg-none">
                                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                                </li>
                            @endauth
                            {{-- =============================================== --}}
                            {{-- END: MOBILE-ONLY AUTH LINKS                   --}}
                            {{-- =============================================== --}}
                        </ul>

                        {{-- =============================================== --}}
                        {{-- START: DYNAMIC DESKTOP DROPDOWN (FOR PC)      --}}
                        {{-- This part is for the desktop icon and is correct.--}}
                        {{-- =============================================== --}}
                        @auth
                            <div class="nav-item dropdown d-none d-lg-block ms-auto">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="navbar-icon dropdown-item bi-person smoothscroll me-2"></i>
                                    <span style="color:#ffffff; font-family:Montserrat, sans-serif; font-size:15px; contrast:1.78;">
                                        Hai, {{ explode(' ', Auth::user()->name)[0] }}
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-light dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                                    @if (Auth::user()->is_admin)
                                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                                    @else
                                        <li><a class="dropdown-item" href="{{ route('profile.user.show') }}">Profile</a></li>
                                    @endif
                                    
                                    @if (Auth::user()->is_admin)
                                        <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <a class="dropdown-item" href="{{ route('logout') }}"
                                                    onclick="event.preventDefault();
                                                                this.closest('form').submit();">
                                                {{ __('Log Out') }}
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            {{-- GUEST ICON (DESKTOP) --}}
                            <div class="d-none d-lg-block ms-auto">
                                <a href="{{ route('login') }}" class="navbar-icon bi-person smoothscroll"></a>
                            </div>
                        @endauth
                        {{-- =============================================== --}}
                        {{-- END: DYNAMIC DESKTOP DROPDOWN                 --}}
                        {{-- =============================================== --}}

                    </div>
                </div>
            </nav>   
        
            @yield('content')
            
        </main>

        <footer class="site-footer section-padding">
            <div class="container">
                <div class="row">

                    <div class="col-lg-3 col-12 mb-4 pb-2">
                        <a class="navbar-brand mb-2" href="/">
                            <img src="{{ asset('images/wopanco2.png') }}" style="max-width:35px">
                            <span>WOPANCO</span>
                        </a>
                    </div>

                    <div class="col-lg-3 col-md-4 col-6">
                        <h6 class="site-footer-title mb-3">Resources</h6>
                        <ul class="site-footer-links">
                            <li class="site-footer-link-item">
                                <a href="#" class="site-footer-link">Home</a>
                            </li>
                            <li class="site-footer-link-item">
                                <a href="#" class="site-footer-link">Event</a>
                            </li>
                            <li class="site-footer-link-item">
                                <a href="#" class="site-footer-link">Modules</a>
                            </li>
                            <li class="site-footer-link-item">
                                <a href="#" class="site-footer-link">Contact</a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-lg-3 col-md-4 col-6 mb-4 mb-lg-0">
                        <h6 class="site-footer-title mb-3">Information</h6>
                        <p class="text-white d-flex mb-1">
                            <a href="tel: 305-240-9671" class="site-footer-link">
                                305-240-9671
                            </a>
                        </p>
                        <p class="text-white d-flex">
                            <a href="mailto:info@company.com" class="site-footer-link">
                                info@company.com
                            </a>
                        </p>
                    </div>

                    <div class="col-lg-3 col-md-4 col-12 mt-4 mt-lg-0 ms-auto">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Indonesia</button>
                            <ul class="dropdown-menu">
                                <li><button class="dropdown-item" type="button">Indonesia</button></li>
                            </ul>
                        </div>
                        <p class="copyright-text mt-lg-5 mt-4">Copyright © 2025 Woman Painter Community. All rights reserved.
                        <br><br>Design: <a rel="nofollow" href="https://templatemo.com" target="_blank">TemplateMo</a> & <a rel="nofollow" href="https://unika.ac.id" target="_blank">Unika Soegijapranata Semarang</a></p>
                    </div>

                </div>
            </div>
        </footer>

        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('js/jquery.sticky.js') }}"></script>
        <!-- <script src="{{ asset('js/click-scroll.js') }}"></script> -->
        <script src="{{ asset('js/custom.js') }}"></script>

        {{-- ADD THIS LINE --}}
        @stack('scripts')

    </body>
</html>