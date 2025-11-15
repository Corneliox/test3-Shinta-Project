@extends('layouts.main')

@section('content')

    {{-- 1. "ABOUT" SECTION (Hero) --}}
    {{-- This uses your template's "hero-section" --}}
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
                        @if($profile)
                            {{ $profile->about }}
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
                    {{-- We can re-use the "Creative" section's layout! --}}
                    <div class="horizontal-scroll-wrapper">
                        <div class="d-flex flex-nowrap">
                            @forelse($lukisan as $artwork)
                                <div class="scroll-item">
                                    <div class="custom-block bg-white shadow-lg">
                                        <a href="#"> {{-- Link to artwork detail page? --}}
                                            <img src="{{ Storage::url($artwork->image_path) }}" class="custom-block-image img-fluid" alt="{{ $artwork->title }}">
                                            <div class="p-3">
                                                <p class="mb-0">{{ $artwork->title }}</p>
                                                <small>{{ $artwork->description }}</small>
                                            </div>
                                        </a>
                                    </div>
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
                        <div class="d-flex flex-nowrap">
                            @forelse($crafts as $artwork)
                                <div class="scroll-item">
                                    <div class="custom-block bg-white shadow-lg">
                                        <a href="#">
                                            <img src="{{ Storage::url($artwork->image_path) }}" class="custom-block-image img-fluid" alt="{{ $artwork->title }}">
                                            <div class="p-3">
                                                <p class="mb-0">{{ $artwork->title }}</p>
                                                <small>{{ $artwork->description }}</small>
                                            </div>
                                        </a>
                                    </div>
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