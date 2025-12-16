@extends('layouts.main')

@section('styles')
<style>
    /* Styling for the Word-like content */
    .news-content img {
        max-width: 100%;
        height: auto !important;
        border-radius: 8px;
        margin: 15px 0;
    }
    .news-content table {
        width: 100% !important;
        border-collapse: collapse;
        margin: 20px 0;
    }
    .news-content td {
        padding: 10px;
        vertical-align: top;
    }
    .news-content p {
        margin-bottom: 15px;
        line-height: 1.8;
        font-size: 1.1rem;
    }
</style>
@endsection

@section('content')
<section class="section-padding" style="padding-top: 150px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-12">
                
                {{-- Breadcrumb / Back --}}
                <a href="{{ route('news.index') }}" class="text-muted mb-4 d-inline-block">
                    <i class="bi-arrow-left me-1"></i> Back to News
                </a>

                {{-- Header --}}
                <h1 class="mb-3 display-5 fw-bold">{{ $news->title }}</h1>
                <p class="text-muted mb-4">
                    Posted on {{ $news->created_at->format('F d, Y') }}
                </p>

                {{-- Main Thumbnail --}}
                <img src="{{ Storage::url($news->thumbnail) }}" class="img-fluid w-100 rounded shadow-sm mb-5" alt="{{ $news->title }}">

                {{-- THE CONTENT --}}
                <div class="news-content bg-white p-4 p-md-5 shadow-sm rounded">
                    {!! $news->content !!}
                </div>

            </div>
        </div>
    </div>
</section>
@endsection