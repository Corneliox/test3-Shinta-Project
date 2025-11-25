@extends('layouts.main')

@section('content')
<section class="section-padding">
    <div class="container">
        <div class="row">
            
            {{-- Sidebar (reuse your admin sidebar style or simple nav) --}}
            <div class="col-lg-3 col-12 mb-4">
                <div class="custom-block bg-white shadow-lg p-4">
                    <div class="d-flex align-items-center mb-4">
                        @if(auth()->user()->artistProfile->profile_picture)
                            <img src="{{ Storage::url(auth()->user()->artistProfile->profile_picture) }}" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                        @endif
                        <div>
                            <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                            <small class="text-muted">Artist Dashboard</small>
                        </div>
                    </div>
                    
                    <hr>

                    <ul class="nav flex-column">
                        <li class="nav-item mb-2">
                            <a href="{{ route('profile.user.show') }}" class="nav-link text-muted">
                                <i class="bi-person me-2"></i> Edit Profile
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a href="{{ route('artworks.index') }}" class="nav-link text-muted">
                                <i class="bi-palette me-2"></i> My Artworks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('artist.dashboard') }}" class="nav-link active fw-bold text-primary">
                                <i class="bi-bell me-2"></i> Pending Sales
                                @if($pendingSales->count() > 0)
                                    <span class="badge bg-danger ms-2">{{ $pendingSales->count() }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="col-lg-9 col-12">
                <div class="custom-block bg-white shadow-lg p-4">
                    <h4 class="mb-4">Incoming Orders (WhatsApp Reservations)</h4>
                    
                    @if (session('status'))
                        <div class="alert alert-success mb-4">{{ session('status') }}</div>
                    @endif

                    @if($pendingSales->isEmpty())
                        <div class="text-center py-5">
                            <img src="{{ asset('images/topics/undraw_happy_music_g6wc.png') }}" class="img-fluid mb-3" style="width: 150px; opacity: 0.5;">
                            <p class="text-muted">No pending sales right now.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th>Reserved Time</th>
                                        <th>Expires In</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingSales as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ Storage::url($item->image_path) }}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <span class="fw-bold d-block">{{ $item->title }}</span>
                                                        <small class="text-muted">Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $item->updated_at->format('d M, H:i') }}</td>
                                            <td>
                                                @if($item->reserved_until && now()->lessThan($item->reserved_until))
                                                    <span class="text-danger fw-bold">
                                                        {{ now()->diffInHours($item->reserved_until) }}h {{ now()->diffInMinutes($item->reserved_until) % 60 }}m
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Expired</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <form action="{{ route('artworks.confirm', $item) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success text-white" title="Confirm Sale">
                                                            <i class="bi-check-lg"></i> Sold
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('artworks.reject', $item) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancel Reservation">
                                                            <i class="bi-x-lg"></i> Cancel
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</section>
@endsection