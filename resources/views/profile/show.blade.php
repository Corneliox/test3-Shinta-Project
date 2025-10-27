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

    {{-- 2. THE NEW TEMPLATE-STYLED FORMS --}}
    <section class="section-padding">
        <div class="container">
            <div class="row">
                
                {{-- Center the content --}}
                <div class="col-lg-8 offset-lg-2 col-12">

                    <div class="mb-5">
                        <h2 class="mb-3">Profile Information</h2>
                        <p class="mb-4">Update your account's profile information and email address.</p>

                        <form method="post" action="{{ route('profile.update') }}" class="custom-form">
                            @csrf
                            @method('patch')

                            {{-- Name Field --}}
                            <div class="form-floating mb-3">
                                <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                                <label for="name">Name</label>
                            </div>
                            @error('name')
                                <p class="text-danger mt-n3 mb-3">{{ $message }}</p>
                            @enderror

                            {{-- Email Field --}}
                            <div class="form-floating mb-3">
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email Address" value="{{ old('email', $user->email) }}" required autocomplete="username">
                                <label for="email">Email Address</label>
                            </div>
                            @error('email')
                                <p class="text-danger mt-n3 mb-3">{{ $message }}</p>
                            @enderror
                            
                            {{-- "Saved" Message --}}
                            <div class="d-flex align-items-center">
                                {{-- This button uses your template's "custom-btn" class --}}
                                <button type="submit" class="custom-btn">Save</button>

                                @if (session('status') === 'profile-updated')
                                    <p class="text-success ms-3 mb-0">Saved.</p>
                                @endif
                            </div>
                        </form>
                    </div>

                    <hr class="my-5">

                    <div class="mb-5">
                        <h2 class="mb-3">Update Password</h2>
                        <p class="mb-4">Ensure your account is using a long, random password to stay secure.</p>

                        <form method="post" action="{{ route('password.update') }}" class="custom-form">
                            @csrf
                            @method('put')

                            {{-- Current Password --}}
                            <div class="form-floating mb-3">
                                <input type="password" name="current_password" id="current_password" class="form-control" placeholder="Current Password" autocomplete="current-password">
                                <label for="current_password">Current Password</label>
                            </div>
                            @error('current_password', 'updatePassword')
                                <p class="text-danger mt-n3 mb-3">{{ $message }}</p>
                            @enderror

                            {{-- New Password --}}
                            <div class="form-floating mb-3">
                                <input type="password" name="password" id="password" class="form-control" placeholder="New Password" autocomplete="new-password">
                                <label for="password">New Password</label>
                            </div>
                            @error('password', 'updatePassword')
                                <p class="text-danger mt-n3 mb-3">{{ $message }}</p>
                            @enderror

                            {{-- Confirm Password --}}
                            <div class="form-floating mb-3">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm Password" autocomplete="new-password">
                                <label for="password_confirmation">Confirm Password</label>
                            </div>
                            @error('password_confirmation', 'updatePassword')
                                <p class="text-danger mt-n3 mb-3">{{ $message }}</p>
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

                    <hr class="my-5">

                    <div>
                        <h2 class="mb-3 text-danger">Delete Account</h2>
                        <p>Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.</p>
                        
                        {{-- We use btn-danger here as your template doesn't have a red button class --}}
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
                {{-- We use .custom-form here to style the modal's input --}}
                <form method="post" action="{{ route('profile.destroy') }}" class="custom-form p-4">
                    @csrf
                    @method('delete')

                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="confirmUserDeletionModalLabel">Are you sure?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Once your account is deleted, all data will be permanently lost. Please enter your password to confirm you would like to permanently delete your account.</p>
                        
                        <div class="form-floating mb-3">
                            <input type="password" name="password" id="password_delete" class="form-control" placeholder="Password" autocomplete="current-password">
                            <label for="password_delete">Password</label>
                        </div>
                        @error('password', 'userDeletion')
                            <p class="text-danger mt-n3 mb-3">{{ $message }}</p>
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