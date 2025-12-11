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
                        
                        {{-- Logic: If Admin is editing someone else's work, go back to THAT user's list --}}
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
                            <select name="category" class="form-select">
                                <option value="Lukisan" {{ $artwork->category == 'Lukisan' ? 'selected' : '' }}>Lukisan</option>
                                <option value="Craft" {{ $artwork->category == 'Craft' ? 'selected' : '' }}>Craft</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $artwork->description) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Artwork Image</label>
                            <div class="mb-2">
                                <img src="{{ Storage::url($artwork->image_path) }}" alt="Current Image" style="height: 100px; border-radius: 10px;">
                            </div>
                            <input type="file" name="image" class="form-control">
                            <small class="text-muted">Leave empty to keep current image.</small>
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
    // Copy the same script logic from create.blade.php here
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

    // Run on load to set percentage if editing
    if(basePriceInput.value && promoPriceInput.value) {
        calculateFromPrice();
    }
</script>
@endpush
@endsection