@extends('layouts.main')

@use('Illuminate\Support\Str')

@section('content')

    {{-- 1. HERO SECTION (WITH SEARCH BAR) --}}
    <section class="hero-section d-flex justify-content-center align-items-center" id="section_1">
        <div class="container">
            <div class="row">

                <div class="col-lg-8 col-12 mx-auto">
                    <h1 class="text-white text-center">Search Results</h1>
                    <h6 class="text-center">You searched for: "{{ $keyword }}"</h6>

                    <form method="GET" action="{{ route('search.index') }}" class="custom-form mt-4 pt-2 mb-lg-0 mb-5" role="search">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bi-search" id="basic-addon1"></span>
                            <input name="keyword" type="search" class="form-control" id="keyword" placeholder="Design, Code, Marketing, Finance ..." aria-label="Search" value="{{ $keyword ?? '' }}">
                            <button type="submit" class="form-control">Search</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>
    
    <section class="section-padding">
        <div class="container">

            {{-- === 2. ARTISTS SECTION === --}}
            @if($artists->count() > 0)
                <h2 class="mb-4">Artists</h2>
                <div class="row">
                    @foreach($artists as $artist)
                        <div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
                            <div class="custom-block bg-white shadow-lg h-100 d-flex flex-column">
                                <a href="{{ route('pelukis.show', $artist->slug) }}">
                                    <img src="{{ $artist->artistProfile?->profile_picture ? Storage::url($artist->artistProfile->profile_picture) : asset('images/topics/undraw_happy_music_g6wc.png') }}" class="custom-block-image img-fluid" alt="{{ $artist->name }}">
                                </a>
                                <div class="p-3 d-flex flex-column flex-grow-1">
                                    <h5 class="mb-2">{{ $artist->name }}</h5>
                                    
                                    {{-- FIX: Added strip_tags() to remove <p> from TinyMCE --}}
                                    <p class="text-muted">
                                        {{ Str::limit(strip_tags($artist->artistProfile?->about ?? 'No bio available.'), 100) }}
                                    </p>

                                    <a href="{{ route('pelukis.show', $artist->slug) }}" class="btn custom-btn mt-auto" style="width: fit-content;">View Profile</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <hr class="my-5">
            @endif
            {{-- === END ARTISTS SECTION === --}}
            
            {{-- Artworks Results --}}
            @if($artworks->count() > 0)
                <h2 class="mb-4">Artworks</h2>
                <div class="row">
                    @foreach($artworks as $artwork)
                        <div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
                            <div class="custom-block bg-white shadow-lg h-100 d-flex flex-column">
                                <a href="{{ route('artworks.show', $artwork->slug) }}">
                                    <img src="{{ Storage::url($artwork->image_path) }}" class="custom-block-image img-fluid" alt="{{ $artwork->title }}">
                                </a>
                                <div class="p-3 d-flex flex-column flex-grow-1">
                                    <h5 class="mb-2">{{ $artwork->title }} <span class="badge bg-info ms-2">{{ $artwork->category }}</span></h5>
                                    <p class="text-muted">by {{ $artwork->user->name }}</p>
                                    
                                    {{-- FIX: Added strip_tags() --}}
                                    <p class="text-muted">
                                        {{ Str::limit(strip_tags($artwork->description), 100) }}
                                    </p>

                                    <a href="{{ route('artworks.show', $artwork->slug) }}" class="btn custom-btn mt-auto" style="width: fit-content;">View Artwork</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <hr class="my-5">
            @endif
            
            {{-- Events Results --}}
            @if($events->count() > 0)
                <h2 class="mb-4">Events</h2>
                <div class="row">
                    @foreach($events as $event)
                        <div class="col-lg-4 col-md-6 col-12 mb-4 d-flex">
                            <div class="custom-block bg-white shadow-lg h-100 d-flex flex-column event-card">
                                <div class="position-relative">
                                    <a href="{{ route('event.details', $event) }}">
                                        <img src="{{ Storage::url($event->image_path) }}" class="custom-block-image img-fluid" alt="{{ $event->title }}">
                                    </a>
                                    <span class="badge bg-secondary rounded-pill event-card-badge">
                                        {{ $event->start_at->format('M d') }}
                                    </span>
                                </div>
                                <div class="p-3 d-flex flex-column flex-grow-1">
                                    <h5 class="mb-2">{{ $event->title }}</h5>
                                    
                                    {{-- FIX: Added strip_tags() --}}
                                    <p class="text-muted">
                                        {{ Str::limit(strip_tags($event->description), 100) }}
                                    </p>

                                    <a href="{{ route('event.details', $event) }}" class="btn custom-btn mt-auto" style="width: fit-content;">Learn More</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <hr class="my-5">
            @endif

            {{-- No Results --}}
            @if($artists->count() == 0 && $artworks->count() == 0 && $events->count() == 0)
                <div class="col-12 text-center">
                    <p class="text-muted fs-4">No results found for "{{ $keyword }}".</p>
                </div>
            @endif

        </div>
    </section>
@endsection