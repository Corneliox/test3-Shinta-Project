@extends('layouts.main')

@section('content')

    {{-- 1. HERO SECTION --}}
    <section class="hero-section d-flex justify-content-center align-items-center" style="min-height: 250px;">
        <div class="container">
            <div class="row align-items-center" style="min-height: 250px;">
                <div class="col-12">
                    <h1 class="text-center text-white">Marketplace</h1>
                    <p class="text-center text-white pb-5">Explore unique artworks and crafts from our talented community.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. MARKETPLACE CONTENT --}}
    <section class="section-padding" style="background-color: #f9f9f9;">
        <div class="container">
            
            {{-- MAIN ROW: Holds Left Content (9) and Right Sidebar (3) --}}
            <div class="row">

                {{-- ============================================================== --}}
                {{-- LEFT COLUMN (Width: 9/12)                                      --}}
                {{-- ============================================================== --}}
                <div class="col-lg-9 col-12">
                    
                    {{-- A. SEARCH BAR ROW --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="custom-block bg-white shadow-sm p-4">
                                <form action="{{ route('marketplace.index') }}" method="GET" class="d-flex">
                                    <input type="text" name="search" class="form-control form-control-lg me-2" placeholder="Search product or artist..." value="{{ request('search') }}">
                                    <button type="submit" class="btn custom-btn">Search</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- B. CONTENT ROW (Filters + Products) --}}
                    <div class="row">
                        
                        {{-- 1. INTERNAL FILTERS (Width: 3/12 of the Left Column) --}}
                        <div class="col-lg-3 col-md-4 col-12 mb-4">
                            <div class="custom-block bg-white shadow-sm p-4">
                                <form id="filterForm" action="{{ route('marketplace.index') }}" method="GET">
                                    @if(request('search'))
                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                    @endif

                                    <h6 class="mb-3 fw-bold">Sort By</h6>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="sort" value="newest" id="sortNew" {{ request('sort', 'newest') == 'newest' ? 'checked' : '' }} onchange="this.form.submit()">
                                            <label class="form-check-label" for="sortNew">Newest</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="sort" value="oldest" id="sortOld" {{ request('sort') == 'oldest' ? 'checked' : '' }} onchange="this.form.submit()">
                                            <label class="form-check-label" for="sortOld">Oldest</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="sort" value="alphabetical" id="sortAlpha" {{ request('sort') == 'alphabetical' ? 'checked' : '' }} onchange="this.form.submit()">
                                            <label class="form-check-label" for="sortAlpha">A-Z</label>
                                        </div>
                                    </div>

                                    <hr>

                                    <h6 class="mb-3 mt-4 fw-bold">Category</h6>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="category" value="" id="catAll" {{ request('category') == '' ? 'checked' : '' }} onchange="this.form.submit()">
                                            <label class="form-check-label" for="catAll">All</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="category" value="Lukisan" id="catLukisan" {{ request('category') == 'Lukisan' ? 'checked' : '' }} onchange="this.form.submit()">
                                            <label class="form-check-label" for="catLukisan">Lukisan</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="category" value="Craft" id="catCraft" {{ request('category') == 'Craft' ? 'checked' : '' }} onchange="this.form.submit()">
                                            <label class="form-check-label" for="catCraft">Craft</label>
                                        </div>
                                    </div>

                                    <hr>

                                    <h6 class="mb-3 mt-4 fw-bold">Artist</h6>
                                    <div class="artist-filter-list">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="artist" value="" id="artistAll" {{ request('artist') == '' ? 'checked' : '' }} onchange="this.form.submit()">
                                            <label class="form-check-label" for="artistAll">All Artists</label>
                                        </div>

                                        @foreach($artists as $index => $artist)
                                            <div class="form-check {{ $index >= 5 ? 'hidden-artist d-none' : '' }}">
                                                <input class="form-check-input" type="radio" name="artist" value="{{ $artist->id }}" id="artist{{ $artist->id }}" {{ request('artist') == $artist->id ? 'checked' : '' }} onchange="this.form.submit()">
                                                <label class="form-check-label" for="artist{{ $artist->id }}">
                                                    {{ $artist->name }}
                                                </label>
                                            </div>
                                        @endforeach

                                        @if($artists->count() > 5)
                                            <a href="javascript:void(0)" class="small text-primary mt-2 d-block" id="showMoreArtists" onclick="toggleArtists()">
                                                Show more (+{{ $artists->count() - 5 }})
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- 2. PRODUCTS GRID (Width: 9/12 of the Left Column) --}}
                        <div class="col-lg-9 col-md-8 col-12">
                            <div class="row">
                                @forelse($artworks as $artwork)
                                    @php 
                                        $isSoldOut = $artwork->isSoldOut(); 
                                        $hasPrice = $artwork->price != null;
                                    @endphp
                                    <div class="col-lg-4 col-md-6 col-12 mb-4">
                                        <div class="custom-block bg-white shadow-sm h-100 d-flex flex-column {{ $isSoldOut ? 'sold-out-card' : '' }}">
                                            
                                            {{-- Image --}}
                                            <div class="custom-block-image-wrap position-relative" style="border-radius: 20px 20px 0 0; overflow: hidden; aspect-ratio: 1/1;">
                                                <a href="{{ route('artworks.show', $artwork) }}" class="d-block w-100 h-100">
                                                    <img src="{{ Storage::url($artwork->image_path) }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="{{ $artwork->title }}">
                                                </a>
                                                @if($isSoldOut)
                                                    <div class="sold-out-overlay">SOLD OUT</div>
                                                @endif
                                            </div>

                                            {{-- Details --}}
                                            <div class="p-3 d-flex flex-column flex-grow-1">
                                                <h5 class="mb-1 text-truncate" title="{{ $artwork->title }}">{{ $artwork->title }}</h5>
                                                
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    @if($hasPrice)
                                                        <span class="fw-bold text-primary">Rp {{ number_format($artwork->price, 0, ',', '.') }}</span>
                                                    @else
                                                        <span class="text-muted small fst-italic">Display</span>
                                                    @endif
                                                    <small class="text-muted text-truncate" style="max-width: 100px;">
                                                        @ {{ explode(' ', $artwork->user->name)[0] }}
                                                    </small>
                                                </div>

                                                <div class="mt-auto pt-2 d-grid gap-2">
                                                    @if($hasPrice && !$isSoldOut)
                                                        <a href="{{ route('artworks.buy', $artwork) }}" class="btn btn-sm btn-outline-primary">Buy</a>
                                                    @else
                                                        <a href="{{ route('artworks.show', $artwork) }}" class="btn btn-sm btn-outline-secondary">Details</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-light text-center py-5">
                                            <h5 class="text-muted">No items found matching your filters.</h5>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div> {{-- End Nested Row --}}
                </div> {{-- End Left Column --}}


                {{-- ============================================================== --}}
                {{-- RIGHT COLUMN (Width: 3/12) - PROMOS                            --}}
                {{-- ============================================================== --}}
                <div class="col-lg-3 col-12">
                    {{-- Sticky Wrapper --}}
                    <div class="sticky-top" style="top: 100px; z-index: 900;">
                        
                        <div class="mb-3 text-center">
                            <h4 class="text-danger fw-bold">HOT PROMOS 🔥</h4>
                        </div>

                        @forelse($promos as $promo)
                            <div class="custom-block bg-white shadow-lg mb-4 border border-danger position-relative promo-card">
                                <div class="promo-badge">PROMO</div>
                                
                                <div class="custom-block-image-wrap" style="height: 200px; border-radius: 20px 20px 0 0; overflow:hidden;">
                                    <img src="{{ Storage::url($promo->image_path) }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="{{ $promo->title }}">
                                </div>

                                <div class="p-3 text-center">
                                    <h5 class="mb-1 text-truncate">{{ $promo->title }}</h5>
                                    <small class="text-muted d-block mb-2">by {{ $promo->user->name }}</small>
                                    
                                    <div class="mb-2">
                                        <span class="text-decoration-line-through text-muted me-2">Rp {{ number_format($promo->price, 0, ',', '.') }}</span>
                                        <br>
                                        <span class="text-danger fs-5 fw-bold">Rp {{ number_format($promo->promo_price, 0, ',', '.') }}</span>
                                        <span class="badge bg-danger ms-1">-{{ $promo->discount_percent }}%</span>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <a href="{{ route('artworks.buy', $promo) }}" class="btn btn-sm btn-danger">Buy Now</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4 border rounded bg-white">
                                No promos available.
                            </div>
                        @endforelse

                    </div>
                </div> {{-- End Right Column --}}

            </div> {{-- End Main Row --}}
        </div>
    </section>

@push('scripts')
<script>
    function toggleArtists() {
        const hiddenArtists = document.querySelectorAll('.hidden-artist');
        const btn = document.getElementById('showMoreArtists');
        hiddenArtists.forEach(el => {
            if (el.classList.contains('d-none')) {
                el.classList.remove('d-none');
                btn.innerHTML = "Show Less";
            } else {
                el.classList.add('d-none');
                btn.innerHTML = "Show More";
            }
        });
    }
</script>
@endpush

<style>
    /* Ensure images inside grid don't overflow */
    .custom-block-image-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .sold-out-card { opacity: 0.7; filter: grayscale(100%); }
    .sold-out-overlay {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.8); color: white; padding: 5px 15px;
        font-weight: bold; border-radius: 4px; z-index: 10;
    }
    .promo-card { border-width: 2px !important; transition: transform 0.3s; }
    .promo-card:hover { transform: translateY(-5px); }
    .promo-badge {
        position: absolute; top: 15px; right: -10px;
        background: #dc3545; color: white; padding: 5px 10px; padding-left: 15px;
        font-weight: bold; font-size: 0.85rem; z-index: 5;
        box-shadow: 2px 2px 5px rgba(0,0,0,0.2);
        clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%, 10% 50%);
    }
</style>

@endsection