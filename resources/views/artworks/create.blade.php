@extends('layouts.main')

@section('content')
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12 mx-auto">
                <div class="custom-block bg-white shadow-lg p-5">
                    <h3 class="mb-4">Upload New Artwork</h3>

                    <form action="{{ route('artworks.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="Lukisan">Lukisan</option>
                                <option value="Craft">Craft</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control" required>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3 text-primary">Marketplace Settings</h5>
                        <div class="alert alert-info fs-6">
                            <strong>Note:</strong> Leave Price empty to show in Creative Gallery only.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (Rp)</label>
                                <input type="number" id="basePrice" name="price" class="form-control" placeholder="Optional">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stock</label>
                                <input type="number" name="stock" class="form-control" value="1">
                            </div>
                        </div>

                        {{-- Promo Settings --}}
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_promo" name="is_promo" value="1">
                            <label class="form-check-label fw-bold" for="is_promo">Enable Promo / Discount?</label>
                        </div>

                        <div class="mb-4 p-3 border rounded bg-light" id="promo_price_wrapper" style="display:none;">
                            <div class="row">
                                {{-- Percentage Input --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Discount Percentage (%)</label>
                                    <div class="input-group">
                                        <input type="number" id="discountPercent" class="form-control" placeholder="e.g. 20" min="0" max="99">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>

                                {{-- Promo Price Input --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Final Promo Price (Rp)</label>
                                    <input type="number" id="promoPrice" name="promo_price" class="form-control">
                                </div>
                            </div>
                            <small class="text-muted">Adjusting one field will automatically update the other.</small>
                        </div>

                        <button type="submit" class="btn custom-btn w-100">Upload Artwork</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // 1. Toggle Promo
    const promoCheckbox = document.getElementById('is_promo');
    const promoWrapper = document.getElementById('promo_price_wrapper');

    promoCheckbox.addEventListener('change', function() {
        promoWrapper.style.display = this.checked ? 'block' : 'none';
    });

    // 2. Live Calculation
    const basePriceInput = document.getElementById('basePrice');
    const discountInput = document.getElementById('discountPercent');
    const promoPriceInput = document.getElementById('promoPrice');

    function calculateFromPercent() {
        const price = parseFloat(basePriceInput.value) || 0;
        const percent = parseFloat(discountInput.value) || 0;

        if (price > 0) {
            // Formula: Price - (Price * %)
            const finalPrice = price - (price * (percent / 100));
            promoPriceInput.value = Math.round(finalPrice);
        }
    }

    function calculateFromPrice() {
        const price = parseFloat(basePriceInput.value) || 0;
        const promo = parseFloat(promoPriceInput.value) || 0;

        if (price > 0 && promo > 0 && promo < price) {
            // Formula: ((Price - Promo) / Price) * 100
            const percent = ((price - promo) / price) * 100;
            discountInput.value = Math.round(percent);
        }
    }

    // Listeners
    discountInput.addEventListener('input', calculateFromPercent);
    promoPriceInput.addEventListener('input', calculateFromPrice);
    
    // Update if base price changes while percentage is filled
    basePriceInput.addEventListener('input', function() {
        if(discountInput.value) calculateFromPercent();
    });
</script>
@endpush
@endsection