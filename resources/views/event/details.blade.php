@extends('layouts.main')

{{-- Add Swiper CSS just for this page --}}
@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    .swiper-slide img {
        transition: transform 0.3s ease;
    }
    .swiper-slide img:hover {
        transform: scale(1.05);
    }
</style>
@endsection

@section('content')

    {{-- 1. HERO SECTION (Event Image) --}}
    <section class="hero-section" style="background-image: url('{{ Storage::url($event->image_path) }}'); background-size: cover; background-position: center; min-height: 450px;">
        <div class="row mt-3 ms-3 mb-4">
            <div class="col-12">
                <a href="{{ route('event') }}" class="btn custom-btn">
                    <i class="bi-arrow-left me-2"></i> Back to Events
                </a>
            </div>
        </div>
        
        <div class="container">
            <div class="row align-items-center" style="min-height: 450px;">
                <div class="col-12">
                    {{-- Spacer --}}
                </div>
            </div>
        </div>
    </section>

    {{-- 2. EVENT DETAILS --}}
    <section class="section-padding">
        <div class="container">
            <div class="row">

                {{-- Main Content --}}
                <div class="col-lg-8 col-12">

                    {{-- Floated image for text-wrap effect --}}
                    <img src="{{ Storage::url($event->image_path) }}" 
                         class="img-fluid shadow-lg float-md-end ms-md-4 mb-3" 
                         alt="{{ $event->title }}" 
                         style="border-radius: var(--border-radius-large); max-width: 300px; width: 40%;">

                    {{-- Event Title --}}
                    <h1 class="mb-3">{{ $event->title }}</h1>
                    <h3 class="mt-5">About this event</h3>
                    <hr class="my-4">

                    {{-- Description (Rich Text Safe) --}}
                    <div class="event-description">
                        {!! $event->description !!}
                    </div>
                </div>

                {{-- Sidebar (Event Details) --}}
                <div class="col-lg-4 col-12 mt-5 mt-lg-0">
                    <div class="custom-block bg-white shadow-lg p-4">
                        <h4 class="mb-3">Event Details</h4>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <strong>Starts:</strong>
                                <p class="text-muted">{{ $event->start_at->format('F d, Y - h:i A') }}</p>
                            </li>
                            <li class="mb-2">
                                <strong>Ends:</strong>
                                <p class="text-muted">{{ $event->end_at->format('F d, Y - h:i A') }}</p>
                            </li>
                            <li class="mb-2">
                                <strong>Posted:</strong>
                                <p class="text-muted">{{ $event->created_at->diffForHumans() }}</p>
                            </li>
                        </ul>

                        {{-- EDIT BUTTON (Admins ONLY) --}}
                        @auth
                            @if(auth()->user()->is_admin)
                                <hr class="my-3">
                                <p class="text-muted">Admin: Manage this event.</p>
                                <a href="{{ route('admin.events.edit', $event->id) }}" class="custom-btn w-100 mb-2">Edit Event</a>
                                
                                {{-- Admin Download Button --}}
                                @if($event->images->count() > 0)
                                    <a href="{{ route('admin.events.download', $event->id) }}" class="btn btn-outline-success w-100">
                                        <i class="bi-download me-1"></i> Download Gallery
                                    </a>
                                @endif
                            @endif
                        @endauth
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- 3. GALLERY CAROUSEL (Infinite Scroll) --}}
    @if($event->images->count() > 0)
    <section class="section-padding bg-light">
        <div class="container-fluid">
            <h3 class="text-center mb-5">Event Gallery</h3>
            
            <div class="swiper eventSwiper pb-5">
                <div class="swiper-wrapper">
                    @foreach($event->images as $img)
                        <div class="swiper-slide text-center">
                            {{-- Click to open full size (Simple lightbox effect) --}}
                            <a href="{{ Storage::url($img->image_path) }}" target="_blank">
                                <img src="{{ Storage::url($img->image_path) }}" 
                                     class="rounded shadow" 
                                     style="height: 300px; width: 100%; object-fit: cover;" 
                                     alt="Gallery Image">
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    {{-- Swiper Logic --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".eventSwiper", {
            slidesPerView: 1,
            spaceBetween: 10,
            loop: true, // INFINITE LOOP
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                },
                1024: {
                    slidesPerView: 4, // Show 4 images at once on desktop
                    spaceBetween: 30,
                },
            },
        });
    </script>
    @endif

@endsection