@extends('layouts.main')

@use('Illuminate\Support\Str') {{-- <-- ADD THIS AT THE TOP OF THE FILE --}}

@section('content')

    {{-- 1. "ABOUT" SECTION (Hero) --}}
    <section class="hero-section" style="min-height: 400px;">
        <div class="container">
            <div class="row align-items-center" style="min-height: 400px;">
                <div class="col-lg-4 col-md-5 col-12 text-center">
                    {{-- The Artist's Profile Picture --}}
                    @if($profile && $profile->profile_picture)
                        <img src="{{ Storage::url($profile->profile_picture) }}" class="artist-profile-frame" style="width: 200px; height: 200px;" alt="{{ $artist->name }}">
                    @else
                        <img src="{{ asset('images/topics/undraw_happy_music_g6wc.png') }}" class="artist-profile-frame" style="width: 200px; height: 200px;" alt="Default Artist Image">
                    @endif
                </div>
                
                <div class="col-lg-8 col-md-7 col-12">
                    <h1 class="text-white">{{ $artist->name }}</h1>
                    <h4 class="text-white">About Me</h4>
                    <p class="text-white">
                        {{-- The "About" description from the database --}}
                        @if($profile && $profile->about)
                            {!! Str::limit(strip_tags($profile->about), 300) !!}
                        @else
                            This artist hasn't written their "about" section yet.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. "LUKISAN" AND "CRAFT" SECTIONS --}}
    <section class="section-padding">
        <div class="container">

            {{-- LUKISAN --}}
            <div class="row mb-5">
                <div class="col-12">
                    <h2 class="mb-4">Lukisan</h2>
                </div>
                <div class="col-12">
                    <div class="horizontal-scroll-wrapper">
                        <div class="d-flex flex-nowrap" style="padding:20px">
                            @forelse($lukisan as $artwork)
                                <div class="scroll-item">
                                    {{-- NEW: The <a> tag now wraps the ENTIRE block --}}
                                    <a href="{{ route('artworks.show', $artwork) }}" class="custom-block-link">
                                        <div class="custom-block bg-white shadow-lg">
                                            
                                            {{-- This is the image+overlay wrapper --}}
                                            <div class="custom-block-image-wrap">
                                                <img src="{{ Storage::url($artwork->image_path) }}" class="custom-block-image img-fluid" alt="{{ $artwork->title }}">
                                                
                                                <div class="artwork-overlay">
                                                    <p class="artwork-overlay-text">
                                                        {{ Str::limit(strip_tags($artwork->description), 100) ?? 'No description available.' }}
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            {{-- This is the text box below the image --}}
                                            <div class="p-3">
                                                <p class="mb-1 fw-bold">{{ $artwork->title }}</p>
                                                <small class="text-muted d-block">{{ $artwork->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @empty
                                <p class="text-muted">This artist has not uploaded any paintings yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- CRAFT --}}
            <div class="row">
                <div class="col-12">
                    <h2 class="mb-4">Craft</h2>
                </div>
                <div class="col-12">
                    <div class="horizontal-scroll-wrapper">
                        <div class="d-flex flex-nowrap" style="padding:20px">
                            @forelse($crafts as $artwork)
                                <div class="scroll-item">
                                    {{-- NEW: The <a> tag now wraps the ENTIRE block --}}
                                    <a href="{{ route('artworks.show', $artwork) }}" class="custom-block-link">
                                        <div class="custom-block bg-white shadow-lg">
                                            
                                            {{-- This is the image+overlay wrapper --}}
                                            <div class="custom-block-image-wrap">
                                                <img src="{{ Storage::url($artwork->image_path) }}" class="custom-block-image img-fluid" alt="{{ $artwork->title }}">
                                                
                                                <div class="artwork-overlay">
                                                    <p class="artwork-overlay-text">
                                                        {{ Str::limit(strip_tags($artwork->description), 100) ?? 'No description available.' }}
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            {{-- This is the text box below the image --}}
                                            <div class="p-3">
                                                <p class="mb-1 fw-bold">{{ $artwork->title }}</p>
                                                <small class="text-muted d-block">{{ $artwork->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @empty
                                <p class="text-muted">This artist has not uploaded any crafts yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection