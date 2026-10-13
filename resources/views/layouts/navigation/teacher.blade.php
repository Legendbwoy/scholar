{{-- ================= TEACHER / FACULTY NAVIGATION ================= --}}
<li class="nav-category">Faculty Desk</li>
<li>
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge-high"></i> <span>Teacher Dashboard</span>
    </a>
</li>

<li class="nav-category">My Cohorts & Classes</li>
<li>
    <a href="{{ route('academic.classes') }}" class="{{ request()->routeIs('academic.classes') ? 'active' : '' }}">
        <i class="fa-solid fa-school"></i> <span>Assigned Classes</span>
    </a>
</li>
<li>
    <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.*') ? 'active' : '' }}">
        <i class="fa-solid fa-users"></i> <span>Student Rosters</span>
    </a>
</li>
<li>
    <a href="{{ route('academic.subjects') }}" class="{{ request()->routeIs('academic.subjects') ? 'active' : '' }}">
        <i class="fa-solid fa-book-bookmark"></i> <span>Curriculum Subjects</span>
    </a>
</li>

<li class="nav-category">Digital LMS Instruction</li>
<li>
    <a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.index', 'courses.show', 'courses.edit') ? 'active' : '' }}">
        <i class="fa-solid fa-book-open-reader"></i> <span>My LMS Courses</span>
    </a>
</li>
<li>
    <a href="{{ route('courses.create') }}" class="{{ request()->routeIs('courses.create') ? 'active' : '' }}">
        <i class="fa-solid fa-plus-circle"></i> <span>Provision Course</span>
    </a>
</li>

<li class="nav-category">Attendance Duties</li>
<li>
    <a href="{{ route('attendance.index') }}" class="{{ request()->routeIs('attendance.index', 'attendance.mark') ? 'active' : '' }}">
        <i class="fa-solid fa-clipboard-check"></i> <span>Daily Class Registers</span>
    </a>
</li>
<li>
    <a href="{{ route('attendance.qr') }}" class="{{ request()->routeIs('attendance.qr') ? 'active' : '' }}">
        <i class="fa-solid fa-qrcode"></i> <span>QR Terminal Scanner</span>
    </a>
</li>

<li class="nav-category">Announcements</li>
<li>
    <a href="{{ route('broadcasts.index') }}" class="{{ request()->routeIs('broadcasts.index', 'broadcasts.show') ? 'active' : '' }}">
        <i class="fa-solid fa-bullhorn"></i> <span>Broadcast Noticeboard</span>
    </a>
</li>
<li>
    <a href="{{ route('broadcasts.create') }}" class="{{ request()->routeIs('broadcasts.create') ? 'active' : '' }}">
        <i class="fa-solid fa-paper-plane"></i> <span>Compose Announcement</span>
    </a>
</li>
