@extends('layouts.portal')

@section('title', 'Communication Gateways & API Configurations — Scholar SMS')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('broadcasts.index') }}" class="text-decoration-none">Communications</a></li>
                <li class="breadcrumb-item active" aria-current="page">Gateway & API Setups</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-1">Communication Gateways & API Configurations</h2>
        <p class="text-muted mb-0">Configure enterprise SMTP/Email servers, SMS telecommunication gateways, and WhatsApp Business Cloud API tokens.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('broadcasts.create') }}" class="btn btn-outline-success">
            <i class="fa-solid fa-paper-plane me-1"></i> Compose Broadcast
        </a>
        <a href="{{ route('audit.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-shield-halved me-1"></i> Security Audit
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert">
    <i class="fa-solid fa-circle-check fs-5 me-2"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert">
    <i class="fa-solid fa-triangle-exclamation fs-5 me-2"></i>
    <div>{{ session('error') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(isset($errors) && $errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
    <div class="fw-bold mb-1"><i class="fa-solid fa-circle-xmark me-1"></i> Validation Errors:</div>
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Channel Status Overview Cards -->
<div class="row g-3 mb-4">
    <!-- Email Status Card -->
    <div class="col-md-4">
        <div class="portal-card p-3 h-100 border">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fa-solid fa-envelope fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Email SMTP</h6>
                        <small class="text-muted">{{ strtoupper($emailGateway->provider) }}</small>
                    </div>
                </div>
                <div>
                    @if($emailGateway->is_active)
                        <span class="badge bg-emerald-subtle border border-emerald-subtle rounded-pill">Active</span>
                    @else
                        <span class="badge bg-light text-secondary border rounded-pill">Disabled</span>
                    @endif
                </div>
            </div>
            <div class="text-muted small mb-2">
                <div><i class="fa-solid fa-at me-1"></i> {{ $emailGateway->from_address ?: 'Not configured' }}</div>
                <div><i class="fa-solid fa-server me-1"></i> {{ $emailGateway->getCredential('smtp_host', 'localhost') }}:{{ $emailGateway->getCredential('smtp_port', '587') }}</div>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                <span class="badge {{ $emailGateway->sandbox_mode ? 'bg-light text-secondary border' : 'bg-dark text-white' }}">
                    {{ $emailGateway->sandbox_mode ? 'Sandbox Mode' : 'Production Live' }}
                </span>
                <small class="text-muted">
                    @if($emailGateway->last_tested_at)
                        Pinged {{ $emailGateway->last_tested_at->diffForHumans() }}
                    @else
                        Untested
                    @endif
                </small>
            </div>
        </div>
    </div>

    <!-- SMS Status Card -->
    <div class="col-md-4">
        <div class="portal-card p-3 h-100 border">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fa-solid fa-comment-sms fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">SMS Telephony</h6>
                        <small class="text-muted">{{ strtoupper($smsGateway->provider) }}</small>
                    </div>
                </div>
                <div>
                    @if($smsGateway->is_active)
                        <span class="badge bg-emerald-subtle border border-emerald-subtle rounded-pill">Active</span>
                    @else
                        <span class="badge bg-light text-secondary border rounded-pill">Disabled</span>
                    @endif
                </div>
            </div>
            <div class="text-muted small mb-2">
                <div><i class="fa-solid fa-id-badge me-1"></i> Sender: {{ $smsGateway->getCredential('sender_id', 'SCHOLAR') }}</div>
                <div><i class="fa-solid fa-phone me-1"></i> {{ $smsGateway->from_address ?: 'Shortcode default' }}</div>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                <span class="badge {{ $smsGateway->sandbox_mode ? 'bg-light text-secondary border' : 'bg-dark text-white' }}">
                    {{ $smsGateway->sandbox_mode ? 'Sandbox Mode' : 'Production Live' }}
                </span>
                <small class="text-muted">
                    @if($smsGateway->last_tested_at)
                        Pinged {{ $smsGateway->last_tested_at->diffForHumans() }}
                    @else
                        Untested
                    @endif
                </small>
            </div>
        </div>
    </div>

    <!-- WhatsApp Status Card -->
    <div class="col-md-4">
        <div class="portal-card p-3 h-100 border">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-emerald-subtle text-emerald d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fa-brands fa-whatsapp fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">WhatsApp Cloud</h6>
                        <small class="text-muted">{{ strtoupper(str_replace('_', ' ', $whatsappGateway->provider)) }}</small>
                    </div>
                </div>
                <div>
                    @if($whatsappGateway->is_active)
                        <span class="badge bg-emerald-subtle border border-emerald-subtle rounded-pill">Active</span>
                    @else
                        <span class="badge bg-light text-secondary border rounded-pill">Disabled</span>
                    @endif
                </div>
            </div>
            <div class="text-muted small mb-2">
                <div><i class="fa-solid fa-phone me-1"></i> {{ $whatsappGateway->from_address ?: 'Meta Cloud Number' }}</div>
                <div><i class="fa-solid fa-hashtag me-1"></i> ID: {{ $whatsappGateway->getCredential('phone_number_id', 'Unset') }}</div>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                <span class="badge {{ $whatsappGateway->sandbox_mode ? 'bg-light text-secondary border' : 'bg-dark text-white' }}">
                    {{ $whatsappGateway->sandbox_mode ? 'Sandbox Mode' : 'Production Live' }}
                </span>
                <small class="text-muted">
                    @if($whatsappGateway->last_tested_at)
                        Pinged {{ $whatsappGateway->last_tested_at->diffForHumans() }}
                    @else
                        Untested
                    @endif
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Main Gateway Configuration Card with Tabs -->
<div class="portal-card p-0 overflow-hidden shadow-sm">
    <div class="bg-light border-bottom px-4 pt-3">
        <ul class="nav nav-tabs border-0" id="gatewayTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-dark px-4 py-3" id="email-tab" data-bs-toggle="tab" data-bs-target="#email-pane" type="button" role="tab">
                    <i class="fa-solid fa-envelope text-info me-2"></i> Email SMTP & Mailers
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-dark px-4 py-3" id="sms-tab" data-bs-toggle="tab" data-bs-target="#sms-pane" type="button" role="tab">
                    <i class="fa-solid fa-comment-sms text-primary me-2"></i> SMS Gateways & Carriers
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-dark px-4 py-3" id="whatsapp-tab" data-bs-toggle="tab" data-bs-target="#whatsapp-pane" type="button" role="tab">
                    <i class="fa-brands fa-whatsapp text-success me-2"></i> WhatsApp Business Cloud API
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content p-4 p-md-5" id="gatewayTabsContent">
        
        {{-- ================= TAB 1: EMAIL / SMTP ================= --}}
        <div class="tab-pane fade show active" id="email-pane" role="tabpanel">
            <div class="row g-4">
                <!-- Configuration Form -->
                <div class="col-lg-8 border-end-lg pe-lg-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold mb-0"><i class="fa-solid fa-envelope-open-text text-info me-2"></i> Outbound Email & SMTP Settings</h4>
                        <span class="badge bg-light text-dark border">Channel: Outbound Mail</span>
                    </div>
                    <p class="text-muted small mb-4">
                        Define transmission parameters for dispatching report cards, parent letters, continuous assessment alerts, and password reset tokens.
                    </p>

                    <form action="{{ route('gateways.update', 'email') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <!-- Provider -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mail Delivery Engine *</label>
                                <select name="provider" class="form-select" required>
                                    <option value="smtp" {{ $emailGateway->provider === 'smtp' ? 'selected' : '' }}>Custom SMTP Server</option>
                                    <option value="sendgrid" {{ $emailGateway->provider === 'sendgrid' ? 'selected' : '' }}>Twilio SendGrid API</option>
                                    <option value="mailgun" {{ $emailGateway->provider === 'mailgun' ? 'selected' : '' }}>Mailgun Cloud</option>
                                    <option value="ses" {{ $emailGateway->provider === 'ses' ? 'selected' : '' }}>Amazon Simple Email Service (SES)</option>
                                </select>
                            </div>

                            <!-- Rate Limit -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Rate Limit (Dispatches / Minute)</label>
                                <input type="number" name="rate_limit_per_minute" class="form-control" value="{{ $emailGateway->rate_limit_per_minute ?? 60 }}" min="10" max="1000">
                                <small class="text-muted">Protects domain sender reputation from throttling.</small>
                            </div>

                            <!-- From Name & Address -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Default Sender Name *</label>
                                <input type="text" name="from_name" class="form-control" value="{{ $emailGateway->from_name }}" placeholder="e.g. Scholar SMS Academy" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Default Sender Email (RFC 5322) *</label>
                                <input type="email" name="from_address" class="form-control" value="{{ $emailGateway->from_address }}" placeholder="e.g. notifications@scholar.edu" required>
                            </div>

                            <!-- SMTP Server & Port -->
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">SMTP Host *</label>
                                <input type="text" name="smtp_host" class="form-control" value="{{ $emailGateway->getCredential('smtp_host', 'smtp.mailtrap.io') }}" placeholder="e.g. smtp.gmail.com or mail.school.edu" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">SMTP Port *</label>
                                <input type="number" name="smtp_port" class="form-control" value="{{ $emailGateway->getCredential('smtp_port', '587') }}" placeholder="587 or 465" required>
                            </div>

                            <!-- SMTP Username & Encryption -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">SMTP Username / API User *</label>
                                <input type="text" name="smtp_username" class="form-control" value="{{ $emailGateway->getCredential('smtp_username') }}" placeholder="Username or API ID" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Transport Encryption *</label>
                                <select name="smtp_encryption" class="form-select" required>
                                    <option value="tls" {{ $emailGateway->getCredential('smtp_encryption') === 'tls' ? 'selected' : '' }}>TLS (STARTTLS - Port 587)</option>
                                    <option value="ssl" {{ $emailGateway->getCredential('smtp_encryption') === 'ssl' ? 'selected' : '' }}>SSL (SMTPS - Port 465)</option>
                                    <option value="none" {{ $emailGateway->getCredential('smtp_encryption') === 'none' ? 'selected' : '' }}>None (Local Relay / Intranet)</option>
                                </select>
                            </div>

                            <!-- Password with Secret Masking -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">SMTP Password / Secret Key</label>
                                <div class="input-group">
                                    <input type="password" name="smtp_password" id="smtpPasswordInput" class="form-control" placeholder="Leave empty to retain existing configured secret key">
                                    <button class="btn btn-outline-secondary" type="button" onclick="toggleSecretVisibility('smtpPasswordInput', this)">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    Current Masked Key: <code class="text-secondary">{{ $emailGateway->getMaskedCredential('smtp_password') ?: 'None set' }}</code>
                                </small>
                            </div>

                            <!-- Switches -->
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="emailActiveSwitch" value="1" {{ $emailGateway->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="emailActiveSwitch">Channel Enabled for Transmissions</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="sandbox_mode" id="emailSandboxSwitch" value="1" {{ $emailGateway->sandbox_mode ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="emailSandboxSwitch">Enable Test Sandbox Mode</label>
                                </div>
                            </div>

                            <div class="col-12 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Email Gateway Configuration
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Diagnostics & Verification Sidebar -->
                <div class="col-lg-4 ps-lg-4">
                    <div class="portal-card bg-light p-3 border mb-4">
                        <h6 class="fw-bold mb-2"><i class="fa-solid fa-vial text-info me-1"></i> SMTP Connection Ping</h6>
                        <p class="text-muted small mb-3">Send a real-time test transmission to verify host handshake, SSL handshake, and credentials.</p>
                        
                        <form action="{{ route('gateways.test', 'email') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Target Test Email Address</label>
                                <input type="email" name="test_recipient" class="form-control form-control-sm" placeholder="admin@domain.com" value="{{ auth()->user()->email }}" required>
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-info w-100">
                                <i class="fa-solid fa-paper-plane me-1"></i> Dispatch SMTP Test Email
                            </button>
                        </form>
                    </div>

                    <!-- Health & Audit Box -->
                    <div class="portal-card p-3 border">
                        <h6 class="fw-bold mb-2"><i class="fa-solid fa-heart-pulse text-success me-1"></i> Channel Health Log</h6>
                        <div class="small">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Last Handshake:</span>
                                <strong>{{ $emailGateway->last_tested_at ? $emailGateway->last_tested_at->format('M d, Y H:i:s') : 'Never' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Health Result:</span>
                                <span>
                                    @if($emailGateway->last_test_status === 'success')
                                        <span class="badge bg-success text-white">Handshake Passed</span>
                                    @elseif($emailGateway->last_test_status === 'failed')
                                        <span class="badge bg-danger text-white">Connection Error</span>
                                    @else
                                        <span class="badge bg-secondary text-white">Not Tested</span>
                                    @endif
                                </span>
                            </div>
                            @if($emailGateway->last_test_message)
                                <div class="p-2 bg-light rounded text-muted" style="font-size: 11px; word-break: break-all;">
                                    {{ $emailGateway->last_test_message }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= TAB 2: SMS GATEWAYS ================= --}}
        <div class="tab-pane fade" id="sms-pane" role="tabpanel">
            <div class="row g-4">
                <!-- SMS Form -->
                <div class="col-lg-8 border-end-lg pe-lg-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold mb-0"><i class="fa-solid fa-comment-sms text-primary me-2"></i> SMS Carrier Gateway & Telephony</h4>
                        <span class="badge bg-light text-dark border">Channel: SMS & GSM</span>
                    </div>
                    <p class="text-muted small mb-4">
                        Configure institutional alphanumeric sender IDs, Twilio/Termii tokens, and API endpoints for rapid parental SMS broadcast notifications.
                    </p>

                    <form action="{{ route('gateways.update', 'sms') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <!-- Provider -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">SMS Provider / Aggregator *</label>
                                <select name="provider" class="form-select" required>
                                    <option value="twilio" {{ $smsGateway->provider === 'twilio' ? 'selected' : '' }}>Twilio Programmable SMS</option>
                                    <option value="termii" {{ $smsGateway->provider === 'termii' ? 'selected' : '' }}>Termii African Telco Gateway</option>
                                    <option value="vonage" {{ $smsGateway->provider === 'vonage' ? 'selected' : '' }}>Vonage (Nexmo) Messaging API</option>
                                    <option value="africastalking" {{ $smsGateway->provider === 'africastalking' ? 'selected' : '' }}>Africa's Talking SMS API</option>
                                    <option value="custom_webhook" {{ $smsGateway->provider === 'custom_webhook' ? 'selected' : '' }}>Custom HTTP REST Webhook</option>
                                </select>
                            </div>

                            <!-- Alphanumeric Sender ID -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Registered Sender ID (Header) *</label>
                                <input type="text" name="sender_id" class="form-control" value="{{ $smsGateway->getCredential('sender_id', 'SCHOLAR_SMS') }}" placeholder="Max 11 alphanumeric chars" maxlength="20" required>
                                <small class="text-muted">Displays as sender name on recipient handsets.</small>
                            </div>

                            <!-- Twilio Account SID or API Key -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Account SID / API Key</label>
                                <input type="text" name="account_sid" class="form-control" value="{{ $smsGateway->getCredential('account_sid') }}" placeholder="e.g. ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                            </div>

                            <!-- Dedicated Phone Number / Shortcode -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Originating Phone Number / Shortcode</label>
                                <input type="text" name="from_address" class="form-control" value="{{ $smsGateway->from_address }}" placeholder="e.g. +15550199 or 38291">
                            </div>

                            <!-- Auth Token / API Secret Key -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">Auth Token / Secret API Key</label>
                                <div class="input-group">
                                    <input type="password" name="auth_token" id="smsAuthTokenInput" class="form-control" placeholder="Leave blank to preserve currently saved token">
                                    <button class="btn btn-outline-secondary" type="button" onclick="toggleSecretVisibility('smsAuthTokenInput', this)">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    Current Token: <code class="text-secondary">{{ $smsGateway->getMaskedCredential('auth_token') ?: 'None set' }}</code>
                                </small>
                            </div>

                            <!-- Rate Limit -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Throughput Rate Limit (SMS / Min)</label>
                                <input type="number" name="rate_limit_per_minute" class="form-control" value="{{ $smsGateway->rate_limit_per_minute ?? 100 }}" min="10" max="5000">
                            </div>

                            <!-- Switches -->
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="smsActiveSwitch" value="1" {{ $smsGateway->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="smsActiveSwitch">Channel Enabled for Dispatch</label>
                                </div>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="sandbox_mode" id="smsSandboxSwitch" value="1" {{ $smsGateway->sandbox_mode ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="smsSandboxSwitch">Sandbox (Simulated SMS Delivery)</label>
                                </div>
                            </div>

                            <div class="col-12 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Save SMS Gateway Parameters
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- SMS Diagnostic Sidebar -->
                <div class="col-lg-4 ps-lg-4">
                    <div class="portal-card bg-light p-3 border mb-4">
                        <h6 class="fw-bold mb-2"><i class="fa-solid fa-paper-plane text-primary me-1"></i> SMS Diagnostic Ping</h6>
                        <p class="text-muted small mb-3">Send a real-time test payload via your carrier route to verify handset routing and balances.</p>
                        
                        <form action="{{ route('gateways.test', 'sms') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Target Handset Phone Number (E.164)</label>
                                <input type="text" name="test_recipient" class="form-control form-control-sm" placeholder="+1234567890" value="+15550199" required>
                                <small class="text-muted" style="font-size: 11px;">Must include country dial code (e.g. +1, +44, +234, +233).</small>
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                <i class="fa-solid fa-bolt me-1"></i> Dispatch Test SMS Ping
                            </button>
                        </form>
                    </div>

                    <!-- Health & Audit Box -->
                    <div class="portal-card p-3 border">
                        <h6 class="fw-bold mb-2"><i class="fa-solid fa-heart-pulse text-success me-1"></i> SMS Carrier Route Log</h6>
                        <div class="small">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Last Transmission:</span>
                                <strong>{{ $smsGateway->last_tested_at ? $smsGateway->last_tested_at->format('M d, Y H:i:s') : 'Never' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Route Status:</span>
                                <span>
                                    @if($smsGateway->last_test_status === 'success')
                                        <span class="badge bg-success text-white">Route Active</span>
                                    @elseif($smsGateway->last_test_status === 'failed')
                                        <span class="badge bg-danger text-white">Carrier Error</span>
                                    @else
                                        <span class="badge bg-secondary text-white">Untested</span>
                                    @endif
                                </span>
                            </div>
                            @if($smsGateway->last_test_message)
                                <div class="p-2 bg-light rounded text-muted" style="font-size: 11px; word-break: break-all;">
                                    {{ $smsGateway->last_test_message }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= TAB 3: WHATSAPP CLOUD API ================= --}}
        <div class="tab-pane fade" id="whatsapp-pane" role="tabpanel">
            <div class="row g-4">
                <!-- WhatsApp Form -->
                <div class="col-lg-8 border-end-lg pe-lg-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold mb-0"><i class="fa-brands fa-whatsapp text-success me-2"></i> WhatsApp Business Cloud API</h4>
                        <span class="badge bg-light text-dark border">Channel: WhatsApp Business</span>
                    </div>
                    <p class="text-muted small mb-4">
                        Connect directly to Meta WhatsApp Cloud API or Twilio WhatsApp endpoint with official phone number IDs, permanent system user tokens, and approved message templates.
                    </p>

                    <form action="{{ route('gateways.update', 'whatsapp') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <!-- Provider -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">WhatsApp Cloud Architecture *</label>
                                <select name="provider" class="form-select" required>
                                    <option value="meta_whatsapp" {{ $whatsappGateway->provider === 'meta_whatsapp' ? 'selected' : '' }}>Meta WhatsApp Cloud API (Graph v20+)</option>
                                    <option value="twilio_whatsapp" {{ $whatsappGateway->provider === 'twilio_whatsapp' ? 'selected' : '' }}>Twilio for WhatsApp Business</option>
                                    <option value="infobip" {{ $whatsappGateway->provider === 'infobip' ? 'selected' : '' }}>Infobip WhatsApp Enterprise</option>
                                </select>
                            </div>

                            <!-- Registered Phone Number -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Official Business WhatsApp Number *</label>
                                <input type="text" name="from_address" class="form-control" value="{{ $whatsappGateway->from_address }}" placeholder="e.g. +15550198" required>
                                <small class="text-muted">Must be verified with green-check or Meta display profile.</small>
                            </div>

                            <!-- Phone Number ID (Meta) -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Meta Phone Number ID *</label>
                                <input type="text" name="phone_number_id" class="form-control" value="{{ $whatsappGateway->getCredential('phone_number_id') }}" placeholder="e.g. 109283746192837" required>
                                <small class="text-muted">Found in Meta Developer Portal > WhatsApp > API Setup.</small>
                            </div>

                            <!-- WhatsApp Business Account ID (WABA) -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">WhatsApp Business Account ID (WABA ID)</label>
                                <input type="text" name="business_account_id" class="form-control" value="{{ $whatsappGateway->getCredential('business_account_id') }}" placeholder="e.g. 293847192837465">
                            </div>

                            <!-- Permanent Access Token -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">System User Permanent Access Token</label>
                                <div class="input-group">
                                    <input type="password" name="access_token" id="waAccessTokenInput" class="form-control" placeholder="EAAG... (Leave empty to keep existing verified token)">
                                    <button class="btn btn-outline-secondary" type="button" onclick="toggleSecretVisibility('waAccessTokenInput', this)">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    Configured Token: <code class="text-secondary">{{ $whatsappGateway->getMaskedCredential('access_token') ?: 'None set' }}</code>
                                </small>
                            </div>

                            <!-- Template Namespace -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Approved Template Namespace</label>
                                <input type="text" name="template_namespace" class="form-control" value="{{ $whatsappGateway->getCredential('template_namespace', 'scholar_sms_alerts') }}" placeholder="e.g. scholar_sms_alerts">
                            </div>

                            <!-- Rate Limit -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Rate Limit (Messages / Min)</label>
                                <input type="number" name="rate_limit_per_minute" class="form-control" value="{{ $whatsappGateway->rate_limit_per_minute ?? 80 }}" min="10" max="1000">
                            </div>

                            <!-- Switches -->
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="waActiveSwitch" value="1" {{ $whatsappGateway->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="waActiveSwitch">Enable WhatsApp Broadcast Channel</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="sandbox_mode" id="waSandboxSwitch" value="1" {{ $whatsappGateway->sandbox_mode ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="waSandboxSwitch">Meta Sandbox Test Environment</label>
                                </div>
                            </div>

                            <div class="col-12 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Save WhatsApp Cloud Configuration
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- WhatsApp Diagnostics Sidebar -->
                <div class="col-lg-4 ps-lg-4">
                    <div class="portal-card bg-light p-3 border mb-4">
                        <h6 class="fw-bold mb-2"><i class="fa-brands fa-whatsapp text-success me-1"></i> Meta Cloud API Ping</h6>
                        <p class="text-muted small mb-3">Send a template hello-world ping to verify webhook subscriptions and Meta token validity.</p>
                        
                        <form action="{{ route('gateways.test', 'whatsapp') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Recipient Mobile Number (E.164)</label>
                                <input type="text" name="test_recipient" class="form-control form-control-sm" placeholder="+1234567890" value="+15550198" required>
                                <small class="text-muted" style="font-size: 11px;">Must be registered with WhatsApp messenger.</small>
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-success w-100">
                                <i class="fa-solid fa-paper-plane me-1"></i> Dispatch WhatsApp Template Ping
                            </button>
                        </form>
                    </div>

                    <!-- Health & Audit Box -->
                    <div class="portal-card p-3 border">
                        <h6 class="fw-bold mb-2"><i class="fa-solid fa-heart-pulse text-success me-1"></i> Meta Cloud API Status</h6>
                        <div class="small">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Last Verification:</span>
                                <strong>{{ $whatsappGateway->last_tested_at ? $whatsappGateway->last_tested_at->format('M d, Y H:i:s') : 'Never' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Session State:</span>
                                <span>
                                    @if($whatsappGateway->last_test_status === 'success')
                                        <span class="badge bg-success text-white">Cloud Active</span>
                                    @elseif($whatsappGateway->last_test_status === 'failed')
                                        <span class="badge bg-danger text-white">Token Expired/Invalid</span>
                                    @else
                                        <span class="badge bg-secondary text-white">Untested</span>
                                    @endif
                                </span>
                            </div>
                            @if($whatsappGateway->last_test_message)
                                <div class="p-2 bg-light rounded text-muted" style="font-size: 11px; word-break: break-all;">
                                    {{ $whatsappGateway->last_test_message }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function toggleSecretVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection
