
@extends('layouts.main')

@section('content')

    {{-- 1. HERO SECTION --}}
    <section class="hero-section" style="min-height: 250px;">
        <div class="container">
            <div class="row align-items-center" style="min-height: 250px;">
                <div class="col-12">
                    <h1 class="text-center text-white">Edit Artwork</h1>
                    <p class="text-center text-white">{{ $artwork->title }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. EDIT ARTWORK FORM --}}
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 col-12">
                    
                    {{-- Note: Action is 'artworks.update' and we use @method('PATCH') --}}
                    <form method="POST" action="{{ route('artworks.update', $artwork) }}" class="custom-form" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        
                        {{-- Title --}}
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            {{-- Pre-fill with existing data --}}
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $artwork->title) }}" required>
                            @error('title')
                                <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select name="category" id="category" class="form-select form-control" required>
                                <option value="" disabled>Select a category...</option>
                                {{-- Pre-select the current category --}}
                                <option value="Lukisan" @selected(old('category', $artwork->category) == 'Lukisan')>Lukisan (Painting)</option>
                                <option value="Craft" @selected(old('category', $artwork->category) == 'Craft')>Craft (Handicraft)</option>
                            </select>
                            @error('category')
                                <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label for="description" class="form-label">Description (Optional)</label>
                            {{-- Pre-fill with existing data --}}
                            <textarea class="form-control" id="description" name="description" style="height: 150px; padding-left: 40px; padding-right: 40px">{{ old('description', $artwork->description) }}</textarea>
                            @error('description')
                                <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Image Upload --}}
                        <div class="mb-3">
                            <label class="form-label d-block">Current Image</label>
                            <img src="{{ Storage::url($artwork->image_path) }}" alt="{{ $artwork->title }}" style="width: 200px; height: auto; border-radius: 10px; margin-bottom: 15px;">
                            
                            <label for="image" class="form-label">Upload New Image (Optional)</label>
                            <input class="form-control" type="file" id="image" name="image">
                            <small class="form-text text-muted">Leave blank to keep the current image.</small>
                            @error('image')
                                <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center">
                            <button type="submit" class="custom-btn">Save Changes</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection