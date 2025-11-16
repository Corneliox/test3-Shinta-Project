@extends('layouts.main')

@section('content')

    {{-- 1. YOUR TEMPLATE'S HERO SECTION --}}
    <section class="hero-section" style="min-height: 250px;">
        <div class="container">
            <div class="row align-items-center" style="min-height: 250px;">
                <div class="col-12">
                    <h1 class="text-center text-white">Profile Settings</h1>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. THE CORRECTED FORMS --}}
    <section class="section-padding">
        <div class="container">
            <div class="row">
                
                {{-- Column for the forms --}}
                <div class="col-lg-8 offset-lg-2 col-12">

                    <div class="mb-5" id="update-profile-information">
                        <h2 class="mb-3">Profile Information</h2>
                        <p class="mb-4">Update your account's profile information and email address.</p>

                        <form method="post" action="{{ route('profile.update') }}" class="custom-form">
                            @csrf
                            @method('patch')

                            {{-- Name Field (FIXED) --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" placeholder="Your Name">
                            </div>
                            @error('name')
                                <p class="text-danger mb-3">{{ $message }}</p>
                            @enderror

                            {{-- Email Field (FIXED) --}}
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username" placeholder="your@email.com">
                            </div>
                            @error('email')
                                <p class="text-danger mb-3">{{ $message }}</p>
                            @enderror
                            
                            {{-- "Saved" Message --}}
                            <div class="d-flex align-items-center">
                                <button type="submit" class="custom-btn">Save</button>
                                @if (session('status') === 'profile-updated')
                                    <p class="text-success ms-3 mb-0">Saved.</p>
                                @endif
                            </div>
                        </form>
                    </div>

                    <hr class="my-5">

                    <div class="mb-5" id="update-password-information">
                        <h2 class="mb-3">Update Password</h2>
                        <p class="mb-4">Ensure your account is using a long, random password to stay secure.</p>

                        <form method="post" action="{{ route('password.update') }}" class="custom-form">
                            @csrf
                            @method('put')

                            {{-- Current Password (FIXED) --}}
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input id="current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" placeholder="Your Current Password">
                            </div>
                            @error('current_password', 'updatePassword')
                                <p class="text-danger mb-3">{{ $message }}</p>
                            @enderror

                            {{-- New Password (FIXED) --}}
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input id="password" name="password" type="password" class="form-control" autocomplete="new-password" placeholder="New Secure Password">
                            </div>
                            @error('password', 'updatePassword')
                                <p class="text-danger mb-3">{{ $message }}</p>
                            @enderror

                            {{-- Confirm Password (FIXED) --}}
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" placeholder="Confirm New Password">
                            </div>
                            @error('password_confirmation', 'updatePassword')
                                <p class="text-danger mb-3">{{ $message }}</p>
                            @enderror

                            {{-- "Saved" Message --}}
                            <div class="d-flex align-items-center">
                                <button type="submit" class="custom-btn">Save</button>
                                @if (session('status') === 'password-updated')
                                    <p class="text-success ms-3 mb-0">Saved.</p>
                                @endif
                            </div>
                        </form>
                    </div>

                    {{-- =================================== --}}
                    {{-- 3. ARTIST PROFILE SECTION (FIXED!)  --}}
                    {{-- =================================== --}}
                    @if (Auth::user()->is_artist)
                        <hr class="my-5">

                        <h2 class="mb-3" id="artist-profile-form">My Artist Profile</h2>
                        <p class="mb-4">This information is visible to everyone on your public "Pelukis" page.</p>

                        <form method="post" action="{{ route('artist.profile.update') }}" class="custom-form" enctype="multipart/form-data">
                            @csrf
                            @method('patch')

                            {{-- Profile Picture (FIXED ALIGNMENT) --}}
                            <div class="mb-4">
                                <label class="form-label d-block">Current Profile Picture</label>
                                @if ($profile->profile_picture)
                                    <img src="{{ Storage::url($profile->profile_picture) }}" class="artist-profile-frame" style="width: 150px; height: 150px; margin-bottom: 15px;" alt="Current Profile Picture">
                                @else
                                    <img src="{{ asset('images/topics/undraw_happy_music_g6wc.png') }}" class="artist-profile-frame" style="width: 150px; height: 150px; margin-bottom: 15px;" alt="Default Profile Picture">
                                @endif
                                
                                </br>
                                
                                <label for="profile_picture" class="form-label">Upload New Picture</label>
                                <input class="form-control" type="file" id="profile_picture" name="profile_picture">
                                @error('profile_picture')
                                    <p class="text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- About Section (FIXED) --}}
                            <div class="mb-3">
                                <label for="about" class="form-label">About Me</label>
                                <textarea class="form-control" id="about" name="about" style="height: 200px; padding-left: 65px; padding-right: 65px" placeholder="Tell everyone about yourself...">{{ old('about', $profile->about) }}</textarea>
                            </div>
                            @error('about')
                                <p class="text-danger mb-3">{{ $message }}</p>
                            @enderror
                            
                            {{-- "Saved" Message --}}
                            <div class="d-flex align-items-center">
                                <button type="submit" class="custom-btn">Save Artist Profile</button>
                                @if (session('status') === 'artist-profile-updated')
                                    <p class="text-success ms-3 mb-0">Saved.</p>
                                @endif
                            </div>
                        </form>

                        <hr class="my-5">

                        {{-- Artwork Management Section --}}
                        <h2 class="mb-3">My Artworks</h2>
                        <p>Manage your "Lukisan" and "Craft" galleries here.</p>
                        <a href="#" class="custom-btn">Manage My Artworks</a>
                    @endif


                    <hr class="my-5">

                    <div>
                        <h2 class="mb-3 text-danger">Delete Account</h2>
                        <p>Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.</p>
                        
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
                            Delete Account
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}" class="custom-form p-4">
                    @csrf
                    @method('delete')

                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="confirmUserDeletionModalLabel">Are you sure?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Once your account is deleted, all data will be permanently lost. Please enter your password to confirm you would like to permanently delete your account.</p>
                        
                        {{-- Password (FIXED) --}}
                        <div class="mb-3">
                            <label for="password_delete" class="form-label">Password</label>
                            <input id="password_delete" name="password" type="password" class="form-control" placeholder="Password" autocomplete="current-password">
                        </div>
                        @error('password', 'userDeletion')
                            <p class="text-danger mb-3">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

{{-- This must be at the very end of the file, after @endsection --}}
@push('scripts')
<script>
    // We must wait for the *entire* window (all images, scripts, etc.) to finish loading.
    window.addEventListener('load', function() {
        
        // Check if there's a hash in the URL (e.g., #update-password-information)
        if (window.location.hash) {
            
            // Give all other scripts (like click-scroll.js) time to finish.
            // 300ms is usually long enough.
            setTimeout(function() {
                try {
                    // Find the element we want to scroll to
                    var element = document.querySelector(window.location.hash);
                    
                    if (element) {
                        // Get the height of your sticky header.
                        // Your navbar is about 80px high, so we'll add 20px of padding.
                        var headerOffset = 100; 
                        
                        // Get the element's position on the page
                        var elementPosition = element.getBoundingClientRect().top;
                        
                        // Calculate the final scroll position (element's top + current scroll - header height)
                        var offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                        
                        // Manually scroll to that exact position
                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                } catch(e) {
                    console.error("Error scrolling to fragment:", e);
                }
            }, 300); // 300ms delay
        }
    });
</script>
@endpush