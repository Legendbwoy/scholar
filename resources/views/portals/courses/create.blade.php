@extends('layouts.portal')

@section('title', 'Provision New Course — Scholar SMS')

@section('content')
<div class="mb-4">
    <a href="{{ route('courses.index') }}" class="btn btn-sm btn-light mb-2"><i class="fa-solid fa-arrow-left me-1"></i> Back to Courses</a>
    <h2 class="fw-bold mb-1">Provision New Academic Course</h2>
    <p class="text-muted mb-0">Initialize an LMS course syllabus, assign cohort, curriculum subject, and faculty lead.</p>
</div>

<div class="portal-card p-4 p-md-5">
    <form action="{{ route('courses.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-12">
                <h5 class="fw-bold text-primary border-bottom pb-2">1. Course Details & Curriculum Code</h5>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold">Course Title *</label>
                <input type="text" name="title" class="form-control form-control-lg" placeholder="e.g. Modern Computational Robotics & Mechatronics" value="{{ old('title') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Unique Course Code *</label>
                <input type="text" name="code" class="form-control form-control-lg" placeholder="e.g. CRS-ROB-101" value="{{ old('code') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Curriculum Subject *</label>
                <select name="subject_id" class="form-select" required>
                    <option value="">Select Curriculum Subject...</option>
                    @foreach($subjects as $sb)
                        <option value="{{ $sb->id }}" {{ old('subject_id') == $sb->id ? 'selected' : '' }}>
                            {{ $sb->name }} ({{ $sb->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Assigned Cohort / Class *</label>
                <select name="school_class_id" class="form-select" required>
                    <option value="">Select Academic Cohort...</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ old('school_class_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} (Section {{ $c->section }} - Room {{ $c->room_number }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 mt-4">
                <h5 class="fw-bold text-primary border-bottom pb-2">2. Instruction & Term Scheduling</h5>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Lead Faculty Instructor *</label>
                <select name="teacher_id" class="form-select" required>
                    <option value="">Select Lead Faculty...</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" {{ old('teacher_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->user->name }} ({{ $t->designation }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Academic Term / Semester *</label>
                <input type="text" name="semester" class="form-control" value="{{ old('semester', $activeTerm->name ?? 'Term 1') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Academic Track / Category *</label>
                <select name="category" class="form-select" required>
                    <option value="Mathematics">Mathematics</option>
                    <option value="Core Sciences">Core Sciences</option>
                    <option value="Technology">Technology</option>
                    <option value="Humanities">Humanities</option>
                    <option value="Creative Arts">Creative Arts</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Planned Lesson Units</label>
                <input type="number" name="total_lessons" class="form-control" value="{{ old('total_lessons', 14) }}" min="1" max="100">
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold">Syllabus Overview & Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Course goals, laboratory schedule, and prerequisites...">{{ old('description') }}</textarea>
            </div>

            <div class="col-12 mt-4 text-end">
                <a href="{{ route('courses.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                <button type="submit" class="btn btn-portal-primary rounded-pill px-5 fw-bold shadow">
                    <i class="fa-solid fa-check me-1"></i> Provision Course to LMS
                </button>
            </div>
        </div>
    </form>
</div>
@endsection