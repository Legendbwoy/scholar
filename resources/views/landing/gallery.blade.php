@extends('layouts.public')

@section('title', 'Campus Life & Media Gallery — Scholar SMS')

@section('content')
<div class="py-5 bg-light" style="margin-top: 80px;">
    <div class="container text-center py-4">
        <h6 class="text-uppercase text-primary fw-bold letter-spacing-1">Visual Chronicle</h6>
        <h1 class="fw-bold mb-3">Campus Life, Labs & Facilities Gallery</h1>
        <p class="lead text-muted mx-auto" style="max-width: 680px;">Take a digital walkthrough across our robotics laboratories, aquatic centers, research facilities, and cultural arts auditoriums.</p>

        <!-- Filter Buttons -->
        <div class="d-flex justify-content-center flex-wrap gap-2 mt-4" id="gallery-filters">
            <button class="filter-btn active" data-filter="all">All Media ({{ $galleryItems->count() }})</button>
            @foreach($categories as $cat)
                <button class="filter-btn" data-filter="{{ Str::slug($cat) }}">{{ $cat }}</button>
            @endforeach
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4" id="gallery-grid">
        @foreach($galleryItems as $item)
            <div class="col-lg-4 col-md-6 gallery-item-wrap" data-category="{{ Str::slug($item->category) }}">
                <div class="gallery-card" data-bs-toggle="modal" data-bs-target="#modalItem{{ $item->id }}">
                    <img src="{{ asset($item->image_path) }}" alt="{{ $item->title }}">
                    <div class="overlay">
                        <span class="badge bg-warning text-dark w-auto align-self-start mb-2">{{ $item->category }}</span>
                        <h5 class="fw-bold mb-1">{{ $item->title }}</h5>
                        <p class="small mb-0 text-light">{{ $item->caption }}</p>
                    </div>
                </div>

                <!-- Modal Lightbox -->
                <div class="modal fade" id="modalItem{{ $item->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
                            <div class="modal-body p-0 position-relative">
                                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 z-3" data-bs-dismiss="modal"></button>
                                <img src="{{ asset($item->image_path) }}" class="w-100" style="max-height: 520px; object-fit: cover;">
                                <div class="p-4 bg-white">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-primary px-3 py-2">{{ $item->category }}</span>
                                        <small class="text-muted"><i class="fa-regular fa-clock me-1"></i> Scholar Campus Archive</small>
                                    </div>
                                    <h4 class="fw-bold mb-2">{{ $item->title }}</h4>
                                    <p class="text-muted mb-0">{{ $item->caption }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-center mt-5">
        <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Home
        </a>
        <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-4 ms-2">
            Access Portals <i class="fa-solid fa-arrow-right ms-1"></i>
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('#gallery-filters .filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('#gallery-filters .filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');
            document.querySelectorAll('.gallery-item-wrap').forEach(item => {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
</script>
@endsection