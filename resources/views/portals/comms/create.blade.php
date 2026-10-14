@extends('layouts.portal')

@section('title', 'Compose Multi-Channel Broadcast — Scholar SMS')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('broadcasts.index') }}" class="text-decoration-none">Communications Hub</a></li>
                <li class="breadcrumb-item active" aria-current="page">Compose Broadcast</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-1">Compose Multi-Channel Broadcast</h2>
        <p class="text-muted mb-0">Transmit simultaneous announcements, seasonal greetings, and holiday advisories across SMS, WhatsApp, Email & In-App channels.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('broadcasts.templates.index') }}" class="btn btn-outline-primary">
            <i class="fa-solid fa-shapes me-1"></i> Browse Template Library
        </a>
        <a href="{{ route('broadcasts.index') }}" class="btn btn-light">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Hub
        </a>
    </div>
</div>

@if(isset($errors) && $errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
    <div class="fw-bold mb-1"><i class="fa-solid fa-circle-xmark me-1"></i> Form Errors:</div>
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<form action="{{ route('broadcasts.send') }}" method="POST" id="broadcastForm">
    @csrf
    <input type="hidden" name="broadcast_template_id" id="broadcastTemplateId" value="{{ $selectedTemplate?->id }}">

    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-lg-7">
            <div class="portal-card p-4 mb-4">
                <!-- 1. Template Quick Picker -->
                <div class="mb-4 p-3 bg-light rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-wand-magic-sparkles text-warning me-1"></i> Load From Pre-Built Greeting or Notification Template
                        </label>
                        <a href="{{ route('broadcasts.templates.index') }}" class="small text-decoration-none fw-semibold">Manage Templates &rarr;</a>
                    </div>
                    <select id="templateSelect" class="form-select form-select-lg">
                        <option value="">-- Choose Greeting or Holiday Template (or compose from scratch) --</option>
                        @php
                            $grouped = $templates->groupBy('category');
                            $catLabels = [
                                'seasonal' => 'Seasonal & New Year Greetings',
                                'religious' => 'Religious Observances & Holy Days',
                                'gender_event' => 'Gender & Inclusion Events',
                                'holiday_notification' => 'Holiday Notifications & Closures',
                                'academic_milestone' => 'Academic Milestones & Directives',
                            ];
                        @endphp
                        @foreach($grouped as $category => $catTemplates)
                            <optgroup label="{{ $catLabels[$category] ?? ucfirst($category) }}">
                                @foreach($catTemplates as $tpl)
                                    <option value="{{ $tpl->id }}" {{ ($selectedTemplate && $selectedTemplate->id == $tpl->id) ? 'selected' : '' }}>
                                        {{ $tpl->title }} {{ $tpl->is_auto_send ? '[Auto-Send]' : '' }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-1">Selecting a template auto-populates title, message, audience, channels, and linked holiday trigger.</small>
                </div>

                <!-- 2. Dispatch Mode Switcher (Immediate vs Scheduled vs Auto-Holiday) -->
                <div class="mb-4">
                    <label class="form-label fw-bold d-block">Dispatch Mode & Scheduling Automation *</label>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="p-3 border rounded-3 w-100 h-100 cursor-pointer send-mode-card active" id="modeCardImmediate" for="modeImmediate">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="send_mode" id="modeImmediate" value="immediate" checked onchange="updateSendMode('immediate')">
                                    <span class="form-check-label fw-bold d-block text-dark">
                                        <i class="fa-solid fa-bolt text-warning me-1"></i> Immediate Send
                                    </span>
                                    <small class="text-muted d-block" style="font-size: 11px;">Transmits instantly upon submission</small>
                                </div>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="p-3 border rounded-3 w-100 h-100 cursor-pointer send-mode-card" id="modeCardScheduled" for="modeScheduled">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="send_mode" id="modeScheduled" value="scheduled" onchange="updateSendMode('scheduled')">
                                    <span class="form-check-label fw-bold d-block text-dark">
                                        <i class="fa-solid fa-clock text-info me-1"></i> Specific Date/Time
                                    </span>
                                    <small class="text-muted d-block" style="font-size: 11px;">Queue for precise future dispatch</small>
                                </div>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="p-3 border rounded-3 w-100 h-100 cursor-pointer send-mode-card" id="modeCardAutoHoliday" for="modeAutoHoliday">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="send_mode" id="modeAutoHoliday" value="auto_holiday" onchange="updateSendMode('auto_holiday')">
                                    <span class="form-check-label fw-bold d-block text-dark">
                                        <i class="fa-solid fa-calendar-day text-success me-1"></i> Holiday Auto-Send
                                    </span>
                                    <small class="text-muted d-block" style="font-size: 11px;">Auto-fires at 08:00 AM on holiday</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Scheduled DateTime Input -->
                    <div id="scheduledPickerWrap" class="mt-3 p-3 bg-light rounded border" style="display: none;">
                        <label class="form-label fw-semibold small text-dark"><i class="fa-solid fa-calendar-clock text-info me-1"></i> Select Scheduled Delivery Date & Time *</label>
                        <input type="datetime-local" name="scheduled_at" id="scheduledAtInput" class="form-control" min="{{ now()->format('Y-m-d\TH:i') }}">
                        <small class="text-muted d-block mt-1">Our background worker process dispatches broadcasts within 60 seconds of schedule.</small>
                    </div>

                    <!-- Holiday Auto-Send Selector -->
                    <div id="holidayPickerWrap" class="mt-3 p-3 bg-light rounded border" style="display: none;">
                        <label class="form-label fw-semibold small text-dark"><i class="fa-solid fa-calendar-star text-success me-1"></i> Link with School Calendar Holiday *</label>
                        <select name="school_calendar_holiday_id" id="holidaySelect" class="form-select">
                            <option value="">-- Choose Calendar Holiday Event --</option>
                            @foreach($holidays as $hol)
                                <option value="{{ $hol->id }}" data-title="{{ $hol->title }}" data-date="{{ $hol->start_date->format('Y-m-d') }}">
                                    {{ $hol->title }} — Starts {{ $hol->start_date->format('M d, Y') }} ({{ $hol->type }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">The system will automatically schedule this message to transmit at 08:00 AM on the day of the holiday.</small>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <!-- Title & Priority -->
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Broadcast Subject / Title *</label>
                        <input type="text" name="title" id="titleInput" class="form-control" placeholder="e.g. Happy New Year & Resumption Protocols" value="{{ $selectedTemplate?->title }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Broadcast Priority *</label>
                        <select name="priority" id="prioritySelect" class="form-select" required>
                            <option value="normal" {{ ($selectedTemplate?->priority === 'normal') ? 'selected' : '' }}>Normal Institutional Notice</option>
                            <option value="urgent" {{ ($selectedTemplate?->priority === 'urgent' || !$selectedTemplate) ? 'selected' : '' }}>Urgent Advisory (High)</option>
                            <option value="emergency" {{ ($selectedTemplate?->priority === 'emergency') ? 'selected' : '' }}>Emergency Protocol</option>
                        </select>
                    </div>

                    <!-- Audience Target -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Target Recipient Audience *</label>
                        <select name="target_audience" id="audienceSelect" class="form-select" required>
                            @if(auth()->user()->isAdmin())
                            <option value="all" {{ ($selectedTemplate?->target_audience === 'all' || !$selectedTemplate) ? 'selected' : '' }}>Entire School Community (All Roles)</option>
                            <option value="teachers" {{ ($selectedTemplate?->target_audience === 'teachers') ? 'selected' : '' }}>Faculty & Staff Members Only</option>
                            @endif
                            <option value="students" {{ ($selectedTemplate?->target_audience === 'students') ? 'selected' : '' }}>Enrolled Students Only</option>
                            <option value="parents" {{ ($selectedTemplate?->target_audience === 'parents') ? 'selected' : '' }}>Guardians & Parents Only</option>
                            <option value="class" {{ ($selectedTemplate?->target_audience === 'class') ? 'selected' : '' }}>Specific Academic Cohort / Class</option>
                        </select>
                    </div>

                    <div class="col-md-6" id="cohortSelectWrap" style="display: {{ ($selectedTemplate?->target_audience === 'class') ? 'block' : 'none' }};">
                        <label class="form-label fw-semibold">Select Academic Cohort</label>
                        <select name="school_class_id" class="form-select">
                            <option value="">Choose Cohort...</option>
                            @foreach($classes as $cl)
                                <option value="{{ $cl->id }}">{{ $cl->name }} (Section {{ $cl->section }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Delivery Channels Selection -->
                <div class="mb-4">
                    <label class="form-label fw-semibold d-block">Transmission Channels (Multi-Channel Dispatch) *:</label>
                    <div class="row g-2">
                        @php
                            $tplChannels = $selectedTemplate?->channels ?? ['sms', 'email', 'whatsapp', 'in_app'];
                        @endphp
                        <div class="col-md-3 col-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <div class="form-check">
                                    <input class="form-check-input ch-toggle" type="checkbox" name="channels[]" value="sms" id="chSms" {{ in_array('sms', $tplChannels) ? 'checked' : '' }} onchange="refreshChannelBadges()">
                                    <label class="form-check-label fw-bold" for="chSms">
                                        <i class="fa-solid fa-comment-sms text-primary me-1"></i> SMS
                                    </label>
                                    <small class="text-muted d-block" style="font-size: 11px;">Twilio / Termii</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <div class="form-check">
                                    <input class="form-check-input ch-toggle" type="checkbox" name="channels[]" value="whatsapp" id="chWa" {{ in_array('whatsapp', $tplChannels) ? 'checked' : '' }} onchange="refreshChannelBadges()">
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
                                    <input class="form-check-input ch-toggle" type="checkbox" name="channels[]" value="email" id="chEmail" {{ in_array('email', $tplChannels) ? 'checked' : '' }} onchange="refreshChannelBadges()">
                                    <label class="form-check-label fw-bold" for="chEmail">
                                        <i class="fa-solid fa-envelope text-info me-1"></i> Email
                                    </label>
                                    <small class="text-muted d-block" style="font-size: 11px;">AWS SES / Mailgun</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <div class="form-check">
                                    <input class="form-check-input ch-toggle" type="checkbox" name="channels[]" value="in_app" id="chApp" {{ in_array('in_app', $tplChannels) ? 'checked' : '' }} onchange="refreshChannelBadges()">
                                    <label class="form-check-label fw-bold" for="chApp">
                                        <i class="fa-solid fa-bell text-warning me-1"></i> In-App
                                    </label>
                                    <small class="text-muted d-block" style="font-size: 11px;">Portal Feed</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Merge Tags Insertion Bar -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Click to Insert Dynamic Merge Tags into Message Body:</label>
                    <div class="d-flex flex-wrap gap-1">
                        <button type="button" class="btn btn-xs btn-light border" onclick="insertTag('{student_name}')"><code>{student_name}</code></button>
                        <button type="button" class="btn btn-xs btn-light border" onclick="insertTag('{parent_name}')"><code>{parent_name}</code></button>
                        <button type="button" class="btn btn-xs btn-light border" onclick="insertTag('{school_name}')"><code>{school_name}</code></button>
                        <button type="button" class="btn btn-xs btn-light border" onclick="insertTag('{class}')"><code>{class}</code></button>
                        <button type="button" class="btn btn-xs btn-light border" onclick="insertTag('{holiday_name}')"><code>{holiday_name}</code></button>
                        <button type="button" class="btn btn-xs btn-light border" onclick="insertTag('{date}')"><code>{date}</code></button>
                        <button type="button" class="btn btn-xs btn-light border" onclick="insertTag('{resumption_date}')"><code>{resumption_date}</code></button>
                    </div>
                </div>

                <!-- Message Body Textarea -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Message Body *</label>
                    <textarea name="message" id="msgTextarea" rows="6" class="form-control" placeholder="Write or load announcement message here..." required>{{ $selectedTemplate?->message }}</textarea>
                    <div class="d-flex justify-content-between mt-1 text-muted small">
                        <span>Dynamic tags are replaced individually for each recipient upon dispatch.</span>
                        <span id="charCountDisplay">0 characters (1 SMS segment)</span>
                    </div>
                </div>

                <!-- Save to Template Library Checkbox -->
                <div class="p-3 bg-light rounded-3 border mb-4">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="save_as_template" value="1" id="saveAsTemplateCheck" onchange="toggleSaveAsTemplate()">
                        <label class="form-check-label fw-bold text-dark" for="saveAsTemplateCheck">
                            <i class="fa-solid fa-floppy-disk text-primary me-1"></i> Save this broadcast into Template Library for future seasonal re-use
                        </label>
                    </div>
                    <div id="templateCategoryWrap" style="display: none;" class="mt-2">
                        <label class="form-label small text-muted">Assign Template Category</label>
                        <select name="template_category" class="form-select form-select-sm">
                            <option value="seasonal">Seasonal & New Year Greetings</option>
                            <option value="religious">Religious Observances & Holy Days</option>
                            <option value="gender_event">Gender & Inclusion Events</option>
                            <option value="holiday_notification">Holiday Notifications & Closures</option>
                            <option value="academic_milestone">Academic Milestones & Directives</option>
                        </select>
                    </div>
                </div>

                <!-- Submit / Action Footer -->
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('broadcasts.index') }}" class="btn btn-light px-4">Cancel</a>
                    <button type="submit" id="submitBroadcastBtn" class="btn btn-warning rounded-pill px-5 fw-bold shadow">
                        <i class="fa-solid fa-paper-plane me-2"></i> <span id="submitBtnText">Transmit Broadcast Now</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Real-Time Interactive Omnichannel Preview Column -->
        <div class="col-lg-5">
            <div class="portal-card p-4 position-sticky" style="top: 20px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-mobile-screen me-2 text-primary"></i> Live Omnichannel Preview
                    </h5>
                    <span class="badge bg-success-subtle text-success border border-success-subtle small">Interactive</span>
                </div>
                <p class="text-muted small mb-3">Preview how recipients will view your message across chosen transmission channels with simulated merge tags.</p>

                <!-- Channel Switcher Tabs -->
                <ul class="nav nav-pills nav-fill mb-3" id="previewTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-2 px-2 small fw-bold" id="tab-sms-btn" data-bs-toggle="pill" data-bs-target="#tab-sms" type="button" role="tab">
                            <i class="fa-solid fa-comment-sms me-1"></i> SMS
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-2 px-2 small fw-bold" id="tab-wa-btn" data-bs-toggle="pill" data-bs-target="#tab-wa" type="button" role="tab">
                            <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-2 px-2 small fw-bold" id="tab-email-btn" data-bs-toggle="pill" data-bs-target="#tab-email" type="button" role="tab">
                            <i class="fa-solid fa-envelope me-1"></i> Email
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-2 px-2 small fw-bold" id="tab-app-btn" data-bs-toggle="pill" data-bs-target="#tab-app" type="button" role="tab">
                            <i class="fa-solid fa-bell me-1"></i> In-App
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="previewTabContent">
                    <!-- SMS Phone Screen Mockup -->
                    <div class="tab-pane fade show active" id="tab-sms" role="tabpanel">
                        <div class="bg-dark text-white rounded-4 p-3 shadow-sm" style="background: #1e293b !important; border: 4px solid #334155;">
                            <!-- Phone Top Bar -->
                            <div class="d-flex justify-content-between align-items-center small text-light pb-2 mb-2 border-bottom border-secondary" style="font-size: 11px;">
                                <span><i class="fa-solid fa-signal me-1"></i> LTE</span>
                                <span class="fw-bold">SCHOLAR SMS</span>
                                <span><i class="fa-solid fa-battery-full"></i> 100%</span>
                            </div>
                            <div class="text-center text-muted small mb-2" style="font-size: 10px;">Today, {{ now()->format('g:i A') }}</div>

                            <!-- SMS Message Bubble -->
                            <div class="bg-primary text-white p-3 rounded-4 mb-2 shadow-sm text-start" style="border-bottom-right-radius: 4px !important; font-size: 13px; line-height: 1.45;">
                                <div class="fw-bold small mb-1 border-bottom border-white-50 pb-1" id="previewSmsTitle">
                                    {{ $selectedTemplate?->title ?? 'Institutional Announcement' }}
                                </div>
                                <div id="previewSmsBody" style="white-space: pre-line;">
                                    {{ $selectedTemplate?->message ?? 'Type your announcement or select a greeting template to preview live substitutions.' }}
                                </div>
                                <div class="text-end text-white-50 mt-1" style="font-size: 10px;">
                                    Delivered via Twilio / Termii Carrier &bull; Just now
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp Screen Mockup -->
                    <div class="tab-pane fade" id="tab-wa" role="tabpanel">
                        <div class="rounded-4 p-3 shadow-sm" style="background: #0b141a; border: 4px solid #1f2c34;">
                            <!-- WhatsApp Header -->
                            <div class="d-flex align-items-center pb-2 mb-2 border-bottom border-secondary text-white">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 14px;">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </div>
                                <div>
                                    <div class="fw-bold small text-white">Scholar SMS Official Verified <i class="fa-solid fa-circle-check text-success ms-1 small"></i></div>
                                    <div class="text-muted" style="font-size: 10px;">Meta Cloud API Business</div>
                                </div>
                            </div>

                            <!-- WA Bubble -->
                            <div class="p-3 rounded-3 mb-1 text-white shadow-sm" style="background: #005c4b; font-size: 13px; border-bottom-left-radius: 4px !important; line-height: 1.45;">
                                <div class="fw-bold text-warning mb-1" id="previewWaTitle">
                                    {{ $selectedTemplate?->title ?? 'Institutional Announcement' }}
                                </div>
                                <div id="previewWaBody" style="white-space: pre-line;">
                                    {{ $selectedTemplate?->message ?? 'Type your announcement or select a greeting template to preview live substitutions.' }}
                                </div>
                                <div class="text-end text-white-50 mt-1" style="font-size: 10px;">
                                    {{ now()->format('g:i A') }} <i class="fa-solid fa-check-double text-info ms-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Preview Mockup -->
                    <div class="tab-pane fade" id="tab-email" role="tabpanel">
                        <div class="border rounded-3 p-3 bg-white shadow-sm">
                            <div class="border-bottom pb-2 mb-2">
                                <div class="small text-muted"><strong class="text-dark">From:</strong> Scholar SMS Academy &lt;no-reply@scholarsms.edu&gt;</div>
                                <div class="small text-muted"><strong class="text-dark">To:</strong> Alexander Cole &lt;alexander@student.scholarsms.edu&gt;</div>
                                <div class="small text-muted"><strong class="text-dark">Subject:</strong> <span id="previewEmailSubject" class="fw-bold text-dark">{{ $selectedTemplate?->title ?? 'Institutional Announcement' }}</span></div>
                            </div>
                            <div class="p-3 bg-light rounded border text-dark" style="font-size: 13px; line-height: 1.5;">
                                <div class="text-center pb-2 mb-3 border-bottom">
                                    <h6 class="fw-bold text-primary mb-0">SCHOLAR SMS INSTITUTIONAL DIRECTIVE</h6>
                                    <small class="text-muted">Official Academic & Campus Bulletin</small>
                                </div>
                                <div id="previewEmailBody" style="white-space: pre-line;">
                                    {{ $selectedTemplate?->message ?? 'Type your announcement or select a greeting template to preview live substitutions.' }}
                                </div>
                                <div class="mt-4 pt-2 border-top text-muted small" style="font-size: 11px;">
                                    Scholar SMS Academy &bull; 100 University Avenue, Tech Park
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- In-App Portal Notification Mockup -->
                    <div class="tab-pane fade" id="tab-app" role="tabpanel">
                        <div class="p-3 rounded-3 border bg-white shadow-sm" style="border-left: 4px solid #009961 !important;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-bell me-1"></i> In-App Notice</span>
                                <small class="text-muted" style="font-size: 11px;">Just now</small>
                            </div>
                            <h6 class="fw-bold text-dark mb-1" id="previewAppTitle">{{ $selectedTemplate?->title ?? 'Institutional Announcement' }}</h6>
                            <p class="text-muted small mb-0" id="previewAppBody" style="font-size: 12px; white-space: pre-line;">
                                {{ $selectedTemplate?->message ?? 'Type your announcement or select a greeting template to preview live substitutions.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Simulation Info Box -->
                <div class="mt-3 p-2 bg-light rounded text-muted small border" style="font-size: 11px;">
                    <i class="fa-solid fa-circle-info text-primary me-1"></i>
                    <strong>Simulated Recipient Context:</strong>
                    Alexander Cole (Grade 10 - Alpha) &bull; Guardian: Mr. & Mrs. Cole &bull; Resumption: {{ now()->addDays(7)->format('l, M d') }}
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    // Templates Data Map from Backend
    const templatesData = @json($templates->keyBy('id'));

    const audienceSelect = document.getElementById('audienceSelect');
    const cohortSelectWrap = document.getElementById('cohortSelectWrap');
    const msgTextarea = document.getElementById('msgTextarea');
    const titleInput = document.getElementById('titleInput');
    const prioritySelect = document.getElementById('prioritySelect');
    const templateSelect = document.getElementById('templateSelect');
    const broadcastTemplateId = document.getElementById('broadcastTemplateId');
    const charCountDisplay = document.getElementById('charCountDisplay');
    const submitBtnText = document.getElementById('submitBtnText');

    // Previews
    const previewSmsTitle = document.getElementById('previewSmsTitle');
    const previewSmsBody = document.getElementById('previewSmsBody');
    const previewWaTitle = document.getElementById('previewWaTitle');
    const previewWaBody = document.getElementById('previewWaBody');
    const previewEmailSubject = document.getElementById('previewEmailSubject');
    const previewEmailBody = document.getElementById('previewEmailBody');
    const previewAppTitle = document.getElementById('previewAppTitle');
    const previewAppBody = document.getElementById('previewAppBody');

    // Audience toggle for cohort
    audienceSelect.addEventListener('change', function() {
        cohortSelectWrap.style.display = (this.value === 'class') ? 'block' : 'none';
    });

    // Tag insertion helper
    function insertTag(tag) {
        msgTextarea.value += ' ' + tag + ' ';
        msgTextarea.focus();
        updateLivePreview();
    }

    // Toggle save as template
    function toggleSaveAsTemplate() {
        const check = document.getElementById('saveAsTemplateCheck');
        const wrap = document.getElementById('templateCategoryWrap');
        wrap.style.display = check.checked ? 'block' : 'none';
    }

    // Send Mode Switcher
    function updateSendMode(mode) {
        document.querySelectorAll('.send-mode-card').forEach(c => c.classList.remove('active', 'border-primary', 'bg-light'));
        
        const schedWrap = document.getElementById('scheduledPickerWrap');
        const holWrap = document.getElementById('holidayPickerWrap');
        const schedInput = document.getElementById('scheduledAtInput');
        const holSelect = document.getElementById('holidaySelect');

        if (mode === 'immediate') {
            document.getElementById('modeCardImmediate').classList.add('active', 'border-primary', 'bg-light');
            schedWrap.style.display = 'none';
            holWrap.style.display = 'none';
            schedInput.required = false;
            holSelect.required = false;
            submitBtnText.innerText = 'Transmit Broadcast Now';
        } else if (mode === 'scheduled') {
            document.getElementById('modeCardScheduled').classList.add('active', 'border-primary', 'bg-light');
            schedWrap.style.display = 'block';
            holWrap.style.display = 'none';
            schedInput.required = true;
            holSelect.required = false;
            submitBtnText.innerText = 'Schedule Broadcast';
        } else if (mode === 'auto_holiday') {
            document.getElementById('modeCardAutoHoliday').classList.add('active', 'border-primary', 'bg-light');
            schedWrap.style.display = 'none';
            holWrap.style.display = 'block';
            schedInput.required = false;
            holSelect.required = true;
            submitBtnText.innerText = 'Activate Holiday Auto-Dispatch';
        }
    }

    // Template Selection Auto-fill
    templateSelect.addEventListener('change', function() {
        const tplId = this.value;
        if (!tplId || !templatesData[tplId]) {
            broadcastTemplateId.value = '';
            return;
        }

        const tpl = templatesData[tplId];
        broadcastTemplateId.value = tpl.id;
        titleInput.value = tpl.title;
        msgTextarea.value = tpl.message;

        if (tpl.priority) {
            prioritySelect.value = tpl.priority;
        }

        if (tpl.target_audience) {
            audienceSelect.value = tpl.target_audience;
            cohortSelectWrap.style.display = (tpl.target_audience === 'class') ? 'block' : 'none';
        }

        // Channels
        const channels = tpl.channels || ['sms', 'whatsapp', 'email', 'in_app'];
        document.getElementById('chSms').checked = channels.includes('sms');
        document.getElementById('chWa').checked = channels.includes('whatsapp');
        document.getElementById('chEmail').checked = channels.includes('email');
        document.getElementById('chApp').checked = channels.includes('in_app');

        // If template has linked holiday, switch to holiday mode
        if (tpl.is_auto_send && tpl.school_calendar_holiday_id) {
            document.getElementById('modeAutoHoliday').checked = true;
            updateSendMode('auto_holiday');
            const holSelect = document.getElementById('holidaySelect');
            if (holSelect) {
                holSelect.value = tpl.school_calendar_holiday_id;
            }
        }

        updateLivePreview();
    });

    // Simulated Replacements for Live Preview
    function substituteTags(text) {
        if (!text) return '';
        return text
            .replaceAll('{student_name}', 'Alexander Cole')
            .replaceAll('{parent_name}', 'Mr. & Mrs. Cole')
            .replaceAll('{school_name}', 'Scholar SMS Academy')
            .replaceAll('{class}', 'Grade 10 - Alpha')
            .replaceAll('{holiday_name}', getHolidayName())
            .replaceAll('{date}', '{{ now()->format("M d, Y") }}')
            .replaceAll('{resumption_date}', '{{ now()->addDays(7)->format("l, M d, Y") }}');
    }

    function getHolidayName() {
        const holSelect = document.getElementById('holidaySelect');
        if (holSelect && holSelect.selectedIndex > 0) {
            return holSelect.options[holSelect.selectedIndex].getAttribute('data-title') || 'National Holiday';
        }
        return 'School Holiday';
    }

    function updateLivePreview() {
        const rawTitle = titleInput.value.trim() || 'Institutional Announcement';
        const rawMsg = msgTextarea.value || 'Type your announcement or select a greeting template to preview live substitutions.';

        const substitutedMsg = substituteTags(rawMsg);
        const substitutedTitle = substituteTags(rawTitle);

        // SMS
        previewSmsTitle.innerText = substitutedTitle;
        previewSmsBody.innerText = substitutedMsg;

        // WhatsApp
        previewWaTitle.innerText = substitutedTitle;
        previewWaBody.innerText = substitutedMsg;

        // Email
        previewEmailSubject.innerText = substitutedTitle;
        previewEmailBody.innerText = substitutedMsg;

        // In-App
        previewAppTitle.innerText = substitutedTitle;
        previewAppBody.innerText = substitutedMsg;

        // Char counter
        const len = rawMsg.length;
        const segments = Math.ceil(len / 160) || 1;
        charCountDisplay.innerText = `${len} characters (${segments} SMS segment${segments > 1 ? 's' : ''})`;
    }

    function refreshChannelBadges() {
        // Can optionally disable preview tabs if channel unchecked
    }

    // Input event listeners for real-time reactivity
    titleInput.addEventListener('input', updateLivePreview);
    msgTextarea.addEventListener('input', updateLivePreview);
    document.getElementById('holidaySelect').addEventListener('change', updateLivePreview);

    // Initial render
    document.addEventListener('DOMContentLoaded', function() {
        updateLivePreview();
    });
</script>
<style>
    .send-mode-card {
        transition: all 0.2s ease-in-out;
    }
    .send-mode-card:hover {
        border-color: #009961 !important;
        background-color: #f8fafc;
    }
    .send-mode-card.active {
        border-color: #009961 !important;
        background-color: #f0fdf4 !important;
    }
</style>
@endsection