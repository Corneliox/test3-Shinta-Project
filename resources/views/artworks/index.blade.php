@extends('layouts.main')

@use('Illuminate\Support\Str')

@section('content')

    {{-- HERO HEADER --}}
    <section class="hero-section" style="min-height: 250px;">
        <div class="container">
            <div class="row align-items-center" style="min-height: 250px;">
                <div class="col-12">
                    <h1 class="text-center text-white">Artist Dashboard</h1>
                    <p class="text-center text-white">Manage portfolio, artworks, and sales.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding">
        <div class="container">
            
            {{-- ADMIN MODE BANNER --}}
            @if(isset($is_impersonating) && $is_impersonating)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Admin Mode Active</p>
                    <p>You are managing artworks for: <strong>{{ $target_user->name }}</strong></p>
                    <a href="{{ route('admin.users.index') }}" class="underline text-sm">Return to User List</a>
                </div>
            @endif

            <div class="row">
                
                {{-- LEFT SIDEBAR --}}
                <div class="col-lg-3 col-12 mb-5">
                    <div class="custom-block bg-white shadow-lg p-4">
                        <div class="text-center mb-4">
                            @if($target_user->artistProfile && $target_user->artistProfile->profile_picture)
                                <img src="{{ Storage::url($target_user->artistProfile->profile_picture) }}" class="rounded-circle img-fluid mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <img src="{{ asset('images/topics/undraw_happy_music_g6wc.png') }}" class="rounded-circle img-fluid mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                            @endif
                            <h5 class="mb-1">{{ $target_user->name }}</h5>
                            <p class="text-muted small">Artist</p>
                        </div>

                        <hr>

                        <ul class="nav flex-column">
                            <li class="nav-item mb-2">
                                <a href="{{ route('profile.user.show') }}" class="nav-link text-dark">
                                    <i class="bi-person me-2"></i> My Profile
                                </a>
                            </li>
                            <li class="nav-item mb-2">
                                <a href="{{ route('artworks.index', ['user_id' => $target_user->id]) }}" class="nav-link text-primary fw-bold">
                                    <i class="bi-palette me-2"></i> Artworks
                                </a>
                            </li>
                        </ul>
                        
                        <div class="mt-4 text-center">
                            <a href="{{ route('artworks.create', ['user_id' => $target_user->id]) }}" class="btn custom-btn w-100">
                                <i class="bi-plus-circle me-1"></i> Upload Artwork
                            </a>
                        </div>
                    </div>
                </div>

                {{-- RIGHT CONTENT (ARTWORK LIST) --}}
                <div class="col-lg-9 col-12">
                    
                    @if (session('status'))
                        <div class="alert alert-success mb-4">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>{{ $target_user->id !== auth()->id() ? $target_user->name . "'s Artworks" : 'My Artworks' }}</h2>
                        <span class="badge bg-secondary">{{ $all_artworks->count() }} Items</span>
                    </div>

                    {{-- TABS --}}
                    <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                        {{-- 1. ALL TAB (Active) --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active text-dark" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-tab-pane" type="button" role="tab">All</button>
                        </li>
                        {{-- 2. LUKISAN TAB --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-dark" id="lukisan-tab" data-bs-toggle="tab" data-bs-target="#lukisan-tab-pane" type="button" role="tab">Lukisan</button>
                        </li>
                        {{-- 3. CRAFT TAB --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-dark" id="craft-tab" data-bs-toggle="tab" data-bs-target="#craft-tab-pane" type="button" role="tab">Craft</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        
                        {{-- PANE 1: ALL ARTWORKS (Default) --}}
                        <div class="tab-pane fade show active" id="all-tab-pane" role="tabpanel">
                            <div class="row">
                                @forelse($all_artworks as $artwork)
                                    <div class="col-md-6 col-12 mb-4">
                                        @include('artworks.partials.artwork-card-manage', ['artwork' => $artwork])
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-5">
                                        <p class="text-muted">No artworks uploaded yet.</p>
                                        <a href="{{ route('artworks.create', ['user_id' => $target_user->id]) }}" class="btn custom-border-btn btn-sm">Upload One</a>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- PANE 2: LUKISAN --}}
                        <div class="tab-pane fade" id="lukisan-tab-pane" role="tabpanel">
                            <div class="row">
                                @forelse($lukisan as $artwork)
                                    <div class="col-md-6 col-12 mb-4">
                                        @include('artworks.partials.artwork-card-manage', ['artwork' => $artwork])
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-5">
                                        <p class="text-muted">No paintings found.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- PANE 3: CRAFT --}}
                        <div class="tab-pane fade" id="craft-tab-pane" role="tabpanel">
                            <div class="row">
                                @forelse($crafts as $artwork)
                                    <div class="col-md-6 col-12 mb-4">
                                        @include('artworks.partials.artwork-card-manage', ['artwork' => $artwork])
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-5">
                                        <p class="text-muted">No crafts found.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection