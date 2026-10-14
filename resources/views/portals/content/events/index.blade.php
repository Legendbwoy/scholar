@extends('layouts.portal')

@section('title', 'Institutional Events & Symposiums — Scholar SMS')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('home') }}" target="_blank" class="text-decoration-none">Public Portal</a></li>
                <li class="breadcrumb-item active" aria-current="page">Events & Symposiums</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-1">Institutional Events & Symposiums</h2>
        <p class="text-muted mb-0">Publish and manage campus assemblies, academic olympiads, athletic meets, and conferences visible on the public landing page.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('home') }}#events" target="_blank" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Preview on Landing Page
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEventModal">
            <i class="fa-solid fa-calendar-plus me-1"></i> Create New Event
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
    <div class="col-md-3 col-6">
        <div class="portal-card p-3 border">
            <span class="metric-label" style="font-size: 11px;">Total Events</span>
            <div class="fs-4 fw-bold text-dark mt-1">{{ $stats['total'] }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="portal-card p-3 border">
            <span class="metric-label" style="font-size: 11px;">Upcoming Calendar</span>
            <div class="fs-4 fw-bold text-emerald mt-1">{{ $stats['upcoming'] }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="portal-card p-3 border">
            <span class="metric-label" style="font-size: 11px;">Published Live</span>
            <div class="fs-4 fw-bold text-dark mt-1">{{ $stats['published'] }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="portal-card p-3 border">
            <span class="metric-label" style="font-size: 11px;">Featured Highlights</span>
            <div class="fs-4 fw-bold text-dark mt-1">{{ $stats['featured'] }}</div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="portal-card p-3 mb-4">
    <form action="{{ route('content.events.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search event title, venue, or details..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">All Event Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published Live</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft / Hidden</option>
                <option value="featured" {{ request('status') === 'featured' ? 'selected' : '' }}>Featured Only</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i> Filter</button>
            @if(request()->hasAny(['search', 'category', 'status']))
                <a href="{{ route('content.events.index') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="fa-solid fa-rotate-left"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Events Table View -->
<div class="portal-card p-0 overflow-hidden shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-uppercase small text-muted">
                <tr>
                    <th class="ps-4">Event Banner & Title</th>
                    <th>Category</th>
                    <th>Schedule & Venue</th>
                    <th>Organizer</th>
                    <th>Public Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $event->banner_url }}" class="rounded-3 shadow-sm border" style="width: 70px; height: 50px; object-fit: cover;" alt="{{ $event->title }}">
                            <div>
                                <div class="fw-bold text-dark">{{ $event->title }}</div>
                                <div class="text-muted small text-truncate" style="max-width: 320px;">{{ $event->description }}</div>
                                @if($event->is_featured)
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill mt-1" style="font-size: 10px;">
                                        <i class="fa-solid fa-star me-1"></i> Featured Highlight
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border px-2 py-1">{{ $event->category }}</span>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">
                            <i class="fa-regular fa-calendar text-primary me-1"></i> {{ $event->event_date->format('M d, Y') }}
                        </div>
                        <div class="text-muted small">
                            <i class="fa-regular fa-clock me-1"></i> {{ $event->event_date->format('g:i A') }}
                            @if($event->end_date)
                                - {{ $event->end_date->format('g:i A') }}
                            @endif
                        </div>
                        <div class="text-muted small">
                            <i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $event->venue }}
                        </div>
                    </td>
                    <td>
                        <span class="small text-muted">{{ $event->organizer }}</span>
                    </td>
                    <td>
                        <form action="{{ route('content.events.toggle-publish', $event->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm p-0 border-0" title="Click to toggle publication">
                                @if($event->is_published)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                        <i class="fa-solid fa-circle-check me-1"></i> Published
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">
                                        <i class="fa-solid fa-eye-slash me-1"></i> Draft / Hidden
                                    </span>
                                @endif
                            </button>
                        </form>
                    </td>
                    <td class="text-end pe-4">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editEventModal{{ $event->id }}" title="Edit Event">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteEventModal{{ $event->id }}" title="Delete Event">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Edit Event Modal -->
                <div class="modal fade" id="editEventModal{{ $event->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <form action="{{ route('content.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i> Edit Institutional Event</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label class="form-label fw-semibold">Event Title *</label>
                                            <input type="text" name="title" class="form-control" value="{{ $event->title }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Event Category *</label>
                                            <select name="category" class="form-select" required>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat }}" {{ $event->category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Detailed Description *</label>
                                            <textarea name="description" class="form-control" rows="3" required>{{ $event->description }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Commencement Date & Time *</label>
                                            <input type="datetime-local" name="event_date" class="form-control" value="{{ $event->event_date->format('Y-m-d\TH:i') }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Concluding Date & Time</label>
                                            <input type="datetime-local" name="end_date" class="form-control" value="{{ $event->end_date ? $event->end_date->format('Y-m-d\TH:i') : '' }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Venue / Location *</label>
                                            <input type="text" name="venue" class="form-control" value="{{ $event->venue }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Organizing Body / Department</label>
                                            <input type="text" name="organizer" class="form-control" value="{{ $event->organizer }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Online Registration / Information Link</label>
                                            <input type="url" name="registration_link" class="form-control" value="{{ $event->registration_link }}" placeholder="https://...">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Update Banner Photograph (Optional)</label>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $event->banner_url }}" class="rounded border" style="width: 80px; height: 50px; object-fit: cover;">
                                                <input type="file" name="banner" class="form-control" accept="image/*">
                                            </div>
                                            <small class="text-muted">Upload high-res JPG, PNG, or WEBP (Max 3MB). Current: {{ $event->banner_image }}</small>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" name="is_published" id="editPub{{ $event->id }}" value="1" {{ $event->is_published ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="editPub{{ $event->id }}">Publish Live on Landing Page</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" name="is_featured" id="editFeat{{ $event->id }}" value="1" {{ $event->is_featured ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="editFeat{{ $event->id }}">Showcase as Hero / Featured Highlight</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Delete Event Modal -->
                <div class="modal fade" id="deleteEventModal{{ $event->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form action="{{ route('content.events.destroy', $event->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i> Confirm Event Deletion</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4 text-center">
                                    <div class="mb-3">
                                        <i class="fa-solid fa-calendar-xmark text-danger fs-1"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">{{ $event->title }}</h5>
                                    <p class="text-muted mb-0">Are you sure you want to permanently delete this event? This will remove it from all public calendars and promotional listings.</p>
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
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fa-regular fa-calendar-xmark fs-2 mb-2 d-block"></i>
                        No institutional events found matching current criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($events->hasPages())
    <div class="p-3 border-top d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} events</small>
        {{ $events->links() }}
    </div>
    @endif
</div>

<!-- Create New Event Modal -->
<div class="modal fade" id="createEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('content.events.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-calendar-plus me-2 text-primary"></i> Create Institutional Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Event Title *</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Annual International STEM & Robotics Exposition" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Category *</label>
                            <select name="category" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Event Description *</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Provide a compelling overview of this symposium, exhibition, or assembly..." required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Commencement Date & Time *</label>
                            <input type="datetime-local" name="event_date" class="form-control" value="{{ now()->addDays(7)->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Concluding Date & Time</label>
                            <input type="datetime-local" name="end_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Venue / Location *</label>
                            <input type="text" name="venue" class="form-control" placeholder="e.g. Grand STEM Quadrangle & Main Auditorium" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Organizing Body / Department</label>
                            <input type="text" name="organizer" class="form-control" placeholder="e.g. Faculty of Engineering & Computing" value="Office of Academic Affairs">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Online Registration / Information Link (Optional)</label>
                            <input type="url" name="registration_link" class="form-control" placeholder="https://scholar.edu/events/register">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Banner Photograph Upload</label>
                            <input type="file" name="banner" class="form-control" accept="image/*">
                            <small class="text-muted">High-resolution JPG, PNG, or WEBP (Max 3MB). If blank, a standard campus banner will be assigned.</small>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_published" id="createPub" value="1" checked>
                                <label class="form-check-label fw-semibold" for="createPub">Publish Live on Landing Page</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="createFeat" value="1" checked>
                                <label class="form-check-label fw-semibold" for="createFeat">Showcase as Featured Highlight</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-paper-plane me-1"></i> Publish Event</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
