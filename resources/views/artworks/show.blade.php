@extends('layouts.main')

@use('Illuminate\Support\Str')

@section('content')

    {{-- 1. HERO SECTION WITH ARTWORK IMAGE --}}
    <section class="hero-section" style="background-image: url('{{ Storage::url($artwork->image_path) }}'); background-size: cover; background-position: center; min-height: 450px;">
        <div class="row mt-3 ms-3 mb-4">
            <div class="col-12">
                <a href="{{ route('creative') }}" class="btn custom-btn">
                    <i class="bi-arrow-left me-2"></i> Back to Creative
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

    {{-- 2. ARTWORK DETAILS --}}
    <section class="section-padding">
        <div class="container">
            <div class="row">

                {{-- LEFT: Main Content --}}
                <div class="col-lg-8 col-12">
                    
                    {{-- Floated Image --}}
                    <img src="{{ Storage::url($artwork->image_path) }}" 
                         class="img-fluid shadow-lg float-md-end ms-md-4 mb-3" 
                         alt="Artwork image of {{ $artwork->title }}" 
                         style="border-radius: 20px; max-width: 300px; width: 40%;">

                    <h1 class="mb-3">{{ $artwork->title }}</h1>
                    <p class="text-muted fs-5">Category: {{ $artwork->category }}</p>

                    <h3 class="mt-5">About this work</h3>
                    <hr class="my-4">
                    
                    {{-- Description with line breaks preserved, tags stripped --}}
                    <p style="line-height: 1.8; white-space: pre-line;">
                        {{ strip_tags($artwork->description) ?? 'No description provided.' }}
                    </p>

                </div>

                {{-- RIGHT: Sidebar --}}
                <div class="col-lg-4 col-12 mt-5 mt-lg-0">
                    
                    <div class="custom-block bg-white shadow-lg p-4" style="height: fit-content;">
                        
                        {{-- MARKETPLACE INFO --}}
                        @if($artwork->price && $artwork->price > 0)
                            <div class="mb-4 pb-4 border-bottom">
                                <h4 class="mb-3">Marketplace Info</h4>

                                {{-- Price --}}
                                <div class="mb-3">
                                    @if($artwork->is_promo && $artwork->promo_price > 0)
                                        <small class="text-decoration-line-through text-muted">Rp {{ number_format($artwork->price, 0, ',', '.') }}</small>
                                        <h2 class="text-danger fw-bold">Rp {{ number_format($artwork->promo_price, 0, ',', '.') }}</h2>
                                        <span class="badge bg-danger">PROMO</span>
                                    @else
                                        <h2 class="text-primary fw-bold">Rp {{ number_format($artwork->price, 0, ',', '.') }}</h2>
                                    @endif
                                </div>

                                {{-- Stock --}}
                                <div class="mb-4">
                                    @if($artwork->stock > 0)
                                        <div class="d-flex align-items-center text-success">
                                            <i class="bi-check-circle-fill me-2 fs-5"></i>
                                            <span class="fw-bold fs-5">In Stock ({{ $artwork->stock }})</span>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center text-secondary">
                                            <i class="bi-x-circle-fill me-2 fs-5"></i>
                                            <span class="fw-bold fs-5">Sold Out</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Action Button --}}
                                <div class="d-grid gap-2">
                                    @auth
                                        @if(auth()->id() === $artwork->user_id)
                                            <a href="{{ route('artworks.edit', $artwork->id) }}" class="btn custom-border-btn">Edit My Artwork</a>
                                        @else
                                            @if($artwork->stock > 0)
                                                <a href="#" class="btn custom-btn btn-lg">Buy Now <i class="bi-bag-check-fill ms-2"></i></a>
                                            @else
                                                <button class="btn btn-secondary btn-lg" disabled>Sold Out</button>
                                            @endif
                                        @endif
                                    @else
                                        @if($artwork->stock > 0)
                                            <a href="{{ route('login') }}" class="btn custom-btn btn-lg">Login to Buy</a>
                                        @else
                                            <button class="btn btn-secondary btn-lg" disabled>Sold Out</button>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        @endif

                        {{-- ARTIST PROFILE --}}
                        <h5 class="mb-3 text-muted">Created by</h5>
                        <a href="{{ route('pelukis.show', $artwork->user) }}" class="d-flex align-items-center text-decoration-none text-dark p-3 rounded" style="background-color: #f8f9fa;">
                            @if($artwork->user->artistProfile && $artwork->user->artistProfile->profile_picture)
                                <img src="{{ Storage::url($artwork->user->artistProfile->profile_picture) }}" class="rounded-circle shadow-sm" style="width: 60px; height: 60px; object-fit: cover;" alt="{{ $artwork->user->name }}">
                            @else
                                <img src="{{ asset('images/topics/undraw_happy_music_g6wc.png') }}" class="rounded-circle shadow-sm" style="width: 60px; height: 60px; object-fit: cover;" alt="Default">
                            @endif
                            
                            <div class="ms-3">
                                <h5 class="mb-0 fw-bold">{{ $artwork->user->name }}</h5>
                                <p class="text-muted mb-0 small">Visit Profile <i class="bi-arrow-right-short"></i></p>
                            </div>
                        </a>

                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection