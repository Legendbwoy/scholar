@extends('layouts.portal')

@section('title', 'LMS Courses & Materials — Scholar SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Courses & LMS Materials</h2>
        <p class="text-muted mb-0">Digital Learning Repository, Lecture Slides, Syllabi & Laboratory Notes</p>
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
    <div class="d-flex gap-2">
        <a href="{{ route('courses.create') }}" class="btn btn-portal-primary rounded-pill px-4">
            <i class="fa-solid fa-plus me-1"></i> Provision New Course
        </a>
    </div>
    @endif
</div>

<div class="row g-4 mb-4">
    @foreach($courses as $course)
        <div class="col-lg-4 col-md-6">
            <div class="portal-card h-100 overflow-hidden d-flex flex-column">
                <div class="position-relative" style="height: 180px;">
                    <img src="{{ asset($course->thumbnail ?? 'scholar/assets/images/course-01.jpg') }}" class="w-100 h-100 object-fit-cover">
                    <span class="position-absolute top-0 end-0 m-3 badge bg-dark px-3 py-2">{{ $course->code }}</span>
                    <span class="position-absolute bottom-0 start-0 m-3 badge bg-primary px-3 py-2">{{ $course->category }}</span>
                </div>
                <div class="p-4 d-flex flex-column flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted"><i class="fa-solid fa-users me-1"></i> {{ $course->schoolClass->name ?? 'All Cohorts' }}</small>
                        <span class="badge bg-warning-subtle text-dark border"><i class="fa-solid fa-folder-open me-1"></i> {{ $course->materials->count() }} Materials</span>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">{{ $course->title }}</h5>
                    <p class="text-muted small flex-grow-1">{{ Str::limit($course->description, 100) }}</p>

                    <hr class="my-3 opacity-25">

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset($course->teacher->user->avatar ?? 'scholar/assets/images/member-01.jpg') }}" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                            <small class="fw-semibold text-truncate" style="max-width: 110px;">{{ $course->teacher->user->name ?? 'Faculty' }}</small>
                        </div>
                        <div class="d-inline-flex gap-1">
                            <a href="{{ route('courses.show', $course->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                LMS Desk <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                            @if(auth()->user()->isAdmin() || (auth()->user()->isTeacher() && auth()->user()->staff?->id == $course->teacher_id))
                            <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2" title="Edit Course">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            @endif
                            @if(auth()->user()->isAdmin())
                            <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline confirm-action" 
                                  data-confirm-title="Delete Course Syllabus?" 
                                  data-confirm-text="Are you sure you want to permanently delete '{{ $course->title }}' ({{ $course->code }})? All course lecture materials will be removed." 
                                  data-confirm-btn="Delete Course" 
                                  data-confirm-color="#dc3545">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Delete Course">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($courses->hasPages())
    <div class="portal-card p-3">
        {{ $courses->links() }}
    </div>
@endif
@endsection