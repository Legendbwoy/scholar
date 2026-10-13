@extends('layouts.portal')

@section('title', 'Curriculum & Subjects — Scholar SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Curriculum Subjects Catalog</h2>
        <p class="text-muted mb-0">Accredited Academic Subjects, Credit Hours & Assigned Faculty</p>
    </div>
    @if(auth()->user()->isAdmin())
    <button type="button" class="btn btn-portal-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createSubjectModal">
        <i class="fa-solid fa-plus me-1"></i> Add Curriculum Subject
    </button>
    @endif
</div>

<div class="portal-card">
    <div class="table-responsive">
        <table class="table table-portal align-middle mb-0">
            <thead>
                <tr>
                    <th>Subject Name</th>
                    <th>Code</th>
                    <th>Grade / Cohort</th>
                    <th>Credit Hours</th>
                    <th>Faculty Lead</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subjects as $sub)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $sub->name }}</div>
                        </td>
                        <td><span class="badge bg-light text-dark border fw-bold">{{ $sub->code }}</span></td>
                        <td><span class="badge bg-primary-subtle text-primary">{{ $sub->schoolClass->name ?? 'Core' }}</span></td>
                        <td><strong>{{ $sub->credit_hours }} Credits</strong></td>
                        <td>
                            <div class="small fw-semibold">{{ $sub->teacher->user->name ?? 'Departmental Faculty' }}</div>
                            <small class="text-muted">{{ $sub->teacher->designation ?? '' }}</small>
                        </td>
                        <td><small class="text-muted">{{ Str::limit($sub->description, 70) }}</small></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Create Subject Modal -->
<div class="modal fade" id="createSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form action="{{ route('academic.subjects.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold">Add Curriculum Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Subject Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. World History & Global Politics" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Subject Code *</label>
                            <input type="text" name="code" class="form-control" placeholder="e.g. HIS-105" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Credit Hours *</label>
                            <input type="number" name="credit_hours" class="form-control" value="3" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Assigned Cohort</label>
                        <select name="school_class_id" class="form-select">
                            <option value="">General / All Classes</option>
                            @foreach($classes as $cl)
                                <option value="{{ $cl->id }}">{{ $cl->name }} (Section {{ $cl->section }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Faculty Lead</label>
                        <select name="teacher_id" class="form-select">
                            <option value="">Select Faculty...</option>
                            @foreach($teachers as $tch)
                                <option value="{{ $tch->id }}">{{ $tch->user->name }} ({{ $tch->designation }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Course Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Syllabus overview and goals..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-portal-primary rounded-pill px-4 fw-bold">Add Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection