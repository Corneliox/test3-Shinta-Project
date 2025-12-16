@extends('layouts.main')

@section('content')

{{-- Error Alert Block --}}
@if (session('error'))
    <div class="alert alert-danger bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Whoops!</strong>
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
@endif

<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12 mx-auto">
                <div class="custom-block bg-white shadow-lg p-5">
                    
                    {{-- HEADER & SMART BACK BUTTON --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">Edit Artwork</h3>
                        
                        @if(auth()->id() !== $artwork->user_id)
                            <a href="{{ route('artworks.index', ['user_id' => $artwork->user_id]) }}" class="btn custom-btn custom-border-btn btn-sm">
                                <i class="bi-arrow-left me-1"></i> Back to {{ $artwork->user->name }}'s Art
                            </a>
                        @else
                            <a href="{{ route('artworks.index') }}" class="btn custom-btn custom-border-btn btn-sm">Back to Dashboard</a>
                        @endif
                    </div>

                    {{-- ADMIN NOTICE --}}
                    @if(auth()->id() !== $artwork->user_id)
                        <div class="alert alert-warning border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Admin Mode Active</p>
                            <p>You are editing an artwork that belongs to: <strong>{{ $artwork->user->name }}</strong></p>
                        </div>
                    @endif

                    <form action="{{ route('artworks.update', $artwork->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $artwork->title) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            {{-- Added ID for JS --}}
                            <select name="category" id="categorySelect" class="form-select">
                                <option value="Lukisan" {{ $artwork->category == 'Lukisan' ? 'selected' : '' }}>Lukisan</option>
                                <option value="Craft" {{ $artwork->category == 'Craft' ? 'selected' : '' }}>Craft</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $artwork->description) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Main Artwork Image</label>
                            
                            {{-- PREVIEW & ROTATE UI --}}
                            <div class="mb-3 text-center p-3 bg-light border rounded position-relative" style="min-height: 200px;">
                                {{-- Current or New Image Preview --}}
                                <img id="mainPreview" 
                                     src="{{ Storage::url($artwork->image_path) }}" 
                                     class="img-fluid rounded shadow-sm" 
                                     style="max-height: 300px; transition: transform 0.3s ease;">
                                
                                {{-- Rotate Button --}}
                                <button type="button" id="btnRotate" class="btn btn-dark btn-sm position-absolute bottom-0 end-0 m-3 shadow" title="Rotate 90° Right">
                                    <i class="bi-arrow-clockwise"></i> Rotate
                                </button>
                            </div>

                            <input type="file" name="image" id="fileInput" class="form-control" accept="image/*" onchange="previewFile(this)">
                            <small class="text-muted">Leave empty to keep current image. You can still rotate the current image.</small>
                            
                            {{-- HIDDEN INPUT FOR ROTATION --}}
                            <input type="hidden" name="rotation" id="rotationInput" value="0">
                        </div>

                        {{-- EXTRA IMAGES MANAGEMENT (Hidden unless Craft) --}}
                        <div id="extraImagesSection" class="mb-4 p-3 bg-light border rounded" style="{{ $artwork->category == 'Craft' ? '' : 'display:none;' }}">
                            <h6 class="fw-bold text-primary mb-3">Additional Craft Images</h6>
                            
                            @if($artwork->additional_images && count($artwork->additional_images) > 0)
                                <div class="row g-2 mb-3">
                                    @foreach($artwork->additional_images as $path)
                                        <div class="col-4 position-relative text-center">
                                            <div class="border rounded p-1 bg-white">
                                                <img src="{{ Storage::url($path) }}" class="img-fluid" style="height: 80px; object-fit: cover;">
                                                <div class="form-check mt-1 d-flex justify-content-center">
                                                    <input class="form-check-input me-1" type="checkbox" name="delete_extras[]" value="{{ $path }}" id="del_{{ $loop->index }}">
                                                    <label class="form-check-label text-danger small fw-bold" for="del_{{ $loop->index }}">Delete</label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @php
                                $count = $artwork->additional_images ? count($artwork->additional_images) : 0;
                                $remaining = 2 - $count;
                            @endphp

                            @if($remaining > 0)
                                <label class="form-label small fw-bold">Add more images (Max {{ $remaining }} more)</label>
                                <input type="file" name="extra_images[]" class="form-control" multiple accept="image/*">
                            @else
                                <div class="alert alert-warning py-2 small mb-0">
                                    <i class="bi-exclamation-triangle me-1"></i> Max 3 images total reached. Delete some to add new ones.
                                </div>
                            @endif
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3 text-primary">Marketplace Settings</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (Rp)</label>
                                <input type="number" id="basePrice" name="price" class="form-control" value="{{ old('price', $artwork->price) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stock</label>
                                <input type="number" name="stock" class="form-control" value="{{ old('stock', $artwork->stock) }}">
                            </div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_promo" name="is_promo" value="1" {{ $artwork->is_promo ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_promo">Enable Promo / Discount?</label>
                        </div>

                        <div class="mb-4 p-3 border rounded bg-light" id="promo_price_wrapper" style="{{ $artwork->is_promo ? '' : 'display:none;' }}">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Discount Percentage (%)</label>
                                    <div class="input-group">
                                        <input type="number" id="discountPercent" class="form-control" placeholder="e.g. 20" min="0" max="99">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Final Promo Price (Rp)</label>
                                    <input type="number" id="promoPrice" name="promo_price" class="form-control" value="{{ old('promo_price', $artwork->promo_price) }}">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn custom-btn w-100">Update Artwork</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // 1. ROTATION LOGIC
    let currentRotation = 0;
    const btnRotate = document.getElementById('btnRotate');
    const mainPreview = document.getElementById('mainPreview');
    const rotationInput = document.getElementById('rotationInput');

    if(btnRotate) {
        btnRotate.addEventListener('click', function() {
            currentRotation = (currentRotation + 90) % 360;
            mainPreview.style.transform = `rotate(${currentRotation}deg)`;
            rotationInput.value = currentRotation;
        });
    }

    function previewFile(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                mainPreview.src = e.target.result;
                
                // Reset rotation on new file select
                currentRotation = 0;
                rotationInput.value = 0;
                mainPreview.style.transform = 'rotate(0deg)';
            }
            reader.readAsDataURL(file);
        }
    }

    // 2. CATEGORY LISTENER (EXTRA IMAGES)
    const catSelect = document.getElementById('categorySelect');
    const extraSection = document.getElementById('extraImagesSection');
    
    function toggleExtras() {
        if (catSelect.value === 'Craft') {
            extraSection.style.display = 'block';
        } else {
            extraSection.style.display = 'none';
        }
    }
    
    catSelect.addEventListener('change', toggleExtras);

    // 3. PROMO PRICE CALCULATOR
    const promoCheckbox = document.getElementById('is_promo');
    const promoWrapper = document.getElementById('promo_price_wrapper');
    const basePriceInput = document.getElementById('basePrice');
    const discountInput = document.getElementById('discountPercent');
    const promoPriceInput = document.getElementById('promoPrice');

    promoCheckbox.addEventListener('change', function() {
        promoWrapper.style.display = this.checked ? 'block' : 'none';
    });

    function calculateFromPercent() {
        const price = parseFloat(basePriceInput.value) || 0;
        const percent = parseFloat(discountInput.value) || 0;
        if (price > 0) {
            const finalPrice = price - (price * (percent / 100));
            promoPriceInput.value = Math.round(finalPrice);
        }
    }

    function calculateFromPrice() {
        const price = parseFloat(basePriceInput.value) || 0;
        const promo = parseFloat(promoPriceInput.value) || 0;
        if (price > 0 && promo > 0 && promo < price) {
            const percent = ((price - promo) / price) * 100;
            discountInput.value = Math.round(percent);
        }
    }

    discountInput.addEventListener('input', calculateFromPercent);
    promoPriceInput.addEventListener('input', calculateFromPrice);
    basePriceInput.addEventListener('input', function() {
        if(discountInput.value) calculateFromPercent();
    });

    if(basePriceInput.value && promoPriceInput.value) {
        calculateFromPrice();
    }
</script>
@endpush
@endsection