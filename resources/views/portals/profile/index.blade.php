@extends('layouts.portal')

@section('title', 'Profile & Account Settings — Scholar SMS')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profile Settings</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-1">User Profile & Account Management</h2>
        <p class="text-muted mb-0">Manage your profile photograph, personal identity credentials, and security settings.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
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

<div class="row g-4">
    <!-- Left Column: Avatar & Persona Card -->
    <div class="col-lg-4">
        <div class="portal-card p-4 text-center shadow-sm mb-4">
            <!-- Profile Avatar with Live Preview -->
            <div class="position-relative d-inline-block mb-3">
                <img id="avatarPreview" src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle shadow border border-3 border-white" style="width: 140px; height: 140px; object-fit: cover; background: #eef2f6;">
                <label for="avatarFileInput" class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 shadow-sm" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; cursor: pointer;" title="Choose New Photo">
                    <i class="fa-solid fa-camera"></i>
                </label>
            </div>

            <h5 class="fw-bold mb-1 text-dark">{{ $user->name }}</h5>
            <div class="mb-2">
                <span class="badge role-badge-{{ $user->role }} px-3 py-1 rounded-pill text-capitalize fs-6">
                    {{ $user->role }}
                </span>
            </div>
            <p class="text-muted small mb-3">{{ $user->email }}</p>

            <!-- Upload Avatar Form -->
            <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data" id="avatarUploadForm" class="mb-2">
                @csrf
                <input type="file" name="avatar" id="avatarFileInput" class="d-none" accept="image/png, image/jpeg, image/jpg, image/webp" onchange="previewAndSubmitAvatar(this)">
                <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="document.getElementById('avatarFileInput').click();">
                    <i class="fa-solid fa-upload me-1"></i> Change Profile Picture
                </button>
            </form>

            @if($user->avatar)
            <!-- Remove Avatar Form -->
            <form action="{{ route('profile.avatar.delete') }}" method="POST" onsubmit="return confirm('Reset profile photo to default placeholder?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                    <i class="fa-solid fa-trash-can me-1"></i> Reset to Default
                </button>
            </form>
            @endif

            <small class="text-muted d-block mt-3" style="font-size: 11px;">
                <i class="fa-solid fa-circle-info me-1"></i> Supported: JPG, PNG, WEBP, GIF up to 3MB.
            </small>
        </div>

        <!-- Role Metadata Summary -->
        <div class="portal-card p-4 shadow-sm">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-id-badge text-primary me-2"></i> Account Persona Details</h6>
            
            <div class="small">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Account Role:</span>
                    <strong class="text-capitalize">{{ $user->role }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Status:</span>
                    <span class="badge bg-success-subtle text-success">{{ ucfirst($user->status ?? 'active') }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Joined:</span>
                    <span>{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</span>
                </div>

                @if($user->isStudent() && $user->student)
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Admission No:</span>
                        <code>{{ $user->student->admission_number }}</code>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Current Cohort:</span>
                        <strong>{{ $user->student->schoolClass?->name ?? 'Standard' }}</strong>
                    </div>
                @elseif($user->isTeacher() && $user->staff)
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Faculty ID:</span>
                        <code>{{ $user->staff->employee_id }}</code>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Department:</span>
                        <strong>{{ $user->staff->department?->name ?? 'Academics' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Designation:</span>
                        <span>{{ $user->staff->designation }}</span>
                    </div>
                @elseif($user->isParent() && $user->guardian)
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Registered Wards:</span>
                        <strong>{{ $user->guardian->students->count() }} Student(s)</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Settings Tabs -->
    <div class="col-lg-8">
        <div class="portal-card p-0 overflow-hidden shadow-sm">
            <div class="bg-light border-bottom px-4 pt-3">
                <ul class="nav nav-tabs border-0" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold text-dark px-4 py-3" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-pane" type="button" role="tab">
                            <i class="fa-solid fa-user-pen text-primary me-2"></i> Personal Information
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-dark px-4 py-3" id="security-tab" data-bs-toggle="tab" data-bs-target="#security-pane" type="button" role="tab">
                            <i class="fa-solid fa-shield-halved text-danger me-2"></i> Password & Security
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content p-4 p-md-5" id="profileTabsContent">
                <!-- Tab 1: Personal Information Form -->
                <div class="tab-pane fade show active" id="info-pane" role="tabpanel">
                    <h5 class="fw-bold mb-1">Identity & Contact Details</h5>
                    <p class="text-muted small mb-4">Keep your contact coordinates and profile name synchronized across institutional registries.</p>

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Full Legal Name *</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address (Login ID) *</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Primary Contact Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+1-555-0100">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Institutional System Role</label>
                                <input type="text" class="form-control bg-light" value="{{ ucfirst($user->role) }}" readonly>
                                <small class="text-muted">Role permissions are configured by System Administrator.</small>
                            </div>

                            <div class="col-12 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Profile Details
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tab 2: Security & Password Update -->
                <div class="tab-pane fade" id="security-pane" role="tabpanel">
                    <h5 class="fw-bold mb-1">Security & Authentication</h5>
                    <p class="text-muted small mb-4">Update your institutional portal password. Ensure your password is at least 6 characters with mixed characters.</p>

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- Hidden inputs to retain profile names -->
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="phone" value="{{ $user->phone }}">

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Current Password *</label>
                                <input type="password" name="current_password" class="form-control" placeholder="Enter existing password" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">New Password *</label>
                                <input type="password" name="new_password" class="form-control" placeholder="Minimum 6 characters" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirm New Password *</label>
                                <input type="password" name="new_password_confirmation" class="form-control" placeholder="Repeat new password" required>
                            </div>

                            <div class="col-12 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-danger px-4">
                                    <i class="fa-solid fa-key me-1"></i> Update Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewAndSubmitAvatar(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 3145728) {
            alert('File exceeds 3MB limit. Please select a smaller photograph.');
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
            // Automatically submit form on selection
            document.getElementById('avatarUploadForm').submit();
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
