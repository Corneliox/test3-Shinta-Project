{{-- This file now just tells Laravel to use the main layout --}}
{{-- and injects the homepage content into the @yield('content') slot --}}

@use('Illuminate\Support\Str') {{-- <-- ADD THIS AT THE TOP OF THE FILE --}}

@extends('layouts.main')

@section('content')

    {{-- Paste your ORIGINAL homepage content (the part --}}
    {{-- you deleted from the layout) back in here. --}}
    {{-- For example: --}}

    <section class="hero-section d-flex justify-content-center align-items-center" id="section_1">
                <div class="container">
                    <div class="row">

                        <div class="col-lg-8 col-12 mx-auto">
                            <h1 class="text-white text-center">Woman Painter Community Semarang</h1>

                            <h6 class="text-center">Platform for creatives for Woman in Semarang</h6>

                            <form method="get" class="custom-form mt-4 pt-2 mb-lg-0 mb-5" role="search">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bi-search" id="basic-addon1">
                                        
                                    </span>

                                    <input name="keyword" type="search" class="form-control" id="keyword" placeholder="Design, Code, Marketing, Finance ..." aria-label="Search">

                                    <button type="submit" class="form-control">Search</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </section>


            <section class="featured-section">
                <div class="container">
                    <div class="row justify-content-center">

                        <div class="col-lg-4 col-12 mb-4 mb-lg-0">
                            <div class="custom-block bg-white shadow-lg">
                                <a href="/topics-detail">
                                    <div class="d-flex">
                                        <div>
                                            <h5 class="mb-2">Web Design</h5>

                                            <p class="mb-0">When you search for free CSS templates, you will notice that TemplateMo is one of the best websites.</p>
                                        </div>

                                        <span class="badge bg-design rounded-pill ms-auto">14</span>
                                    </div>

                                    <img src="images/topics/undraw_Remote_design_team_re_urdx.png" class="custom-block-image img-fluid" alt="">
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-6 col-12">
                            <div class="custom-block custom-block-overlay">
                                <div class="d-flex flex-column h-100">
                                    <img src="images/businesswoman-using-tablet-analysis.jpg" class="custom-block-image img-fluid" alt="">

                                    <div class="custom-block-overlay-text d-flex">
                                        <div>
                                            <h5 class="text-white mb-2">Finance</h5>

                                            <p class="text-white">Topic Listing Template includes homepage, listing page, detail page, and contact page. You can feel free to edit and adapt for your CMS requirements.</p>

                                            <a href="/topics-detail" class="btn custom-btn mt-2 mt-lg-3">Learn More</a>
                                        </div>

                                        <span class="badge bg-finance rounded-pill ms-auto">25</span>
                                    </div>

                                    <div class="social-share d-flex">
                                        <p class="text-white me-4">Share:</p>

                                        <ul class="social-icon">
                                            <li class="social-icon-item">
                                                <a href="#" class="social-icon-link bi-twitter"></a>
                                            </li>

                                            <li class="social-icon-item">
                                                <a href="#" class="social-icon-link bi-facebook"></a>
                                            </li>

                                            <li class="social-icon-item">
                                                <a href="#" class="social-icon-link bi-pinterest"></a>
                                            </li>
                                        </ul>

                                        <a href="#" class="custom-icon bi-bookmark ms-auto"></a>
                                    </div>

                                    <div class="section-overlay"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>


            <section class="section-padding" id="section_2">
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h2 class="mb-5">Profile Pelukis</h2>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            {{-- We use the same scroll wrapper as the "Creative" section --}}
                            <div class="horizontal-scroll-wrapper">
                                <div class="d-flex flex-nowrap">
                                    
                                    {{-- This is the NEW dynamic loop --}}
                                    @foreach ($artists as $artist)
                                    <div class="artist-scroll-item">
                                        {{-- This now links to the artist's page --}}
                                        <a href="{{ route('pelukis.show', $artist) }}">



                                            
                                            @if($artist->artistProfile && $artist->artistProfile->profile_picture)
                                                <img src="{{ Storage::url($artist->artistProfile->profile_picture) }}" class="artist-profile-frame" alt="{{ $artist->name }}">
                                            @else
                                                {{-- Placeholder image if they haven't uploaded one --}}
                                                <img src="{{ asset('images/topics/undraw_happy_music_g6wc.png') }}" class="artist-profile-frame" alt="Default artist image">
                                            @endif

                                            <h5 class="mt-3 mb-0">{{ $artist->name }}</h5>
                                            {{-- You can add a subtitle in the artist_profiles table if you want --}}
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
            {{-- SECTION 3: EVENTS (NOW DYNAMIC)   --}}
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
                                {{-- This uses your template's overlay block style --}}
                                <div class="custom-block custom-block-overlay">
                                    <div class="d-flex flex-column h-100">
                                        <img src="{{ Storage::url($pinned_event->image_path) }}" class="custom-block-image img-fluid" alt="">

                                        <div class="custom-block-overlay-text d-flex">
                                            <div>
                                                <h5 class="text-white mb-2">{{ $pinned_event->title }}</h5>
                                                <p class="text-white">{{ Str::limit($pinned_event->description, 100) }}</p>
                                                <a href="{{ route('event.details', $pinned_event) }}" class="btn custom-btn mt-2 mt-lg-3">Learn More</a>
                                            </div>
                                            <span class="badge bg-finance rounded-pill ms-auto">{{ $pinned_event->start_at->format('M d') }}</span>
                                        </div>
                                        <div class="section-overlay"></div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-lg-12 col-12">
                            <h3 class="mb-4 mt-5">Newest Events</h3>
                        </div>

                        {{-- 2. THE 3 NEWEST EVENTS --}}
                        @forelse($newest_events as $event)
                            <div class="col-lg-4 col-md-6 col-12">
                                {{-- This uses the standard topics-listing block --}}
                                <div class="custom-block custom-block-topics-listing bg-white shadow-lg mb-4">
                                    <div class="d-flex">
                                        <img src="{{ Storage::url($event->image_path) }}" class="custom-block-image img-fluid" alt="">
                                        <div class="custom-block-topics-listing-info d-flex">
                                            <div>
                                                <h5 class="mb-2">{{ $event->title }}</h5>
                                                <p class="mb-0">{{ Str::limit($event->description, 50) }}</p>
                                                <a href="{{ route('event.details', $event) }}" class="btn custom-btn mt-3 mt-lg-4">Learn More</a>
                                            </div>
                                            <span class="badge bg-secondary rounded-pill ms-auto">{{ $event->start_at->format('M d') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted">No new events posted yet. Check back soon!</p>
                            </div>
                        @endforelse

                        {{-- 3. THE "SEE MORE" BUTTON --}}
                        <div class="col-12 text-center mt-4">
                            <a href="{{ route('event') }}" class="custom-btn">See More Events</a>
                        </div>

                    </div>
                </div>
            </section>

<!-- 
            <section class="faq-section section-padding" id="section_4">
                <div class="container">
                    <div class="row">

                        <div class="col-lg-6 col-12">
                            <h2 class="mb-4">Frequently Asked Questions</h2>
                        </div>

                        <div class="clearfix"></div>

                        <div class="col-lg-5 col-12">
                            <img src="images/faq_graphic.jpg" class="img-fluid" alt="FAQs">
                        </div>

                        <div class="col-lg-6 col-12 m-auto">
                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        What is Topic Listing?
                                        </button>
                                    </h2>

                                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            Topic Listing is free Bootstrap 5 CSS template. <strong>You are not allowed to redistribute this template</strong> on any other template collection website without our permission. Please contact TemplateMo for more detail. Thank you.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        How to find a topic?
                                    </button>
                                    </h2>

                                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            You can search on Google with <strong>keywords</strong> such as templatemo portfolio, templatemo one-page layouts, photography, digital marketing, etc.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        Does it need to paid?
                                    </button>
                                    </h2>

                                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section> -->

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
                                            {{-- NEW: The <a> tag now wraps the ENTIRE block --}}
                                            <a href="{{ route('artworks.show', $artwork) }}" class="custom-block-link">
                                                <div class="custom-block bg-white shadow-lg">
                                                    
                                                    {{-- This is the image+overlay wrapper --}}
                                                    <div class="custom-block-image-wrap">
                                                        <img src="{{ Storage::url($artwork->image_path) }}" class="custom-block-image img-fluid" alt="{{ $artwork->title }}">
                                                        
                                                        <div class="artwork-overlay">
                                                            <p class="artwork-overlay-text">
                                                                {{ Str::limit($artwork->description, 100) ?? 'No description available.' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    {{-- This is the text box below the image --}}
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
                                            {{-- NEW: The <a> tag now wraps the ENTIRE block --}}
                                            <a href="{{ route('artworks.show', $artwork) }}" class="custom-block-link">
                                                <div class="custom-block bg-white shadow-lg">
                                                    
                                                    {{-- This is the image+overlay wrapper --}}
                                                    <div class="custom-block-image-wrap">
                                                        <img src="{{ Storage::url($artwork->image_path) }}" class="custom-block-image img-fluid" alt="{{ $artwork->title }}">
                                                        
                                                        <div class="artwork-overlay">
                                                            <p class="artwork-overlay-text">
                                                                {{ Str::limit($artwork->description, 100) ?? 'No description available.' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    {{-- This is the text box below the image --}}
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

                            <p>Bay St &amp;, Larkin St, San Francisco, CA 94109, United States</p>

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

    {{-- ... etc. ... --}}

@endsection

        