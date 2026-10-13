@extends('layouts.portal')

@section('title', 'Institutional Report Cards Registry — Scholar SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Class Report Cards Registry</h2>
        <p class="text-muted mb-0">Official Term Academic Transcripts, GPAs, Class Standing & Conduct Records</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('gradebook.index', ['class_id' => $selectedClassId, 'term' => $selectedTerm]) }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Assessments Matrix
        </a>
    </div>
</div>

<!-- Filters Bar -->
<div class="portal-card p-3 mb-4 shadow-sm border-0">
    <form action="{{ route('gradebook.reports') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <label class="form-label small text-muted fw-bold mb-1">Academic Cohort / Class</label>
            <select name="class_id" class="form-select border-light shadow-sm" onchange="this.form.submit()">
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }} (Section {{ $c->section }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted fw-bold mb-1">Term</label>
            <select name="term" class="form-select border-light shadow-sm" onchange="this.form.submit()">
                <option value="Term 1" {{ $selectedTerm === 'Term 1' ? 'selected' : '' }}>Term 1 (Fall)</option>
                <option value="Term 2" {{ $selectedTerm === 'Term 2' ? 'selected' : '' }}>Term 2 (Spring)</option>
                <option value="Term 3" {{ $selectedTerm === 'Term 3' ? 'selected' : '' }}>Term 3 (Summer)</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted fw-bold mb-1">Academic Year</label>
            <input type="text" name="academic_year" class="form-control border-light shadow-sm" value="{{ $selectedYear }}" readonly>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary rounded-3 w-100" style="background-color: #7a6ad8; border-color: #7a6ad8; margin-top: 24px;">Filter Registry</button>
        </div>
    </form>
</div>

<!-- Students Standing Table -->
<div class="portal-card shadow-sm border-0 overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-portal align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 70px;">Rank</th>
                    <th>Student Candidate</th>
                    <th>Admission #</th>
                    <th>Cumulative GPA</th>
                    <th>Term Score</th>
                    <th>Attendance</th>
                    <th>Conduct</th>
                    <th class="text-end">Official Transcript</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $st)
                    @php
                        $rep = $classReports[$st->id] ?? null;
                        $rank = $rep['class_rank'] ?? $loop->iteration;
                        $gpa = $rep['cumulative_gpa'] ?? 0;
                        $pct = $rep['overall_percentage'] ?? 0;
                        $letter = $rep['overall_letter'] ?? '--';
                        $att = $rep['attendance_rate'] ?? 100;
                        $conduct = $rep['conduct'] ?? 'Exemplary';
                    @endphp
                    <tr>
                        <td>
                            @if($rank === 1)
                                <span class="badge bg-warning text-dark border shadow-sm px-2.5 py-1 fw-bold">
                                    <i class="fa-solid fa-crown text-dark me-1"></i> 1st
                                </span>
                            @elseif($rank === 2)
                                <span class="badge bg-secondary-subtle text-dark border px-2.5 py-1 fw-bold">
                                    <i class="fa-solid fa-medal me-1"></i> 2nd
                                </span>
                            @elseif($rank === 3)
                                <span class="badge bg-warning-subtle text-dark border px-2.5 py-1 fw-bold">
                                    <i class="fa-solid fa-award me-1"></i> 3rd
                                </span>
                            @else
                                <span class="badge bg-light text-muted border fw-bold">{{ $rank }}th</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ asset($st->user->avatar ?? 'scholar/assets/images/event-01.jpg') }}" class="rounded-circle shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <div class="fw-bold text-dark">{{ $st->user->name }}</div>
                                    <small class="text-muted">{{ $st->schoolClass->name ?? 'Cohort' }} &bull; Roll {{ $st->roll_number ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border fw-bold">{{ $st->admission_number }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="fw-bold fs-6 text-primary">{{ number_format($gpa, 2) }}</div>
                                <small class="text-muted">/ 4.0</small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1.5">
                                <span class="fw-semibold text-dark">{{ $pct }}%</span>
                                <span class="badge bg-{{ in_array($letter, ['A+', 'A']) ? 'success' : (in_array($letter, ['B', 'C']) ? 'primary' : 'warning text-dark') }} rounded-pill px-2 py-0.5" style="font-size: 11px;">
                                    {{ $letter }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $att >= 90 ? 'success' : ($att >= 75 ? 'warning text-dark' : 'danger') }} rounded-pill px-2.5 py-1">
                                {{ $att }}%
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1">
                                {{ $conduct }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('gradebook.report-card', ['studentId' => $st->id, 'term' => $selectedTerm, 'academic_year' => $selectedYear]) }}" 
                               class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-sm" 
                               target="_blank" 
                               title="View Official Printable Report Card">
                                <i class="fa-solid fa-print me-1"></i> Print Report Card
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-file-excel fs-2 d-block mb-2"></i> No student records found in this cohort.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
