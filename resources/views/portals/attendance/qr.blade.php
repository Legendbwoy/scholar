@extends('layouts.portal')

@section('title', 'Smart QR Attendance Terminal — Scholar SMS')

@section('content')
<div class="mb-4">
    <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-light mb-2"><i class="fa-solid fa-arrow-left me-1"></i> Back to Attendance</a>
    <h2 class="fw-bold mb-1">Smart QR Code & Biometric Check-In Terminal</h2>
    <p class="text-muted mb-0">Contactless attendance verification with real-time hash stamping and parent confirmation.</p>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="portal-card p-4 p-md-5 text-center">
            <h5 class="fw-bold mb-3">Student Smart ID Pass Terminal</h5>
            <p class="text-muted small mb-4">Select an enrolled student to generate their active QR security token, then simulate scanning into the campus hall.</p>

            <div class="mb-4 text-start">
                <label class="form-label fw-semibold small">Select Enrolled Student:</label>
                <select id="studentSelect" class="form-select form-select-lg">
                    @foreach($classes as $c)
                        <optgroup label="{{ $c->name }}">
                            @foreach($c->students as $st)
                                <option value="{{ $st->id }}" data-name="{{ $st->user->name }}" data-adm="{{ $st->admission_number }}" data-class="{{ $c->name }}">
                                    {{ $st->user->name }} ({{ $st->admission_number }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <!-- Simulated QR Visual Card -->
            <div class="p-4 bg-light rounded-4 border d-inline-block text-center shadow-sm mb-4" style="min-width: 280px;">
                <div class="bg-white p-3 rounded-3 shadow-sm d-inline-block mb-3">
                    <img id="qrImage" src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=SCHOLAR_PASS_2026_001" alt="QR Code" style="width: 160px; height: 160px;">
                </div>
                <h5 id="qrStudentName" class="fw-bold mb-1 text-dark">Alexander Cole</h5>
                <p id="qrAdmNum" class="text-muted small mb-0">SCH-2026-001 • Grade 10 - Alpha</p>
                <span class="badge bg-success-subtle text-success border border-success mt-2">Active Student Token</span>
            </div>

            <div>
                <button type="button" id="scanBtn" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow">
                    <i class="fa-solid fa-expand me-2"></i> Simulate Terminal Scan Tap
                </button>
            </div>
        </div>
    </div>

    <!-- Live Scan Feed -->
    <div class="col-lg-6">
        <div class="portal-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-satellite-dish text-success me-2"></i>Terminal Scan Live Ledger</h5>
                <span class="badge bg-success"><i class="fa-solid fa-circle text-white me-1" style="font-size: 8px;"></i> Scanner Online</span>
            </div>

            <div id="scanFeedback" class="alert alert-success d-none rounded-3 mb-3">
                <i class="fa-solid fa-circle-check me-2"></i> <span id="feedbackText">Scan verified</span>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle" id="scansTable">
                    <thead>
                        <tr class="text-muted small">
                            <th>Timestamp</th>
                            <th>Student</th>
                            <th>Verification</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="scanLogBody">
                        <tr>
                            <td><small class="text-muted">08:02:14 AM</small></td>
                            <td><strong>Alexander Cole</strong></td>
                            <td><span class="badge bg-light text-dark border">QR Scan</span></td>
                            <td><span class="badge bg-success">Present</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const studentSelect = document.getElementById('studentSelect');
    const qrImage = document.getElementById('qrImage');
    const qrStudentName = document.getElementById('qrStudentName');
    const qrAdmNum = document.getElementById('qrAdmNum');
    const scanBtn = document.getElementById('scanBtn');
    const scanFeedback = document.getElementById('scanFeedback');
    const feedbackText = document.getElementById('feedbackText');
    const scanLogBody = document.getElementById('scanLogBody');

    studentSelect.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const name = opt.getAttribute('data-name');
        const adm = opt.getAttribute('data-adm');
        const cls = opt.getAttribute('data-class');

        qrStudentName.textContent = name;
        qrAdmNum.textContent = adm + ' • ' + cls;
        qrImage.src = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' + encodeURIComponent('SCHOLAR_PASS_' + adm);
    });

    scanBtn.addEventListener('click', function() {
        const studentId = studentSelect.value;
        const opt = studentSelect.options[studentSelect.selectedIndex];
        const name = opt.getAttribute('data-name');

        scanBtn.disabled = true;
        scanBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Verifying Pass...';

        fetch('{{ route("attendance.qr.verify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ student_id: studentId })
        })
        .then(res => res.json())
        .then(data => {
            scanBtn.disabled = false;
            scanBtn.innerHTML = '<i class="fa-solid fa-expand me-2"></i> Simulate Terminal Scan Tap';

            scanFeedback.classList.remove('d-none');
            feedbackText.textContent = data.message + ' successfully recorded at ' + data.time;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><small class="text-muted">${data.time}</small></td>
                <td><strong>${name}</strong></td>
                <td><span class="badge bg-light text-dark border">Smart QR Pass</span></td>
                <td><span class="badge bg-success">${data.status}</span></td>
            `;
            scanLogBody.prepend(tr);
        })
        .catch(err => {
            scanBtn.disabled = false;
            scanBtn.innerHTML = '<i class="fa-solid fa-expand me-2"></i> Simulate Terminal Scan Tap';
            alert('Scan error occurred.');
        });
    });
</script>
@endsection