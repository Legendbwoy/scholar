@extends('layouts.portal')

@section('title', 'Campus Life Media Gallery — Scholar SMS')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('landing.gallery') }}" target="_blank" class="text-decoration-none">Public Gallery</a></li>
                <li class="breadcrumb-item active" aria-current="page">Media Gallery</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-1">Campus Life Media Gallery</h2>
        <p class="text-muted mb-0">Manage visual media, laboratory photographs, athletic facilities, and symposium archives displayed on the public landing portal.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('landing.gallery') }}" target="_blank" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Public Gallery
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadMediaModal">
            <i class="fa-solid fa-cloud-arrow-up me-1"></i> Add Media Item
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert">
    <i class="fa-solid fa-circle-check fs-5 me-2"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(isset($errors) && $errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
    <div class="fw-bold mb-1"><i class="fa-solid fa-circle-xmark me-1"></i> Validation Errors:</div>
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Stats Counters -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="portal-card p-3 border-start border-4 border-primary">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Total Media Assets</small>
            <div class="fs-4 fw-bold text-dark mt-1">{{ $stats['total'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="portal-card p-3 border-start border-4 border-warning">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Featured On Landing Page</small>
            <div class="fs-4 fw-bold text-warning mt-1">{{ $stats['featured'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="portal-card p-3 border-start border-4 border-info">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Campus Facility Categories</small>
            <div class="fs-4 fw-bold text-info mt-1">{{ $stats['categories_count'] }}</div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="portal-card p-3 mb-4">
    <form action="{{ route('content.gallery.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search by media title or caption..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">All Media Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i> Filter Media</button>
            @if(request()->hasAny(['search', 'category', 'featured']))
                <a href="{{ route('content.gallery.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Gallery Grid -->
<div class="row g-4 mb-4">
    @forelse($galleryItems as $item)
    <div class="col-lg-4 col-md-6">
        <div class="portal-card p-0 h-100 overflow-hidden shadow-sm d-flex flex-column border">
            <!-- Media Image Container -->
            <div class="position-relative overflow-hidden" style="height: 220px; background-color: #f1f3f7;">
                <img src="{{ $item->image_url }}" class="w-100 h-100 object-fit-cover" alt="{{ $item->title }}">
                <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white border border-light border-opacity-25 rounded-pill px-2.5 py-1">
                    {{ $item->category }}
                </span>
                @if($item->is_featured)
                <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark fw-bold rounded-pill shadow-sm">
                    <i class="fa-solid fa-star me-1"></i> Featured
                </span>
                @endif
            </div>

            <!-- Content Details -->
            <div class="p-3 flex-grow-1 d-flex flex-column justify-content-between">
                <div>
                    <h6 class="fw-bold text-dark mb-1">{{ $item->title }}</h6>
                    <p class="text-muted small mb-3">{{ $item->caption ?: 'No caption provided.' }}</p>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <!-- Toggle Featured Status -->
                    <form action="{{ route('content.gallery.toggle-featured', $item->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none {{ $item->is_featured ? 'text-warning' : 'text-muted' }}" title="Click to toggle landing page highlight">
                            <i class="fa-solid fa-star me-1"></i>
                            <small class="fw-semibold">{{ $item->is_featured ? 'Highlighted' : 'Make Featured' }}</small>
                        </button>
                    </form>

                    <!-- Edit / Delete Actions -->
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editMediaModal{{ $item->id }}" title="Edit Media">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteMediaModal{{ $item->id }}" title="Delete Media">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Media Modal -->
    <div class="modal fade" id="editMediaModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('content.gallery.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i> Edit Media Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Media Title *</label>
                            <input type="text" name="title" class="form-control" value="{{ $item->title }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Facility / Category *</label>
                            <select name="category" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ $item->category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description / Caption</label>
                            <textarea name="caption" class="form-control" rows="2">{{ $item->caption }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Replace Image File</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <img src="{{ $item->image_url }}" class="rounded border" style="width: 70px; height: 50px; object-fit: cover;">
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                            <small class="text-muted">Upload high-res JPG, PNG, or WEBP (Max 4MB). Current: {{ $item->image_path }}</small>
                        </div>
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="editFeat{{ $item->id }}" value="1" {{ $item->is_featured ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="editFeat{{ $item->id }}">Feature on Landing Page Showcase</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Save Media</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Media Modal -->
    <div class="modal fade" id="deleteMediaModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('content.gallery.destroy', $item->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i> Confirm Media Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <img src="{{ $item->image_url }}" class="rounded shadow-sm mb-3 border" style="width: 140px; height: 90px; object-fit: cover;">
                        <h6 class="fw-bold mb-1">{{ $item->title }}</h6>
                        <p class="text-muted small mb-0">Are you sure you want to permanently delete this media asset? It will be removed from all public galleries.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4">Permanently Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="portal-card text-center py-5 text-muted">
            <i class="fa-regular fa-images fs-1 mb-3 d-block text-secondary"></i>
            <h5>No media assets found</h5>
            <p class="text-muted">Start creating campus life highlights to enrich your public website gallery.</p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadMediaModal">
                <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload First Image
            </button>
        </div>
    </div>
    @endforelse
</div>

@if($galleryItems->hasPages())
<div class="portal-card p-3 d-flex justify-content-between align-items-center">
    <small class="text-muted">Showing {{ $galleryItems->firstItem() }} to {{ $galleryItems->lastItem() }} of {{ $galleryItems->total() }} items</small>
    {{ $galleryItems->links() }}
</div>
@endif

<!-- Upload Media Modal -->
<div class="modal fade" id="uploadMediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('content.gallery.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-cloud-arrow-up me-2 text-primary"></i> Upload Media Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Media Title *</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Modern Olympic Swimming Complex" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Facility / Category *</label>
                        <select name="category" class="form-select" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description / Caption</label>
                        <textarea name="caption" class="form-control" rows="2" placeholder="Brief context or description of this facility or moment..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Image File *</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">High-res JPG, PNG, or WEBP (Max 4MB). Or specify existing asset path below if preferred.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Or Pre-existing Asset Path</label>
                        <input type="text" name="image_path" class="form-control form-control-sm" placeholder="e.g. scholar/assets/images/banner-item-01.jpg">
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="uploadFeat" value="1" checked>
                        <label class="form-check-label fw-semibold" for="uploadFeat">Feature on Landing Page Showcase</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
