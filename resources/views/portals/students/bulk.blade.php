@extends('layouts.portal')

@section('title', 'Bulk Student Onboarding — Scholar SMS')

@section('content')
<!-- Header Area -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <a href="{{ route('students.index') }}" class="btn btn-sm btn-light mb-2">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Students Roster
        </a>
        <h2 class="fw-bold mb-1">Bulk Student Onboarding</h2>
        <p class="text-muted mb-0">Batch Student Registry Ingestion via Microsoft Excel (.xlsx) & CSV Worksheets</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('students.create') }}" class="btn btn-light rounded-pill px-3 fw-semibold">
            <i class="fa-solid fa-user-plus me-1"></i> Single Enrollment
        </a>
        <a href="{{ route('students.bulk.template', ['format' => 'xlsx']) }}" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
            <i class="fa-solid fa-file-excel me-1"></i> Download Template (.xlsx)
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 p-3 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-check fs-4"></i>
            <div>
                <strong>Import Complete!</strong> {{ session('success') }}
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('import_warnings'))
    <div class="alert alert-warning alert-dismissible fade show rounded-4 p-3 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-start gap-2">
            <i class="fa-solid fa-triangle-exclamation fs-4 mt-1"></i>
            <div>
                <strong>Notice: Some rows were skipped during processing:</strong>
                <ul class="mb-0 mt-2 small">
                    @foreach(session('import_warnings') as $warn)
                        <li>{{ $warn }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-4 p-3 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-exclamation fs-4"></i>
            <div>
                <strong>Upload Error:</strong> {{ $errors->first() }}
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    <!-- Left Column: Template Download & File Upload Form -->
    <div class="col-lg-7">
        
        <!-- Step 1: Download Standardized Template -->
        <div class="portal-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-circle p-2" style="width: 28px; height: 28px; display: grid; place-items: center;">1</span>
                    <h5 class="fw-bold mb-0 text-dark">Download Pre-Formatted Spreadsheet</h5>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                    <i class="fa-solid fa-shield-check me-1"></i> Verified Column Schema
                </span>
            </div>

            <p class="text-muted small mb-3">
                Our template includes formatted headers, pre-filled sample demonstration rows, and a dedicated <strong>Cohort Reference</strong> sheet containing all accredited classes in your institution.
            </p>

            <div class="p-3 bg-light rounded-3 border mb-3">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered text-center mb-0 small" style="font-size: 11px;">
                        <thead class="table-dark">
                            <tr>
                                <th>Full Name</th>
                                <th>Email Address</th>
                                <th>Phone Number</th>
                                <th>Class / Cohort</th>
                                <th>Gender</th>
                                <th>Date of Birth</th>
                                <th>Guardian Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-light text-muted">
                                <td>Sophia Martinez</td>
                                <td>sophia@scholar.edu</td>
                                <td>+1-555-0191</td>
                                <td>Grade 10 - Alpha</td>
                                <td>Female</td>
                                <td>2009-04-12</td>
                                <td>Robert Martinez</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('students.bulk.template', ['format' => 'xlsx']) }}" class="btn btn-outline-success rounded-pill px-3 fw-bold">
                    <i class="fa-solid fa-file-excel me-1"></i> Download .XLSX (Microsoft Excel)
                </a>
                <a href="{{ route('students.bulk.template', ['format' => 'csv']) }}" class="btn btn-outline-secondary rounded-pill px-3 fw-bold">
                    <i class="fa-solid fa-file-csv me-1"></i> Download .CSV (Universal Spreadsheet)
                </a>
            </div>
        </div>

        <!-- Step 2: Upload Populated Spreadsheet -->
        <div class="portal-card p-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge bg-primary rounded-circle p-2" style="width: 28px; height: 28px; display: grid; place-items: center;">2</span>
                <h5 class="fw-bold mb-0 text-dark">Upload & Process Student Batch</h5>
            </div>

            <form action="{{ route('students.bulk.upload') }}" method="POST" enctype="multipart/form-data" id="bulkUploadForm">
                @csrf

                <!-- File Drag & Drop Box -->
                <div class="mb-4">
                    <label class="form-label fw-semibold small text-muted">Select Filled Spreadsheet (.xlsx, .xls, or .csv)</label>
                    <div id="excelDropArea" class="p-4 border-2 border-dashed rounded-4 text-center bg-light" style="border: 2px dashed #009961; cursor: pointer; transition: all 0.25s ease;" onclick="document.getElementById('excelFileInput').click();">
                        <i class="fa-solid fa-cloud-arrow-up fs-1 text-primary mb-2"></i>
                        <h6 class="fw-bold mb-1">Click to select or drag and drop your file here</h6>
                        <small class="text-muted d-block mb-2">Supports Microsoft Excel (.xlsx, .xls) and CSV files up to 15MB</small>
                        <span id="selectedFileName" class="badge bg-primary-subtle text-primary border px-3 py-2 rounded-pill fw-semibold" style="display: none;">
                            <i class="fa-solid fa-file-excel me-1"></i> No file chosen
                        </span>
                        <input type="file" name="excel_file" id="excelFileInput" class="d-none" accept=".xlsx,.xls,.csv,.txt" required onchange="handleFileSelected(this)">
                    </div>
                </div>

                <!-- Batch Defaults Configuration -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Fallback Class / Cohort (Optional)</label>
                        <select name="default_class_id" class="form-select">
                            <option value="">Auto-resolve from spreadsheet column</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} (Grade {{ $c->numeric_grade }}, Section {{ $c->section }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 11px;">Applied if a student row has an unassigned cohort name.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Default Student Password</label>
                        <input type="text" name="default_password" class="form-control" value="password" placeholder="Default: password">
                        <small class="text-muted" style="font-size: 11px;">Students can change this password after initial login.</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-portal-primary w-100 rounded-pill py-2 fw-bold shadow-sm" id="uploadSubmitBtn">
                    <i class="fa-solid fa-file-import me-2"></i> Process Bulk Onboarding Batch
                </button>
            </form>
        </div>

    </div>

    <!-- Right Column: Schema Specifications & Audit Trail -->
    <div class="col-lg-5">
        
        <!-- Field Specifications Guide -->
        <div class="portal-card p-4 mb-4">
            <h5 class="fw-bold mb-3 text-dark">
                <i class="fa-solid fa-list-check text-primary me-2"></i>Column Ingestion Schema
            </h5>

            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0" style="font-size: 12px;">
                    <thead class="table-light">
                        <tr>
                            <th>Column Header</th>
                            <th class="text-center">Requirement</th>
                            <th>Formatting Rule</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong class="text-dark">Full Name</strong></td>
                            <td class="text-center"><span class="badge bg-danger-subtle text-danger">Required</span></td>
                            <td>Student legal name (max 150 chars)</td>
                        </tr>
                        <tr>
                            <td><strong class="text-dark">Email Address</strong></td>
                            <td class="text-center"><span class="badge bg-danger-subtle text-danger">Required</span></td>
                            <td>Must be unique institutional or personal email</td>
                        </tr>
                        <tr>
                            <td><strong class="text-dark">Class / Cohort</strong></td>
                            <td class="text-center"><span class="badge bg-warning-subtle text-dark">Recommended</span></td>
                            <td>Cohort Name or Section (e.g. <code>Grade 10 - Alpha</code>)</td>
                        </tr>
                        <tr>
                            <td><strong class="text-dark">Gender</strong></td>
                            <td class="text-center"><span class="badge bg-light text-muted border">Optional</span></td>
                            <td><code>Male</code>, <code>Female</code>, or <code>Other</code></td>
                        </tr>
                        <tr>
                            <td><strong class="text-dark">Date of Birth</strong></td>
                            <td class="text-center"><span class="badge bg-light text-muted border">Optional</span></td>
                            <td>Format: <code>YYYY-MM-DD</code> (e.g. 2009-04-12)</td>
                        </tr>
                        <tr>
                            <td><strong class="text-dark">Blood Group</strong></td>
                            <td class="text-center"><span class="badge bg-light text-muted border">Optional</span></td>
                            <td>e.g. <code>O+</code>, <code>A+</code>, <code>B+</code> (Default: O+)</td>
                        </tr>
                        <tr>
                            <td><strong class="text-dark">Guardian Name</strong></td>
                            <td class="text-center"><span class="badge bg-light text-muted border">Optional</span></td>
                            <td>Parent / Guardian contact person</td>
                        </tr>
                        <tr>
                            <td><strong class="text-dark">Guardian Email</strong></td>
                            <td class="text-center"><span class="badge bg-light text-muted border">Optional</span></td>
                            <td>Creates or links parent account for SMS alerts</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 p-3 bg-light rounded-3 small text-muted">
                <i class="fa-solid fa-circle-info text-primary me-1"></i>
                <strong>Automated Sequential Sequencing:</strong>
                Institutional Admission Numbers (<code>SCH-2026-XXX</code>) and Roll Numbers (<code>R-XXX</code>) are calculated and assigned sequentially for every imported row.
            </div>
        </div>

        <!-- Recent Bulk Operations Ledger -->
        <div class="portal-card p-4">
            <h5 class="fw-bold mb-3 text-dark">
                <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Recent Ingestion Operations
            </h5>

            @forelse($recentBatches as $batch)
                <div class="p-3 bg-light rounded-3 border mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge bg-success-subtle text-success border small">
                            <i class="fa-solid fa-circle-check me-1"></i> Batch Ingested
                        </span>
                        <small class="text-muted">{{ $batch->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="small fw-semibold text-dark">{{ $batch->description }}</div>
                    <div class="d-flex justify-content-between align-items-center mt-2" style="font-size: 11px;">
                        <span class="text-muted">Operator: <strong>{{ $batch->user->name ?? 'Administrator' }}</strong></span>
                        <span class="badge bg-primary-subtle text-primary font-monospace">{{ substr($batch->record_hash, 0, 10) }}...</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted small">
                    <i class="fa-solid fa-folder-open fs-3 d-block mb-2"></i> No bulk onboarding operations recorded yet.
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    function handleFileSelected(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const badge = document.getElementById('selectedFileName');
            badge.style.display = 'inline-block';
            badge.innerHTML = '<i class="fa-solid fa-file-excel me-1 text-success"></i> ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
        }
    }

    const excelDrop = document.getElementById('excelDropArea');
    if (excelDrop) {
        ['dragenter', 'dragover'].forEach(name => {
            excelDrop.addEventListener(name, (e) => {
                e.preventDefault();
                e.stopPropagation();
                excelDrop.style.backgroundColor = 'rgba(0, 153, 97, 0.08)';
                excelDrop.style.borderColor = '#007a4d';
            }, false);
        });

        ['dragleave', 'drop'].forEach(name => {
            excelDrop.addEventListener(name, (e) => {
                e.preventDefault();
                e.stopPropagation();
                excelDrop.style.backgroundColor = '#f8f9fa';
                excelDrop.style.borderColor = '#009961';
            }, false);
        });

        excelDrop.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                const input = document.getElementById('excelFileInput');
                input.files = files;
                handleFileSelected(input);
            }
        }, false);
    }

    document.getElementById('bulkUploadForm').addEventListener('submit', function() {
        const btn = document.getElementById('uploadSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Ingesting Students & Generating Admission Numbers...';
    });
</script>
@endsection