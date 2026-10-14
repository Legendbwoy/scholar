@extends('layouts.portal')

@section('title', 'Create Question Bank — LMS')

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.quizzes.index') }}" class="text-decoration-none">Assessments</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.banks.index') }}" class="text-decoration-none">Question Banks</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create Bank</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-folder-plus text-success me-2"></i>Provision New Question Bank</h2>
            <p class="text-muted mb-0">Establish a centralized question repository classified by academic discipline and syllabus topic.</p>
        </div>
        <a href="{{ route('lms.banks.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Banks
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="portal-card p-4 p-md-5">
            <form action="{{ route('lms.banks.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="form-label small fw-bold text-uppercase text-muted">Question Bank Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror" 
                           placeholder="e.g. Advanced Data Structures & Algorithm Optimization Bank" value="{{ old('title') }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-uppercase text-muted">Syllabus Topic / Unit</label>
                        <input type="text" name="topic" class="form-control" placeholder="e.g. Dynamic Programming, Trees & Graphs" value="{{ old('topic') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-uppercase text-muted">Curriculum Subject</label>
                        <select name="subject_id" class="form-select">
                            <option value="">-- General / Multi-Disciplinary --</option>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}" {{ old('subject_id') == $sub->id ? 'selected' : '' }}>
                                    {{ $sub->name }} ({{ $sub->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-uppercase text-muted">Linked Course (Optional)</label>
                        <select name="course_id" class="form-select">
                            <option value="">-- Applicable to All Courses --</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}" {{ old('course_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->code }} - {{ $c->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-uppercase text-muted">Description / Scope</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Contextual notes on item difficulty, intended assessment levels, or bloom taxonomy tags...">{{ old('description') }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="{{ route('lms.banks.index') }}" class="btn btn-light px-4">Cancel</a>
                    <button type="submit" class="btn btn-success px-5 fw-bold rounded-pill shadow-sm">
                        Create Question Bank <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
