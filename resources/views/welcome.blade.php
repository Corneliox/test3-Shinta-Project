@use('Illuminate\Support\Str')

@extends('layouts.main')

@section('styles')
    {{-- CRITICAL CSS FOR CLS --}}
    {{-- By pre-defining the height/ratio in CSS, we reserve space BEFORE images load --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        /* 1. Reserve Space for Hero Slider to prevent CLS */
        .heroSwiper {
            width: 100%;
            height: 400px; /* Force height so it doesn't collapse */
            background-color: #f0f0f0; /* Grey placeholder while loading */
            border-radius: 15px;
            overflow: hidden;
        }
        
        /* 2. Mobile Height Adjustment */
        @media(max-width: 768px) {
            .heroSwiper {
                height: 300px; 
            }
        }

        /* 3. Image Fill Strategy */
        .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* 4. Reserve Space for Creative Cards (prevents list jumping) */
        .custom-block-image-wrap {
            aspect-ratio: 4/3; /* Standard art ratio, adjust if needed */
            background-color: #eee;
            overflow: hidden;
        }
    </style>
@endsection

@section('content')

    {{-- =================================== --}}
    {{-- 1. HERO SECTION (LCP OPTIMIZED)     --}}
    {{-- =================================== --}}

    <section class="hero-section d-flex flex-column justify-content-center" id="section_1"
        style="min-height: 80vh; padding-top: 100px; padding-bottom: 50px;">
        <div class="container-fluid px-lg-5">

            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h1 class="text-white hero-title">Woman Painter Community</h1>
                </div>
            </div>

            <div class="row align-items-center justify-content-center h-100">

                {{-- LEFT: HERO CAROUSEL --}}
                <div class="col-lg-8 col-12 order-2 order-lg-1 mb-4 mb-lg-0 position-relative">

                    <div class="swiper heroSwiper">
                        <div class="swiper-wrapper">
                            @if(isset($hero_images) && count($hero_images) > 0)
                                @foreach($hero_images as $index => $img)
                                    <div class="swiper-slide">
                                        {{-- 
                                            LCP FIX: 
                                            1. If it's the FIRST image ($index == 0), load EAGERLY and HIGH Priority.
                                            2. If it's the 2nd/3rd image, load LAZY to save bandwidth.
                                        --}}
                                        @if($index === 0)
                                            <img src="{{ Storage::url($img->image_path) }}" 
                                                 alt="Wopanco Art Highlight" 
                                                 fetchpriority="high" {{-- Tell browser this is #1 priority --}}
                                                 loading="eager"      {{-- Download immediately --}}
                                                 width="800" height="600" {{-- Hint dimensions for browser --}}
                                            />
                                        @else
                                            <img src="{{ Storage::url($img->image_path) }}" 
                                                 alt="Wopanco Art" 
                                                 loading="lazy"       {{-- Load later --}}
                                                 width="800" height="600"
                                            />
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                {{-- Fallback --}}
                                <div class="swiper-slide">
                                    <img src="{{ asset('images/topics/undraw_Remote_design_team_re_urdx.png') }}" fetchpriority="high" loading="eager" />
                                </div>
                            @endif
                        </div>

                        <div class="swiper-button-next text-white"></div>
                        <div class="swiper-button-prev text-white"></div>
                    </div>

                </div>

                {{-- RIGHT: CONTENT FORM --}}
                <div class="col-lg-3 col-12 order-3 order-lg-2 offset-lg-1 d-flex flex-column justify-content-center text-center text-lg-start">
                    <h4 class="mb-4" style="color: var(--border-color); font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                        Painting, Sharing, <br> Empowering
                    </h4>

                    <form method="GET" action="{{ route('search.index') }}" class="custom-form mb-4" role="search">
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bi-search bg-white border-0 text-muted"></span>
                            <input name="keyword" type="search" class="form-control border-0" id="keyword" placeholder="Search art, artist..." aria-label="Search">
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
                                    <div class="artist-wrapper">
                                        @if($artist->artistProfile && $artist->artistProfile->profile_picture)
                                            {{-- Aspect Ratio & Lazy Load --}}
                                            <img src="{{ Storage::url($artist->artistProfile->profile_picture) }}" 
                                                 loading="lazy" 
                                                 class="artist-profile-frame" 
                                                 alt="{{ $artist->name }}"
                                                 style="aspect-ratio: 1/1;"> {{-- Force Square --}}
                                        @else
                                            <img src="{{ asset('images/topics/undraw_happy_music_g6wc.png') }}" loading="lazy" class="artist-profile-frame" alt="Default">
                                        @endif

                                        <h5 class="artist-default-name">{{ $artist->name }}</h5>
                                        <div class="artist-info-card">
                                            <div class="artist-info-content">
                                                <h6 class="text-white mb-1">{{ $artist->name }}</h6>
                                                <p class="text-white-50 mb-0 small" style="font-size: 0.85rem; line-height: 1.3;">
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

                    @if($pinned_event)
                        <div class="col-lg-10 col-12 mx-auto">
                            <div class="pinned custom-block custom-block-overlay">
                                <div class="d-flex flex-column h-100">
                                    {{-- Use Aspect Ratio --}}
                                    <img src="{{ Storage::url($pinned_event->image_path) }}" 
                                         loading="lazy" 
                                         class="custom-block-image img-fluid" 
                                         alt=""
                                         style="aspect-ratio: 16/9; object-fit: cover;">

                                    <div class="custom-block-overlay-text d-flex flex-column">
                                        <div class="d-flex">
                                            <div>
                                                <h3 class="text-white mb-4">{{ $pinned_event->title }}</h5>
                                                <p class="text-white">{!! Str::limit(strip_tags($pinned_event->description), 150) !!}</p>
                                            </div>
                                            <span class="badge bg-finance ms-auto">{{ $pinned_event->start_at->format('M d') }}</span>
                                        </div>
                                        <a href="{{ route('event.details', $pinned_event) }}" class="btn custom-btn mt-auto ms-auto">Learn More</a>
                                    </div>
                                    <div class="section-overlay"></div>
                                </div>
                            </div>
                        </div>
                    @endif

                <div class="col-lg-12 col-12">
                    <h3 class="mb-4 mt-5">Newest Events</h3>
                </div>

                <div class="row">
                    @forelse($newest_events as $event)
                        <div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
                            <div class="custom-block bg-white shadow-lg h-100 d-flex flex-column event-card">
                                <span class="badge bg-secondary event-card-badge" style="position: absolute; top: 25px; right: 25px;">
                                    {{ $event->start_at->format('M d') }}
                                </span>
                                <div class="position-relative">
                                    <a href="{{ route('event.details', $event) }}">
                                        {{-- Fixed Height on Card Image to stop jumps --}}
                                        <img src="{{ Storage::url($event->image_path) }}" 
                                             loading="lazy" 
                                             class="custom-block-image img-fluid" 
                                             alt="{{ $event->title }}"
                                             style="height: 250px; width: 100%; object-fit: cover;">
                                    </a>
                                </div>
                                <div class="p-3 d-flex flex-column flex-grow-1">
                                    <h5 class="mb-2">{{ $event->title }}</h5>
                                    <p class="text-muted">{!! Str::limit(strip_tags($event->description), 100) !!}</p>
                                    <a href="{{ route('event.details', $event) }}" class="btn custom-btn mt-auto" style="width: fit-content;">Learn More</a>
                                </div>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>

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
            <div class="row">
                <div class="col-12 text-center">
                    <h2 class="mb-5">Creative</h2>
                </div>
            </div>

            {{-- ROW 1: LUKISAN --}}
            <div class="row mb-4 align-items-center">
                <div class="col-lg-2 col-md-3 col-12">
                    <h3 class="mb-3 mb-md-0">Lukisan</h3>
                </div>
                <div class="col-lg-10 col-md-9 col-12">
                    <div class="horizontal-scroll-wrapper">
                        <div class="d-flex flex-nowrap" style="padding:20px">
                            @foreach ($lukisan_artworks as $artwork)
                                <div class="scroll-item">
                                    <a href="{{ route('artworks.show', $artwork) }}" class="custom-block-link">
                                        <div class="custom-block bg-white shadow-lg">
                                            {{-- CLS FIX: Use custom-block-image-wrap class defined in style section --}}
                                            <div class="custom-block-image-wrap">
                                                <img src="{{ Storage::url($artwork->image_path) }}" 
                                                     loading="lazy" 
                                                     class="custom-block-image img-fluid" 
                                                     alt="{{ $artwork->title }}">
                                                <div class="artwork-overlay">
                                                    <p class="artwork-overlay-text">
                                                        {!! Str::limit(strip_tags($artwork->description), 100) ?? 'No description.' !!}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="p-3">
                                                <p class="mb-1 fw-bold">{{ $artwork->title }}</p>
                                                <small class="text-muted d-block">by {{ $artwork->user->name }}</small>
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
                <div class="col-lg-10 col-md-9 col-12 order-md-1">
                    <div class="horizontal-scroll-wrapper reverse-row">
                        <div class="d-flex flex-nowrap" style="padding:20px">
                            @foreach ($craft_artworks as $artwork)
                                <div class="scroll-item">
                                    <a href="{{ route('artworks.show', $artwork) }}" class="custom-block-link">
                                        <div class="custom-block bg-white shadow-lg">
                                            <div class="custom-block-image-wrap">
                                                <img src="{{ Storage::url($artwork->image_path) }}" 
                                                     loading="lazy" 
                                                     class="custom-block-image img-fluid" 
                                                     alt="{{ $artwork->title }}">
                                                <div class="artwork-overlay">
                                                    <p class="artwork-overlay-text">
                                                        {!! Str::limit(strip_tags($artwork->description), 100) ?? 'No description.' !!}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="p-3">
                                                <p class="mb-1 fw-bold">{{ $artwork->title }}</p>
                                                <small class="text-muted d-block">by {{ $artwork->user->name }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-12 order-md-2 text-md-end">
                    <h3 class="mb-3 mb-md-0">Craft</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-12 text-center mt-4">
                    <a href="{{ route('creative') }}" class="custom-btn">See More</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Contact Section remains the same --}}
    <section class="contact-section section-padding section-bg" id="section_5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-12 text-center">
                    <h2 class="mb-5">Get in touch</h2>
                </div>
                <div class="col-lg-5 col-12 mb-4 mb-lg-0">
                    {{-- Use loading=lazy on iframe too --}}
                    <iframe class="google-map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.203023267856!2d110.39271837587637!3d-6.985324669288132!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e708b4929760129%3A0x6753066922207904!2sPuri%20Anjasmoro!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>    
                </div>
                <div class="col-lg-6 col-12 mb-3 mb-lg-0 ms-auto">
                    <h4 class="mb-3">{{ __('messages.address_title') }}</h4>
                    <p>{{ __('messages.address_line_1') }}<br>{{ __('messages.address_line_2') }}</p>
                    <hr>
                    <p class="d-flex align-items-center mb-1">
                        <span class="me-2" style="width: 67px">{{ __('messages.contact_admin_label') }}</span>
                        <a href="https://wa.me/6289668411463" target="_blank" class="site-footer-link" style="text-transform: lowercase; font-size: 0.75rem;">
                            {{ __('messages.contact_admin') }}
                        </a>
                    </p>
                    <p class="d-flex align-items-center mb-1">
                        <span class="me-2" style="width: 67px">Email</span>
                        <a href="mailto:{{ __('messages.contact_email') }}" class="site-footer-link" style="text-transform: lowercase; font-size: 0.75rem;">
                            {{ __('messages.contact_email') }}
                        </a>
                    </p>
                    <p class="d-flex align-items-center">
                        <span class="me-2" style="width: 67px">Instagram</span>
                        <a href="https://instagram.com/wopanco.indonesia" target="_blank" class="site-footer-link" style="text-transform: lowercase; font-size: 0.75rem;">
                            {{ __('messages.contact_ig') }}
                        </a>
                    </p>
                </div>
                <div class="col-12 text-center mt-5">
                    <a href="{{ route('contact') }}" class="btn custom-btn custom-border-btn justify-content-center ms-3">Fill our form</a>
                </div>
            </div>
        </div>
    </section>
@endsection

{{-- SCRIPTS --}}
@push('scripts')
    <script src="{{ asset('js/click-scroll.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var swiper = new Swiper(".heroSwiper", {
                speed: 1000,
                centeredSlides: true,
                slidesPerView: "auto",
                loop: true,
                spaceBetween: 0,
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
                        this.slides.removeClass('swiper-slide-prev swiper-slide-next');
                        var active = this.activeIndex;
                        var prev = this.slides.eq(active - 1);
                        var next = this.slides.eq(active + 1);
                        if (!prev.length) prev = this.slides.eq(this.slides.length - 1);
                        if (!next.length) next = this.slides.eq(0);
                        prev.addClass("swiper-slide-prev");
                        next.addClass("swiper-slide-next");
                    }
                }
            });
        });
    </script>
    
    {{-- Move CSS here if pushing to stack, or keep in section styles --}}
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
        .hero-section {
            background-image: linear-gradient(15deg, #81131C 0%, #4B726D 100%);
            position: relative;
            overflow: hidden;
        }
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
        /* Style for Swiper buttons */
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
@endpush