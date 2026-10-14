@extends('layouts.portal')

@section('title', 'Comms Hub & Broadcasts â€” Scholar SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Omni-Channel Communication Hub</h2>
        <p class="text-muted mb-0">Multi-Channel Broadcast Engine: SMS, WhatsApp, Email & In-App Alerts</p>
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
    <a href="{{ route('broadcasts.create') }}" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">
        <i class="fa-solid fa-paper-plane me-1"></i> Compose Broadcast
    </a>
    @endif
</div>

<!-- Channel Delivery Stats -->
<div class="row g-3 mb-4">
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card text-center d-block">
            <p>Total Broadcasts</p>
            <h3>{{ $stats['total_broadcasts'] }}</h3>
            <small class="text-muted">Campaigns</small>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card text-center d-block">
            <p>Total Delivered</p>
            <h3 class="text-success">{{ $stats['total_delivered'] }}</h3>
            <small class="text-success">Verified Receipts</small>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card text-center d-block">
            <p>SMS Gateway</p>
            <h3 class="text-primary">{{ $stats['sms_sent'] }}</h3>
            <small class="text-muted">Twilio / Termii</small>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card text-center d-block">
            <p>Email Mailer</p>
            <h3 class="text-info">{{ $stats['email_sent'] }}</h3>
            <small class="text-muted">SES / SMTP</small>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card text-center d-block">
            <p>WhatsApp Cloud</p>
            <h3 class="text-success">{{ $stats['whatsapp_sent'] }}</h3>
            <small class="text-muted">Meta API</small>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="kpi-card text-center d-block">
            <p>In-App & Push</p>
            <h3 class="text-warning">{{ $stats['in_app_sent'] }}</h3>
            <small class="text-muted">WebSockets / FCM</small>
        </div>
    </div>
</div>

<!-- Broadcast History Table -->
<div class="portal-card">
    <div class="table-responsive">
        <table class="table table-portal align-middle mb-0">
            <thead>
                <tr>
                    <th>Broadcast Title & Excerpt</th>
                    <th>Audience Scope</th>
                    <th>Channels Utilized</th>
                    <th class="text-center">Priority</th>
                    <th>Dispatched By</th>
                    <th class="text-center">Timestamp</th>
                    <th class="text-end">Delivery Logs</th>
                </tr>
            </thead>
            <tbody>
                @forelse($broadcasts as $b)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $b->title }}</div>
                            <small class="text-muted">{{ Str::limit($b->message, 80) }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border text-capitalize">
                                <i class="fa-solid fa-users me-1"></i> {{ $b->target_audience }}
                                @if($b->schoolClass) ({{ $b->schoolClass->name }}) @endif
                            </span>
                        </td>
                        <td>
                            @foreach($b->channels ?? [] as $ch)
                                <span class="badge bg-white text-dark border me-1">
                                    <i class="fa-solid fa-check text-success me-1"></i>{{ ucfirst($ch) }}
                                </span>
                            @endforeach
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $b->priority === 'urgent' ? 'danger' : ($b->priority === 'emergency' ? 'danger' : 'primary') }} text-uppercase" style="font-size: 10px;">
                                {{ $b->priority }}
                            </span>
                        </td>
                        <td>
                            <div class="small fw-semibold">{{ $b->sender->name ?? 'System' }}</div>
                        </td>
                        <td class="text-center"><small class="text-muted">{{ $b->created_at->format('M d, Y H:i') }}</small></td>
                        <td class="text-end">
                            @if(auth()->user()->isAdmin() || $b->sender_id === auth()->id())
                            <a href="{{ route('broadcasts.show', $b->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="fa-solid fa-chart-pie me-1"></i> Logs ({{ $b->deliveries->count() }})
                            </a>
                            @else
                            <a href="{{ route('broadcasts.show', $b->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                <i class="fa-solid fa-envelope-open-text me-1"></i> View Bulletin
                            </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No broadcasts dispatched yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($broadcasts->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <small class="text-muted">Showing {{ $broadcasts->firstItem() }} to {{ $broadcasts->lastItem() }} of {{ $broadcasts->total() }} bulletins</small>
            <div>
                {{ $broadcasts->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
