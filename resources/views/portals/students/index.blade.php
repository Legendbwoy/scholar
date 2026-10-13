@extends('layouts.portal')

@section('title', 'Students Roster â€” Scholar SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Students Directory</h2>
        <p class="text-muted mb-0">Institutional Enrolled Students, Academic Records & Standing</p>
    </div>
    @if(auth()->user()->isAdmin())
    <div class="d-flex gap-2">
        <a href="{{ route('students.bulk') }}" class="btn btn-outline-success rounded-pill px-3 fw-bold">
            <i class="fa-solid fa-file-excel me-1"></i> Bulk Excel Import
        </a>
        <a href="{{ route('students.create') }}" class="btn btn-portal-primary rounded-pill px-4">
            <i class="fa-solid fa-user-plus me-1"></i> Enroll New Student
        </a>
    </div>
    @endif
</div>

<!-- Filters & Search Bar -->
<div class="portal-card p-3 mb-4">
    <form action="{{ route('students.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email, admission # or roll..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-4">
            <select name="class_id" class="form-select" onchange="this.form.submit()">
                <option value="">All Cohorts / Classes</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} (Section {{ $c->section }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-3 w-100">Filter</button>
            <a href="{{ route('students.index') }}" class="btn btn-light rounded-3">Reset</a>
        </div>
    </form>
</div>

<!-- Students Table -->
<div class="portal-card">
    <div class="table-responsive">
        <table class="table table-portal align-middle mb-0">
            <thead>
                <tr>
                    <th>Student Details</th>
                    <th>Admission #</th>
                    <th>Cohort / Class</th>
                    <th>Parent / Guardian</th>
                    <th>Attendance</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $s)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ asset($s->user->avatar ?? 'scholar/assets/images/event-01.jpg') }}" class="rounded-circle" style="width: 42px; height: 42px; object-fit: cover;">
                                <div>
                                    <div class="fw-bold text-dark">{{ $s->user->name }}</div>
                                    <small class="text-muted">{{ $s->user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border fw-bold">{{ $s->admission_number }}</span></td>
                        <td>
                            <div class="fw-semibold small">{{ $s->schoolClass->name ?? 'Unassigned' }}</div>
                            <small class="text-muted">Section {{ $s->schoolClass->section ?? 'N/A' }} â€¢ Roll {{ $s->roll_number }}</small>
                        </td>
                        <td>
                            @if($s->guardian)
                                <div class="small fw-semibold">{{ $s->guardian->user->name }}</div>
                                <small class="text-muted">{{ $s->guardian->user->phone ?? 'Phone on record' }}</small>
                            @else
                                <span class="text-muted small">Independent</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $s->attendance_rate >= 90 ? 'success' : ($s->attendance_rate >= 75 ? 'warning text-dark' : 'danger') }} rounded-pill px-3 py-1">
                                {{ $s->attendance_rate }}%
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $s->status === 'active' ? 'success' : ($s->status === 'suspended' ? 'danger' : 'secondary') }}-subtle text-{{ $s->status === 'active' ? 'success' : ($s->status === 'suspended' ? 'danger' : 'secondary') }} text-capitalize">
                                {{ $s->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('students.show', $s->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2" title="View 360 Profile">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('students.edit', $s->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2" title="Edit Student">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('students.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive or suspend student {{ $s->user->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Archive / Suspend">
                                        <i class="fa-solid fa-user-xmark"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-user-slash fs-2 d-block mb-2"></i> No student records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($students->hasPages())
        <div class="p-3 border-top">
            {{ $students->links() }}
        </div>
    @endif
</div>
@endsection