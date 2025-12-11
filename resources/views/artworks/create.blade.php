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

                        {{-- Title & Category --}}
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="" disabled selected>Select Category</option>
                                <option value="Lukisan">Lukisan</option>
                                <option value="Craft">Craft</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <hr class="my-4">

                        {{-- === IMAGE UPLOAD SECTION === --}}
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
                                {{-- HIDDEN INPUT to store the temp path --}}
                                <input type="hidden" name="image_temp_path" id="imageTempPath">
                            </div>
                            
                            {{-- Error Message --}}
                            <div id="pullError" class="text-danger small mt-2" style="display:none;"></div>
                        </div>

                        @error('image') <p class="text-danger mt-1">{{ $message }}</p> @enderror
                        @error('image_temp_path') <p class="text-danger mt-1">{{ $message }}</p> @enderror

                        <hr class="my-4">

                        {{-- Marketplace Settings (Same as before) --}}
                        <h5 class="mb-3 text-primary">Marketplace Settings</h5>
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

                        <button type="submit" class="btn custom-btn w-100 mt-3">Upload Artwork</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // 1. Toggle between File and Link
    const btnUpload = document.getElementById('btnUploadMethod');
    const btnLink = document.getElementById('btnLinkMethod');
    const sectionUpload = document.getElementById('uploadInputSection');
    const sectionLink = document.getElementById('linkInputSection');
    const fileInput = document.getElementById('fileInput');
    const imageTempPath = document.getElementById('imageTempPath');

    btnUpload.addEventListener('click', () => {
        btnUpload.classList.add('active');
        btnLink.classList.remove('active');
        sectionUpload.style.display = 'block';
        sectionLink.style.display = 'none';
        
        // Clear link data if switching back to file
        imageTempPath.value = ''; 
    });

    btnLink.addEventListener('click', () => {
        btnLink.classList.add('active');
        btnUpload.classList.remove('active');
        sectionLink.style.display = 'block';
        sectionUpload.style.display = 'none';
        
        // Clear file input if switching to link
        fileInput.value = ''; 
    });

    // 2. Handle "Pull Image" AJAX
    const btnPull = document.getElementById('btnPullImage');
    const urlInput = document.getElementById('urlInput');
    const pullLoading = document.getElementById('pullLoading');
    const previewArea = document.getElementById('previewArea');
    const previewImg = document.getElementById('previewImg');
    const pullError = document.getElementById('pullError');

    btnPull.addEventListener('click', function() {
        const url = urlInput.value;
        if(!url) return;

        // Reset UI
        pullLoading.style.display = 'block';
        pullError.style.display = 'none';
        previewArea.style.display = 'none';
        btnPull.disabled = true;

        // AJAX Request
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
                // Show Preview
                previewImg.src = data.preview_url;
                imageTempPath.value = data.temp_path; // Store path for form submission
                previewArea.style.display = 'block';
            } else {
                // Show Error
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
</script>
@endpush
@endsection