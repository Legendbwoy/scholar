@extends('layouts.portal')

@section('title', 'Institutional Question Banks — Reusable Question Repositories')

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lms.quizzes.index') }}" class="text-decoration-none">Assessments & Quizzes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Question Banks</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-box-archive text-success me-2"></i>Institutional Question Banks</h2>
            <p class="text-muted mb-0">Curated, editable, and reusable item banks organized by subject, course, and topic for tests, quizzes, and examinations.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('lms.banks.create') }}" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                <i class="fa-solid fa-plus-circle me-1"></i> New Question Bank
            </a>
            <a href="{{ route('lms.quizzes.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Assessments
            </a>
        </div>
    </div>
</div>

<!-- Metrics Strip -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="metric-card-standard">
            <div>
                <span class="metric-label">Active Question Banks</span>
                <h3 class="mb-0">{{ $totalBanks }}</h3>
            </div>
            <div class="metric-icon-wrap">
                <i class="fa-solid fa-folder-open"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="metric-card-standard">
            <div>
                <span class="metric-label">Archived Reusable Questions</span>
                <h3 class="mb-0">{{ $totalQuestions }}</h3>
            </div>
            <div class="metric-icon-wrap">
                <i class="fa-solid fa-box-archive"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="metric-card-standard">
            <div>
                <span class="metric-label">Supported Formats</span>
                <h3 class="mb-0">5 Types</h3>
            </div>
            <div class="metric-icon-wrap">
                <i class="fa-solid fa-layer-group"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filtering -->
<div class="portal-card p-4 mb-4">
    <form method="GET" action="{{ route('lms.banks.index') }}" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label small fw-bold text-muted text-uppercase">Search Keyword</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Bank title, topic, or description..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted text-uppercase">Subject Filter</label>
            <select name="subject_id" class="form-select">
                <option value="">All Academic Subjects</option>
                @foreach($subjects as $sub)
                    <option value="{{ $sub->id }}" {{ request('subject_id') == $sub->id ? 'selected' : '' }}>{{ $sub->name }} ({{ $sub->code }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark w-100 fw-bold">Filter</button>
            <a href="{{ route('lms.banks.index') }}" class="btn btn-light border px-3" title="Clear"><i class="fa-solid fa-rotate-left"></i></a>
        </div>
    </form>
</div>

<!-- Banks Grid -->
<div class="row g-4 mb-4">
    @forelse($banks as $b)
        <div class="col-md-6 col-xl-4">
            <div class="portal-card h-100 d-flex flex-column border-0 shadow-sm transition-all" style="border-radius: 14px; overflow: hidden;">
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                    <span class="badge bg-success bg-opacity-10 text-success fw-bold text-uppercase px-2 py-1">
                        <i class="fa-solid fa-folder me-1"></i> {{ $b->subject->code ?? ($b->course->code ?? 'General') }}
                    </span>
                    <span class="badge bg-white text-dark border">
                        {{ $b->questions->count() }} Questions
                    </span>
                </div>

                <div class="p-4 flex-grow-1">
                    <h5 class="fw-bold text-dark mb-1">{{ $b->title }}</h5>
                    @if($b->topic)
                        <span class="badge bg-light text-muted border mb-2"><i class="fa-solid fa-tag me-1"></i>{{ $b->topic }}</span>
                    @endif
                    <p class="text-muted small mb-3 line-clamp-2">
                        {{ $b->description ?? 'Institutional repository of curated questions ready for multi-assessment reuse.' }}
                    </p>

                    <div class="p-3 bg-light rounded-3 small text-muted">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Target Course:</span>
                            <strong>{{ $b->course->title ?? 'All Cohorts' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Author:</span>
                            <strong>{{ $b->creator->name ?? 'Faculty' }}</strong>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
                    <a href="{{ route('lms.banks.show', $b->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                        <i class="fa-solid fa-sliders me-1"></i> Manage Items ({{ $b->questions->count() }})
                    </a>
                    <form action="{{ route('lms.banks.destroy', $b->id) }}" method="POST" class="d-inline confirm-action" 
                          data-confirm-title="Delete Question Bank?" 
                          data-confirm-text="Purging this question bank will remove all contained items. Questions already cloned into quizzes will not be affected." 
                          data-confirm-btn="Delete Bank" 
                          data-confirm-color="#dc3545">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Delete Bank">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="portal-card p-5 text-center">
                <div class="p-4 bg-light rounded-circle d-inline-block mb-3">
                    <i class="fa-solid fa-box-open fs-1 text-muted"></i>
                </div>
                <h4 class="fw-bold text-dark">No Question Banks Found</h4>
                <p class="text-muted max-w-md mx-auto mb-4">Question Banks allow you to curate, edit, and reuse items across weekly quizzes, midterm tests, and final exams without typing them repeatedly.</p>
                <a href="{{ route('lms.banks.create') }}" class="btn btn-success rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-plus-circle me-1"></i> Create First Question Bank
                </a>
            </div>
        </div>
    @endforelse
</div>

@if($banks->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $banks->links() }}
    </div>
@endif
@endsection
