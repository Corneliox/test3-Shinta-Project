@extends('layouts.main') 

<!-- Ubah halaman menjadi tema tampilan marketplace -->

{{-- Add this to use the 'Str::limit' helper for the description --}}
@use('Illuminate\Support\Str')

@section('content')

    {{-- 1. HERO SECTION --}}
    <section class="hero-section" style="min-height: 250px;">
        <div class="container">
            <div class="row align-items-center" style="min-height: 250px;">
                <div class="col-12">
                    <h1 class="text-center text-white">Creative Gallery</h1>
                    <p class="text-center text-white">See the latest artworks from all our artists, sorted by new.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. ARTISTS ARTWORK LOOP --}}
    <section class="section-padding">
        <div class="container">
            
            @forelse($artists as $artist)
                
                {{-- This is the "Row" for a single artist --}}
                <div class="row mb-5 pb-lg-5 align-items-center">

                    {{-- 
                    =====================================
                    EVEN ROW (Artist 2, 4, 6...)
                    =====================================
                    --}}
                    @if($loop->even)
                        
                        {{-- ARTIST INFO (RIGHT) --}}
                        <div class="col-lg-2 col-md-3 col-12 order-md-2 text-md-end mb-4 mb-md-0">
                            <a href="{{ route('pelukis.show', $artist) }}">
                                @if($artist->artistProfile && $artist->artistProfile->profile_picture)
                                    <img src="{{ Storage::url($artist->artistProfile->profile_picture) }}" class="artist-profile-frame mb-3" alt="{{ $artist->name }}">
                                @else
                                    <img src="{{ asset('images/topics/undraw_happy_music_g6wc.png') }}" class="artist-profile-frame mb-3" alt="Default artist image">
                                @endif
                                <h4 class="mb-0">{{ $artist->name }}</h4>
                            </a>
                        </div>
                        
                        {{-- ARTWORK SCROLLER (LEFT, scrolls R-to-L) --}}
                        <div class="col-lg-10 col-md-9 col-12 order-md-1">
                            <div class="horizontal-scroll-wrapper reverse-row">
                                <div class="d-flex flex-nowrap">
                                    
                                    @forelse($artist->artworks as $artwork)
                                        <div class="scroll-item">
                                            {{-- FIX: The <a> tag now wraps the ENTIRE block --}}
                                            <a href="{{ route('artworks.show', $artwork) }}" class="custom-block-link">
                                                <div class="custom-block bg-white shadow-lg">
                                                    
                                                    {{-- This is the image+overlay wrapper (no longer a link) --}}
                                                    <div class="custom-block-image-wrap">
                                                        <img src="{{ Storage::url($artwork->image_path) }}" class="custom-block-image img-fluid" alt="{{ $artwork->title }}">
                                                        
                                                        <div class="artwork-overlay">
                                                            <p class="artwork-overlay-text">
                                                                {{ Str::limit($artwork->description, 100) ?? 'No description available.' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    {{-- FIX: This text box is back, and no longer a link --}}
                                                    <div class="p-3">
                                                        <p class="mb-1 fw-bold">{{ $artwork->title }}</p>
                                                        <small class="text-muted d-block">{{ $artwork->category }}</small>
                                                        <small class="text-muted d-block">{{ $artwork->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @empty
                                        <p class="text-muted">This artist has not uploaded any artworks yet.</p>
                                    @endforelse

                                </div>
                            </div>
                        </div>
                    
                    {{-- 
                    =====================================
                    ODD ROW (Artist 1, 3, 5...)
                    =====================================
                    --}}
                    @else
                        
                        {{-- ARTIST INFO (LEFT) --}}
                        <div class="col-lg-2 col-md-3 col-12 mb-4 mb-md-0">
                            <a href="{{ route('pelukis.show', $artist) }}">
                                @if($artist->artistProfile && $artist->artistProfile->profile_picture)
                                    <img src="{{ Storage::url($artist->artistProfile->profile_picture) }}" class="artist-profile-frame mb-3" alt="{{ $artist->name }}">
                                @else
                                    <img src="{{ asset('images/topics/undraw_happy_music_g6wc.png') }}" class="artist-profile-frame mb-3" alt="Default artist image">
                                @endif
                                <h4 class="mb-0">{{ $artist->name }}</h4>
                            </a>
                        </div>
                        
                        {{-- ARTWORK SCROLLER (RIGHT, scrolls L-to-R) --}}
                        <div class="col-lg-10 col-md-9 col-12">
                            <div class="horizontal-scroll-wrapper">
                                <div class="d-flex flex-nowrap">
                                    
                                    @forelse($artist->artworks as $artwork)
                                        <div class="scroll-item">
                                            {{-- FIX: The <a> tag now wraps the ENTIRE block --}}
                                            <a href="{{ route('artworks.show', $artwork) }}" class="custom-block-link">
                                                <div class="custom-block bg-white shadow-lg">
                                                    
                                                    {{-- This is the image+overlay wrapper (no longer a link) --}}
                                                    <div class="custom-block-image-wrap">
                                                        <img src="{{ Storage::url($artwork->image_path) }}" class="custom-block-image img-fluid" alt="{{ $artwork->title }}">
                                                        
                                                        <div class="artwork-overlay">
                                                            <p class="artwork-overlay-text">
                                                                {{ Str::limit($artwork->description, 100) ?? 'No description available.' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    {{-- FIX: This text box is back, and no longer a link --}}
                                                    <div class="p-3">
                                                        <p class="mb-1 fw-bold">{{ $artwork->title }}</p>
                                                        <small class="text-muted d-block">{{ $artwork->category }}</small>
                                                        <small class="text-muted d-block">{{ $artwork->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @empty
                                         <p class="text-muted">This artist has not uploaded any artworks yet.</p>
                                    @endforelse

                                </div>
                            </div>
                        </div>
                    @endif

                </div> {{-- End of Artist Row --}}

            @empty
                {{-- Show this if no artists have posted anything --}}
                <div class="col-12 text-center">
                    <p>No artworks have been posted by any artists yet. Check back soon!</p>
                </div>
            @endforelse

        </div>
    </section>

@endsection