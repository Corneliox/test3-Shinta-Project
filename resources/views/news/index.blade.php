@extends('layouts.main')

@section('content')
<section class="section-padding" style="padding-top: 150px;">
    <div class="container">
        <h1 class="text-center mb-5">Latest News & Articles</h1>

        <div class="row">
            @foreach($news as $article)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="custom-block bg-white shadow-lg h-100">
                        <a href="{{ route('news.show', $article) }}">
                            <img src="{{ Storage::url($article->thumbnail) }}" class="img-fluid" style="height: 250px; width: 100%; object-fit: cover;" alt="{{ $article->title }}">
                        </a>
                        <div class="p-4">
                            <div class="text-muted small mb-2">
                                {{ $article->created_at->format('d M Y') }}
                            </div>
                            <h4 class="mb-3">
                                <a href="{{ route('news.show', $article) }}" class="text-dark text-decoration-none">
                                    {{ $article->title }}
                                </a>
                            </h4>
                            <p class="text-muted">
                                {!! Str::limit(strip_tags($article->content), 100) !!}
                            </p>
                            <a href="{{ route('news.show', $article) }}" class="btn custom-btn mt-3">Read More</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5">
            {{ $news->links() }}
        </div>
    </div>
</section>
@endsection