@extends('layouts.main')

@section('content')

    {{-- 1. HERO SECTION --}}
    <section class="hero-section" style="min-height: 250px;">
        <div class="container">
            <div class="row align-items-center" style="min-height: 250px;">
                <div class="col-12">
                    <h1 class="text-center text-white">Manage My Artworks</h1>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. UPLOAD ARTWORK FORM --}}
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 col-12">
                    
                    <h2 class="mb-3">Upload New Artwork</h2>
                    <p class="mb-4">Add a new painting or craft to your gallery.</p>

                    <form method="POST" action="{{ route('artworks.store') }}" class="custom-form" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Title --}}
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="My Artwork Title" value="{{ old('title') }}" required>
                            @error('title')
                                <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select name="category" id="category" class="form-select form-control" required>
                                <option value="" disabled selected>Select a category...</option>
                                <option value="Lukisan" @if(old('category') == 'Lukisan') selected @endif>Lukisan (Painting)</option>
                                <option value="Craft" @if(old('category') == 'Craft') selected @endif>Craft (Handicraft)</option>
                            </select>
                            @error('category')
                                <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control" id="description" name="description" style="height: 150px; padding-left:40px; padding-right:40px" placeholder="Details about your artwork...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Image Upload --}}
                        <div class="mb-3">
                            <label for="image" class="form-label">Artwork Image</label>
                            <input class="form-control" type="file" id="image" name="image" required>
                            @error('image')
                                <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center">
                            <button type="submit" class="custom-btn">Upload Artwork</button>
                            @if (session('status') === 'artwork-uploaded')
                                <p class="text-success ms-3 mb-0">Upload successful!</p>
                            @endif
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. MY ARTWORKS LISTS --}}
    <section class="section-padding pt-0">
        <div class="container">
            
            {{-- MY LUKISAN --}}
            <div class="row mb-5">
                <div class="col-12">
                    <h2 class="mb-4">My Lukisan (Paintings)</h2>
                </div>
                <div class="col-12">
                    <div class="horizontal-scroll-wrapper">
                        <div class="d-flex flex-nowrap">
                            @forelse($lukisan as $artwork)
                                <div class="scroll-item">
                                    <div class="custom-block bg-white shadow-lg">
                                        <img src="{{ Storage::url($artwork->image_path) }}" class="custom-block-image img-fluid" alt="{{ $artwork->title }}">
                                        <div class="p-3">
                                            <p class="mb-0">{{ $artwork->title }}</p>
                                            
                                            {{-- DELETE BUTTON FORM --}}
                                            <form method="POST" action="{{ route('artworks.destroy', $artwork) }}" onsubmit="return confirm('Are you sure you want to delete this artwork?');">
                                                @csrf
                                                @method('DELETE')
                                                {{-- We use btn-sm for a small button --}}
                                                <button type="submit" class="btn btn-danger btn-sm mt-2">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">You have not uploaded any paintings yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- MY CRAFT --}}
            <div class="row">
                <div class="col-12">
                    <h2 class="mb-4">My Craft</h2>
                </div>
                <div class="col-12">
                    <div class="horizontal-scroll-wrapper">
                        <div class="d-flex flex-nowrap">
                            @forelse($crafts as $artwork)
                                <div class="scroll-item">
                                    <div class="custom-block bg-white shadow-lg">
                                        <img src="{{ Storage::url($artwork->image_path) }}" class="custom-block-image img-fluid" alt="{{ $artwork->title }}">
                                        <div class="p-3">
                                            <p class="mb-0">{{ $artwork->title }}</p>
                                            
                                            {{-- DELETE BUTTON FORM --}}
                                            <form method="POST" action="{{ route('artworks.destroy', $artwork) }}" onsubmit="return confirm('Are you sure you want to delete this artwork?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm mt-2">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">You have not uploaded any crafts yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            @if (session('status') === 'artwork-deleted')
                <div class="alert alert-success mt-4">
                    Artwork deleted successfully.
                </div>
            @endif

        </div>
    </section>

@endsection