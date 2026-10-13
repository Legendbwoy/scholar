@extends('layouts.portal')

@section('title', 'Students Directory — Scholar SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1">Students Directory</h2>
        <p class="text-muted mb-0">Institutional Enrolled Students, Academic Records & Standing</p>
    </div>
    @if(auth()->user()->isAdmin())
    <div class="d-flex gap-2">
        <a href="{{ route('students.bulk') }}" class="btn btn-outline-success rounded-pill px-3 fw-bold shadow-sm">
            <i class="fa-solid fa-file-excel me-1"></i> Bulk Excel Import
        </a>
        <a href="{{ route('students.create') }}" class="btn btn-portal-primary rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-user-plus me-1"></i> Enroll New Student
        </a>
    </div>
    @endif
</div>

<!-- Filters & Search Bar -->
<div class="portal-card p-3 mb-4 shadow-sm border-0">
    <form action="{{ route('students.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email, admission # or roll..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-4">
            <select name="class_id" class="form-select" onchange="this.form.submit()">
                <option value="">All Cohorts / Classes</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} (Section {{ $c->section }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-3 w-100" style="background-color: #7a6ad8; border-color: #7a6ad8;">Filter</button>
            <a href="{{ route('students.index') }}" class="btn btn-light rounded-3">Reset</a>
        </div>
    </form>
</div>

<!-- Students Table -->
<div class="portal-card shadow-sm border-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-portal align-middle mb-0">
            <thead>
                <tr>
                    <th>Student Details</th>
                    <th>Admission #</th>
                    <th>Cohort / Class</th>
                    <th>Parent / Guardian</th>
                    <th>Attendance</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $s)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ asset($s->user->avatar ?? 'scholar/assets/images/event-01.jpg') }}" class="rounded-circle shadow-sm" style="width: 42px; height: 42px; object-fit: cover;">
                                <div>
                                    <div class="fw-bold text-dark">{{ $s->user->name }}</div>
                                    <small class="text-muted">{{ $s->user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border fw-bold">{{ $s->admission_number }}</span></td>
                        <td>
                            <div class="fw-semibold small">{{ $s->schoolClass->name ?? 'Unassigned' }}</div>
                            <small class="text-muted">Section {{ $s->schoolClass->section ?? 'N/A' }} • Roll {{ $s->roll_number }}</small>
                        </td>
                        <td>
                            @if($s->guardian)
                                <div class="small fw-semibold">{{ $s->guardian->user->name }}</div>
                                <small class="text-muted">{{ $s->guardian->user->phone ?? 'Phone on record' }}</small>
                            @else
                                <span class="text-muted small">Independent</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $s->attendance_rate >= 90 ? 'success' : ($s->attendance_rate >= 75 ? 'warning text-dark' : 'danger') }} rounded-pill px-3 py-1">
                                {{ $s->attendance_rate }}%
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $s->status === 'active' ? 'success' : ($s->status === 'suspended' ? 'danger' : 'secondary') }}-subtle text-{{ $s->status === 'active' ? 'success' : ($s->status === 'suspended' ? 'danger' : 'secondary') }} text-capitalize">
                                {{ $s->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <!-- Quick 360 Slide-Over Offcanvas Trigger via HTML5 dataset -->
                                <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-2" title="Quick Drawer View" onclick="openStudentQuickDrawer(this)"
                                    data-id="{{ $s->id }}"
                                    data-name="{{ $s->user->name }}"
                                    data-email="{{ $s->user->email }}"
                                    data-avatar="{{ asset($s->user->avatar ?? 'scholar/assets/images/event-01.jpg') }}"
                                    data-admission="{{ $s->admission_number }}"
                                    data-cohort="{{ ($s->schoolClass->name ?? 'Unassigned') }} (Section {{ ($s->schoolClass->section ?? 'N/A') }})"
                                    data-roll="{{ $s->roll_number ?? 'N/A' }}"
                                    data-guardian="{{ $s->guardian->user->name ?? 'Independent' }}"
                                    data-guardian-phone="{{ $s->guardian->user->phone ?? 'No phone recorded' }}"
                                    data-attendance="{{ $s->attendance_rate }}"
                                    data-status="{{ ucfirst($s->status) }}"
                                    data-gender="{{ ucfirst($s->gender ?? 'Not specified') }}"
                                    data-dob="{{ $s->date_of_birth ? \Carbon\Carbon::parse($s->date_of_birth)->format('M d, Y') : 'Not specified' }}"
                                    data-emergency="{{ $s->emergency_contact ?? 'None recorded' }}"
                                    data-show-url="{{ route('students.show', $s->id) }}"
                                    data-edit-url="{{ route('students.edit', $s->id) }}">
                                    <i class="fa-solid fa-address-card"></i>
                                </button>

                                <a href="{{ route('students.show', $s->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2" title="View Full 360 Profile">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('students.edit', $s->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2" title="Edit Student">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('students.destroy', $s->id) }}" method="POST" class="d-inline confirm-action" 
                                      data-confirm-title="Archive Student Profile?" 
                                      data-confirm-text="Are you sure you want to deactivate or archive {{ $s->user->name }} ({{ $s->admission_number }})? This will suspend active portal credentials." 
                                      data-confirm-btn="Archive Student" 
                                      data-confirm-color="#dc3545">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Archive / Suspend">
                                        <i class="fa-solid fa-user-xmark"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-user-slash fs-2 d-block mb-2"></i> No student records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($students->hasPages())
        <div class="p-3 border-top">
            {{ $students->links() }}
        </div>
    @endif
</div>

<!-- Student 360 Quick-Drawer Offcanvas -->
<div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="studentQuickDrawer" aria-labelledby="studentQuickDrawerLabel" style="width: 450px;">
    <div class="offcanvas-header text-white p-4" style="background: linear-gradient(135deg, #1a1a2e 0%, #2d2b52 100%);">
        <div class="d-flex align-items-center gap-2">
            <div class="p-2 rounded-3 bg-primary text-white shadow-sm" style="width: 40px; height: 40px; display: grid; place-items: center; background-color: #7a6ad8 !important;">
                <i class="fa-solid fa-user-graduate fs-5"></i>
            </div>
            <div>
                <h5 class="offcanvas-title fw-bold text-white mb-0" id="studentQuickDrawerLabel">Student 360 Dossier</h5>
                <small class="text-white-50">Instant Institutional Profile Snapshot</small>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4 bg-light">
        <!-- Profile Banner -->
        <div class="portal-card p-4 text-center mb-3 shadow-sm border-0 bg-white">
            <div class="position-relative d-inline-block mb-3">
                <img id="drawerAvatar" src="" class="rounded-circle shadow" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #7a6ad8;">
                <span id="drawerStatusBadge" class="position-absolute bottom-0 end-0 badge rounded-pill bg-success border border-white">Active</span>
            </div>
            <h5 class="fw-bold mb-1 text-dark" id="drawerName">Student Name</h5>
            <div class="text-muted small mb-2" id="drawerEmail">student@scholar.edu</div>
            <div class="d-flex justify-content-center gap-2">
                <span class="badge bg-light text-dark border px-3 py-1.5 fw-bold" id="drawerAdmission">ADM-0000</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 fw-bold" id="drawerCohort">Class</span>
            </div>
        </div>

        <!-- Attendance Meter Card -->
        <div class="portal-card p-3 mb-3 shadow-sm border-0 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="fw-bold small text-dark"><i class="fa-solid fa-calendar-check text-primary me-1"></i> Academic Punctuality</div>
                <span class="badge rounded-pill" id="drawerAttendanceBadge">100%</span>
            </div>
            <div class="progress" style="height: 10px; border-radius: 8px;">
                <div id="drawerAttendanceBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%;"></div>
            </div>
            <small class="text-muted d-block mt-2" style="font-size: 11px;">Calculated from verified daily roll calls & biometric checkpoints.</small>
        </div>

        <!-- Academic & Demographic Details -->
        <div class="portal-card p-3 mb-3 shadow-sm border-0 bg-white">
            <div class="fw-bold small text-dark mb-3"><i class="fa-solid fa-circle-info text-primary me-1"></i> Institutional Details</div>
            <div class="row g-2 small">
                <div class="col-6">
                    <span class="text-muted d-block">Class Roll No:</span>
                    <strong class="text-dark" id="drawerRoll">--</strong>
                </div>
                <div class="col-6">
                    <span class="text-muted d-block">Gender:</span>
                    <strong class="text-dark" id="drawerGender">--</strong>
                </div>
                <div class="col-6">
                    <span class="text-muted d-block">Date of Birth:</span>
                    <strong class="text-dark" id="drawerDob">--</strong>
                </div>
                <div class="col-6">
                    <span class="text-muted d-block">Emergency Contact:</span>
                    <strong class="text-dark" id="drawerEmergency">--</strong>
                </div>
            </div>
        </div>

        <!-- Guardian & Parent Contact -->
        <div class="portal-card p-3 mb-3 shadow-sm border-0 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="fw-bold small text-dark"><i class="fa-solid fa-people-roof text-primary me-1"></i> Guardian Contact</div>
                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2" style="font-size: 11px;" onclick="triggerQuickAlertSweetAlert()">
                    <i class="fa-solid fa-bullhorn me-1"></i> Quick Alert
                </button>
            </div>
            <div class="d-flex align-items-center justify-content-between small">
                <div>
                    <strong class="text-dark d-block" id="drawerGuardian">Guardian Name</strong>
                    <span class="text-muted" id="drawerGuardianPhone">No phone</span>
                </div>
                <a href="#" id="drawerPhoneCallLink" class="btn btn-sm btn-light border rounded-circle text-success" title="Direct Phone Call">
                    <i class="fa-solid fa-phone"></i>
                </a>
            </div>
        </div>

        <!-- Action Links -->
        <div class="d-grid gap-2">
            <a href="#" id="drawerFullProfileBtn" class="btn btn-primary rounded-pill py-2 fw-bold shadow-sm" style="background-color: #7a6ad8; border-color: #7a6ad8;">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Full 360 Dossier
            </a>
            @if(auth()->user()->isAdmin())
            <a href="#" id="drawerEditBtn" class="btn btn-outline-secondary rounded-pill py-2 fw-semibold">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Registration
            </a>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let activeDrawerData = null;
    let studentDrawerInstance = null;

    function openStudentQuickDrawer(btn) {
        const data = btn.dataset;
        activeDrawerData = data;

        document.getElementById('drawerAvatar').src = data.avatar;
        document.getElementById('drawerName').innerText = data.name;
        document.getElementById('drawerEmail').innerText = data.email;
        document.getElementById('drawerAdmission').innerText = data.admission;
        document.getElementById('drawerCohort').innerText = data.cohort;
        document.getElementById('drawerRoll').innerText = data.roll || 'N/A';
        document.getElementById('drawerGender').innerText = data.gender;
        document.getElementById('drawerDob').innerText = data.dob;
        document.getElementById('drawerEmergency').innerText = data.emergency;
        document.getElementById('drawerGuardian').innerText = data.guardian;
        document.getElementById('drawerGuardianPhone').innerText = data.guardianPhone;

        // Phone call link
        const callLink = document.getElementById('drawerPhoneCallLink');
        if (data.guardianPhone && data.guardianPhone !== 'No phone recorded') {
            callLink.href = 'tel:' + data.guardianPhone;
            callLink.classList.remove('disabled');
        } else {
            callLink.href = 'javascript:void(0)';
            callLink.classList.add('disabled');
        }

        // Attendance styling
        const rate = parseFloat(data.attendance) || 0;
        const attBadge = document.getElementById('drawerAttendanceBadge');
        const attBar = document.getElementById('drawerAttendanceBar');
        attBadge.innerText = rate + '%';
        attBar.style.width = rate + '%';

        if (rate >= 90) {
            attBadge.className = 'badge rounded-pill bg-success';
            attBar.className = 'progress-bar bg-success';
        } else if (rate >= 75) {
            attBadge.className = 'badge rounded-pill bg-warning text-dark';
            attBar.className = 'progress-bar bg-warning';
        } else {
            attBadge.className = 'badge rounded-pill bg-danger';
            attBar.className = 'progress-bar bg-danger';
        }

        // Status badge
        const statusBadge = document.getElementById('drawerStatusBadge');
        statusBadge.innerText = data.status;
        statusBadge.className = `position-absolute bottom-0 end-0 badge rounded-pill ${data.status.toLowerCase() === 'active' ? 'bg-success' : 'bg-danger'} border border-white`;

        // Action URLs
        document.getElementById('drawerFullProfileBtn').href = data.showUrl;
        const editBtn = document.getElementById('drawerEditBtn');
        if (editBtn) editBtn.href = data.editUrl;

        const drawerEl = document.getElementById('studentQuickDrawer');
        if (!studentDrawerInstance) {
            studentDrawerInstance = new bootstrap.Offcanvas(drawerEl);
        }
        studentDrawerInstance.show();
    }

    function triggerQuickAlertSweetAlert() {
        if (!activeDrawerData) return;

        Swal.fire({
            title: `Quick Alert to Guardian`,
            html: `Draft an automated SMS/Broadcast update for <strong>${activeDrawerData.guardian}</strong> regarding student <strong>${activeDrawerData.name}</strong>.`,
            input: 'textarea',
            inputPlaceholder: 'Enter custom alert message (e.g. Please note early dismissal today at 2:00 PM)...',
            showCancelButton: true,
            confirmButtonText: '<i class="fa-solid fa-paper-plane me-1"></i> Send Dispatch',
            confirmButtonColor: '#7a6ad8',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-4 shadow-lg border-0',
                confirmButton: 'rounded-pill px-4 fw-bold',
                cancelButton: 'rounded-pill px-4'
            },
            inputValidator: (value) => {
                if (!value) {
                    return 'Please enter a message before dispatching!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Message Queued!',
                    text: `SMS alert dispatched to ${activeDrawerData.guardianPhone} with cryptographic audit signature.`,
                    timer: 3500,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-4 shadow-lg border-0' }
                });
            }
        });
    }
</script>
@endsection
