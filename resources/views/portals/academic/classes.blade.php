@extends('layouts.portal')

@section('title', 'Academic Cohorts & Classes — Scholar SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Academic Cohorts & Classes</h2>
        <p class="text-muted mb-0">Grade Cohorts, Classroom Sections, Capacities & Assigned Form Masters</p>
    </div>
    @if(auth()->user()->isAdmin())
    <button type="button" class="btn btn-portal-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createClassModal">
        <i class="fa-solid fa-plus me-1"></i> Add Academic Cohort
    </button>
    @endif
</div>

<div class="row g-4">
    @foreach($classes as $c)
        <div class="col-lg-4 col-md-6">
            <div class="portal-card p-4 h-100 border-top border-4 border-primary">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="badge bg-light text-primary border mb-1">Grade {{ $c->numeric_grade }}</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ $c->name }}</h4>
                        <small class="text-muted">Section {{ $c->section }} • Room {{ $c->room_number ?? 'TBD' }}</small>
                    </div>
                    <span class="badge bg-primary fs-6">{{ $c->students_count }} / {{ $c->capacity }}</span>
                </div>

                <div class="my-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Capacity Utilization</span>
                        <span>{{ round(($c->students_count / max($c->capacity, 1)) * 100) }}%</span>
                    </div>
                    <div class="progress" style="height: 7px;">
                        <div class="progress-bar bg-primary" style="width: {{ ($c->students_count / max($c->capacity, 1)) * 100 }}%"></div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 mb-3 small">
                    <div class="text-muted mb-1">Class Teacher (Form Master):</div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-chalkboard-user text-primary"></i>
                        <strong class="text-dark">{{ $c->classTeacher->user->name ?? 'Unassigned' }}</strong>
                    </div>
                </div>

                @php
                    $canMark = auth()->user()->isAdmin() || 
                               (auth()->user()->isTeacher() && (
                                   auth()->user()->staff?->id == $c->class_teacher_id || 
                                   (auth()->user()->staff && auth()->user()->staff->courses->where('school_class_id', $c->id)->count() > 0)
                               ));
                @endphp
                <div class="d-flex gap-2">
                    @if($canMark)
                    <a href="{{ route('attendance.mark', $c->id) }}" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                        <i class="fa-solid fa-clipboard-check me-1"></i> Mark Register
                    </a>
                    @endif
                    <a href="{{ route('students.index', ['class_id' => $c->id]) }}" class="btn btn-sm btn-light rounded-pill w-100">
                        View Students
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($classes->hasPages())
    <div class="portal-card p-3 mt-4">
        {{ $classes->links() }}
    </div>
@endif

@if(auth()->user()->isAdmin())
<!-- Create Class Modal -->
<div class="modal fade" id="createClassModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form action="{{ route('academic.classes.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold">Create New Academic Cohort</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Cohort Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Grade 10 - Beta" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Numeric Grade (1-12) *</label>
                            <input type="number" name="numeric_grade" class="form-control" value="10" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Section Letter *</label>
                            <input type="text" name="section" class="form-control" value="B" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Assigned Room</label>
                            <input type="text" name="room_number" class="form-control" placeholder="Room 105">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Capacity *</label>
                            <input type="number" name="capacity" class="form-control" value="35" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Class Teacher</label>
                        <select name="class_teacher_id" class="form-select">
                            <option value="">Select Instructor...</option>
                            @foreach($staffMembers as $sm)
                                <option value="{{ $sm->id }}">{{ $sm->user->name }} ({{ $sm->designation }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-portal-primary rounded-pill px-4 fw-bold">Create Cohort</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection