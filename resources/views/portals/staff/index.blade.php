@extends('layouts.portal')

@section('title', 'Faculty & Staff Roster — Scholar SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Faculty & Academic Staff</h2>
        <p class="text-muted mb-0">Certified Instructors, Heads of Department & Academic Officers</p>
    </div>
    <a href="{{ route('staff.create') }}" class="btn btn-portal-primary rounded-pill px-4">
        <i class="fa-solid fa-user-plus me-1"></i> Onboard Faculty Member
    </a>
</div>

<!-- Departments Summary Bar -->
<div class="row g-3 mb-4">
    @foreach($departments as $dept)
        <div class="col-md-4">
            <div class="p-3 bg-white rounded-3 border shadow-sm h-100">
                <span class="badge bg-light text-primary border mb-1">{{ $dept->code }}</span>
                <h6 class="fw-bold mb-1">{{ $dept->name }}</h6>
                <small class="text-muted">{{ $dept->staff_count }} Faculty Members</small>
            </div>
        </div>
    @endforeach
</div>

<!-- Staff Table -->
<div class="portal-card">
    <div class="table-responsive">
        <table class="table table-portal align-middle mb-0">
            <thead>
                <tr>
                    <th>Faculty Member</th>
                    <th>Employee ID</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Specialization / Credentials</th>
                    <th>Master Cohort</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staffMembers as $s)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ asset($s->user->avatar ?? 'scholar/assets/images/member-01.jpg') }}" class="rounded-circle" style="width: 44px; height: 44px; object-fit: cover;">
                                <div>
                                    <div class="fw-bold text-dark">{{ $s->user->name }}</div>
                                    <small class="text-muted">{{ $s->user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border fw-bold">{{ $s->employee_id }}</span></td>
                        <td><span class="badge bg-primary-subtle text-primary">{{ $s->department->name ?? 'Academics' }}</span></td>
                        <td><div class="small fw-semibold">{{ $s->designation }}</div></td>
                        <td>
                            <small class="d-block fw-semibold text-secondary">{{ $s->qualification }}</small>
                            <small class="text-muted">{{ $s->specialization }}</small>
                        </td>
                        <td>
                            @if($s->classesAsTeacher->count() > 0)
                                <span class="badge bg-success-subtle text-success border border-success">
                                    {{ $s->classesAsTeacher->first()->name }}
                                </span>
                            @else
                                <span class="text-muted small">None</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('staff.show', $s->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2" title="View Profile">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('staff.edit', $s->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2" title="Edit Faculty">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('staff.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Deactivate faculty member {{ $s->user->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Deactivate">
                                        <i class="fa-solid fa-user-xmark"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection