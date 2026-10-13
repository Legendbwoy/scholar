@extends('layouts.portal')

@section('title', 'Immutable Audit Trail — Scholar SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Cryptographic Audit Trail Ledger</h2>
        <p class="text-muted mb-0">Immutable, SHA-256 Hash-Chained Activity Records & Tamper-Evident Verification</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 border border-success">
            <i class="fa-solid fa-shield-halved me-1"></i> {{ $verificationStatus['status_label'] }}
        </span>
    </div>
</div>

<!-- Ledger Cryptographic Header Card -->
<div class="portal-card p-4 mb-4 text-white" style="background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);">
    <div class="row align-items-center g-3">
        <div class="col-md-8">
            <span class="badge bg-primary px-3 py-1 mb-2">{{ $verificationStatus['algorithm'] }}</span>
            <h4 class="fw-bold mb-1"><i class="fa-solid fa-link me-2 text-warning"></i>Blockchain-Lite Ledger Verification</h4>
            <p class="text-white-50 small mb-0">Every mutation to student enrollment, attendance records, role switching, and broadcast triggers recalculates a SHA-256 hash mathematically linked to the previous log entry.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="bg-white bg-opacity-10 p-3 rounded-3 d-inline-block text-start">
                <small class="d-block text-white-50">Verified Ledger Records</small>
                <h3 class="fw-bold mb-0 text-success">{{ $verificationStatus['total_checked'] }} / {{ $totalLogs }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="portal-card p-3 mb-4">
    <form action="{{ route('audit.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Search audit logs by description, IP address or entity..." value="{{ request('search') }}">
        </div>
        <div class="col-md-4">
            <select name="action" class="form-select" onchange="this.form.submit()">
                <option value="">All Security Actions</option>
                @foreach($actions as $act)
                    <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ $act }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-3 w-100">Filter</button>
            <a href="{{ route('audit.index') }}" class="btn btn-light rounded-3">Reset</a>
        </div>
    </form>
</div>

<!-- Audit Trail Table -->
<div class="portal-card">
    <div class="table-responsive">
        <table class="table table-portal align-middle mb-0">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Action</th>
                    <th>Actor / Operator</th>
                    <th>Description</th>
                    <th>IP / Endpoint</th>
                    <th>Cryptographic SHA-256 Hash</th>
                </tr>
            </thead>
            <tbody>
                @forelse($audits as $log)
                    <tr>
                        <td>
                            <strong class="d-block small text-dark">{{ $log->created_at->format('M d, Y') }}</strong>
                            <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border fw-bold">{{ $log->action }}</span>
                        </td>
                        <td>
                            <div class="small fw-semibold">{{ $log->user->name ?? 'System Automated' }}</div>
                            <small class="text-muted text-capitalize">{{ $log->user->role ?? 'Daemon' }}</small>
                        </td>
                        <td>
                            <span class="small">{{ $log->description }}</span>
                        </td>
                        <td><small class="text-muted">{{ $log->ip_address }}</small></td>
                        <td>
                            <div class="text-truncate" style="max-width: 200px; font-family: monospace; font-size: 11px;" title="{{ $log->record_hash }}">
                                <span class="badge bg-dark-subtle text-dark border">{{ substr($log->record_hash, 0, 16) }}...</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">No audit entries found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($audits->hasPages())
        <div class="p-3 border-top">
            {{ $audits->links() }}
        </div>
    @endif
</div>
@endsection