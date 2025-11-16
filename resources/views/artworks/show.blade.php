@extends('layouts.main')

@section('content')

    {{-- 1. HERO SECTION WITH ARTWORK IMAGE --}}
    <section class="hero-section" style="background-image: url('{{ Storage::url($artwork->image_path) }}'); background-size: cover; background-position: center; min-height: 450px;">
        <div class="container">
            <div class="row align-items-center" style="min-height: 450px;">
                <div class="col-12">
                    {{-- This is just a spacer --}}
                </div>
            </div>
        </div>
    </section>

    {{-- 2. ARTWORK DETAILS (NEW LAYOUT) --}}
    <section class="section-padding">
        <div class="container">
            <div class="row">

                {{-- =================================== --}}
                {{-- Main Content (This part is rebuilt) --}}
                {{-- =================================== --}}
                <div class="col-lg-8 col-12">
                    
                    {{-- 
                      This is the "Word Wrap" trick. We float the image to the right.
                      - 'float-md-end' makes it float right on medium screens and up.
                      - 'ms-md-4' adds a margin to its left.
                      - 'mb-3' adds a margin to its bottom.
                      - 'style' gives it the frame and controls its size.
                    --}}
                    <img src="{{ Storage::url($artwork->image_path) }}" 
                         class="img-fluid shadow-lg float-md-end ms-md-4 mb-3" 
                         alt="Artwork image of {{ $artwork->title }}" 
                         style="border-radius: var(--border-radius-large); max-width: 300px; width: 40%;">

                    {{-- 
                      All the text below will now automatically wrap
                      around the floated image.
                    --}}

                    <h1 class="mb-3">{{ $artwork->title }}</h1>
                    <p class="text-muted fs-5">Category: {{ $artwork->category }}</p>

                    <h3 class="mt-5">About this work</h3>
                    
                    {{-- 
                      This <hr> will automatically stop at the
                      floated image, just as you wanted.
                    --}}
                    <hr class="my-4">
                    
                    <p>{{ $artwork->description ?? 'No description provided.' }}</p>
                    {{-- 
                      When this <p> tag gets long enough, it will 
                      flow right underneath the image.
                    --}}

                </div>
                {{-- =================================== --}}
                {{-- End of Rebuilt Content            --}}
                {{-- =================================== --}}


                {{-- Artist Sidebar (This part remains the same) --}}
                <div class="col-lg-4 col-12 mt-5 mt-lg-0">
                    <div class="custom-block bg-white shadow-lg p-4">
                        <h4 class="mb-3">Artist</h4>
                        <a href="{{ route('pelukis.show', $artwork->user) }}" class="d-flex align-items-center">
                            @if($artwork->user->artistProfile && $artwork->user->artistProfile->profile_picture)
                                <img src="{{ Storage::url($artwork->user->artistProfile->profile_picture) }}" class="artist-profile-frame" style="width: 80px; height: 80px;" alt="{{ $artwork->user->name }}">
                            @else
                                <img src="{{ asset('images/topics/undraw_happy_music_g6wc.png') }}" class="artist-profile-frame" style="width: 80px; height: 80px;" alt="Default artist image">
                            @endif
                            
                            <div class="ms-3">
                                <h5 class="mb-0">{{ $artwork->user->name }}</h5>
                                <p class="text-muted mb-0">View Profile</p>
                            </div>
                        </a>

                        {{-- EDIT BUTTON (OWNER ONLY) --}}
                        @auth
                            @if(auth()->id() === $artwork->user_id)
                                <hr class="my-3">
                                <p class="text-muted">This is your artwork.</p>
                                <a href="{{ route('artworks.edit', $artwork) }}" class="custom-btn">Edit Artwork</a>
                            @endif
                        @endauth
                    </div>

                    @if (session('status') === 'artwork-updated')
                        <div class="alert alert-success mt-4">
                            Artwork updated successfully!
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </section>
@endsection