@extends('layouts.main')

@use('Illuminate\Support\Str')

@section('content')
    <section class="hero-section" style="min-height: 250px;">
        <div class="container">
            <div class="row align-items-center" style="min-height: 250px;">
                <div class="col-12">
                    <h1 class="text-center text-white">All Events</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding">
        <div class="container">
            <div class="row">

                @forelse($events as $event)
                    <div class="col-lg-4 col-md-6 col-12 mb-4">
                        {{-- This uses the standard topics-listing block --}}
                        <div class="custom-block custom-block-topics-listing bg-white shadow-lg">
                            <div class="d-flex">
                                <img src="{{ Storage::url($event->image_path) }}" class="custom-block-image img-fluid" alt="">
                                <div class="custom-block-topics-listing-info d-flex">
                                    <div>
                                        <h5 class="mb-2">{{ $event->title }}</h5>
                                        <p class="mb-0">{{ Str::limit($event->description, 50) }}</p>
                                        <a href="{{ route('event.details', $event) }}" class="btn custom-btn mt-3 mt-lg-4">Learn More</a>
                                    </div>
                                    <span class="badge bg-secondary rounded-pill ms-auto">{{ $event->start_at->format('M d') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted fs-4">No events have been posted yet. Please check back later!</p>
                    </div>
                @endforelse

                {{-- Pagination Links --}}
                <div class="col-12 mt-4">
                    {{ $events->links() }}
                </div>

            </div>
        </div>
    </section>
@endsection