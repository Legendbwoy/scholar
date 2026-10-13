@extends('layouts.portal')

@section('title', $course->title . ' — LMS Desk')

@section('content')
<div class="mb-4">
    <a href="{{ route('courses.index') }}" class="btn btn-sm btn-light mb-2"><i class="fa-solid fa-arrow-left me-1"></i> Back to Courses</a>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <span class="badge bg-primary px-3 py-1 mb-1">{{ $course->category }}</span>
            <span class="badge bg-dark px-3 py-1 mb-1">{{ $course->code }}</span>
            <h2 class="fw-bold mb-1">{{ $course->title }}</h2>
            <p class="text-muted mb-0">Cohort: <strong>{{ $course->schoolClass->name ?? 'General' }}</strong> • Faculty: <strong>{{ $course->teacher->user->name ?? 'Faculty' }}</strong></p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            @if(auth()->user()->isAdmin() || (auth()->user()->isTeacher() && auth()->user()->staff?->id == $course->teacher_id))
            <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-warning rounded-pill px-3 fw-bold">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Syllabus
            </a>
            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadMaterialModal">
                <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Material
            </button>
            <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this course?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger rounded-pill px-3">
                    <i class="fa-solid fa-trash me-1"></i> Delete
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Course Info Card -->
    <div class="col-lg-4">
        <div class="portal-card p-4">
            <img src="{{ asset($course->thumbnail ?? 'scholar/assets/images/course-01.jpg') }}" class="w-100 rounded-3 mb-3" style="height: 180px; object-fit: cover;">
            <h5 class="fw-bold text-dark mb-2">Curriculum Objectives</h5>
            <p class="text-muted small mb-4">{{ $course->description }}</p>

            <div class="p-3 bg-light rounded-3 small">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Units:</span>
                    <strong>{{ $course->total_lessons }} Lessons</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subject Code:</span>
                    <strong>{{ $course->subject->code ?? 'N/A' }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Active Term:</span>
                    <strong>{{ $course->semester }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Uploaded LMS Materials Repository -->
    <div class="col-lg-8">
        <div class="portal-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-folder-open text-primary me-2"></i>Uploaded LMS Materials ({{ $course->materials->count() }})</h5>
                <span class="badge bg-light text-muted border">Instant Download Enabled</span>
            </div>

            @forelse($course->materials as $mat)
                <div class="p-3 bg-light rounded-3 border mb-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 rounded-3 {{ $mat->file_type === 'pdf' ? 'bg-danger text-white' : ($mat->file_type === 'slides' ? 'bg-warning text-dark' : 'bg-primary text-white') }} fs-4">
                                <i class="fa-solid {{ $mat->file_type === 'pdf' ? 'fa-file-pdf' : ($mat->file_type === 'slides' ? 'fa-file-powerpoint' : 'fa-file-lines') }}"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">{{ $mat->title }}</h6>
                                <p class="text-muted small mb-1">{{ $mat->description ?? 'Official student study resource.' }}</p>
                                <div class="d-flex gap-2 small">
                                    <span class="badge bg-white text-dark border">{{ $mat->file_size }}</span>
                                    <span class="badge bg-white text-dark border">v{{ $mat->version }}</span>
                                    <span class="badge bg-white text-success border"><i class="fa-solid fa-download me-1"></i> {{ $mat->download_count }} downloads</span>
                                    <span class="text-muted">Uploaded {{ $mat->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-inline-flex gap-2 align-items-center">
                            <a href="{{ route('materials.download', $mat->id) }}" class="btn btn-primary rounded-pill px-3 fw-bold">
                                <i class="fa-solid fa-download me-1"></i> Download
                            </a>
                            @if(auth()->user()->isAdmin() || (auth()->user()->isTeacher() && auth()->user()->staff?->id == $course->teacher_id))
                            <form action="{{ route('materials.destroy', $mat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove material {{ $mat->title }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Delete Material">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-cloud-arrow-up fs-1 d-block mb-2 text-primary opacity-50"></i>
                    No materials uploaded for this course yet.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Upload Material Modal -->
<div class="modal fade" id="uploadMaterialModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form action="{{ route('courses.materials.store', $course->id) }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold">Upload LMS Learning Material</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Resource Title *</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Unit 3: Thermodynamics Problem Set & Lab Protocol" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">File Format Type *</label>
                            <select name="file_type" class="form-select" required>
                                <option value="pdf">PDF Document (.pdf)</option>
                                <option value="slides">Slide Deck (.pptx)</option>
                                <option value="document">Code / Archive (.zip/.doc)</option>
                                <option value="video">Lecture Video Link</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">File Size / Version</label>
                            <input type="text" name="file_size" class="form-control" value="4.8 MB">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Version</label>
                        <input type="text" name="version" class="form-control" value="1.0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Description / Student Instructions</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Key topics covered, homework submission dates, prerequisites..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Publish to LMS</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection