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
            <form action="{{ route('courses.materials.store', $course->id) }}" method="POST" enctype="multipart/form-data" id="uploadLmsMaterialForm">
                @csrf
                <div class="modal-header border-0 text-white p-4" style="background: linear-gradient(135deg, #0b1a13 0%, #152c20 100%); border-bottom: 2px solid #009961 !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 rounded-3 text-white shadow-sm" style="width: 44px; height: 44px; display: grid; place-items: center; background-color: #009961;">
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
                    <!-- Digital Asset Dropzone -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-dark d-flex justify-content-between align-items-center">
                            <span>Upload Material Document / Media Asset</span>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Supported: PDF, PPTX, DOCX, ZIP, MP4 (Max 50MB)</span>
                        </label>
                        <div class="border border-2 border-dashed rounded-4 p-4 text-center bg-white shadow-sm position-relative transition" 
                             id="materialDropArea"
                             style="border-color: #009961 !important; cursor: pointer; transition: all 0.25s ease;" 
                             onclick="document.getElementById('materialFileInput').click()">
                            <input type="file" name="material_file" id="materialFileInput" class="d-none" 
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.mp4,.mov,.txt,.png,.jpg" 
                                   onchange="handleMaterialFileSelected(this)">
                            
                            <div id="dropPromptState">
                                <i class="fa-solid fa-cloud-arrow-up fs-1 text-success mb-2"></i>
                                <div class="fw-bold text-dark fs-6">Click to browse or drag and drop course file here</div>
                                <small class="text-muted d-block">Automatic format parsing, metadata detection & indexing</small>
                            </div>

                            <div id="dropSelectedState" class="d-none">
                                <div class="p-3 rounded-3 bg-light border border-success d-inline-flex align-items-center gap-3">
                                    <div class="p-2 rounded-3 bg-success text-white">
                                        <i class="fa-solid fa-file-circle-check fs-4"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="fw-bold text-dark" id="dispFileName">sample.pdf</div>
                                        <div class="d-flex align-items-center gap-2 small text-muted">
                                            <span class="badge bg-success text-white" id="dispFileSize">0 KB</span>
                                            <span id="dispFileType">Document</span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-1 ms-2" onclick="clearMaterialFile(event)" title="Choose another file">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Resource Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="materialTitleInput" class="form-control border-light shadow-sm" placeholder="e.g. Unit 4: Kinetic Molecular Theory — Lecture Slides & Problem Set" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Material Category <span class="text-danger">*</span></label>
                            <select name="file_type" id="materialTypeSelect" class="form-select border-light shadow-sm" required>
                                <option value="pdf">📄 PDF Document (.pdf)</option>
                                <option value="slides">📊 Slide Deck (.pptx / .ppt)</option>
                                <option value="document">📦 Worksheet / Document / Archive (.docx / .zip)</option>
                                <option value="video">🎥 Multimedia Video Stream (.mp4)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-dark">File Size</label>
                            <input type="text" name="file_size" id="materialSizeInput" class="form-control border-light shadow-sm" value="5.2 MB">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-dark">Syllabus Version</label>
                            <input type="text" name="version" class="form-control border-light shadow-sm" value="v2.1">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-dark">Description & Student Instructions</label>
                        <textarea name="description" class="form-control border-light shadow-sm" rows="3" placeholder="Key learning outcomes, homework submission deadlines, suggested readings..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn rounded-pill px-4 fw-bold shadow-sm text-white" style="background-color: #009961;" id="publishMaterialSubmitBtn">
                        <i class="fa-solid fa-check me-1"></i> Publish to LMS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function handleMaterialFileSelected(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const dropPrompt = document.getElementById('dropPromptState');
            const dropSelected = document.getElementById('dropSelectedState');
            
            dropPrompt.classList.add('d-none');
            dropSelected.classList.remove('d-none');

            document.getElementById('dispFileName').innerText = file.name;
            
            // Format size
            const sizeStr = file.size >= 1048576 
                ? (file.size / 1048576).toFixed(1) + ' MB' 
                : (file.size / 1024).toFixed(1) + ' KB';
            document.getElementById('dispFileSize').innerText = sizeStr;
            document.getElementById('materialSizeInput').value = sizeStr;

            // Title autofill if empty
            const titleInput = document.getElementById('materialTitleInput');
            if (!titleInput.value || titleInput.value.trim() === '') {
                const cleanName = file.name.substring(0, file.name.lastIndexOf('.')) || file.name;
                titleInput.value = cleanName.replace(/[_-]/g, ' ');
            }

            // Auto-detect Category
            const ext = file.name.split('.').pop().toLowerCase();
            const typeSelect = document.getElementById('materialTypeSelect');
            if (['pdf'].includes(ext)) {
                typeSelect.value = 'pdf';
                document.getElementById('dispFileType').innerText = 'PDF Document';
            } else if (['ppt', 'pptx', 'key'].includes(ext)) {
                typeSelect.value = 'slides';
                document.getElementById('dispFileType').innerText = 'Slide Deck';
            } else if (['mp4', 'mov', 'avi', 'mkv', 'webm'].includes(ext)) {
                typeSelect.value = 'video';
                document.getElementById('dispFileType').innerText = 'Video Multimedia';
            } else {
                typeSelect.value = 'document';
                document.getElementById('dispFileType').innerText = 'Worksheet / Archive';
            }
        }
    }

    function clearMaterialFile(e) {
        if (e) e.stopPropagation();
        const input = document.getElementById('materialFileInput');
        input.value = '';
        document.getElementById('dropPromptState').classList.remove('d-none');
        document.getElementById('dropSelectedState').classList.add('d-none');
    }

    // Drag and Drop listeners
    const dropArea = document.getElementById('materialDropArea');
    if (dropArea) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropArea.style.backgroundColor = 'rgba(0, 153, 97, 0.08)';
                dropArea.style.borderColor = '#007a4d';
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropArea.style.backgroundColor = '#ffffff';
                dropArea.style.borderColor = '#009961';
            }, false);
        });

        dropArea.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                const input = document.getElementById('materialFileInput');
                input.files = files;
                handleMaterialFileSelected(input);
            }
        }, false);
    }

    // Submission spinner
    const lmsForm = document.getElementById('uploadLmsMaterialForm');
    if (lmsForm) {
        lmsForm.addEventListener('submit', function() {
            const btn = document.getElementById('publishMaterialSubmitBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Encrypting & Publishing Asset...';
        });
    }
</script>
@endsection
@endsection
