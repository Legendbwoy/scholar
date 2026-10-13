@extends('layouts.portal')

@section('title', 'Official Academic Report Card: ' . $student->user->name . ' — Scholar SMS')

@section('content')
<!-- Screen Action Bar (Hidden on print) -->
<div class="d-print-none mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <a href="{{ route('gradebook.reports', ['class_id' => $student->school_class_id, 'term' => $term]) }}" class="btn btn-sm btn-light border rounded-pill px-3 mb-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Class Registry
            </a>
            <h3 class="fw-bold mb-0 text-dark">Institutional Academic Transcript 360</h3>
            <p class="text-muted mb-0">Official Term Report Card & Authenticated Scholastic Dossier</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#editRemarksModal">
                <i class="fa-solid fa-signature me-1"></i> Edit Conduct & Remarks
            </button>
            @endif
            <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="window.print()" style="background-color: #009961; border-color: #009961;">
                <i class="fa-solid fa-print me-1"></i> Print / Save Official PDF
            </button>
        </div>
    </div>
</div>

<!-- Printable Report Card Paper Sheet -->
<div class="report-card-paper mx-auto p-4 p-md-5 mb-5 bg-white rounded-4 shadow-sm border position-relative" style="max-width: 960px;">
    <!-- Institutional Watermark -->
    <div class="report-watermark position-absolute top-50 start-50 translate-middle opacity-5 pointer-events-none text-center user-select-none" style="z-index: 0; pointer-events: none;">
        <i class="fa-solid fa-graduation-cap" style="font-size: 260px; color: #009961;"></i>
    </div>

    <div class="position-relative" style="z-index: 1;">
        <!-- Institutional Header & Masthead -->
        <div class="text-center pb-4 mb-4 border-bottom border-2 border-dark-subtle">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                <div class="p-2 rounded-3 text-white shadow-sm" style="width: 52px; height: 52px; display: grid; place-items: center; background-color: #009961;">
                    <i class="fa-solid fa-school fs-3"></i>
                </div>
                <div class="text-start">
                    <h2 class="fw-bolder mb-0 text-dark tracking-wide" style="font-family: 'Outfit', sans-serif; letter-spacing: -0.5px;">SCHOLAR ACADEMY OF ADVANCED SCIENCES</h2>
                    <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 1px;">Office of Institutional Examination & Academic Records</div>
                </div>
            </div>
            <div class="text-muted small">
                100 University Boulevard &bull; Accredited Institutional Board of Secondary Education &bull; License #SCH-9941
            </div>
            <div class="badge bg-dark text-white text-uppercase px-3 py-1.5 mt-2 fw-bold tracking-wider" style="letter-spacing: 1.5px; font-size: 11px;">
                Official Scholastic Record & Bullion Transcript
            </div>
        </div>

        <!-- Student Dossier Snapshot Header -->
        <div class="bg-light rounded-4 p-4 mb-4 border">
            <div class="row g-3 align-items-center">
                <div class="col-md-2 text-center text-md-start">
                    <img src="{{ asset($student->user->avatar ?? 'scholar/assets/images/event-01.jpg') }}" class="rounded-4 shadow-sm border border-2 border-white" style="width: 90px; height: 90px; object-fit: cover;">
                </div>
                <div class="col-md-5">
                    <span class="text-muted small fw-bold text-uppercase d-block" style="font-size: 11px;">Candidate Name</span>
                    <h4 class="fw-bold text-dark mb-1">{{ $student->user->name }}</h4>
                    <div class="text-muted small mb-1">Email: <strong>{{ $student->user->email }}</strong></div>
                    <div class="text-muted small">Guardian: <strong>{{ $student->guardian->user->name ?? 'Independent' }} ({{ $student->guardian->relationship ?? 'Parent' }})</strong></div>
                </div>
                <div class="col-md-5">
                    <div class="row g-2 small">
                        <div class="col-6">
                            <span class="text-muted d-block">Admission No:</span>
                            <span class="badge bg-white text-dark border fw-bold">{{ $student->admission_number }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Class Roll No:</span>
                            <strong class="text-dark">{{ $student->roll_number ?? '01' }}</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Class / Section:</span>
                            <strong class="text-dark">{{ $student->schoolClass->name ?? 'Class' }} (Sec {{ $student->schoolClass->section ?? 'A' }})</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Academic Term:</span>
                            <strong class="text-primary">{{ $term }} &bull; {{ $year }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scholastic Standing Summary KPIs -->
        <div class="row g-3 mb-4 text-center">
            <div class="col-6 col-md-3">
                <div class="p-3 bg-white rounded-3 border shadow-2xs">
                    <span class="text-muted small fw-semibold d-block">Cumulative GPA</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ number_format($reportData['cumulative_gpa'], 2) }}</h3>
                    <small class="text-muted" style="font-size: 11px;">Scale of 4.00</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-white rounded-3 border shadow-2xs">
                    <span class="text-muted small fw-semibold d-block">Class Standing</span>
                    <h3 class="fw-bold mb-0 text-dark">
                        {{ $reportData['class_rank'] }}<span class="fs-6 text-muted"> / {{ $reportData['total_students_in_class'] }}</span>
                    </h3>
                    <small class="badge bg-warning-subtle text-dark border" style="font-size: 10px;">
                        Top {{ round(($reportData['class_rank'] / max(1, $reportData['total_students_in_class'])) * 100) }}% Rank
                    </small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-white rounded-3 border shadow-2xs">
                    <span class="text-muted small fw-semibold d-block">Aggregate Score</span>
                    <h3 class="fw-bold mb-0 text-success">{{ $reportData['overall_percentage'] }}%</h3>
                    <small class="badge bg-success-subtle text-success border" style="font-size: 10px;">
                        Grade {{ $reportData['overall_letter'] }}
                    </small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-white rounded-3 border shadow-2xs">
                    <span class="text-muted small fw-semibold d-block">Punctuality & Presence</span>
                    <h3 class="fw-bold mb-0 text-info">{{ $reportData['attendance_rate'] }}%</h3>
                    <small class="text-muted" style="font-size: 11px;">Verified Register</small>
                </div>
            </div>
        </div>

        <!-- Curriculum Subject Breakdown Table -->
        <div class="mb-4">
            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-book-bookmark text-primary me-2"></i> Curriculum Performance Breakdown</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0" style="font-size: 13px;">
                    <thead class="table-light text-center">
                        <tr>
                            <th class="text-start" style="width: 28%;">Curriculum Subject</th>
                            <th style="width: 7%;">Credits</th>
                            <th style="width: 14%;">Quiz (20%)</th>
                            <th style="width: 14%;">Midterm (30%)</th>
                            <th style="width: 14%;">Final (50%)</th>
                            <th style="width: 10%;">Total %</th>
                            <th style="width: 7%;">Grade</th>
                            <th style="width: 6%;">GPA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData['subjects'] as $subRes)
                            @php
                                $quizScore = null;
                                $midtermScore = null;
                                $finalScore = null;
                                foreach ($subRes['assessments'] as $a) {
                                    if ($a['type'] === 'quiz') $quizScore = $a['percentage'] . '%';
                                    if ($a['type'] === 'midterm') $midtermScore = $a['percentage'] . '%';
                                    if ($a['type'] === 'final') $finalScore = $a['percentage'] . '%';
                                }
                            @endphp
                            <tr>
                                <td>
                                    <strong class="text-dark d-block">{{ $subRes['subject']->name }}</strong>
                                    <small class="text-muted">{{ $subRes['subject']->code }} &bull; Instructor: {{ $subRes['teacher'] }}</small>
                                </td>
                                <td class="text-center fw-semibold">{{ $subRes['credits'] }}</td>
                                <td class="text-center">{{ $quizScore ?? 'N/A' }}</td>
                                <td class="text-center">{{ $midtermScore ?? 'N/A' }}</td>
                                <td class="text-center">{{ $finalScore ?? 'N/A' }}</td>
                                <td class="text-center fw-bold fs-6 text-dark">{{ $subRes['final_percentage'] }}%</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ in_array($subRes['letter_grade'], ['A+', 'A']) ? 'success' : (in_array($subRes['letter_grade'], ['B', 'C']) ? 'primary' : 'warning text-dark') }} fw-bold px-2 py-1">
                                        {{ $subRes['letter_grade'] }}
                                    </span>
                                </td>
                                <td class="text-center fw-bold text-primary">{{ number_format($subRes['gpa_point'], 1) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">Aggregate Weighted Evaluation:</td>
                            <td class="text-center fs-6 text-dark">{{ $reportData['overall_percentage'] }}%</td>
                            <td class="text-center text-success">{{ $reportData['overall_letter'] }}</td>
                            <td class="text-center text-primary fs-6">{{ number_format($reportData['cumulative_gpa'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Grading Legend Standard -->
        <div class="p-2 px-3 bg-light rounded-3 border mb-4 small d-flex justify-content-between align-items-center flex-wrap gap-2 text-muted" style="font-size: 11px;">
            <strong class="text-dark">Institutional Grading Key:</strong>
            <span><strong>A+</strong> (90-100% / 4.0) Distinction</span>
            <span><strong>A</strong> (80-89% / 3.7) Excellent</span>
            <span><strong>B</strong> (70-79% / 3.0) Commendable</span>
            <span><strong>C</strong> (60-69% / 2.0) Competent</span>
            <span><strong>D</strong> (50-59% / 1.0) Passing</span>
            <span><strong>F</strong> (&lt;50% / 0.0) Remediation</span>
        </div>

        <!-- Official Remarks & Endorsements -->
        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <div class="p-3 bg-white rounded-3 border h-100">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-dark small text-uppercase"><i class="fa-solid fa-pen-nib text-primary me-1"></i> Form Master & Faculty Remark:</strong>
                        <span class="badge bg-info-subtle text-info border">Conduct: {{ $reportData['conduct'] }}</span>
                    </div>
                    <p class="text-muted small mb-2 fst-italic">"{{ $reportData['teacher_remarks'] }}"</p>
                    <div class="mt-2 text-muted small border-top pt-1">
                        <strong>Principal Endorsement:</strong> {{ $reportData['principal_remarks'] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-white rounded-3 border h-100 text-center d-flex flex-column justify-content-between">
                    <div>
                        <strong class="text-dark small text-uppercase d-block mb-2">Institutional Authenticity Seal</strong>
                        <!-- Seal Graphic Mockup -->
                        <div class="mx-auto rounded-circle border border-2 border-primary-subtle p-2 text-primary" style="width: 65px; height: 65px; display: grid; place-items: center; background-color: #f7f6fc;">
                            <i class="fa-solid fa-certificate fs-2 text-warning"></i>
                        </div>
                    </div>
                    <div class="border-top pt-2 mt-2">
                        <div class="fw-bold small text-dark">Dr. Evelyn Vance, Ph.D.</div>
                        <small class="text-muted d-block" style="font-size: 10px;">Dean of Examination & Academic Affairs</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cryptographic Ledger Authenticity Footer -->
        <div class="border-top border-2 pt-3 d-flex justify-content-between align-items-center flex-wrap gap-3 small text-muted">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2 bg-light border rounded-3 text-center" style="width: 52px; height: 52px; display: grid; place-items: center;">
                    <i class="fa-solid fa-qrcode fs-3 text-dark"></i>
                </div>
                <div>
                    <div class="text-dark fw-bold" style="font-size: 11px;">CRYPTOGRAPHIC LEDGER VALIDATED</div>
                    <div class="font-monospace text-muted" style="font-size: 10px;">
                        HASH: {{ hash('sha256', $student->id . $term . $year . $reportData['cumulative_gpa']) }}
                    </div>
                    <div style="font-size: 10px;">Scan or verify at: scholar-academy.edu/verify/transcript/{{ $student->admission_number }}</div>
                </div>
            </div>
            <div class="text-md-end text-muted" style="font-size: 10px;">
                Issued on: {{ date('M d, Y') }} &bull; System ID: SCH-TX-{{ str_pad($student->id, 5, '0', STR_PAD_LEFT) }}<br>
                Official Student Record Document. Alteration or forgery is strictly prohibited by law.
            </div>
        </div>
    </div>
</div>

<!-- Edit Conduct Remarks Modal (Admin & Faculty) -->
@if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
<div class="modal fade d-print-none" id="editRemarksModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <form action="{{ route('gradebook.reports.remarks', $student->id) }}" method="POST">
                @csrf
                <input type="hidden" name="term" value="{{ $term }}">
                <input type="hidden" name="academic_year" value="{{ $year }}">
                <div class="modal-header border-0 text-white p-4" style="background: linear-gradient(135deg, #1a1a2e 0%, #2d2b52 100%);">
                    <h5 class="fw-bold mb-0 text-white"><i class="fa-solid fa-signature me-2"></i> Edit Report Remarks & Conduct</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Candidate Conduct Rating</label>
                        <select name="conduct" class="form-select border-light shadow-sm">
                            <option value="Exemplary" {{ $reportData['conduct'] === 'Exemplary' ? 'selected' : '' }}>Exemplary Conduct</option>
                            <option value="Very Good" {{ $reportData['conduct'] === 'Very Good' ? 'selected' : '' }}>Very Good</option>
                            <option value="Satisfactory" {{ $reportData['conduct'] === 'Satisfactory' ? 'selected' : '' }}>Satisfactory</option>
                            <option value="Needs Attention" {{ $reportData['conduct'] === 'Needs Attention' ? 'selected' : '' }}>Needs Attention</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Form Master / Teacher Remark</label>
                        <textarea name="teacher_remarks" class="form-control border-light shadow-sm" rows="3">{{ $reportData['teacher_remarks'] }}</textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-dark">Principal / Board Endorsement</label>
                        <textarea name="principal_remarks" class="form-control border-light shadow-sm" rows="2">{{ $reportData['principal_remarks'] }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" style="background-color: #009961; border-color: #009961;">
                        Save Endorsement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<style>
    @media print {
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            font-size: 12pt;
        }
        .portal-sidebar, .portal-topbar, .d-print-none, .alert, header {
            display: none !important;
        }
        .main-wrapper, .main-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .report-card-paper {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            max-width: 100% !important;
        }
        @page {
            size: A4 portrait;
            margin: 12mm 15mm;
        }
    }
</style>
@endsection
