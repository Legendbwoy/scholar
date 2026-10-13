@extends('layouts.portal')

@section('title', 'Compose Broadcast Message — Scholar SMS')

@section('content')
<div class="mb-4">
    <a href="{{ route('broadcasts.index') }}" class="btn btn-sm btn-light mb-2"><i class="fa-solid fa-arrow-left me-1"></i> Back to Hub</a>
    <h2 class="fw-bold mb-1">Compose Multi-Channel Broadcast</h2>
    <p class="text-muted mb-0">Transmit simultaneous announcements across SMS, WhatsApp, Email & In-App alerts.</p>
</div>

<div class="portal-card p-4 p-md-5">
    <form action="{{ route('broadcasts.send') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Title & Priority -->
            <div class="col-md-8">
                <label class="form-label fw-semibold">Broadcast Subject / Title *</label>
                <input type="text" name="title" class="form-control form-control-lg" placeholder="e.g. Mandatory Parent-Teacher Assembly & Examination Protocol" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Broadcast Priority *</label>
                <select name="priority" class="form-select form-select-lg" required>
                    <option value="normal">Normal Institutional Notice</option>
                    <option value="urgent" selected>Urgent Advisory (High Priority)</option>
                    <option value="emergency">Emergency Protocol (Instant Dispatch)</option>
                </select>
            </div>

            <!-- Audience Target -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Target Recipient Audience *</label>
                <select name="target_audience" id="audienceSelect" class="form-select" required>
                    <option value="all">Entire School Community (All Faculty, Students & Guardians)</option>
                    <option value="teachers">Faculty & Staff Members Only</option>
                    <option value="students">Enrolled Students Only</option>
                    <option value="parents">Guardians & Parents Only</option>
                    <option value="class">Specific Academic Cohort / Class</option>
                </select>
            </div>

            <div class="col-md-6" id="cohortSelectWrap" style="display: none;">
                <label class="form-label fw-semibold">Select Academic Cohort</label>
                <select name="school_class_id" class="form-select">
                    <option value="">Choose Cohort...</option>
                    @foreach($classes as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->name }} (Section {{ $cl->section }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Delivery Channels Selection -->
            <div class="col-12">
                <label class="form-label fw-semibold d-block">Transmission Channels (Multi-Channel Dispatch):</label>
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="channels[]" value="sms" id="chSms" checked>
                                <label class="form-check-label fw-bold" for="chSms">
                                    <i class="fa-solid fa-comment-sms text-primary me-1"></i> SMS Gateway
                                </label>
                                <small class="text-muted d-block" style="font-size: 11px;">Twilio / Termii Carrier</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="channels[]" value="email" id="chEmail" checked>
                                <label class="form-check-label fw-bold" for="chEmail">
                                    <i class="fa-solid fa-envelope text-info me-1"></i> Email Digest
                                </label>
                                <small class="text-muted d-block" style="font-size: 11px;">AWS SES / Mailgun</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="channels[]" value="whatsapp" id="chWa" checked>
                                <label class="form-check-label fw-bold" for="chWa">
                                    <i class="fa-brands fa-whatsapp text-success me-1"></i> WhatsApp
                                </label>
                                <small class="text-muted d-block" style="font-size: 11px;">Meta Cloud API</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="channels[]" value="in_app" id="chApp" checked>
                                <label class="form-check-label fw-bold" for="chApp">
                                    <i class="fa-solid fa-bell text-warning me-1"></i> In-App Portal
                                </label>
                                <small class="text-muted d-block" style="font-size: 11px;">Real-Time WebSockets</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamic Merge Tags -->
            <div class="col-12">
                <label class="form-label fw-semibold small text-muted">Click to Insert Dynamic Merge Tags into Message Body:</label>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-light border" onclick="insertTag('{student_name}')"><code>{student_name}</code></button>
                    <button type="button" class="btn btn-sm btn-light border" onclick="insertTag('{parent_name}')"><code>{parent_name}</code></button>
                    <button type="button" class="btn btn-sm btn-light border" onclick="insertTag('{class}')"><code>{class}</code></button>
                    <button type="button" class="btn btn-sm btn-light border" onclick="insertTag('{school_name}')"><code>{school_name}</code></button>
                </div>
            </div>

            <!-- Message Body -->
            <div class="col-12">
                <label class="form-label fw-semibold">Message Body *</label>
                <textarea name="message" id="msgTextarea" rows="5" class="form-control" placeholder="Write your announcement or notice here..." required></textarea>
                <small class="text-muted">Broadcast is automatically queued and logged to the immutable audit trail.</small>
            </div>

            <div class="col-12 text-end">
                <a href="{{ route('broadcasts.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                <button type="submit" class="btn btn-warning rounded-pill px-5 fw-bold shadow">
                    <i class="fa-solid fa-paper-plane me-2"></i> Transmit Broadcast Now
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const audienceSelect = document.getElementById('audienceSelect');
    const cohortSelectWrap = document.getElementById('cohortSelectWrap');
    const msgTextarea = document.getElementById('msgTextarea');

    audienceSelect.addEventListener('change', function() {
        if (this.value === 'class') {
            cohortSelectWrap.style.display = 'block';
        } else {
            cohortSelectWrap.style.display = 'none';
        }
    });

    function insertTag(tag) {
        msgTextarea.value += ' ' + tag + ' ';
        msgTextarea.focus();
    }
</script>
@endsection