@extends('layouts.portal')

@section('title', 'Faculty Member Profile — Scholar SMS')

@section('content')
<div class="mb-4">
    <a href="{{ route('staff.index') }}" class="btn btn-sm btn-light mb-2"><i class="fa-solid fa-arrow-left me-1"></i> Back to Faculty Directory</a>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-0">Faculty Profile: {{ $staff->user->name }}</h2>
            <p class="text-muted mb-0">Employee ID: <strong>{{ $staff->employee_id }}</strong> • Department: <strong>{{ $staff->department->name ?? 'Academics' }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->isAdmin())
            <a href="{{ route('staff.edit', $staff->id) }}" class="btn btn-warning rounded-pill px-4 fw-bold">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Faculty
            </a>
            <form action="{{ route('staff.destroy', $staff->id) }}" method="POST" class="d-inline confirm-action" 
                  data-confirm-title="Deactivate Faculty Appointment?" 
                  data-confirm-text="Are you sure you want to deactivate faculty appointment for {{ $staff->user->name }} ({{ $staff->employee_id }})?" 
                  data-confirm-btn="Deactivate Faculty" 
                  data-confirm-color="#dc3545">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger rounded-pill px-3">
                    <i class="fa-solid fa-user-xmark me-1"></i> Deactivate
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="portal-card p-4 text-center">
            <img src="{{ $staff->user->avatar_url }}" class="rounded-circle shadow-sm mb-3" style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #f0f2f8;">
            <h4 class="fw-bold mb-1">{{ $staff->user->name }}</h4>
            <p class="text-muted small mb-2">{{ $staff->designation }}</p>
            <span class="badge bg-primary-subtle text-primary mb-3">{{ $staff->department->name ?? 'Academics' }}</span>

            <div class="p-3 bg-light rounded-3 text-start small">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Email:</span>
                    <strong>{{ $staff->user->email }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Phone:</span>
                    <strong>{{ $staff->user->phone ?? '+1 (555) 000-0000' }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Qualification:</span>
                    <strong>{{ $staff->qualification }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Appointment:</span>
                    <strong>{{ $staff->joining_date ?? '2022' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Assigned Courses & Subjects -->
        <div class="portal-card p-4 mb-4">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-book-bookmark text-primary me-2"></i>Curriculum Subjects & Assigned Courses</h5>
            <div class="table-responsive">
                <table class="table table-portal align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Subject / Course</th>
                            <th class="text-center">Code</th>
                            <th>Cohort</th>
                            <th class="text-center">LMS Materials</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff->courses as $c)
                            <tr>
                                <td><strong>{{ $c->title }}</strong></td>
                                <td class="text-center"><span class="badge bg-light text-dark border">{{ $c->code }}</span></td>
                                <td>{{ $c->schoolClass->name ?? 'General' }}</td>
                                <td class="text-center"><span class="badge bg-primary">{{ $c->materials->count() }} Materials</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">No course assignments linked to this profile.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection