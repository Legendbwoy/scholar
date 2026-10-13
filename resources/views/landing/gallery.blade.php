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
                <li data-filter=".filter-campus">Campus Architecture</li>
                <li data-filter=".filter-labs">Laboratories & STEM</li>
                <li data-filter=".filter-sports">Athletics</li>
                <li data-filter=".filter-arts">Arts & Convocation</li>
            </ul>

            <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="100">
                @php
                    $sampleImages = [
                        ['cat' => 'filter-campus', 'title' => 'Main Academic Quadrangle', 'desc' => 'Historic architecture meeting modern digital classrooms.', 'img' => 'knightone/img/portfolio/portfolio-1.jpg'],
                        ['cat' => 'filter-labs', 'title' => 'Advanced Robotics Suite', 'desc' => 'Experiential STEM research & autonomous robotics.', 'img' => 'knightone/img/portfolio/portfolio-2.jpg'],
                        ['cat' => 'filter-sports', 'title' => 'Championship Aquatic Center', 'desc' => 'Olympic-standard athletic training facility.', 'img' => 'knightone/img/portfolio/portfolio-3.jpg'],
                        ['cat' => 'filter-arts', 'title' => 'Grand Symphony Auditorium', 'desc' => 'Convocations, symphony performances & public forums.', 'img' => 'knightone/img/portfolio/portfolio-4.jpg'],
                        ['cat' => 'filter-labs', 'title' => 'Computing & AI Data Lab', 'desc' => 'Algorithmic problem-solving and software design.', 'img' => 'knightone/img/portfolio/portfolio-5.jpg'],
                        ['cat' => 'filter-campus', 'title' => 'Central Research Library', 'desc' => 'Over 50,000 physical volumes and 24/7 digital archives.', 'img' => 'knightone/img/portfolio/portfolio-6.jpg'],
                        ['cat' => 'filter-sports', 'title' => 'Varsity Track & Field Oval', 'desc' => 'Track and field arena and multi-sport pavilions.', 'img' => 'knightone/img/portfolio/portfolio-7.jpg'],
                        ['cat' => 'filter-arts', 'title' => 'Ceramics & Visual Arts Studio', 'desc' => 'Sculpture, digital arts, and contemporary gallery.', 'img' => 'knightone/img/portfolio/portfolio-8.jpg'],
                        ['cat' => 'filter-labs', 'title' => 'Biomedical Microscopy Facility', 'desc' => 'Electron microscopy and cellular bio-investigation.', 'img' => 'knightone/img/portfolio/portfolio-9.jpg'],
                    ];
                @endphp

                @foreach($sampleImages as $img)
                    <div class="col-lg-4 col-md-6 portfolio-item isotope-item {{ $img['cat'] }}">
                        <div class="portfolio-content h-100 rounded-4 overflow-hidden border shadow-sm position-relative bg-white">
                            <img src="{{ asset($img['img']) }}" class="img-fluid w-100" style="height: 260px; object-fit: cover;" alt="{{ $img['title'] }}">
                            <div class="portfolio-info p-3">
                                <h6 class="fw-bold mb-1 text-dark">{{ $img['title'] }}</h6>
                                <p class="text-muted small mb-0">{{ $img['desc'] }}</p>
                                <a href="{{ asset($img['img']) }}" title="{{ $img['title'] }}" data-gallery="portfolio-gallery-all" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                            </div>
                        </div>
                    </div>
                @endforeach
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
