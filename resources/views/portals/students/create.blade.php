@extends('layouts.portal')

@section('title', 'Enroll Student — Scholar SMS')

@section('content')
<div class="mb-4">
    <a href="{{ route('students.index') }}" class="btn btn-sm btn-light mb-2"><i class="fa-solid fa-arrow-left me-1"></i> Back to Roster</a>
    <h2 class="fw-bold mb-1">New Student Institutional Enrollment</h2>
    <p class="text-muted mb-0">Register an incoming student, assign academic cohort, and link guardian profile.</p>
</div>

<div class="portal-card p-4 p-md-5">
    <form action="{{ route('students.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-12">
                <h5 class="fw-bold text-primary border-bottom pb-2">1. Student Identity & Contact</h5>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Full Legal Name *</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Benjamin Hayes" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Institutional Email *</label>
                <input type="email" name="email" class="form-control" placeholder="e.g. benjamin@scholar.edu" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Contact Phone</label>
                <input type="text" name="phone" class="form-control" placeholder="+1 (555) 000-0000">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Gender *</label>
                <select name="gender" class="form-select" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Blood Group</label>
                <select name="blood_group" class="form-select">
                    <option value="O+">O+</option>
                    <option value="A+">A+</option>
                    <option value="B+">B+</option>
                    <option value="AB+">AB+</option>
                    <option value="O-">O-</option>
                </select>
            </div>

            <div class="col-12 mt-4">
                <h5 class="fw-bold text-primary border-bottom pb-2">2. Academic Assignment & Guardian</h5>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Academic Cohort / Class *</label>
                <select name="school_class_id" class="form-select" required>
                    <option value="">Select Cohort...</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} (Section {{ $c->section }} - Room {{ $c->room_number }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Linked Guardian / Parent</label>
                <select name="guardian_id" class="form-select">
                    <option value="">Select Guardian (or leave blank if independent)...</option>
                    @foreach($guardians as $g)
                        <option value="{{ $g->id }}">{{ $g->user->name }} ({{ $g->relationship }} - {{ $g->user->phone }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Date of Birth</label>
                <input type="date" name="dob" class="form-control" value="2010-05-15">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Portal Password (Default: password)</label>
                <input type="password" name="password" class="form-control" value="password">
            </div>

            <div class="col-12 mt-4 text-end">
                <a href="{{ route('students.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                <button type="submit" class="btn btn-portal-primary rounded-pill px-5 fw-bold">
                    <i class="fa-solid fa-check me-1"></i> Seal Student Enrollment
                </button>
            </div>
        </div>
    </form>
</div>
@endsection