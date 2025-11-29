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
                            <!-- {{-- NEW MARKETPLACE LINK --}}
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('marketplace.index') }}">Marketplace</a>
                            </li> -->
                            <li class="nav-item">
                                <a class="nav-link click-scroll" href="{{ route('home') }}/#section_5">Contact</a>
                            </li>

                            {{-- =============================================== --}}
                            {{-- MOBILE ONLY LINKS                               --}}
                            {{-- =============================================== --}}
                            @auth
                                <li class="nav-item d-lg-none"><hr class="my-2"></li>

                                {{-- Artist Dashboard (Mobile) --}}
                                @if(Auth::user()->is_artist)
                                    <li class="nav-item d-lg-none">
                                        <a class="nav-link" href="{{ route('artist.dashboard') }}">
                                            Artist Dashboard
                                            @php $mobNotif = Auth::user()->artworks()->where('reserved_stock', '>', 0)->count(); @endphp
                                            @if($mobNotif > 0) <span class="badge bg-danger ms-2">{{ $mobNotif }}</span> @endif
                                        </a>
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

                        {{-- =============================================== --}}
                        {{-- DESKTOP RIGHT SECTION                           --}}
                        {{-- =============================================== --}}
                        <div class="d-none d-lg-flex align-items-center ms-auto">
                            
                            {{-- 1. MARKETPLACE BUTTON (Blue Theme with Cart) --}}
                            <a href="{{ route('marketplace.index') }}" class="btn custom-btn btn-sm me-3 d-flex align-items-center">
                                <i class="bi-cart-fill me-2"></i> Marketplace
                            </a>

                            @auth
                                {{-- 2. ARTIST NOTIFICATION BELL --}}
                                @if(auth()->user()->is_artist)
                                    @php
                                        $notificationCount = auth()->user()->artworks()->where('reserved_stock', '>', 0)->count();
                                    @endphp
                                    <a href="{{ route('artist.dashboard') }}" class="btn position-relative me-3 text-dark p-0" title="Artist Dashboard" style="border:none; background:transparent;">
                                        <i class="bi-bell fs-4 text-white"></i> {{-- White icon to match navbar text --}}
                                        @if($notificationCount > 0)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                {{ $notificationCount }}
                                            </span>
                                        @endif
                                    </a>
                                @endif

                                {{-- 3. USER DROPDOWN --}}
                                <div class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="navbar-icon dropdown-item bi-person smoothscroll me-2"></i>
                                        <span style="color:#ffffff; font-family:Montserrat, sans-serif; font-size:15px;">
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
                                {{-- GUEST LOGIN ICON --}}
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

                    {{-- 1. LEFT COLUMN: BRAND + HIBAH TEXT --}}
                    <div class="col-lg-3 col-12 mb-4 pb-2">
                        <a class="navbar-brand mb-2" href="/">
                            <img src="{{ asset('images/wopanco2.png') }}" style="max-width:35px">
                            <span>WOPANCO</span>
                        </a>
                        
                        {{-- HIBAH TEXT --}}
                        <p class="mt-4 mb-0" style="font-size: 0.8rem; opacity: 0.85; line-height: 1.5;">
                            {{ __('messages.footer_grant') }}
                        </p>
                    </div>

                    {{-- 2. SPONSORS / LABELS (UPDATED LAYOUT) --}}
                    <div class="col-lg-3 col-md-4 col-6">
                        <h6 class="site-footer-title mb-3">Supported By</h6>
                        
                        {{-- Row 2: Diktisaintek & BIMA (Side by Side / Split) --}}
                        <div class="d-flex gap-2">
                            {{-- Diktisaintek --}}
                            <div class="bg-white rounded p-2 d-flex align-items-center justify-content-center" style="width: fit-content;">
                                <img src="{{ asset('Sponsors/logo diktisaintek.png') }}" 
                                class="img-fluid" 
                                alt="Diktisaintek" 
                                style="max-height: 35px; width: auto;">
                            </div>
                            
                            {{-- BIMA Trans --}}
                            <div class="bg-white rounded p-2 d-flex align-items-center justify-content-center" style="width: fit-content;">
                                <img src="{{ asset('Sponsors/logo BIMA trans.png') }}" 
                                class="img-fluid" 
                                alt="BIMA Trans" 
                                style="max-height: 35px; width: auto;">
                            </div>
                        </div>

                        {{-- Row 1: SCU (Full Block) --}}
                        <div class="bg-white rounded p-2 mb-2 d-inline-block" style="width: fit-content;">
                            <img src="{{ asset('Sponsors/logo SCU.png') }}" 
                                 class="img-fluid" 
                                 alt="SCU" 
                                 style="max-height: 40px; width: auto;">
                        </div>
                    </div>
                    
                    {{-- 3. INFO LINKS --}}
                    <div class="col-lg-3 col-md-4 col-6 mb-4 mb-lg-0">
                        <h6 class="site-footer-title mb-3">Information</h6>
                        <p class="text-white d-flex mb-1"><a href="tel: 305-240-9671" class="site-footer-link">305-240-9671</a></p>
                        <p class="text-white d-flex"><a href="mailto:info@company.com" class="site-footer-link">info@company.com</a></p>
                    </div>

                    {{-- 4. COPYRIGHT --}}
                    <div class="col-lg-3 col-md-4 col-12 mt-4 mt-lg-0 ms-auto">
                        
                        {{-- LANGUAGE SWITCHER --}}
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

                        <p class="copyright-text">Copyright © 2025 Woman Painter Community.<br><br>Design: <a rel="nofollow" href="https://templatemo.com" target="_blank">TemplateMo</a> & <a rel="nofollow" href="https://github.com/Corneliox" target="_blank">Corneliox</a></p>
                    </div>

                </div>
            </div>
        </footer>

        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('js/jquery.sticky.js') }}"></script>
        <script src="{{ asset('js/custom.js') }}"></script>
        @stack('scripts')

    </body>
</html>