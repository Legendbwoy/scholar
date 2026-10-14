{{-- ================= SUPER ADMIN NAVIGATION ================= --}}
<li class="nav-category">Executive Console</li>
<li>
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge-high"></i> <span>Executive Dashboard</span>
    </a>
</li>

<li class="nav-category">Academic Registry</li>
<li>
    <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.index', 'students.show', 'students.edit') ? 'active' : '' }}">
        <i class="fa-solid fa-user-graduate"></i> <span>Students Roster</span>
    </a>
</li>
<li>
    <a href="{{ route('students.create') }}" class="{{ request()->routeIs('students.create') ? 'active' : '' }}">
        <i class="fa-solid fa-user-plus"></i> <span>Enroll Student</span>
    </a>
</li>
<li>
    <a href="{{ route('students.bulk') }}" class="{{ request()->routeIs('students.bulk*') ? 'active' : '' }}">
        <i class="fa-solid fa-file-excel"></i> <span>Bulk Excel Import</span>
    </a>
</li>
<li>
    <a href="{{ route('staff.index') }}" class="{{ request()->routeIs('staff.index', 'staff.show', 'staff.edit') ? 'active' : '' }}">
        <i class="fa-solid fa-chalkboard-user"></i> <span>Faculty & Staff</span>
    </a>
</li>
<li>
    <a href="{{ route('staff.create') }}" class="{{ request()->routeIs('staff.create') ? 'active' : '' }}">
        <i class="fa-solid fa-user-tie"></i> <span>Onboard Faculty</span>
    </a>
</li>

<li class="nav-category">Assessments & Grading</li>
<li>
    <a href="{{ route('gradebook.index') }}" class="{{ request()->routeIs('gradebook.index', 'gradebook.sheet') ? 'active' : '' }}">
        <i class="fa-solid fa-file-signature"></i> <span>Gradebook & Matrix</span>
    </a>
</li>
<li>
    <a href="{{ route('gradebook.reports') }}" class="{{ request()->routeIs('gradebook.reports*') ? 'active' : '' }}">
        <i class="fa-solid fa-award"></i> <span>Class Report Cards</span>
    </a>
</li>

<li class="nav-category">Cohorts & Curriculum</li>
<li>
    <a href="{{ route('academic.calendar') }}" class="{{ request()->routeIs('academic.calendar*') ? 'active' : '' }}">
        <i class="fa-solid fa-calendar-days"></i> <span>Academic Calendar</span>
    </a>
</li>
<li>
    <a href="{{ route('academic.grading-policies') }}" class="{{ request()->routeIs('academic.grading-policies*') ? 'active' : '' }}">
        <i class="fa-solid fa-scale-balanced"></i> <span>Grading Policies</span>
    </a>
</li>
<li>
    <a href="{{ route('academic.classes') }}" class="{{ request()->routeIs('academic.classes') ? 'active' : '' }}">
        <i class="fa-solid fa-school"></i> <span>Cohorts & Classes</span>
    </a>
</li>
<li>
    <a href="{{ route('academic.subjects') }}" class="{{ request()->routeIs('academic.subjects') ? 'active' : '' }}">
        <i class="fa-solid fa-book-bookmark"></i> <span>Curriculum Subjects</span>
    </a>
</li>

<li class="nav-category">Digital LMS & Materials</li>
<li>
    <a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.index', 'courses.show', 'courses.edit') ? 'active' : '' }}">
        <i class="fa-solid fa-laptop-file"></i> <span>All LMS Courses</span>
    </a>
</li>
<li>
    <a href="{{ route('courses.create') }}" class="{{ request()->routeIs('courses.create') ? 'active' : '' }}">
        <i class="fa-solid fa-plus-circle"></i> <span>Provision Course</span>
    </a>
</li>

<li class="nav-category">Attendance Control</li>
<li>
    <a href="{{ route('attendance.index') }}" class="{{ request()->routeIs('attendance.index', 'attendance.mark') ? 'active' : '' }}">
        <i class="fa-solid fa-calendar-check"></i> <span>Daily Registers</span>
    </a>
</li>
<li>
    <a href="{{ route('attendance.qr') }}" class="{{ request()->routeIs('attendance.qr') ? 'active' : '' }}">
        <i class="fa-solid fa-qrcode"></i> <span>Smart QR Terminal</span>
    </a>
</li>

<li class="nav-category">Communications Hub</li>
<li>
    <a href="{{ route('broadcasts.index') }}" class="{{ request()->routeIs('broadcasts.index', 'broadcasts.show') ? 'active' : '' }}">
        <i class="fa-solid fa-bullhorn"></i> <span>Broadcast History</span>
    </a>
</li>
<li>
    <a href="{{ route('broadcasts.create') }}" class="{{ request()->routeIs('broadcasts.create') ? 'active' : '' }}">
        <i class="fa-solid fa-paper-plane"></i> <span>Compose Broadcast</span>
    </a>
</li>
<li>
    <a href="{{ route('broadcasts.templates.index') }}" class="{{ request()->routeIs('broadcasts.templates.*') ? 'active' : '' }}">
        <i class="fa-solid fa-shapes"></i> <span>Message Templates</span>
    </a>
</li>
<li>
    <a href="{{ route('gateways.index') }}" class="{{ request()->routeIs('gateways.*') ? 'active' : '' }}">
        <i class="fa-solid fa-tower-broadcast"></i> <span>Gateway & API Configs</span>
    </a>
</li>

<li class="nav-category">Portal Media & Events</li>
<li>
    <a href="{{ route('content.events.index') }}" class="{{ request()->routeIs('content.events.*') ? 'active' : '' }}">
        <i class="fa-solid fa-calendar-star"></i> <span>Events & Symposiums</span>
    </a>
</li>
<li>
    <a href="{{ route('content.gallery.index') }}" class="{{ request()->routeIs('content.gallery.*') ? 'active' : '' }}">
        <i class="fa-solid fa-images"></i> <span>Campus Life Gallery</span>
    </a>
</li>

<li class="nav-category">Security & Compliance</li>
<li>
    <a href="{{ route('audit.index') }}" class="{{ request()->routeIs('audit.*') ? 'active' : '' }}">
        <i class="fa-solid fa-shield-halved"></i> <span>Immutable Audit Trail</span>
    </a>
</li>
