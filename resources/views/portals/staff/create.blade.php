@extends('layouts.portal')

@section('title', 'Onboard Faculty Member — Scholar SMS')

@section('content')
<div class="mb-4">
    <a href="{{ route('staff.index') }}" class="btn btn-sm btn-light mb-2"><i class="fa-solid fa-arrow-left me-1"></i> Back to Faculty Directory</a>
    <h2 class="fw-bold mb-1">Onboard New Academic Faculty</h2>
    <p class="text-muted mb-0">Provision credentials, assign academic department, and designate title.</p>
</div>

<div class="portal-card p-4 p-md-5">
    <form action="{{ route('staff.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Faculty Legal Name *</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Dr. Julian Croft" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Institutional Email *</label>
                <input type="email" name="email" class="form-control" placeholder="e.g. jcroft@scholar.edu" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Department Assignment *</label>
                <select name="department_id" class="form-select" required>
                    <option value="">Select Department...</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Academic Designation *</label>
                <input type="text" name="designation" class="form-control" placeholder="e.g. Senior Lecturer in Theoretical Physics" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Highest Qualification</label>
                <input type="text" name="qualification" class="form-control" placeholder="e.g. Ph.D. Applied Physics (Stanford)">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Specialization Focus</label>
                <input type="text" name="specialization" class="form-control" placeholder="e.g. Quantum Computing & Solid-State Physics">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Annual Salary Grade (USD)</label>
                <input type="number" name="salary" class="form-control" value="82000" step="500">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Direct Phone</label>
                <input type="text" name="phone" class="form-control" placeholder="+1 (555) 000-0000">
            </div>

            <div class="col-12 mt-4 text-end">
                <a href="{{ route('staff.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                <button type="submit" class="btn btn-portal-primary rounded-pill px-5 fw-bold">
                    <i class="fa-solid fa-check me-1"></i> Provision Faculty Credentials
                </button>
            </div>
        </div>
    </form>
</div>
@endsection