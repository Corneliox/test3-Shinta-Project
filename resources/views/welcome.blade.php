@use('Illuminate\Support\Str')

@extends('layouts.main')

@section('content')

    {{-- =================================== --}}
    {{-- 1. HERO SECTION (REDESIGNED)        --}}
    {{-- =================================== --}}

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <section class="hero-section d-flex flex-column justify-content-center" id="section_1"
        style="min-height: 80vh; padding-top: 100px; padding-bottom: 50px;">
        <div class="container-fluid px-lg-5">

            {{-- ROW 1: TITLE --}}
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h1 class="text-white hero-title">Woman Painter Community</h1>
                </div>
            </div>

            {{-- ROW 2: CONTENT --}}
            <div class="row align-items-center justify-content-center h-100">

                {{-- LEFT: ROULETTE CAROUSEL --}}
                {{-- DESKTOP → LEFT  |  MOBILE → MIDDLE (below title) --}}
                <div class="col-lg-8 col-12 order-2 order-lg-1 mb-4 mb-lg-0 position-relative">

                    <div class="swiper heroSwiper">
                        <div class="swiper-wrapper">
                            @if(isset($hero_images) && count($hero_images) > 0)
                                @foreach($hero_images as $img)
                                    <div class="swiper-slide">
                                        {{-- CHANGE: Use Storage::url() for database images --}}
                                        <img src="{{ Storage::url($img->image_path) }}" alt="Wopanco Art" />
                                    </div>
                                @endforeach
                            @else
                                {{-- Fallback --}}
                                <div class="swiper-slide"><img src="{{ asset('images/topics/undraw_Remote_design_team_re_urdx.png') }}" /></div>
                                <div class="swiper-slide"><img src="{{ asset('images/topics/undraw_Redesign_feedback_re_jvm0.png') }}" /></div>
                            @endif
                        </div>

                        {{-- Navigation Buttons --}}

                        <div class="swiper-button-next text-white"></div>
                        <div class="swiper-button-prev text-white"></div>
                    </div>

                </div>

                {{-- RIGHT: CONTENT FORM --}}
                {{-- MOBILE → BOTTOM (after slider) --}}
                <div class="col-lg-3 col-12 order-3 order-lg-2 offset-lg-1 d-flex flex-column justify-content-center text-center text-lg-start">

                    <h4 class="mb-4"
                        style="color: var(--border-color); font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                        Painting, Sharing, <br>
                        Empowering
                    </h4>

                    <form method="GET" action="{{ route('search.index') }}" class="custom-form mb-4" role="search">
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bi-search bg-white border-0 text-muted"></span>
                            <input name="keyword" type="search" class="form-control border-0" id="keyword"
                                placeholder="Search art, artist..." aria-label="Search">
                        </div>
                        <button type="submit" class="btn custom-btn w-100 mt-3 shadow-sm">Search</button>
                    </form>

                    </div>

            </div>
        </div>
    </section>



    
    {{-- =================================== --}}
    {{-- 3. PROFIL PELUKIS SECTION         --}} 
    {{-- =================================== --}}
    <section class="section-padding" id="section_2">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h2 class="mb-5">Profile Pelukis</h2>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="horizontal-scroll-wrapper">
                        <div class="d-flex flex-nowrap" style="padding: 50px 20px; padding-right: 350px;"> 
                            
                            @foreach ($artists as $artist)
                            <div class="artist-scroll-item">
                                <a href="{{ route('pelukis.show', $artist) }}" class="text-decoration-none">
                                    
                                    {{-- THE WRAPPER --}}
                                    <div class="artist-wrapper">
                                        
                                        {{-- 1. THE IMAGE (TOP LAYER) --}}
                                        @if($artist->artistProfile && $artist->artistProfile->profile_picture)
                                            <img src="{{ Storage::url($artist->artistProfile->profile_picture) }}" class="artist-profile-frame" alt="{{ $artist->name }}">
                                        @else
                                            <img src="{{ asset('images/topics/undraw_happy_music_g6wc.png') }}" class="artist-profile-frame" alt="Default">
                                        @endif

                                        {{-- 2. THE DEFAULT NAME (VISIBLE BEFORE HOVER) --}}
                                        <h5 class="artist-default-name">{{ $artist->name }}</h5>

                                        {{-- 3. THE HIDDEN INFO CARD (EXPANDS ON HOVER) --}}
                                        <div class="artist-info-card">
                                            <div class="artist-info-content">
                                                <h6 class="text-white mb-1">{{ $artist->name }}</h6>
                                                <p class="text-white-50 mb-0 small" style="font-size: 0.85rem; line-height: 1.3;">
                                                    {{-- FIX: Strip tags for artist bio --}}
                                                    {!! Str::limit(strip_tags($artist->artistProfile->about ?? 'Member of WOPANCO'), 60) !!}
                                                </p>
                                            </div>
                                        </div>

                                    </div>

                                </a>
                            </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
    </section>

    {{-- =================================== --}}
    {{-- 2. EVENTS SECTION                   --}}
    {{-- =================================== --}}
    <section class="section-padding section-bg" id="section_3">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-12 text-center">
                    <h2 class="mb-5">Upcoming Events</h2>
                </div>

                    {{-- 1. THE PINNED/BIGGEST EVENT --}}
                        @if($pinned_event)
                            <div class="col-lg-10 col-12 mx-auto">
                                <div class="pinned custom-block custom-block-overlay">
                                    <div class="d-flex flex-column h-100">
                                        <img src="{{ Storage::url($pinned_event->image_path) }}" class="custom-block-image img-fluid" alt="">

                                        {{-- === THIS IS THE UPDATED FLEXBOX LAYOUT === --}}
                                        {{-- 1. flex-column stacks the top and bottom content --}}
                                        <div class="custom-block-overlay-text d-flex flex-column">
                                            
                                            {{-- 2. This is the TOP row (title and date) --}}
                                            <div class="d-flex">
                                                <div>
                                                    <h3 class="text-white mb-4">{{ $pinned_event->title }}</h5>
                                                    {{-- FIX: Strip tags for pinned event description --}}
                                                    <p class="text-white">{!! Str::limit(strip_tags($pinned_event->description), 150) !!}</p>
                                                </div>
                                                {{-- The badge will now be sized correctly by the new CSS --}}
                                                <span class="badge bg-finance ms-auto">{{ $pinned_event->start_at->format('M d') }}</span>
                                            </div>

                                            {{-- 3. 'mt-auto' pushes this button to the bottom --}}
                                            {{--    'ms-auto' pushes it to the right --}}
                                            <a href="{{ route('event.details', $pinned_event) }}" class="btn custom-btn mt-auto ms-auto">Learn More</a>

                                        </div>
                                        {{-- === END OF UPDATED PART === --}}

                                        <div class="section-overlay"></div>
                                    </div>
                                </div>
                            </div>
                        @endif

                <div class="col-lg-12 col-12">
                    <h3 class="mb-4 mt-5">Newest Events</h3>
                </div>

                {{-- === 2. THE 3 NEWEST EVENTS (FIXED LAYOUT) === --}}
                <div class="row">
                    @forelse($newest_events as $event)
                        <div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
                            
                            {{-- This card has the 'event-card' class now --}}
                            <div class="custom-block bg-white shadow-lg h-100 d-flex flex-column event-card">
                                
                                <span class="badge bg-secondary event-card-badge" style="position: absolute; top: 25px; right: 25px;">
                                    {{ $event->start_at->format('M d') }}
                                </span>

                                <div class="position-relative">
                                    <a href="{{ route('event.details', $event) }}">
                                        {{-- This image will be 200px high and fully rounded --}}
                                        <img src="{{ Storage::url($event->image_path) }}" class="custom-block-image img-fluid" alt="{{ $event->title }}">
                                    </a>
                                </div>

                                <div class="p-3 d-flex flex-column flex-grow-1">
                                    <h5 class="mb-2">{{ $event->title }}</h5>
                                    {{-- FIX: Strip tags for newest event descriptions --}}
                                    <p class="text-muted">{!! Str::limit(strip_tags($event->description), 100) !!}</p>
                                    
                                    <a href="{{ route('event.details', $event) }}" class="btn custom-btn mt-auto" style="width: fit-content;">Learn More</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted">No new events posted yet. Check back soon!</p>
                        </div>
                    @endforelse
                </div>
                {{-- === END OF FIXED LAYOUT === --}}

                {{-- 3. THE "SEE MORE" BUTTON --}}
                <div class="col-12 text-center mt-4">
                    <a href="{{ route('event') }}" class="custom-btn">See More Events</a>
                </div>
            </div>
        </div>
    </section>

    {{-- =================================== --}}
    {{-- 4. CREATIVE SECTION               --}}
    {{-- =================================== --}}
    <section class="section-padding" id="section_4">
        <div class="container">

            {{-- MAIN TITLE --}}
            <div class="row">
                <div class="col-12 text-center">
                    <h2 class="mb-5">Creative</h2>
                </div>
            </div>

            {{-- ROW 1: LUKISAN --}}
            <div class="row mb-4 align-items-center">
                
                {{-- Title on the left --}}
                <div class="col-lg-2 col-md-3 col-12">
                    <h3 class="mb-3 mb-md-0">Lukisan</h3>
                </div>
                
                {{-- Scrolling items --}}
                <div class="col-lg-10 col-md-9 col-12">
                    <div class="horizontal-scroll-wrapper">
                        <div class="d-flex flex-nowrap" style="padding:20px">
                            
                            @foreach ($lukisan_artworks as $artwork)
                                <div class="scroll-item">
                                    <a href="{{ route('artworks.show', $artwork) }}" class="custom-block-link">
                                        <div class="custom-block bg-white shadow-lg">
                                            
                                            <div class="custom-block-image-wrap">
                                                <img src="{{ Storage::url($artwork->image_path) }}" class="custom-block-image img-fluid" alt="{{ $artwork->title }}">
                                                
                                                <div class="artwork-overlay">
                                                    <p class="artwork-overlay-text">
                                                        {{-- FIX: Strip tags for Lukisan --}}
                                                        {!! Str::limit(strip_tags($artwork->description), 100) ?? 'No description available.' !!}
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="p-3">
                                                <p class="mb-1 fw-bold">{{ $artwork->title }}</p>
                                                <small class="text-muted d-block">by {{ $artwork->user->name }}</small>
                                                <small class="text-muted d-block">{{ $artwork->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>

            {{-- ROW 2: CRAFT --}}
            <div class="row mb-4 align-items-center">
                
                {{-- Scrolling items (comes first in HTML) --}}
                <div class="col-lg-10 col-md-9 col-12 order-md-1">
                    
                    <div class="horizontal-scroll-wrapper reverse-row">
                        
                        <div class="d-flex flex-nowrap" style="padding:20px">

                            @foreach ($craft_artworks as $artwork)
                                <div class="scroll-item">
                                    <a href="{{ route('artworks.show', $artwork) }}" class="custom-block-link">
                                        <div class="custom-block bg-white shadow-lg">
                                            
                                            <div class="custom-block-image-wrap">
                                                <img src="{{ Storage::url($artwork->image_path) }}" class="custom-block-image img-fluid" alt="{{ $artwork->title }}">
                                                
                                                <div class="artwork-overlay">
                                                    <p class="artwork-overlay-text">
                                                        {{-- FIX: Strip tags for Craft --}}
                                                        {!! Str::limit(strip_tags($artwork->description), 100) ?? 'No description available.' !!}
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="p-3">
                                                <p class="mb-1 fw-bold">{{ $artwork->title }}</p>
                                                <small class="text-muted d-block">by {{ $artwork->user->name }}</small>
                                                <small class="text-muted d-block">{{ $artwork->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>

                {{-- Title on the right --}}
                <div class="col-lg-2 col-md-3 col-12 order-md-2 text-md-end">
                    <h3 class="mb-3 mb-md-0">Craft</h3>
                </div>
            </div>

            {{-- "SEE MORE" BUTTON --}}
            <div class="row">
                <div class="col-12 text-center mt-4">
                    <a href="{{ route('creative') }}" class="custom-btn">See More</a>
                </div>
            </div>

        </div>
    </section>

    {{-- =================================== --}}
    {{-- 5. CONTACT SECTION                  --}}
    {{-- =================================== --}}
    <section class="contact-section section-padding section-bg" id="section_5">
        <div class="container">
            <div class="row">

                <div class="col-lg-12 col-12 text-center">
                    <h2 class="mb-5">Get in touch</h2>
                </div>

                {{-- Left Side: Google Map --}}
                <div class="col-lg-5 col-12 mb-4 mb-lg-0">
                <iframe class="google-map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.203023267856!2d110.39271837587637!3d-6.985324669288132!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e708b4929760129%3A0x6753066922207904!2sPuri%20Anjasmoro!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>    
                </div>

                {{-- Right Side: Contact Info (Standardized) --}}
                <div class="col-lg-6 col-12 mb-3 mb-lg-0 ms-auto">
                    <h4 class="mb-3">{{ __('messages.address_title') }}</h4>

                    <p>{{ __('messages.address_line_1') }}<br>{{ __('messages.address_line_2') }}</p>

                    <hr>

                    {{-- Admin Phone --}}
                    <p class="d-flex align-items-center mb-1">
                        <span class="me-2">{{ __('messages.contact_admin_label') }}</span>
                        <a href="https://wa.me/6289668411463" target="_blank" class="site-footer-link">
                            {{ __('messages.contact_admin') }}
                        </a>
                    </p>

                    {{-- Email --}}
                    <p class="d-flex align-items-center mb-1">
                        <span class="me-2">Email</span>
                        <a href="mailto:{{ __('messages.contact_email') }}" class="site-footer-link">
                            {{ __('messages.contact_email') }}
                        </a>
                    </p>

                    {{-- Instagram --}}
                    <p class="d-flex align-items-center">
                        <span class="me-2">Instagram</span>
                        <a href="https://instagram.com/wopanco.indonesia" target="_blank" class="site-footer-link">
                            {{ __('messages.contact_ig') }}
                        </a>
                    </p>
                </div>
                        
                {{-- Button to Contact Page --}}
                <div class="col-12 text-center mt-5">
                    <a href="{{ route('contact') }}" class="btn custom-btn custom-border-btn justify-content-center ms-3">Fill our form</a>
                </div>

            </div>
        </div>
    </section>
@endsection

{{-- ADD THIS SCRIPT --}}
@push('scripts')
    <script src="{{ asset('js/click-scroll.js') }}"></script>

    {{-- SWIPER CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    {{-- CUSTOM CSS --}}
    <style>

        .btn.custom-border-btn {
            border: 2px solid var(--border-color) !important;
            color: var(--border-color) !important;
            font-weight: 700 !important;
            letter-spacing: 1px;
            padding: 14px 20px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .btn.custom-border-btn:hover {
            background: var(--border-color) !important;
            color: #000 !important;
            box-shadow: 0 0 15px rgba(255, 193, 7, 0.5);
            transform: translateY(-2px);
        }

        /* 1. Hero Background */
        .hero-section {
            background-image: linear-gradient(15deg, #81131C 0%, #4B726D 100%);
            position: relative;
            overflow: hidden;
        }

        /* 2. Title Typography */
        .hero-title {
            font-size: 2.5rem;
            font-weight: 800;
            white-space: normal;
        }
        @media(min-width: 992px) {
            .hero-title {
                font-size: 3.5rem;
                white-space: nowrap;
            }
        }

        /* Swiper Container */
        .heroSwiper {
            width: 100%;
            padding-top: 30px;
            padding-bottom: 30px;
            overflow: hidden; 
        }

        /* Swiper Wrapper - Custom Easing for the "Swipe Feel" */
        .swiper-wrapper {
            /* This bezier curve starts slightly fast and slows down smoothly, mimicking momentum */
            transition-timing-function: cubic-bezier(0.25, 0.1, 0.25, 1) !important;
        }

        /* Slide Styles */
        .swiper-slide {
            background-position: center;
            background-size: cover;
            height: 400px; 
            width: 60%; 
            border-radius: 15px;
            overflow: hidden;
            
            /* Visuals for side images */
            opacity: 0.6; 
            filter: blur(4px); 
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);

            /* GPU Acceleration for smoothness */
            transform: translate3d(0,0,0);
            backface-visibility: hidden; 
            will-change: transform, opacity, filter;
            
            /* Only animate opacity/filter via CSS. Position is handled by Swiper JS */
            transition: opacity 1s ease, filter 1s ease; 
        }

        /* Mobile Adjustments */
        @media(max-width: 768px) {
            .swiper-slide {
                width: 75%; 
                height: 300px; 
            }
        }

        .swiper-slide img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover; 
        }

        /* Active Slide (Center) */
        .swiper-slide-active {
            opacity: 1 !important; 
            filter: blur(0) !important; 
            z-index: 10;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6); /* Deeper shadow when active */
            border: 2px solid var(--border-color);
        }
        
        /* Navigation Buttons */
        .swiper-button-next, .swiper-button-prev {
            color: var(--white-color);
            background: rgba(0,0,0,0.3);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            backdrop-filter: blur(5px);
            transition: background 0.3s;
        }
        .swiper-button-next:hover, .swiper-button-prev:hover {
            background: var(--primary-color);
        }
        .swiper-button-next:after, .swiper-button-prev:after {
            font-size: 18px;
            font-weight: bold;
        }
    </style>

    {{-- SWIPER JS INIT --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var swiper = new Swiper(".heroSwiper", {
                speed: 1000,
                centeredSlides: true,
                slidesPerView: "auto",
                loop: true,
                spaceBetween: 0,

                // turn OFF coverflow completely
                effect: "slide",

                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                },

                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },

                on: {
                    slideChangeTransitionStart: function () {
                        // force Swiper to recalibrate classes instantly
                        this.slides.removeClass('swiper-slide-prev swiper-slide-next');
                        var active = this.activeIndex;

                        var prev = this.slides.eq(active - 1);
                        var next = this.slides.eq(active + 1);

                        // loop wrap fixes
                        if (!prev.length) prev = this.slides.eq(this.slides.length - 1);
                        if (!next.length) next = this.slides.eq(0);

                        prev.addClass("swiper-slide-prev");
                        next.addClass("swiper-slide-next");
                    }
                }
            });
        });
    </script>

@endpush