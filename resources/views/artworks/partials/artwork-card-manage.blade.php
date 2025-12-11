<div class="custom-block bg-white shadow-sm h-100 d-flex flex-row align-items-center p-2 border rounded">
    
    {{-- Thumbnail --}}
    <img src="{{ Storage::url($artwork->image_path) }}" 
         class="rounded" 
         style="width: 100px; height: 100px; object-fit: cover;" 
         alt="{{ $artwork->title }}">

    {{-- Details --}}
    <div class="ms-3 flex-grow-1">
        <h6 class="mb-1">
            <a href="{{ route('artworks.show', $artwork) }}" class="text-dark text-decoration-none hover-primary">
                {{ $artwork->title }}
            </a>
        </h6>
        
        <p class="text-muted small mb-1">
            {{ Str::limit(strip_tags($artwork->description), 50) }}
        </p>

        @if($artwork->price)
            <span class="badge bg-success small">Rp {{ number_format($artwork->price, 0, ',', '.') }}</span>
        @else
            <span class="badge bg-secondary small">Gallery Only</span>
        @endif
    </div>

    {{-- ACTIONS (Edit & Delete Side-by-Side) --}}
    <div class="d-flex flex-column ms-2 gap-2">
        
        {{-- EDIT BUTTON --}}
        <a href="{{ route('artworks.edit', $artwork->id) }}" class="btn btn-warning btn-sm text-white" title="Edit">
            <i class="bi-pencil-fill"></i>
        </a>

        {{-- DELETE BUTTON --}}
        <form method="POST" action="{{ route('artworks.destroy', $artwork->id) }}" onsubmit="return confirm('Delete this artwork?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                <i class="bi-trash-fill"></i>
            </button>
        </form>
    </div>
</div>