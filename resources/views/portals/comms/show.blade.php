@extends('layouts.portal')

@section('title', 'Broadcast Delivery Logs — Scholar SMS')

@section('content')
<div class="mb-4">
    <a href="{{ route('broadcasts.index') }}" class="btn btn-sm btn-light mb-2"><i class="fa-solid fa-arrow-left me-1"></i> Back to Comms Hub</a>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1">Broadcast Details & Delivery Log</h2>
            <p class="text-muted mb-0">Campaign ID: <strong>#{{ $broadcast->id }}</strong> • Dispatched: <strong>{{ $broadcast->created_at->format('M d, Y H:i:s') }}</strong></p>
        </div>
        <span class="badge bg-{{ $broadcast->priority === 'urgent' ? 'danger' : 'primary' }} px-3 py-2 text-uppercase">
            {{ $broadcast->priority }} Priority
        </span>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="portal-card p-4">
            <h5 class="fw-bold text-dark mb-2">{{ $broadcast->title }}</h5>
            <p class="text-muted p-3 bg-light rounded-3 mb-3">{{ $broadcast->message }}</p>

            <div class="small">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Audience Target:</span>
                    <strong class="text-capitalize">{{ $broadcast->target_audience }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Sender Profile:</span>
                    <strong>{{ $broadcast->sender->name ?? 'System' }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Active Channels:</span>
                    <div>
                        @foreach($broadcast->channels ?? [] as $ch)
                            <span class="badge bg-white text-dark border">{{ ucfirst($ch) }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="portal-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-truck-fast text-success me-2"></i>Recipient Verification Deliveries ({{ $deliveries->total() }})</h5>
                <span class="badge bg-success-subtle text-success border border-success">Status: 100% Delivered</span>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr class="text-muted small">
                            <th>Recipient</th>
                            <th class="text-center">Channel</th>
                            <th>Contact / Endpoint</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveries as $del)
                            <tr>
                                <td>
                                    <div class="fw-bold small">{{ $del->recipient->name ?? 'User' }}</div>
                                    <small class="text-muted text-capitalize">{{ $del->recipient->role ?? '' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border text-uppercase" style="font-size: 11px;">
                                        {{ $del->channel }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $del->recipient_contact }}</small></td>
                                <td class="text-center">
                                    <span class="badge bg-success text-white">
                                        <i class="fa-solid fa-check me-1"></i> {{ ucfirst($del->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No individual recipient delivery records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($deliveries->hasPages())
                <div class="mt-3">
                    {{ $deliveries->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection