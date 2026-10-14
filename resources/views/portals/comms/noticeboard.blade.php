@extends('layouts.portal')

@section('title', 'Campus Noticeboard & Advisories — Scholar SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1"><i class="fa-solid fa-bullhorn text-warning me-2"></i>Campus Noticeboard</h2>
        <p class="text-muted mb-0">Official Institutional Directives, Academic Bulletins & Safety Advisories</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-light text-dark border px-3 py-2">
            <i class="fa-solid fa-circle-check text-success me-1"></i> Active Channel: Verified Portal Alerts
        </span>
    </div>
</div>

<!-- Noticeboard Feed -->
<div class="row g-4">
    @forelse($broadcasts as $b)
        <div class="col-lg-6">
            <div class="portal-card h-100 p-4 d-flex flex-column border {{ $b->priority === 'emergency' ? 'border-danger' : ($b->priority === 'urgent' ? 'border-warning' : '') }} position-relative">
                <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge bg-{{ $b->priority === 'emergency' ? 'danger' : ($b->priority === 'urgent' ? 'warning text-dark' : 'dark') }} text-uppercase" style="font-size: 11px;">
                                <i class="fa-solid {{ $b->priority === 'emergency' ? 'fa-triangle-exclamation' : ($b->priority === 'urgent' ? 'fa-bell' : 'fa-bullhorn') }} me-1"></i>
                                {{ $b->priority }} Notice
                            </span>
                            <span class="badge bg-light text-muted border text-capitalize" style="font-size: 11px;">
                                <i class="fa-solid fa-users me-1"></i>
                                @if($b->target_audience === 'all')
                                    Schoolwide Bulletin
                                @elseif($b->target_audience === 'class')
                                    Cohort: {{ $b->schoolClass->name ?? 'Enrolled Class' }}
                                @else
                                    For {{ ucfirst($b->target_audience) }}
                                @endif
                            </span>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">{{ $b->renderTitleFor(auth()->user()) }}</h5>
                    </div>
                </div>

                <p class="text-muted small flex-grow-1 mb-3" style="line-height: 1.6;">
                    {{ Str::limit($b->renderFor(auth()->user()), 160) }}
                </p>

                <div class="pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2 mt-auto">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 rounded-circle bg-light text-primary" style="width: 32px; height: 32px; display: grid; place-items: center; font-size: 12px;">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div class="small">
                            <span class="text-muted d-block" style="font-size: 10px;">ISSUED BY</span>
                            <strong class="text-dark">{{ $b->sender->name ?? 'School Administration' }}</strong>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <small class="text-muted"><i class="fa-regular fa-clock me-1"></i>{{ $b->created_at->diffForHumans() }}</small>
                        <a href="{{ route('broadcasts.show', $b->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                            Read Bulletin <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="portal-card p-5 text-center">
                <div class="mb-3">
                    <i class="fa-solid fa-bullhorn fs-1 text-muted opacity-50"></i>
                </div>
                <h5 class="fw-bold text-dark">No Notices Currently Posted</h5>
                <p class="text-muted small mb-0">There are no announcements addressed to your cohort or profile at this time.</p>
            </div>
        </div>
    @endforelse
</div>

@if($broadcasts->hasPages())
    <div class="portal-card p-3 mt-4">
        {{ $broadcasts->links() }}
    </div>
@endif
@endsection
