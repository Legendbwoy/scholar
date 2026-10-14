@extends('layouts.public')

@section('title', 'Campus Life & Media Gallery — Scholar SMS')

@section('content')
<!-- Page Title Header -->
<div class="page-title dark-background position-relative py-5 text-center" style="background: url('{{ asset("knightone/img/hero-bg.jpg") }}') center center / cover no-repeat; margin-top: 70px;">
    <div class="container position-relative py-5">
        <span class="badge rounded-pill px-3 py-1.5 mb-2 text-uppercase fw-bold" style="background-color: rgba(0, 153, 97, 0.25); border: 1px solid #009961; color: #4ade80;">Visual Chronicle</span>
        <h1 class="fw-bolder text-white mb-2" style="font-family: 'Raleway', sans-serif;">Campus Life, Labs & Facilities Gallery</h1>
        <p class="text-white-50 mx-auto mb-0" style="max-width: 680px;">Take a digital walkthrough across our robotics laboratories, aquatic centers, research facilities, and cultural arts auditoriums.</p>
    </div>
</div>

<!-- Gallery Grid Section -->
<section id="portfolio" class="portfolio section py-5">
    <div class="container">
        <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">
            <ul class="portfolio-filters isotope-filters text-center mb-5" data-aos="fade-up">
                <li data-filter="*" class="filter-active">All Highlights</li>
                <li data-filter=".filter-campus">Campus Life</li>
                <li data-filter=".filter-labs">Laboratories & STEM</li>
                <li data-filter=".filter-sports">Athletics & Sports</li>
                <li data-filter=".filter-arts">Arts & Events</li>
            </ul>

            <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="100">
                @forelse($galleryItems as $item)
                    @php
                        $catLower = strtolower($item->category);
                        $filterClass = 'filter-campus';
                        if (str_contains($catLower, 'lab') || str_contains($catLower, 'stem')) {
                            $filterClass = 'filter-labs';
                        } elseif (str_contains($catLower, 'sport') || str_contains($catLower, 'athletic')) {
                            $filterClass = 'filter-sports';
                        } elseif (str_contains($catLower, 'art') || str_contains($catLower, 'event') || str_contains($catLower, 'symposium')) {
                            $filterClass = 'filter-arts';
                        }
                    @endphp
                    <div class="col-lg-4 col-md-6 portfolio-item isotope-item {{ $filterClass }}">
                        <div class="portfolio-content h-100 rounded-4 overflow-hidden border shadow-sm position-relative bg-white">
                            <img src="{{ $item->image_url }}" class="img-fluid w-100" style="height: 260px; object-fit: cover;" alt="{{ $item->title }}">
                            <div class="portfolio-info p-3">
                                <span class="badge bg-light text-dark border mb-1" style="font-size: 10px;">{{ $item->category }}</span>
                                <h6 class="fw-bold mb-1 text-dark">{{ $item->title }}</h6>
                                <p class="text-muted small mb-0">{{ $item->caption ?: 'Experiential learning and campus life.' }}</p>
                                <a href="{{ $item->image_url }}" title="{{ $item->title }}" data-gallery="portfolio-gallery-all" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="bi bi-images fs-1 text-secondary mb-2 d-block"></i>
                        <p>No gallery photographs published yet.</p>
                    </div>
                @endforelse
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold me-2">
                    <i class="bi bi-arrow-left me-1"></i> Back to Homepage
                </a>
                <a href="{{ route('login') }}" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" style="background-color: #009961; border: none;">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Access Portals
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
