@extends('layouts.main')

@use('Illuminate\Support\Str')

@section('content')

    {{-- 1. HERO SECTION --}}
    <section class="hero-section" style="min-height: 250px;">
        <div class="container">
            <div class="row align-items-center" style="min-height: 250px;">
                <div class="col-12 text-center">
                    <h1 class="text-white">All Events</h1>
                    
                    {{-- ADMIN BUTTON --}}
                    @auth
                        @if(auth()->user()->is_admin)
                            <div class="mt-4">
                                <a href="{{ route('admin.events.create') }}" class="custom-btn">
                                    <i class="bi-plus-circle me-2"></i>
                                    Create New Event
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </section>
    
    {{-- 2. NEW: PINNED EVENT SECTION --}}
    @if($pinned_event)
        <section class="section-padding" style="padding-bottom: 0;">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 col-12 mx-auto">
                        <h3 class="mb-4">Pinned Event</h3>
                        <div class="pinned custom-block custom-block-overlay">
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
                </div>
            </div>
        </section>
    @endif

    {{-- 3. ALL EVENTS LIST (FIXED LAYOUT) --}}
    <section class="section-padding">
        <div class="container">
            
            {{-- This title only shows if there was a pinned event --}}
            @if($pinned_event)
                <div class="row">
                    <div class="col-12">
                        <h3 class="mb-4 mt-5">All Events</h3>
                    </div>
                </div>
            @endif

            <div class="row">
                
                @forelse($events as $event)
                    {{-- This column uses d-flex to make cards equal height --}}
                    <div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
                        
                        {{-- This is the 'event-card' layout from your homepage --}}
                        <div class="custom-block bg-white shadow-lg h-100 d-flex flex-column event-card">
                            
                            <div class="position-relative">
                                <a href="{{ route('event.details', $event) }}">
                                    <img src="{{ Storage::url($event->image_path) }}" class="custom-block-image img-fluid" alt="{{ $event->title }}">
                                </a>
                                <span class="badge bg-secondary rounded-pill event-card-badge">
                                    {{ $event->start_at->format('M d') }}
                                </span>

                                {{-- Add a "Pinned" badge if it's the pinned event --}}
                                @if($event->is_pinned)
                                    <span class="badge bg-danger event-card-badge" style="top: 15px; left: 15px; right: auto;">
                                        Pinned
                                    </span>
                                @endif
                            </div>

                            <div class="p-3 d-flex flex-column flex-grow-1">
                                <h5 class="mb-2">{{ $event->title }}</h5>
                                <p class="text-muted">{{ Str::limit($event->description, 100) }}</p>
                                
                                <a href="{{ route('event.details', $event) }}" class="btn custom-btn mt-auto" style="width: fit-content;">Learn More</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted fs-4">No events have been posted yet. Please check back later!</p>
                    </div>
                @endforelse

                {{-- Pagination Links --}}
                <div class="col-12 mt-4">
                    {{ $events->links() }}
                </div>

            </div>
        </div>
    </section>
@endsection