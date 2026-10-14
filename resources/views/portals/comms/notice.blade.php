@extends('layouts.portal')

@section('title', $broadcast->title . ' — Official Notice')

@section('content')
<div class="mb-4">
    <a href="{{ route('broadcasts.index') }}" class="btn btn-sm btn-light mb-3">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Campus Noticeboard
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <!-- Official Directive Bulletin Document Card -->
        <div class="portal-card p-4 p-md-5 border-0 shadow-sm position-relative overflow-hidden">
            <!-- Priority Ribbon Header -->
            <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-{{ $broadcast->priority === 'emergency' ? 'danger' : ($broadcast->priority === 'urgent' ? 'warning text-dark' : 'primary-subtle text-primary') }} text-uppercase px-3 py-2">
                        <i class="fa-solid {{ $broadcast->priority === 'emergency' ? 'fa-triangle-exclamation' : ($broadcast->priority === 'urgent' ? 'fa-bell' : 'fa-circle-info') }} me-1"></i>
                        {{ $broadcast->priority }} Directive
                    </span>
                    <span class="badge bg-light text-dark border">Ref: #BCAST-{{ str_pad($broadcast->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="small text-muted">
                    <i class="fa-regular fa-calendar-check me-1"></i> Dispatched: <strong>{{ $broadcast->created_at->format('M d, Y • h:i A') }}</strong>
                </div>
            </div>

            <!-- School Directive Letterhead -->
            <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-3 bg-light border">
                <div class="p-3 rounded-circle text-white shadow-sm" style="background-color: #009961; width: 48px; height: 48px; display: grid; place-items: center;">
                    <i class="fa-solid fa-graduation-cap fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0">SCHOLAR INTERNATIONAL ACADEMY</h6>
                    <small class="text-muted">Office of Academic Affairs & Campus Administration</small>
                </div>
            </div>

            <!-- Notice Title & Meta -->
            <div class="mb-4">
                <h3 class="fw-bold text-dark mb-2">{{ $broadcast->title }}</h3>
                <div class="d-flex align-items-center gap-2 small text-muted flex-wrap">
                    <span><i class="fa-solid fa-users me-1 text-primary"></i> Target Audience: 
                        <strong class="text-capitalize text-dark">
                            @if($broadcast->target_audience === 'all')
                                Entire School Community
                            @elseif($broadcast->target_audience === 'class')
                                {{ $broadcast->schoolClass->name ?? 'Enrolled Class' }} Cohort
                            @else
                                {{ ucfirst($broadcast->target_audience) }}
                            @endif
                        </strong>
                    </span>
                    <span>•</span>
                    <span><i class="fa-solid fa-shield-check me-1 text-success"></i> Official Verification: <span class="text-success fw-semibold">Authenticated</span></span>
                </div>
            </div>

            <hr class="opacity-25 my-4">

            <!-- Body Message -->
            <div class="notice-body p-4 bg-light rounded-4 border mb-4" style="line-height: 1.8; font-size: 1.05rem; color: #2c3e50; white-space: pre-line;">
{{ $broadcast->message }}
            </div>

            <!-- Signatory Endorsement Footer -->
            <div class="pt-4 border-top d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded-circle bg-light border text-primary" style="width: 44px; height: 44px; display: grid; place-items: center;">
                        <i class="fa-solid fa-signature fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">{{ $broadcast->sender->name ?? 'Head of Administration' }}</div>
                        <small class="text-muted text-capitalize">{{ $broadcast->sender->role ?? 'Administration' }} • Authorized Dispatcher</small>
                    </div>
                </div>

                <div class="text-end">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="window.print()">
                        <i class="fa-solid fa-print me-1"></i> Print Directive
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
