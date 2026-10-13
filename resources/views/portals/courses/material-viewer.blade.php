@extends('layouts.portal')

@section('title', $material->title . ' — Academic LMS Reader')

@section('styles')
<style>
    .reader-top-bar {
        background: #ffffff;
        border-radius: 16px;
        padding: 16px 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.06);
    }
    .document-viewport {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        border: 1px solid rgba(0,0,0,0.06);
        overflow: hidden;
    }
    @media print {
        .portal-sidebar, .portal-navbar, .reader-top-bar, .reader-sidebar-col {
            display: none !important;
        }
        .portal-main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
    }
</style>
@endsection

@section('content')
<!-- Top Reader Navigation Bar -->
<div class="reader-top-bar mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('courses.show', $material->course_id) }}" class="btn btn-light rounded-circle shadow-sm p-2 text-dark" title="Back to Course">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">{{ $material->course->code ?? 'LMS' }}</span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border">Version {{ $material->version ?? 'v1.0' }}</span>
                    <span class="badge bg-{{ $material->file_type === 'pdf' ? 'danger' : ($material->file_type === 'slides' ? 'warning text-dark' : 'primary') }}">
                        {{ strtoupper($material->file_type) }}
                    </span>
                </div>
                <h4 class="fw-bold mb-0 text-dark">{{ $material->title }}</h4>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm" title="Print Material">
                <i class="fa-solid fa-print me-1"></i> Print
            </button>
            <a href="{{ route('materials.download', $material->id) }}" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm text-white" style="background-color: #009961; border: none;">
                <i class="fa-solid fa-download me-1"></i> Download ({{ $material->file_size }})
            </a>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Main Content Reader Viewport -->
    <div class="col-lg-8">
        <div class="document-viewport p-0">
            @if($material->file_type === 'pdf')
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                    <span class="small text-muted"><i class="fa-solid fa-file-pdf text-danger me-1"></i> High-Resolution Document Reader View</span>
                    <a href="{{ route('materials.preview', [$material->id, 'raw' => 1]) }}" target="_blank" class="small text-decoration-none text-success fw-bold">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open in New Tab
                    </a>
                </div>
                <iframe src="{{ route('materials.preview', [$material->id, 'raw' => 1]) }}" class="w-100" style="height: 80vh; border: none;"></iframe>
            @elseif($material->file_type === 'video')
                <div class="p-4 bg-black text-center">
                    <video controls autoplay class="w-100 rounded-3 shadow" style="max-height: 75vh;">
                        <source src="{{ route('materials.preview', [$material->id, 'raw' => 1]) }}">
                        Your browser does not support HTML5 video playback.
                    </video>
                </div>
            @else
                <!-- Document / Slides / Text Reader Dossier -->
                <div class="p-4 p-md-5">
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="p-3 rounded-3 bg-success bg-opacity-10 text-success fs-3">
                            <i class="fa-solid {{ $material->file_type === 'slides' ? 'fa-file-powerpoint' : 'fa-file-lines' }}"></i>
                        </div>
                        <div>
                            <span class="text-uppercase small text-muted fw-bold" style="letter-spacing: 1px;">Syllabus Course Resource</span>
                            <h3 class="fw-bold text-dark mb-0">{{ $material->title }}</h3>
                        </div>
                    </div>

                    @if($material->description)
                        <div class="p-4 rounded-4 bg-light border mb-4">
                            <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-circle-info text-success me-2"></i>Resource Synopsis & Instructions</h6>
                            <p class="text-muted mb-0" style="line-height: 1.7;">{{ $material->description }}</p>
                        </div>
                    @endif

                    <!-- Raw Document Transcript / Preview -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-book-open text-primary me-2"></i>Curriculum Content & Asset Transcript</h6>
                            <span class="badge bg-light text-muted border">Certified Scholar LMS Asset</span>
                        </div>
                        <div class="p-4 bg-light rounded-4 border font-monospace small" style="white-space: pre-wrap; line-height: 1.6; max-height: 45vh; overflow-y: auto;">
@if($material->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($material->file_path))
@php
    $rawContent = \Illuminate\Support\Facades\Storage::disk('public')->get($material->file_path);
@endphp
{{ substr($rawContent, 0, 4000) }}
@else
{{ $material->description ?? 'Official student learning material and certified curriculum asset.' }}
@endif
                        </div>
                    </div>

                    <div class="p-4 rounded-4 border bg-light text-center">
                        <i class="fa-solid fa-cloud-arrow-down fs-2 text-success mb-2"></i>
                        <h5 class="fw-bold text-dark mb-1">Download Offline Course Material</h5>
                        <p class="text-muted small mb-3">Save this resource to your local workstation for offline examination revision and study.</p>
                        <a href="{{ route('materials.download', $material->id) }}" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm text-white" style="background-color: #009961; border: none;">
                            <i class="fa-solid fa-download me-1"></i> Save {{ basename($material->file_path) }} ({{ $material->file_size }})
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Sidebar Metadata & Quick Links -->
    <div class="col-lg-4 reader-sidebar-col">
        <!-- Resource Metadata Dossier -->
        <div class="portal-card p-4 mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-circle-check text-success me-2"></i>Asset Specifications</h6>
            <div class="d-flex justify-content-between py-2 border-bottom small">
                <span class="text-muted">Academic Discipline:</span>
                <span class="fw-bold text-dark">{{ $material->course->title ?? 'General' }}</span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom small">
                <span class="text-muted">Instructor:</span>
                <span class="fw-bold text-dark">{{ $material->teacher->user->name ?? 'Faculty Office' }}</span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom small">
                <span class="text-muted">File Payload:</span>
                <span class="badge bg-light text-dark border">{{ $material->file_size }}</span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom small">
                <span class="text-muted">Revision Version:</span>
                <span class="fw-bold text-success">{{ $material->version ?? 'v1.0' }}</span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom small">
                <span class="text-muted">Student Accesses:</span>
                <span class="badge bg-success text-white">{{ $material->download_count }} Downloads</span>
            </div>
            <div class="d-flex justify-content-between py-2 small">
                <span class="text-muted">Publication Date:</span>
                <span class="text-dark">{{ $material->created_at->format('M d, Y') }}</span>
            </div>
        </div>

        <!-- Related Materials in this Course -->
        @if(isset($relatedMaterials) && $relatedMaterials->count() > 0)
        <div class="portal-card p-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-folder-tree text-primary me-2"></i>More in This Course</h6>
            <div class="d-grid gap-2">
                @foreach($relatedMaterials as $rel)
                    <div class="p-2.5 rounded-3 bg-light border d-flex justify-content-between align-items-center">
                        <div class="text-truncate me-2">
                            <a href="{{ route('materials.preview', $rel->id) }}" class="fw-semibold text-dark text-decoration-none small d-block text-truncate">
                                <i class="fa-solid {{ $rel->file_type === 'pdf' ? 'fa-file-pdf text-danger' : 'fa-file-lines text-primary' }} me-1"></i>
                                {{ $rel->title }}
                            </a>
                            <small class="text-muted" style="font-size: 11px;">{{ $rel->file_size }} • v{{ $rel->version }}</small>
                        </div>
                        <a href="{{ route('materials.preview', $rel->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-2 py-0 text-nowrap" style="font-size: 11px;">
                            Review
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
