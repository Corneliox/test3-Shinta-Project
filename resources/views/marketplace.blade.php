@extends('layouts.main')

@section('content')

    {{-- 1. HERO SECTION --}}
    <section class="hero-section d-flex justify-content-center align-items-center marketplace-hero">
        <div class="container">
            <div class="row align-items-center h-100">
                <div class="col-12">
                    <h1 class="text-center text-white">Marketplace</h1>
                    <p class="text-center text-white pb-lg-5">Explore unique artworks and crafts.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. MARKETPLACE CONTENT --}}
    <section class="section-padding" style="background-color: #f9f9f9;">
        <div class="container">
            
            {{-- A. SEARCH BAR (Moved Outside Columns for better Mobile View) --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="custom-block bg-white shadow-sm p-3 p-lg-4">
                        <form action="{{ route('marketplace.index') }}" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control form-control-lg me-2" placeholder="Search product or artist..." value="{{ request('search') }}">
                            <button type="submit" class="btn custom-btn">Search</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- MAIN ROW --}}
            <div class="row">

                {{-- ============================================================== --}}
                {{-- RIGHT COLUMN (PROMOS) - MOVED TO TOP ON MOBILE (Order 1)       --}}
                {{-- ============================================================== --}}
                <div class="col-lg-3 col-12 order-1 order-lg-2 mb-4">
                    
                    {{-- Sticky only on Desktop (via CSS class below) --}}
                    <div class="sticky-desktop" style="z-index: 8;">
                        
                        <div class="mb-3 text-center d-flex justify-content-between align-items-center d-lg-block">
                            <h4 class="text-danger fw-bold mb-0 mb-lg-2">HOT PROMOS 🔥</h4>
                            {{-- Mobile Hint --}}
                            <small class="text-muted d-lg-none">Swipe for more <i class="bi-arrow-right"></i></small>
                        </div>

                        {{-- SWIPER CONTAINER --}}
                        <div class="swiper promoSwiper" style="padding-bottom: 30px;">
                            <div class="swiper-wrapper">
                                
                                @forelse($promos as $promo)
                                    <div class="swiper-slide">
                                        {{-- PROMO CARD --}}
                                        <div class="custom-block bg-white shadow-lg border border-danger position-relative promo-card h-100">
                                            <div class="promo-badge">PROMO</div>
                                            
                                            <div class="custom-block-image-wrap" style="height: 250px; border-radius: 20px 20px 0 0; overflow:hidden;">
                                                <a href="{{ route('artworks.show', $promo) }}">
                                                    <img src="{{ Storage::url($promo->image_path) }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="{{ $promo->title }}">
                                                </a>
                                            </div>

                                            <div class="p-3 text-center">
                                                <h5 class="mb-1 text-truncate">{{ $promo->title }}</h5>
                                                <small class="text-muted d-block mb-2">by {{ $promo->user->name }}</small>
                                                
                                                <div class="mb-3">
                                                    <span class="text-decoration-line-through text-muted me-2 small">Rp {{ number_format($promo->price, 0, ',', '.') }}</span>
                                                    <br>
                                                    <span class="text-danger fs-4 fw-bold">Rp {{ number_format($promo->promo_price, 0, ',', '.') }}</span>
                                                    <span class="badge bg-danger ms-1">-{{ $promo->discount_percent }}%</span>
                                                </div>

                                                <div class="d-grid gap-2">
                                                    <a href="{{ route('artworks.buy', $promo) }}" class="btn btn-danger">Buy Now</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="swiper-slide">
                                        <div class="text-center text-muted py-5 border rounded bg-white">
                                            No promos available right now.
                                        </div>
                                    </div>
                                @endforelse

                            </div>
                            
                            {{-- PAGINATION DOTS --}}
                            <div class="swiper-pagination"></div>
                        </div>

                    </div>
                </div> 


                {{-- ============================================================== --}}
                {{-- LEFT COLUMN (PRODUCTS) - ORDER 2 ON MOBILE                     --}}
                {{-- ============================================================== --}}
                <div class="col-lg-9 col-12 order-2 order-lg-1">
                    <div class="row">
                        
                        {{-- 1. INTERNAL FILTERS (Width: 3/12 of Left) --}}
                        {{-- Hidden on very small screens? No, let's keep it stacked --}}
                        <div class="col-lg-3 col-md-4 col-12 mb-4">
                            <div class="custom-block bg-white shadow-sm p-4">
                                <form id="filterForm" action="{{ route('marketplace.index') }}" method="GET">
                                    @if(request('search'))
                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                    @endif

                                    {{-- Collapsible Filter Header for Mobile --}}
                                    <div class="d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#mobileFilterCollapse" role="button" aria-expanded="false" aria-controls="mobileFilterCollapse">
                                        <h6 class="mb-0 fw-bold">Filters & Sort</h6>
                                        <i class="bi-chevron-down d-md-none"></i>
                                    </div>

                                    {{-- Collapse Wrapper for Mobile --}}
                                    <div class="collapse d-md-block mt-3 mt-md-0" id="mobileFilterCollapse">
                                        {{-- SORT --}}
                                        <div class="mb-3">
                                            <small class="text-uppercase text-muted fw-bold">Sort By</small>
                                            <div class="form-check mt-2">
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

                                        {{-- CATEGORY --}}
                                        <div class="mb-3">
                                            <small class="text-uppercase text-muted fw-bold">Category</small>
                                            <div class="form-check mt-2">
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

                                        {{-- ARTIST --}}
                                        <div class="mb-3">
                                            <small class="text-uppercase text-muted fw-bold">Artist</small>
                                            <div class="artist-filter-list mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="artist" value="" id="artistAll" {{ request('artist') == '' ? 'checked' : '' }} onchange="this.form.submit()">
                                                    <label class="form-check-label" for="artistAll">All</label>
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
                                        </div>
                                    </div> {{-- End Collapse --}}
                                </form>
                            </div>
                        </div>

                        {{-- 2. PRODUCTS GRID (Width: 9/12 of Left) --}}
                        <div class="col-lg-9 col-md-8 col-12">
                            <div class="row">
                                @forelse($artworks as $artwork)
                                    @php 
                                        $isSoldOut = $artwork->isSoldOut(); 
                                        $hasPrice = $artwork->price != null;
                                    @endphp
                                    {{-- MOBILE FIX: Changed col-12 to col-6 for 2-column grid on mobile --}}
                                    <div class="col-lg-4 col-6 mb-4">
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
                                            <div class="p-2 p-md-3 d-flex flex-column flex-grow-1">
                                                {{-- Title smaller on mobile --}}
                                                <h5 class="mb-1 text-truncate fs-6 fs-md-5" title="{{ $artwork->title }}">{{ $artwork->title }}</h5>
                                                
                                                <div class="mb-2">
                                                    @if($hasPrice)
                                                        <span class="fw-bold text-primary d-block">Rp {{ number_format($artwork->price, 0, ',', '.') }}</span>
                                                    @else
                                                        <span class="text-muted small fst-italic">Display Only</span>
                                                    @endif
                                                    <small class="text-muted text-truncate d-block" style="max-width: 100%;">
                                                        @ {{ explode(' ', $artwork->user->name)[0] }}
                                                    </small>
                                                </div>

                                                <div class="mt-auto d-grid gap-2">
                                                    @if($hasPrice && !$isSoldOut)
                                                        <a href="{{ route('artworks.buy', $artwork) }}" class="btn btn-sm btn-outline-primary">Buy</a>
                                                    @else
                                                        <a href="{{ route('artworks.show', $artwork) }}" class="btn btn-sm btn-outline-secondary">View</a>
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

                    </div>
                </div> {{-- End Left Column --}}

            </div> {{-- End Main Row --}}
        </div>
    </section>

@push('scripts')
    {{-- SWIPER CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        // 1. Artist Toggle Script
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

        // 2. PROMO SWIPER INIT
        document.addEventListener("DOMContentLoaded", function() {
            var promoSwiper = new Swiper(".promoSwiper", {
                slidesPerView: 1,      
                spaceBetween: 20,      
                loop: true,            
                autoplay: {
                    delay: 4000,       
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                effect: 'cards',       
                grabCursor: true,
            });
        });
    </script>
@endpush

<style>
    /* Hero Height adjustments */
    .marketplace-hero { min-height: 250px; }
    @media (max-width: 768px) {
        .marketplace-hero { min-height: 180px !important; }
        .marketplace-hero h1 { font-size: 2rem; }
    }

    /* Sticky Desktop Only */
    @media (min-width: 992px) {
        .sticky-desktop {
            position: sticky;
            top: 100px;
        }
    }

    /* Images */
    .custom-block-image-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .sold-out-card { opacity: 0.7; filter: grayscale(100%); }
    .sold-out-overlay {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.8); color: white; padding: 5px 15px;
        font-weight: bold; border-radius: 4px; z-index: 2; font-size: 0.8rem;
    }
    
    .promo-card { border-width: 2px !important; transition: transform 0.3s; }
    
    .promo-badge {
        position: absolute; top: 15px; right: -10px;
        background: #dc3545; color: white; padding: 5px 10px; padding-left: 15px;
        font-weight: bold; font-size: 0.85rem; z-index: 3;
        box-shadow: 2px 2px 5px rgba(0,0,0,0.2);
        clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%, 10% 50%);
    }

    .swiper-pagination-bullet-active {
        background-color: #dc3545 !important;
    }
</style>

@endsection