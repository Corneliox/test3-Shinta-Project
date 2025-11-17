@extends('layouts.main')

@section('content')

    {{-- 1. HERO SECTION (Event Image) --}}
    <section class="hero-section" style="background-image: url('{{ Storage::url($event->image_path) }}'); background-size: cover; background-position: center; min-height: 450px;">
        <div class="container">
            <div class="row align-items-center" style="min-height: 450px;">
                <div class="col-12">
                    {{-- Spacer --}}
                </div>
            </div>
        </div>
    </section>

    {{-- 2. EVENT DETAILS (with text-wrap) --}}
    <section class="section-padding">
        <div class="container">
            <div class="row">

                {{-- Main Content --}}
                <div class="col-lg-8 col-12">

                    {{-- Floated image for text-wrap effect --}}
                    <img src="{{ Storage::url($event->image_path) }}" 
                         class="img-fluid shadow-lg float-md-end ms-md-4 mb-3" 
                         alt="{{ $event->title }}" 
                         style="border-radius: var(--border-radius-large); max-width: 300px; width: 40%;">

                    {{-- Event Title & Description --}}
                    <h1 class="mb-3">{{ $event->title }}</h1>
                    <h3 class="mt-5">About this event</h3>
                    <hr class="my-4">

                    {{-- Use {!! nl2br(e($event->description)) !!} to respect line breaks --}}
                    <p>{!! nl2br(e($event->description)) !!}</p>
                </div>

                {{-- Sidebar (Event Details) --}}
                <div class="col-lg-4 col-12 mt-5 mt-lg-0">
                    <div class="custom-block bg-white shadow-lg p-4">
                        <h4 class="mb-3">Event Details</h4>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <strong>Starts:</strong>
                                <p class="text-muted">{{ $event->start_at->format('F d, Y - h:i A') }}</p>
                            </li>
                            <li class="mb-2">
                                <strong>Ends:</strong>
                                <p class="text-muted">{{ $event->end_at->format('F d, Y - h:i A') }}</p>
                            </li>
                            <li class="mb-2">
                                <strong>Posted:</strong>
                                <p class="text-muted">{{ $event->created_at->diffForHumans() }}</p>
                            </li>
                        </ul>

                        {{-- EDIT BUTTON (Admins ONLY) --}}
                        @auth
                            @if(auth()->user()->is_admin)
                                <hr class="my-3">
                                <p class="text-muted">Admin: You can edit this event.</p>
                                {{-- This route doesn't exist yet, but we're preparing for it --}}
                                <a href="#" class="custom-btn">Edit Event</a>
                            @endif
                        @endauth
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection