@use('Illuminate\Support\Str')

@extends('layouts.main')

@section('content')

    {{-- =================================== --}}
    {{-- 1. HERO SEARCH SECTION              --}}
    {{-- =================================== --}}
    <section class="hero-section d-flex justify-content-center align-items-center" id="section_1">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-12 mx-auto">
                    <h1 class="text-white text-center">Woman Painter Community Semarang</h1>
                    <h6 class="text-center">Platform for creatives for Woman in Semarang</h6>
                    <form method="get" class="custom-form mt-4 pt-2 mb-lg-0 mb-5" role="search">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bi-search" id="basic-addon1"></span>
                            <input name="keyword" type="search" class="form-control" id="keyword" placeholder="Design, Code, Marketing, Finance ..." aria-label="Search">
                            <button type="submit" class="form-control">Search</button>
                        </div>
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
                        <div class="d-flex flex-nowrap">
                            
                            @foreach ($artists as $artist)
                            <div class="artist-scroll-item">
                                <a href="{{ route('pelukis.show', $artist) }}">
                                    @if($artist->artistProfile && $artist->artistProfile->profile_picture)
                                        <img src="{{ Storage::url($artist->artistProfile->profile_picture) }}" class="artist-profile-frame" alt="{{ $artist->name }}">
                                    @else
                                        <img src="{{ asset('images/topics/undraw_happy_music_g6wc.png') }}" class="artist-profile-frame" alt="Default artist image">
                                    @endif
                                    <h5 class="mt-3 mb-0">{{ $artist->name }}</h5>
                                    <p class="text-muted"><small>Artist</small></p>
                                </a>
                            </div>
                            @endforeach

                        </div>
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
                                                    <p class="text-white">{{ Str::limit($pinned_event->description, 300) }}</p>
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
                                
                                <div class="position-relative">
                                    <a href="{{ route('event.details', $event) }}">
                                        {{-- This image will be 200px high and fully rounded --}}
                                        <img src="{{ Storage::url($event->image_path) }}" class="custom-block-image img-fluid" alt="{{ $event->title }}">
                                    </a>
                                    <span class="badge bg-secondary event-card-badge">
                                        {{ $event->start_at->format('M d') }}
                                    </span>
                                </div>

                                <div class="p-3 d-flex flex-column flex-grow-1">
                                    <h5 class="mb-2">{{ $event->title }}</h5>
                                    <p class="text-muted">{{ Str::limit($event->description, 100) }}</p>
                                    
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
                                                        {{ Str::limit($artwork->description, 100) ?? 'No description available.' }}
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
                    
                    <div class="horizontal-scroll-wrapper justify-content-end">
                        
                        <div class="d-flex flex-nowrap" style="padding:20px">

                            @foreach ($craft_artworks as $artwork)
                                <div class="scroll-item">
                                    <a href="{{ route('artworks.show', $artwork) }}" class="custom-block-link">
                                        <div class="custom-block bg-white shadow-lg">
                                            
                                            <div class="custom-block-image-wrap">
                                                <img src="{{ Storage::url($artwork->image_path) }}" class="custom-block-image img-fluid" alt="{{ $artwork->title }}">
                                                
                                                <div class="artwork-overlay">
                                                    <p class="artwork-overlay-text">
                                                        {{ Str::limit($artwork->description, 100) ?? 'No description available.' }}
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
    {{-- 5. CONTACT SECTION                --}}
    {{-- =================================== --}}
    <section class="contact-section section-padding section-bg" id="section_5">
        <div class="container">
            <div class="row">

                <div class="col-lg-12 col-12 text-center">
                    <h2 class="mb-5">Get in touch</h2>
                </div>

                <div class="col-lg-5 col-12 mb-4 mb-lg-0">
                    <iframe class="google-map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2595.065641062665!2d-122.4230416990949!3d37.80335401520422!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80858127459fabad%3A0x808ba520e5e9edb7!2sFrancisco%20Park!5e1!3m2!1sen!2sth!4v1684340239744!5m2!1sen!2sth" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>

                <div class="col-lg-3 col-md-6 col-12 mb-3 mb-lg- mb-md-0 ms-auto">
                    <h4 class="mb-3">Head office</h4>

                    <p>Bay St &, Larkin St, San Francisco, CA 94109, United States</p>

                    <hr>

                    <p class="d-flex align-items-center mb-1">
                        <span class="me-2">Phone</span>

                        <a href="tel: 305-240-9671" class="site-footer-link">
                            305-240-9671
                        </a>
                    </p>

                    <p class="d-flex align-items-center">
                        <span class="me-2">Email</span>

                        <a href="mailto:info@company.com" class="site-footer-link">
                            info@company.com
                        </a>
                    </p>
                </div>

                <div class="col-lg-3 col-md-6 col-12 mx-auto">
                    <h4 class="mb-3">Dubai office</h4>

                    <p>Burj Park, Downtown Dubai, United Arab Emirates</p>

                    <hr>

                    <p class="d-flex align-items-center mb-1">
                        <span class="me-2">Phone</span>

                        <a href="tel: 110-220-3400" class="site-footer-link">
                            110-220-3400
                        </a>
                    </p>

                    <p class="d-flex align-items-center">
                        <span class="me-2">Email</span>

                        <a href="mailto:info@company.com" class="site-footer-link">
                            info@company.com
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