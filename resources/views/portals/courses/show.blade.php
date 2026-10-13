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
            <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline confirm-action" 
                  data-confirm-title="Delete Academic Course?" 
                  data-confirm-text="Deleting '{{ $course->title }}' ({{ $course->code }}) will purge associated course files and syllabi from student portals." 
                  data-confirm-btn="Delete Course" 
                  data-confirm-color="#dc3545">
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
                            <form action="{{ route('materials.destroy', $mat->id) }}" method="POST" class="d-inline confirm-action" 
                                  data-confirm-title="Remove Learning Material?" 
                                  data-confirm-text="Are you sure you want to remove '{{ $mat->title }}' from the digital LMS library?" 
                                  data-confirm-btn="Remove Material" 
                                  data-confirm-color="#dc3545">
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <form action="{{ route('courses.materials.store', $course->id) }}" method="POST" id="uploadLmsMaterialForm">
                @csrf
                <div class="modal-header border-0 text-white p-4" style="background: linear-gradient(135deg, #1a1a2e 0%, #2d2b52 100%);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 rounded-3 bg-success text-white shadow-sm" style="width: 44px; height: 44px; display: grid; place-items: center;">
                            <i class="fa-solid fa-cloud-arrow-up fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-white">Publish Course Asset to Digital LMS</h5>
                            <small class="text-white-50">Upload syllabi, lecture decks, assignments or laboratory protocols</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Resource Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control border-light shadow-sm" placeholder="e.g. Unit 4: Kinetic Molecular Theory — Lecture Slides & Problem Set" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Material Category <span class="text-danger">*</span></label>
                            <select name="file_type" class="form-select border-light shadow-sm" required>
                                <option value="pdf">📄 PDF Document (.pdf)</option>
                                <option value="slides">📊 Slide Deck (.pptx)</option>
                                <option value="document">📦 Worksheet / Archive (.zip/.docx)</option>
                                <option value="video">🎥 Multimedia Video Stream</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-dark">File Size</label>
                            <input type="text" name="file_size" class="form-control border-light shadow-sm" value="5.2 MB">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-dark">Syllabus Version</label>
                            <input type="text" name="version" class="form-control border-light shadow-sm" value="v2.1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Digital Asset Dropzone Preview</label>
                        <div class="border border-2 border-dashed rounded-4 p-4 text-center bg-white shadow-sm" style="border-color: #009961 !important;">
                            <i class="fa-solid fa-file-arrow-up fs-2 text-primary mb-2"></i>
                            <div class="fw-semibold text-dark">Drag and drop course material here, or browse</div>
                            <small class="text-muted d-block">Supported formats: PDF, PPTX, DOCX, ZIP, MP4 (Max institutional payload: 50 MB)</small>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-dark">Description & Student Instructions</label>
                        <textarea name="description" class="form-control border-light shadow-sm" rows="3" placeholder="Key learning outcomes, homework submission deadlines, suggested readings..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" onclick="Swal.fire({title: 'Publishing Material...', text: 'Encrypting and indexing learning asset in course catalog...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});">
                        <i class="fa-solid fa-check me-1"></i> Publish to LMS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
