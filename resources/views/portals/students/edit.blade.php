@extends('layouts.portal')

@section('title', 'Edit Student — Scholar SMS')

@section('content')
<div class="mb-4">
    <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-light mb-2"><i class="fa-solid fa-arrow-left me-1"></i> Back to Profile</a>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1">Edit Student: {{ $student->user->name }}</h2>
            <p class="text-muted mb-0">Admission No: <strong>{{ $student->admission_number }}</strong> • Enrolled: {{ $student->admission_date ?? '2024' }}</p>
        </div>
        <span class="badge bg-primary fs-6 px-3 py-2">{{ $student->admission_number }}</span>
    </div>
</div>

<div class="portal-card p-4 p-md-5">
    <form action="{{ route('students.update', $student->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-12">
                <h5 class="fw-bold text-primary border-bottom pb-2">1. Personal & Contact Information</h5>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Student Full Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $student->user->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Institutional Email *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $student->user->email) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Contact Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->user->phone) }}" placeholder="+1 (555) 000-0000">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Gender *</label>
                <select name="gender" class="form-select" required>
                    <option value="Male" {{ $student->gender === 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ $student->gender === 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ $student->gender === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Blood Group</label>
                <select name="blood_group" class="form-select">
                    @foreach(['O+', 'A+', 'B+', 'AB+', 'O-', 'A-', 'B-', 'AB-'] as $bg)
                        <option value="{{ $bg }}" {{ $student->blood_group === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 mt-4">
                <h5 class="fw-bold text-primary border-bottom pb-2">2. Academic Status & Guardian Linkage</h5>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Academic Cohort / Class *</label>
                <select name="school_class_id" class="form-select" required>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $student->school_class_id == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} (Section {{ $c->section }} - Room {{ $c->room_number }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Linked Guardian / Parent</label>
                <select name="guardian_id" class="form-select">
                    <option value="">Independent / No Guardian Linked</option>
                    @foreach($guardians as $g)
                        <option value="{{ $g->id }}" {{ $student->guardian_id == $g->id ? 'selected' : '' }}>
                            {{ $g->user->name }} ({{ $g->relationship }} - {{ $g->user->phone }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Roll Number</label>
                <input type="text" name="roll_number" class="form-control" value="{{ old('roll_number', $student->roll_number) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Date of Birth</label>
                <input type="date" name="dob" class="form-control" value="{{ old('dob', $student->dob) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Enrollment Standing / Status *</label>
                <select name="status" class="form-select" required>
                    <option value="active" {{ $student->status === 'active' ? 'selected' : '' }}>Active (Good Standing)</option>
                    <option value="inactive" {{ $student->status === 'inactive' ? 'selected' : '' }}>Inactive (On Leave)</option>
                    <option value="suspended" {{ $student->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="graduated" {{ $student->status === 'graduated' ? 'selected' : '' }}>Graduated / Alum</option>
                </select>
            </div>

            <div class="col-12 mt-4 text-end">
                <a href="{{ route('students.show', $student->id) }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                <button type="submit" class="btn btn-portal-primary rounded-pill px-5 fw-bold shadow">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes to Profile
                </button>
            </div>
        </div>
    </form>
</div>
@endsection