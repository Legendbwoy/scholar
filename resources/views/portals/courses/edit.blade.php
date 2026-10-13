@extends('layouts.portal')

@section('title', 'Edit Course — Scholar SMS')

@section('content')
<div class="mb-4">
    <a href="{{ route('courses.show', $course->id) }}" class="btn btn-sm btn-light mb-2"><i class="fa-solid fa-arrow-left me-1"></i> Back to LMS Desk</a>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1">Edit Course: {{ $course->title }}</h2>
            <p class="text-muted mb-0">Course Code: <strong>{{ $course->code }}</strong> • Active Term: {{ $course->semester }}</p>
        </div>
        <span class="badge bg-primary fs-6 px-3 py-2">{{ $course->code }}</span>
    </div>
</div>

<div class="portal-card p-4 p-md-5">
    <form action="{{ route('courses.update', $course->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-12">
                <h5 class="fw-bold text-primary border-bottom pb-2">Course Definition & Curriculum</h5>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold">Course Title *</label>
                <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title', $course->title) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Course Code *</label>
                <input type="text" name="code" class="form-control form-control-lg" value="{{ old('code', $course->code) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Curriculum Subject *</label>
                <select name="subject_id" class="form-select" required>
                    @foreach($subjects as $sb)
                        <option value="{{ $sb->id }}" {{ $course->subject_id == $sb->id ? 'selected' : '' }}>
                            {{ $sb->name }} ({{ $sb->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Assigned Cohort / Class *</label>
                <select name="school_class_id" class="form-select" required>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $course->school_class_id == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} (Section {{ $c->section }} - Room {{ $c->room_number }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Lead Faculty Instructor *</label>
                <select name="teacher_id" class="form-select" required>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" {{ $course->teacher_id == $t->id ? 'selected' : '' }}>
                            {{ $t->user->name }} ({{ $t->designation }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Academic Term / Semester *</label>
                <input type="text" name="semester" class="form-control" value="{{ old('semester', $course->semester) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Academic Track *</label>
                <select name="category" class="form-select" required>
                    @foreach(['Mathematics', 'Core Sciences', 'Technology', 'Humanities', 'Creative Arts'] as $cat)
                        <option value="{{ $cat }}" {{ $course->category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Total Planned Lessons</label>
                <input type="number" name="total_lessons" class="form-control" value="{{ old('total_lessons', $course->total_lessons) }}" min="1" max="100" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Publication Visibility *</label>
                <select name="is_published" class="form-select" required>
                    <option value="1" {{ $course->is_published ? 'selected' : '' }}>Published (Active on Student Portals)</option>
                    <option value="0" {{ !$course->is_published ? 'selected' : '' }}>Draft / Hidden</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Syllabus Overview & Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $course->description) }}</textarea>
            </div>

            <div class="col-12 mt-4 text-end">
                <a href="{{ route('courses.show', $course->id) }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                <button type="submit" class="btn btn-portal-primary rounded-pill px-5 fw-bold shadow">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Update Course Syllabus
                </button>
            </div>
        </div>
    </form>
</div>
@endsection