@extends('layouts.main')

@section('content')
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12 mx-auto">
                <div class="custom-block bg-white shadow-lg p-5">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">Upload New Artwork</h3>
                        <a href="{{ route('artworks.index') }}" class="btn custom-btn custom-border-btn btn-sm">Back to Dashboard</a>
                    </div>

                    <form action="{{ route('artworks.store') }}" method="POST" enctype="multipart/form-data" id="artworkForm">
                        @csrf

                        {{-- 1. BASIC INFO --}}
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="" disabled selected>Select Category</option>
                                <option value="Lukisan" {{ old('category') == 'Lukisan' ? 'selected' : '' }}>Lukisan</option>
                                <option value="Craft" {{ old('category') == 'Craft' ? 'selected' : '' }}>Craft</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <hr class="my-4">

                        {{-- 2. IMAGE UPLOAD (FILE or LINK) --}}
                        <label class="form-label fw-bold">Artwork Image</label>
                        
                        {{-- Toggle Buttons --}}
                        <div class="mb-3">
                            <div class="btn-group w-100" role="group">
                                <button type="button" class="btn btn-outline-primary active" id="btnUploadMethod">
                                    <i class="bi-upload me-2"></i> Upload File
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="btnLinkMethod">
                                    <i class="bi-link-45deg me-2"></i> Upload via Link
                                </button>
                            </div>
                        </div>

                        {{-- Option A: Standard File Upload --}}
                        <div id="uploadInputSection">
                            <input type="file" name="image" id="fileInput" class="form-control" accept="image/*">
                            <small class="text-muted">Max size: 5MB</small>
                        </div>

                        {{-- Option B: Google Drive / Link Puller --}}
                        <div id="linkInputSection" style="display: none;">
                            <label class="form-label small text-muted">Paste a direct link or a Google Drive sharing link</label>
                            <div class="input-group mb-2">
                                <input type="text" id="urlInput" class="form-control" placeholder="https://drive.google.com/file/d/...">
                                <button type="button" class="btn btn-dark" id="btnPullImage">
                                    <i class="bi-cloud-download me-1"></i> Pull Image
                                </button>
                            </div>
                            
                            {{-- Loading Spinner --}}
                            <div id="pullLoading" class="text-center text-primary mt-2" style="display:none;">
                                <div class="spinner-border spinner-border-sm" role="status"></div> Processing link...
                            </div>

                            {{-- Preview Area --}}
                            <div id="previewArea" class="mt-3 text-center border rounded p-2 bg-light" style="display:none;">
                                <p class="text-success small mb-1"><i class="bi-check-circle"></i> Image pulled successfully!</p>
                                <img id="previewImg" src="" style="max-height: 200px; max-width: 100%; border-radius: 8px;">
                                
                                {{-- HIDDEN INPUT to store the temp path sent to controller --}}
                                <input type="hidden" name="image_temp_path" id="imageTempPath">
                            </div>
                            
                            {{-- Error Message --}}
                            <div id="pullError" class="text-danger small mt-2" style="display:none;"></div>
                        </div>

                        @error('image') <p class="text-danger mt-1">{{ $message }}</p> @enderror
                        @error('image_temp_path') <p class="text-danger mt-1">{{ $message }}</p> @enderror

                        <hr class="my-4">

                        {{-- 3. MARKETPLACE SETTINGS (RESTORED) --}}
                        <h5 class="mb-3 text-primary">Marketplace Settings</h5>
                        <div class="alert alert-info fs-6">
                            <strong>Note:</strong> Leave Price empty to show in Creative Gallery only.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (Rp)</label>
                                <input type="number" id="basePrice" name="price" class="form-control" placeholder="Optional" value="{{ old('price') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stock</label>
                                <input type="number" name="stock" class="form-control" value="{{ old('stock', 1) }}">
                            </div>
                        </div>

                        {{-- Promo Settings --}}
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_promo" name="is_promo" value="1" {{ old('is_promo') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_promo">Enable Promo / Discount?</label>
                        </div>

                        <div class="mb-4 p-3 border rounded bg-light" id="promo_price_wrapper" style="display:none;">
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
                                    <input type="number" id="promoPrice" name="promo_price" class="form-control" value="{{ old('promo_price') }}">
                                </div>
                            </div>
                            <small class="text-muted">Adjusting one field will automatically update the other.</small>
                        </div>

                        <button type="submit" class="btn custom-btn w-100 mt-3">Upload Artwork</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // ===============================
    // 1. IMAGE UPLOAD TOGGLE & AJAX
    // ===============================
    const btnUpload = document.getElementById('btnUploadMethod');
    const btnLink = document.getElementById('btnLinkMethod');
    const sectionUpload = document.getElementById('uploadInputSection');
    const sectionLink = document.getElementById('linkInputSection');
    const fileInput = document.getElementById('fileInput');
    const imageTempPath = document.getElementById('imageTempPath');

    // Toggle View
    btnUpload.addEventListener('click', () => {
        btnUpload.classList.add('active');
        btnLink.classList.remove('active');
        sectionUpload.style.display = 'block';
        sectionLink.style.display = 'none';
        imageTempPath.value = ''; // Clear temp path
    });

    btnLink.addEventListener('click', () => {
        btnLink.classList.add('active');
        btnUpload.classList.remove('active');
        sectionLink.style.display = 'block';
        sectionUpload.style.display = 'none';
        fileInput.value = ''; // Clear file input
    });

    // AJAX Pull Logic
    const btnPull = document.getElementById('btnPullImage');
    const urlInput = document.getElementById('urlInput');
    const pullLoading = document.getElementById('pullLoading');
    const previewArea = document.getElementById('previewArea');
    const previewImg = document.getElementById('previewImg');
    const pullError = document.getElementById('pullError');

    btnPull.addEventListener('click', function() {
        const url = urlInput.value;
        if(!url) return;

        // UI Reset
        pullLoading.style.display = 'block';
        pullError.style.display = 'none';
        previewArea.style.display = 'none';
        btnPull.disabled = true;

        // Fetch
        fetch('{{ route("artworks.preview") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ url: url })
        })
        .then(response => response.json())
        .then(data => {
            pullLoading.style.display = 'none';
            btnPull.disabled = false;

            if(data.success) {
                previewImg.src = data.preview_url;
                imageTempPath.value = data.temp_path; // THIS IS CRITICAL
                previewArea.style.display = 'block';
            } else {
                pullError.innerText = data.error || 'Failed to fetch image.';
                pullError.style.display = 'block';
            }
        })
        .catch(error => {
            console.error(error);
            pullLoading.style.display = 'none';
            btnPull.disabled = false;
            pullError.innerText = 'System error. Please verify the link.';
            pullError.style.display = 'block';
        });
    });

    // ===============================
    // 2. PROMO PRICE CALCULATOR
    // ===============================
    const promoCheckbox = document.getElementById('is_promo');
    const promoWrapper = document.getElementById('promo_price_wrapper');
    const basePriceInput = document.getElementById('basePrice');
    const discountInput = document.getElementById('discountPercent');
    const promoPriceInput = document.getElementById('promoPrice');

    // Toggle Promo Section
    function togglePromo() {
        promoWrapper.style.display = promoCheckbox.checked ? 'block' : 'none';
    }
    promoCheckbox.addEventListener('change', togglePromo);
    togglePromo(); // Run once on load to set initial state

    // Math Functions
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

    // Listeners
    discountInput.addEventListener('input', calculateFromPercent);
    promoPriceInput.addEventListener('input', calculateFromPrice);
    
    // Recalculate if base price changes
    basePriceInput.addEventListener('input', function() {
        if(discountInput.value && promoCheckbox.checked) {
            calculateFromPercent();
        }
    });
</script>
@endpush
@endsection