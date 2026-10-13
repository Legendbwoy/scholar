@extends('layouts.portal')

@section('title', 'Edit Faculty — Scholar SMS')

@section('content')
<div class="mb-4">
    <a href="{{ route('staff.show', $staff->id) }}" class="btn btn-sm btn-light mb-2"><i class="fa-solid fa-arrow-left me-1"></i> Back to Profile</a>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1">Edit Faculty: {{ $staff->user->name }}</h2>
            <p class="text-muted mb-0">Employee ID: <strong>{{ $staff->employee_id }}</strong> • Appointment: {{ $staff->joining_date ?? '2022' }}</p>
        </div>
        <span class="badge bg-primary fs-6 px-3 py-2">{{ $staff->employee_id }}</span>
    </div>
</div>

<div class="portal-card p-4 p-md-5">
    <form action="{{ route('staff.update', $staff->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-12">
                <h5 class="fw-bold text-primary border-bottom pb-2">Faculty Identification & Department</h5>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Faculty Legal Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $staff->user->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Institutional Email *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $staff->user->email) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Department Assignment *</label>
                <select name="department_id" class="form-select" required>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ $staff->department_id == $d->id ? 'selected' : '' }}>
                            {{ $d->name }} ({{ $d->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Academic Designation *</label>
                <input type="text" name="designation" class="form-control" value="{{ old('designation', $staff->designation) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Highest Qualification</label>
                <input type="text" name="qualification" class="form-control" value="{{ old('qualification', $staff->qualification) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Specialization Area</label>
                <input type="text" name="specialization" class="form-control" value="{{ old('specialization', $staff->specialization) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Annual Salary Grade (USD)</label>
                <input type="number" name="salary" class="form-control" value="{{ old('salary', $staff->salary) }}" step="500">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Direct Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $staff->user->phone) }}" placeholder="+1 (555) 000-0000">
            </div>

            <div class="col-12 mt-4 text-end">
                <a href="{{ route('staff.show', $staff->id) }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                <button type="submit" class="btn btn-portal-primary rounded-pill px-5 fw-bold shadow">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Update Faculty Record
                </button>
            </div>
        </div>
    </form>
</div>
@endsection