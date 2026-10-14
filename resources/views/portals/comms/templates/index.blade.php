@extends('layouts.portal')

@section('title', 'Broadcast Templates Library — Scholar SMS')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('broadcasts.index') }}" class="text-decoration-none">Communications Hub</a></li>
                <li class="breadcrumb-item active" aria-current="page">Message Templates</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-1">Broadcast Templates & Auto-Greetings</h2>
        <p class="text-muted mb-0">Pre-crafted templates for seasonal milestones, religious observances, gender events, and holiday notifications with manual and auto-send capabilities.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('broadcasts.create') }}" class="btn btn-outline-warning">
            <i class="fa-solid fa-paper-plane me-1"></i> Compose Broadcast
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
            <i class="fa-solid fa-plus-circle me-1"></i> Create New Template
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert">
    <i class="fa-solid fa-circle-check fs-5 me-2"></i>
    <div>{{ session('success') }}</div>
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

<!-- Stats Counters -->
<div class="row g-3 mb-4">
    <div class="col-xl-2 col-md-4 col-6">
        <div class="portal-card p-3 border h-100">
            <span class="metric-label" style="font-size: 11px;">Total Templates</span>
            <div class="fs-4 fw-bold text-dark mt-1">{{ $stats['total'] }}</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="portal-card p-3 border h-100">
            <span class="metric-label" style="font-size: 11px;">Auto-Send Active</span>
            <div class="fs-4 fw-bold text-emerald mt-1">
                <i class="fa-solid fa-bolt me-1 text-emerald small"></i>{{ $stats['auto_send_active'] }}
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="portal-card p-3 border h-100">
            <span class="metric-label" style="font-size: 11px;">Seasonal Greetings</span>
            <div class="fs-4 fw-bold text-dark mt-1">{{ $stats['seasonal'] }}</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="portal-card p-3 border h-100">
            <span class="metric-label" style="font-size: 11px;">Religious Events</span>
            <div class="fs-4 fw-bold text-dark mt-1">{{ $stats['religious'] }}</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="portal-card p-3 border h-100">
            <span class="metric-label" style="font-size: 11px;">Gender & Inclusion</span>
            <div class="fs-4 fw-bold text-dark mt-1">{{ $stats['gender_event'] }}</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="portal-card p-3 border h-100">
            <span class="metric-label" style="font-size: 11px;">Holiday Notices</span>
            <div class="fs-4 fw-bold text-dark mt-1">{{ $stats['holiday_notification'] }}</div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="portal-card p-3 mb-4">
    <form action="{{ route('broadcasts.templates.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-lg-4 col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search templates by keyword, title, tag..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <select name="category" class="form-select" onchange="this.form.submit()">
                <option value="">All Greeting Categories</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="form-check form-switch pt-1">
                <input class="form-check-input" type="checkbox" name="auto_send" value="1" id="filterAutoSend" {{ request('auto_send') ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="form-check-label fw-semibold small" for="filterAutoSend">
                    <i class="fa-solid fa-clock-rotate-left text-success me-1"></i> Show Auto-Send Enabled Only
                </label>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 text-end">
            <a href="{{ route('broadcasts.templates.index') }}" class="btn btn-outline-secondary w-100">
                <i class="fa-solid fa-rotate-left me-1"></i> Reset
            </a>
        </div>
    </form>
</div>

<!-- Category Pills Navigation -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('broadcasts.templates.index') }}" class="btn btn-sm {{ !request('category') ? 'btn-dark' : 'btn-light border' }} rounded-pill px-3">
        All Categories ({{ $stats['total'] }})
    </a>
    <a href="{{ route('broadcasts.templates.index', ['category' => 'seasonal']) }}" class="btn btn-sm {{ request('category') === 'seasonal' ? 'btn-info text-white' : 'btn-light border' }} rounded-pill px-3">
        <i class="fa-solid fa-snowflake me-1"></i> Seasonal & New Year ({{ $stats['seasonal'] }})
    </a>
    <a href="{{ route('broadcasts.templates.index', ['category' => 'religious']) }}" class="btn btn-sm {{ request('category') === 'religious' ? 'btn-warning text-dark' : 'btn-light border' }} rounded-pill px-3">
        <i class="fa-solid fa-hands-praying me-1"></i> Religious Holy Days ({{ $stats['religious'] }})
    </a>
    <a href="{{ route('broadcasts.templates.index', ['category' => 'gender_event']) }}" class="btn btn-sm {{ request('category') === 'gender_event' ? 'btn-primary' : 'btn-light border' }} rounded-pill px-3">
        <i class="fa-solid fa-venus-mars me-1"></i> Gender & Inclusion ({{ $stats['gender_event'] }})
    </a>
    <a href="{{ route('broadcasts.templates.index', ['category' => 'holiday_notification']) }}" class="btn btn-sm {{ request('category') === 'holiday_notification' ? 'btn-danger' : 'btn-light border' }} rounded-pill px-3">
        <i class="fa-solid fa-calendar-xmark me-1"></i> Holiday Notifications ({{ $stats['holiday_notification'] }})
    </a>
</div>

<!-- Template Cards Grid -->
@if($templates->count() > 0)
<div class="row g-4">
    @foreach($templates as $tpl)
    @php
        $meta = $tpl->category_meta;
    @endphp
    <div class="col-xl-4 col-md-6">
        <div class="portal-card h-100 d-flex flex-column border shadow-sm position-relative">
            <!-- Card Header -->
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top">
                <span class="badge {{ $meta['badge'] }} px-2 py-1">
                    <i class="{{ $meta['icon'] }} me-1"></i> {{ $meta['label'] }}
                </span>
                <div class="d-flex align-items-center gap-1">
                    @if($tpl->priority === 'urgent')
                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-triangle-exclamation me-1"></i>Urgent</span>
                    @elseif($tpl->priority === 'emergency')
                        <span class="badge bg-danger"><i class="fa-solid fa-bell me-1"></i>Emergency</span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary">Normal</span>
                    @endif
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-3 flex-grow-1">
                <h5 class="fw-bold mb-1 text-dark">{{ $tpl->title }}</h5>
                <p class="text-muted small mb-2"><i class="fa-regular fa-envelope me-1"></i> <strong>Subject:</strong> {{ Str::limit($tpl->subject, 50) }}</p>

                <!-- Channels & Recipient -->
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <span class="badge bg-light text-dark border">
                        <i class="fa-solid fa-users me-1 text-primary"></i> {{ ucfirst($tpl->target_audience) }}
                    </span>
                    @if(in_array('sms', $tpl->channels ?? []))
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="fa-solid fa-comment-sms me-1"></i>SMS</span>
                    @endif
                    @if(in_array('whatsapp', $tpl->channels ?? []))
                        <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fa-brands fa-whatsapp me-1"></i>WA</span>
                    @endif
                    @if(in_array('email', $tpl->channels ?? []))
                        <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="fa-solid fa-envelope me-1"></i>Email</span>
                    @endif
                    @if(in_array('in_app', $tpl->channels ?? []))
                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle"><i class="fa-solid fa-bell me-1"></i>Portal</span>
                    @endif
                </div>

                <!-- Message Body Preview -->
                <div class="p-2 bg-light rounded-2 border text-secondary small font-monospace mb-3" style="font-size: 12px; max-height: 90px; overflow-y: auto;">
                    {{ Str::limit($tpl->message, 170) }}
                </div>

                <!-- Auto-Send Status Pill & Linked Calendar Trigger -->
                <div class="p-2 rounded-2 border {{ $tpl->is_auto_send ? 'bg-success-subtle border-success-subtle' : 'bg-light' }} d-flex justify-content-between align-items-center">
                    <div>
                        @if($tpl->is_auto_send)
                            <div class="fw-bold text-success small d-flex align-items-center">
                                <span class="spinner-grow spinner-grow-sm text-success me-1" style="width: 8px; height: 8px;" role="status"></span>
                                Auto-Send Active
                            </div>
                            <small class="text-muted" style="font-size: 11px;">
                                @if($tpl->linkedHoliday)
                                    Trigger: {{ $tpl->linkedHoliday->title }} ({{ $tpl->linkedHoliday->start_date->format('M d') }} @ {{ $tpl->auto_trigger_time ?? '08:00' }})
                                @else
                                    Trigger: Calendar Holiday Linked
                                @endif
                            </small>
                        @else
                            <div class="fw-semibold text-secondary small">
                                <i class="fa-solid fa-hand me-1"></i> Manual Dispatch Only
                            </div>
                            <small class="text-muted" style="font-size: 11px;">Requires manual one-click compose</small>
                        @endif
                    </div>
                    @if(auth()->user()->isAdmin())
                    <form action="{{ route('broadcasts.templates.toggle-auto-send', $tpl->id) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-xs {{ $tpl->is_auto_send ? 'btn-outline-danger' : 'btn-outline-success' }} rounded-pill px-2 py-1" style="font-size: 11px;" title="{{ $tpl->is_auto_send ? 'Disable automated trigger' : 'Enable automated trigger' }}">
                            {{ $tpl->is_auto_send ? 'Disable Auto' : 'Enable Auto' }}
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Card Actions Footer -->
            <div class="p-3 border-top bg-light rounded-bottom d-flex justify-content-between align-items-center">
                <a href="{{ route('broadcasts.create', ['template_id' => $tpl->id]) }}" class="btn btn-sm btn-warning fw-bold px-3">
                    <i class="fa-solid fa-paper-plane me-1"></i> Use in Compose
                </a>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#previewModal-{{ $tpl->id }}" title="Preview with simulated merge tags">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal-{{ $tpl->id }}" title="Edit Template">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    @if(auth()->user()->isAdmin())
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $tpl->id }}" title="Delete Template">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal-{{ $tpl->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="{{ $meta['icon'] }} me-2 text-primary"></i> {{ $tpl->title }} Preview
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">TEMPLATE SUBJECT</label>
                        <div class="p-2 bg-light border rounded fw-semibold text-dark">{{ $tpl->subject }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">MERGE TAG SUBSTITUTED PREVIEW (RECIPIENT: ALEXANDER COLE)</label>
                        <div class="p-3 rounded border text-dark" style="background-color: #f8fbf9; border-left: 4px solid #009961 !important; white-space: pre-line;">
                            {{ $tpl->renderSubstitutedMessage([
                                '{student_name}' => 'Alexander Cole',
                                '{parent_name}' => 'Mr. & Mrs. Cole',
                                '{class}' => 'Grade 10 - Alpha',
                                '{school_name}' => 'Scholar SMS Academy',
                                '{holiday_name}' => $tpl->linkedHoliday ? $tpl->linkedHoliday->title : 'Public Holiday',
                                '{date}' => now()->format('M d, Y'),
                                '{resumption_date}' => now()->addDays(7)->format('l, M d, Y')
                            ]) }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center text-muted small p-2 bg-light rounded">
                        <span><i class="fa-solid fa-users me-1"></i> Target: <strong>{{ ucfirst($tpl->target_audience) }}</strong></span>
                        <span><i class="fa-solid fa-signal me-1"></i> Priority: <strong>{{ ucfirst($tpl->priority) }}</strong></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <a href="{{ route('broadcasts.create', ['template_id' => $tpl->id]) }}" class="btn btn-warning fw-bold">
                        <i class="fa-solid fa-paper-plane me-1"></i> Use Template in Compose
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal-{{ $tpl->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('broadcasts.templates.update', $tpl->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i> Edit Broadcast Template</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Template Title *</label>
                                <input type="text" name="title" class="form-control" value="{{ $tpl->title }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Greeting Category *</label>
                                <select name="category" class="form-select" required>
                                    @foreach($categories as $key => $lbl)
                                        <option value="{{ $key }}" {{ $tpl->category === $key ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Email & In-App Subject Line *</label>
                                <input type="text" name="subject" class="form-control" value="{{ $tpl->subject }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Target Audience *</label>
                                <select name="target_audience" class="form-select" required>
                                    <option value="all" {{ $tpl->target_audience === 'all' ? 'selected' : '' }}>All School Community</option>
                                    <option value="students" {{ $tpl->target_audience === 'students' ? 'selected' : '' }}>Students Only</option>
                                    <option value="parents" {{ $tpl->target_audience === 'parents' ? 'selected' : '' }}>Guardians & Parents Only</option>
                                    <option value="teachers" {{ $tpl->target_audience === 'teachers' ? 'selected' : '' }}>Teachers & Faculty</option>
                                    <option value="class" {{ $tpl->target_audience === 'class' ? 'selected' : '' }}>Specific Cohort</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Default Priority *</label>
                                <select name="priority" class="form-select" required>
                                    <option value="normal" {{ $tpl->priority === 'normal' ? 'selected' : '' }}>Normal Institutional Notice</option>
                                    <option value="urgent" {{ $tpl->priority === 'urgent' ? 'selected' : '' }}>Urgent Advisory</option>
                                    <option value="emergency" {{ $tpl->priority === 'emergency' ? 'selected' : '' }}>Emergency Protocol</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Delivery Channels *</label>
                                <div class="d-flex flex-wrap gap-3 p-2 bg-light rounded border">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="channels[]" value="sms" id="editChSms-{{ $tpl->id }}" {{ in_array('sms', $tpl->channels ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="editChSms-{{ $tpl->id }}">SMS Gateway</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="channels[]" value="whatsapp" id="editChWa-{{ $tpl->id }}" {{ in_array('whatsapp', $tpl->channels ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="editChWa-{{ $tpl->id }}">WhatsApp</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="channels[]" value="email" id="editChEmail-{{ $tpl->id }}" {{ in_array('email', $tpl->channels ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="editChEmail-{{ $tpl->id }}">Email Digest</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="channels[]" value="in_app" id="editChApp-{{ $tpl->id }}" {{ in_array('in_app', $tpl->channels ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="editChApp-{{ $tpl->id }}">In-App Portal</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Merge Tag Toolbar -->
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-muted">Click to Append Dynamic Merge Tags:</label>
                                <div class="d-flex flex-wrap gap-1">
                                    <button type="button" class="btn btn-xs btn-light border" onclick="appendTagTo('msgEdit-{{ $tpl->id }}', '{student_name}')"><code>{student_name}</code></button>
                                    <button type="button" class="btn btn-xs btn-light border" onclick="appendTagTo('msgEdit-{{ $tpl->id }}', '{parent_name}')"><code>{parent_name}</code></button>
                                    <button type="button" class="btn btn-xs btn-light border" onclick="appendTagTo('msgEdit-{{ $tpl->id }}', '{school_name}')"><code>{school_name}</code></button>
                                    <button type="button" class="btn btn-xs btn-light border" onclick="appendTagTo('msgEdit-{{ $tpl->id }}', '{class}')"><code>{class}</code></button>
                                    <button type="button" class="btn btn-xs btn-light border" onclick="appendTagTo('msgEdit-{{ $tpl->id }}', '{holiday_name}')"><code>{holiday_name}</code></button>
                                    <button type="button" class="btn btn-xs btn-light border" onclick="appendTagTo('msgEdit-{{ $tpl->id }}', '{resumption_date}')"><code>{resumption_date}</code></button>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Message Body *</label>
                                <textarea name="message" id="msgEdit-{{ $tpl->id }}" rows="5" class="form-control" required>{{ $tpl->message }}</textarea>
                            </div>

                            <!-- Auto-Send Automation Section -->
                            <div class="col-12">
                                <div class="p-3 bg-light rounded border">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="is_auto_send" value="1" id="autoSwitch-{{ $tpl->id }}" {{ $tpl->is_auto_send ? 'checked' : '' }} onchange="toggleHolidaySelect('{{ $tpl->id }}')">
                                        <label class="form-check-label fw-bold text-dark" for="autoSwitch-{{ $tpl->id }}">
                                            <i class="fa-solid fa-bolt text-warning me-1"></i> Enable Automated Holiday Dispatch
                                        </label>
                                    </div>
                                    <div id="holidayWrap-{{ $tpl->id }}" style="display: {{ $tpl->is_auto_send ? 'block' : 'none' }};">
                                        <div class="row g-2 mt-1">
                                            <div class="col-md-8">
                                                <label class="form-label small text-muted">Trigger on School Calendar Holiday</label>
                                                <select name="school_calendar_holiday_id" class="form-select form-select-sm">
                                                    <option value="">Choose Calendar Holiday to link...</option>
                                                    @foreach($holidays as $hol)
                                                        <option value="{{ $hol->id }}" {{ $tpl->school_calendar_holiday_id == $hol->id ? 'selected' : '' }}>
                                                            {{ $hol->title }} ({{ $hol->start_date->format('M d, Y') }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Dispatch Time</label>
                                                <input type="time" name="auto_trigger_time" class="form-control form-control-sm" value="{{ $tpl->auto_trigger_time ?? '08:00' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if(auth()->user()->isAdmin())
    <div class="modal fade" id="deleteModal-{{ $tpl->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('broadcasts.templates.destroy', $tpl->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i> Delete Template</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <i class="fa-solid fa-trash-can text-danger fs-1 mb-3"></i>
                        <h5 class="fw-bold">Are you sure you want to delete this template?</h5>
                        <p class="text-muted mb-0">"<strong>{{ $tpl->title }}</strong>" will be permanently removed from the template library.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4 fw-bold">Delete Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endforeach
</div>

<div class="mt-4">
    {{ $templates->links('pagination::bootstrap-5') }}
</div>
@else
<div class="portal-card p-5 text-center">
    <div class="mb-3">
        <i class="fa-solid fa-envelope-open-text text-muted" style="font-size: 3rem;"></i>
    </div>
    <h4 class="fw-bold">No Templates Found</h4>
    <p class="text-muted mb-4">No broadcast templates match your filter criteria or search keyword.</p>
    <a href="{{ route('broadcasts.templates.index') }}" class="btn btn-outline-secondary me-2">Reset Filters</a>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
        <i class="fa-solid fa-plus-circle me-1"></i> Create First Template
    </button>
</div>
@endif

<!-- Create Template Modal -->
<div class="modal fade" id="createTemplateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('broadcasts.templates.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-wand-magic-sparkles me-2"></i> Create Broadcast Template</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Template Title *</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Eid al-Fitr Mubarak Community Feast" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Greeting Category *</label>
                            <select name="category" class="form-select" required>
                                @foreach($categories as $key => $lbl)
                                    <option value="{{ $key }}">{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Email & In-App Subject Line *</label>
                            <input type="text" name="subject" class="form-control" placeholder="e.g. Wishing You Peace & Harmony on this Blessed Day" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Target Audience *</label>
                            <select name="target_audience" class="form-select" required>
                                <option value="all" selected>All School Community (Faculty, Students, Parents)</option>
                                <option value="students">Students Only</option>
                                <option value="parents">Guardians & Parents Only</option>
                                <option value="teachers">Teachers & Faculty Only</option>
                                <option value="class">Specific Academic Cohort</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Default Priority *</label>
                            <select name="priority" class="form-select" required>
                                <option value="normal" selected>Normal Institutional Notice</option>
                                <option value="urgent">Urgent Advisory</option>
                                <option value="emergency">Emergency Protocol</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Default Transmission Channels *</label>
                            <div class="d-flex flex-wrap gap-3 p-2 bg-light rounded border">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="sms" id="createChSms" checked>
                                    <label class="form-check-label fw-semibold" for="createChSms">SMS Gateway</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="whatsapp" id="createChWa" checked>
                                    <label class="form-check-label fw-semibold" for="createChWa">WhatsApp</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="email" id="createChEmail" checked>
                                    <label class="form-check-label fw-semibold" for="createChEmail">Email Digest</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="in_app" id="createChApp" checked>
                                    <label class="form-check-label fw-semibold" for="createChApp">In-App Portal</label>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Merge Tag Insertion Toolbar -->
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-muted">Click to Insert Dynamic Merge Tags into Message Body:</label>
                            <div class="d-flex flex-wrap gap-1">
                                <button type="button" class="btn btn-xs btn-light border" onclick="appendTagTo('createMsgBody', '{student_name}')"><code>{student_name}</code></button>
                                <button type="button" class="btn btn-xs btn-light border" onclick="appendTagTo('createMsgBody', '{parent_name}')"><code>{parent_name}</code></button>
                                <button type="button" class="btn btn-xs btn-light border" onclick="appendTagTo('createMsgBody', '{school_name}')"><code>{school_name}</code></button>
                                <button type="button" class="btn btn-xs btn-light border" onclick="appendTagTo('createMsgBody', '{class}')"><code>{class}</code></button>
                                <button type="button" class="btn btn-xs btn-light border" onclick="appendTagTo('createMsgBody', '{holiday_name}')"><code>{holiday_name}</code></button>
                                <button type="button" class="btn btn-xs btn-light border" onclick="appendTagTo('createMsgBody', '{date}')"><code>{date}</code></button>
                                <button type="button" class="btn btn-xs btn-light border" onclick="appendTagTo('createMsgBody', '{resumption_date}')"><code>{resumption_date}</code></button>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Message Body *</label>
                            <textarea name="message" id="createMsgBody" rows="5" class="form-control" placeholder="Compose greeting or holiday notification..." required></textarea>
                            <div class="d-flex justify-content-between mt-1 text-muted small">
                                <span>Dynamic tags will be replaced with real recipient data during transmission.</span>
                                <span id="createCharCount">0 characters (1 SMS)</span>
                            </div>
                        </div>

                        <!-- Automated Holiday Dispatch Option -->
                        <div class="col-12">
                            <div class="p-3 bg-light rounded border">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_auto_send" value="1" id="createAutoSwitch" onchange="toggleCreateHolidaySelect()">
                                    <label class="form-check-label fw-bold text-dark" for="createAutoSwitch">
                                        <i class="fa-solid fa-bolt text-warning me-1"></i> Enable Automated Holiday Dispatch
                                    </label>
                                </div>
                                <div id="createHolidayWrap" style="display: none;">
                                    <div class="row g-2 mt-1">
                                        <div class="col-md-8">
                                            <label class="form-label small text-muted">Trigger on School Calendar Holiday</label>
                                            <select name="school_calendar_holiday_id" class="form-select form-select-sm">
                                                <option value="">Choose Calendar Holiday to link...</option>
                                                @foreach($holidays as $hol)
                                                    <option value="{{ $hol->id }}">{{ $hol->title }} ({{ $hol->start_date->format('M d, Y') }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small text-muted">Auto Dispatch Time</label>
                                            <input type="time" name="auto_trigger_time" class="form-control form-control-sm" value="08:00">
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-1"><i class="fa-solid fa-circle-info me-1"></i> The scheduler triggers automatic broadcast on the holiday start date at the specified time.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="fa-solid fa-check me-1"></i> Create Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function appendTagTo(elementId, tag) {
        const el = document.getElementById(elementId);
        if (el) {
            el.value += ' ' + tag + ' ';
            el.focus();
        }
    }

    function toggleHolidaySelect(id) {
        const sw = document.getElementById('autoSwitch-' + id);
        const wrap = document.getElementById('holidayWrap-' + id);
        if (sw && wrap) {
            wrap.style.display = sw.checked ? 'block' : 'none';
        }
    }

    function toggleCreateHolidaySelect() {
        const sw = document.getElementById('createAutoSwitch');
        const wrap = document.getElementById('createHolidayWrap');
        if (sw && wrap) {
            wrap.style.display = sw.checked ? 'block' : 'none';
        }
    }

    const createMsgBody = document.getElementById('createMsgBody');
    const createCharCount = document.getElementById('createCharCount');
    if (createMsgBody && createCharCount) {
        createMsgBody.addEventListener('input', function() {
            const len = this.value.length;
            const segments = Math.ceil(len / 160) || 1;
            createCharCount.innerText = `${len} characters (${segments} SMS segment${segments > 1 ? 's' : ''})`;
        });
    }
</script>
@endsection
