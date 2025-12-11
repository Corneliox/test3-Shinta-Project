@extends('layouts.guest') {{-- Use your simple layout --}}

@section('content')
<div class="container text-center py-5">
    <div class="card shadow-lg mx-auto" style="max-width: 400px;">
        <div class="card-header bg-danger text-white">
            <h4 class="mb-0">Security Check</h4>
        </div>
        <div class="card-body p-4">
            <p class="fs-5 fw-bold mb-4">April? Mom?</p>
            
            <form action="{{ route('admin.security.verify') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <input type="text" name="answer" class="form-control text-center" placeholder="Answer..." required autofocus>
                    @error('answer')
                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-dark w-100">Verify Device</button>
            </form>
        </div>
    </div>
</div>
@endsection