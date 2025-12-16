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
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">Upload New Artwork</h3>
                        <a href="{{ route('artworks.index') }}" class="btn custom-btn custom-border-btn btn-sm">Back to Dashboard</a>
                    </div>

                    <form action="{{ route('artworks.store') }}" method="POST" enctype="multipart/form-data" id="artworkForm">
                        @csrf

                        {{-- NEW: If admin passed a User ID, store it here --}}
                        @if(isset($targetUserId) && $targetUserId)
                            <input type="hidden" name="behalf_user_id" value="{{ $targetUserId }}">
                            <div class="alert alert-warning">
                                <strong>Admin Notice:</strong> You are adding this artwork for User ID: {{ $targetUserId }}
                            </div>
                        @endif

                        {{-- 1. BASIC INFO --}}
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" id="categorySelect" class="form-select" required>
                                <option value="" disabled selected>Select Category</option>
                                <option value="Lukisan" {{ old('category') == 'Lukisan' ? 'selected' : '' }}>Lukisan (1 Image Max)</option>
                                <option value="Craft" {{ old('category') == 'Craft' ? 'selected' : '' }}>Craft (Max 3 Images)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <hr class="my-4">

                        {{-- 2. IMAGE UPLOAD (FILE or LINK) --}}
                        <label class="form-label fw-bold">Main Artwork Image <span class="text-danger">*</span></label>
                        
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

                        {{-- Option A: Standard File Upload WITH ROTATION --}}
                        <div id="uploadInputSection">
                            {{-- Preview & Rotate UI --}}
                            <div class="mb-3 text-center p-3 bg-light border rounded position-relative" id="filePreviewContainer" style="display:none;">
                                <img id="mainPreview" src="#" class="img-fluid rounded shadow-sm" style="max-height: 300px; transition: transform 0.3s ease;">
                                
                                {{-- Rotate Button --}}
                                <button type="button" id="btnRotate" class="btn btn-dark btn-sm position-absolute bottom-0 end-0 m-3 shadow" title="Rotate 90° Right">
                                    <i class="bi-arrow-clockwise"></i> Rotate
                                </button>
                            </div>

                            <input type="file" name="image" id="fileInput" class="form-control" accept="image/*" onchange="previewFile(this)">
                            <small class="text-muted">Max size: 5MB</small>
                            
                            {{-- HIDDEN INPUT FOR ROTATION --}}
                            <input type="hidden" name="rotation" id="rotationInput" value="0">
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

                        {{-- NEW: EXTRA IMAGES (Hidden by default) --}}
                        <div id="extraImagesSection" class="mt-4 p-3 bg-light border rounded" style="display: none;">
                            <label class="form-label fw-bold text-primary">Additional Craft Images (Optional)</label>
                            <p class="text-muted small">You can upload up to 2 extra photos for Crafts.</p>
                            <input type="file" name="extra_images[]" class="form-control" multiple accept="image/*">
                        </div>

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

    // ===============================
    // 2. IMAGE PREVIEW & ROTATION
    // ===============================
    let currentRotation = 0;
    const btnRotate = document.getElementById('btnRotate');
    const mainPreview = document.getElementById('mainPreview');
    const filePreviewContainer = document.getElementById('filePreviewContainer');
    const rotationInput = document.getElementById('rotationInput');

    // Rotation Button Click
    if(btnRotate) {
        btnRotate.addEventListener('click', function() {
            // Increment by 90 degrees
            currentRotation = (currentRotation + 90) % 360;
            // Update Visuals
            mainPreview.style.transform = `rotate(${currentRotation}deg)`;
            // Update Hidden Input for Server
            rotationInput.value = currentRotation;
        });
    }

    // File Selection Handler
    function previewFile(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                mainPreview.src = e.target.result;
                filePreviewContainer.style.display = 'block';
                
                // Reset rotation on new file select
                currentRotation = 0;
                rotationInput.value = 0;
                mainPreview.style.transform = 'rotate(0deg)';
            }
            reader.readAsDataURL(file);
        } else {
            filePreviewContainer.style.display = 'none';
        }
    }

    // ===============================
    // 3. AJAX PULL LOGIC (Link)
    // ===============================
    const btnPull = document.getElementById('btnPullImage');
    const urlInput = document.getElementById('urlInput');
    const pullLoading = document.getElementById('pullLoading');
    const previewArea = document.getElementById('previewArea');
    const previewImg = document.getElementById('previewImg');
    const pullError = document.getElementById('pullError');

    btnPull.addEventListener('click', function() {
        const url = urlInput.value;
        if(!url) return;

        pullLoading.style.display = 'block';
        pullError.style.display = 'none';
        previewArea.style.display = 'none';
        btnPull.disabled = true;

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
                imageTempPath.value = data.temp_path; 
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
    // 4. CATEGORY LISTENER (EXTRA IMAGES)
    // ===============================
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
    toggleExtras(); 

    // ===============================
    // 5. PROMO PRICE CALCULATOR
    // ===============================
    const promoCheckbox = document.getElementById('is_promo');
    const promoWrapper = document.getElementById('promo_price_wrapper');
    const basePriceInput = document.getElementById('basePrice');
    const discountInput = document.getElementById('discountPercent');
    const promoPriceInput = document.getElementById('promoPrice');

    function togglePromo() {
        promoWrapper.style.display = promoCheckbox.checked ? 'block' : 'none';
    }
    promoCheckbox.addEventListener('change', togglePromo);
    togglePromo(); 

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
        if(discountInput.value && promoCheckbox.checked) {
            calculateFromPercent();
        }
    });
</script>
@endpush
@endsection