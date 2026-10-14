{{-- ================= TEACHER / FACULTY COLLAPSIBLE NAVIGATION ================= --}}
<li class="nav-category">Faculty Desk</li>

<!-- 1. Teacher Dashboard -->
<li>
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge-high"></i> <span>Teacher Dashboard</span>
    </a>
</li>

<!-- 2. My Cohorts & Classes (Collapsible) -->
@php
    $isTeacherCohortsActive = request()->routeIs('academic.classes', 'students.*', 'academic.subjects');
@endphp
<li class="nav-item">
    <a class="nav-dropdown-toggle {{ $isTeacherCohortsActive ? 'active-parent' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuTeacherCohorts" role="button" aria-expanded="{{ $isTeacherCohortsActive ? 'true' : 'false' }}" aria-controls="menuTeacherCohorts">
        <span class="menu-lead">
            <i class="fa-solid fa-school"></i>
            <span>My Cohorts & Rosters</span>
        </span>
        <i class="fa-solid fa-chevron-down arrow-icon"></i>
    </a>
    <div class="collapse {{ $isTeacherCohortsActive ? 'show' : '' }}" id="menuTeacherCohorts">
        <ul class="nav-submenu">
            <li>
                <a href="{{ route('academic.classes') }}" class="{{ request()->routeIs('academic.classes') ? 'active' : '' }}">
                    <span>Assigned Classes</span>
                </a>
            </li>
            <li>
                <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <span>Student Rosters</span>
                </a>
            </li>
            <li>
                <a href="{{ route('academic.subjects') }}" class="{{ request()->routeIs('academic.subjects') ? 'active' : '' }}">
                    <span>Curriculum Subjects</span>
                </a>
            </li>
        </ul>
    </div>
</li>

<!-- 3. Assessments & Grading (Collapsible) -->
@php
    $isTeacherGradebookActive = request()->routeIs('gradebook.*');
@endphp
<li class="nav-item">
    <a class="nav-dropdown-toggle {{ $isTeacherGradebookActive ? 'active-parent' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuTeacherAssessments" role="button" aria-expanded="{{ $isTeacherGradebookActive ? 'true' : 'false' }}" aria-controls="menuTeacherAssessments">
        <span class="menu-lead">
            <i class="fa-solid fa-file-signature"></i>
            <span>Assessments & Grading</span>
        </span>
        <i class="fa-solid fa-chevron-down arrow-icon"></i>
    </a>
    <div class="collapse {{ $isTeacherGradebookActive ? 'show' : '' }}" id="menuTeacherAssessments">
        <ul class="nav-submenu">
            <li>
                <a href="{{ route('gradebook.index') }}" class="{{ request()->routeIs('gradebook.index', 'gradebook.sheet') ? 'active' : '' }}">
                    <span>My Gradebook</span>
                </a>
            </li>
            <li>
                <a href="{{ route('gradebook.reports') }}" class="{{ request()->routeIs('gradebook.reports*') ? 'active' : '' }}">
                    <span>Student Report Cards</span>
                </a>
            </li>
        </ul>
    </div>
</li>

<!-- 4. Digital LMS Instruction (Collapsible) -->
@php
    $isTeacherLmsActive = request()->routeIs('courses.*');
@endphp
<li class="nav-item">
    <a class="nav-dropdown-toggle {{ $isTeacherLmsActive ? 'active-parent' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuTeacherLms" role="button" aria-expanded="{{ $isTeacherLmsActive ? 'true' : 'false' }}" aria-controls="menuTeacherLms">
        <span class="menu-lead">
            <i class="fa-solid fa-book-open-reader"></i>
            <span>Digital LMS Instruction</span>
        </span>
        <i class="fa-solid fa-chevron-down arrow-icon"></i>
    </a>
    <div class="collapse {{ $isTeacherLmsActive ? 'show' : '' }}" id="menuTeacherLms">
        <ul class="nav-submenu">
            <li>
                <a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.index', 'courses.show', 'courses.edit') ? 'active' : '' }}">
                    <span>My LMS Courses</span>
                </a>
            </li>
            <li>
                <a href="{{ route('courses.create') }}" class="{{ request()->routeIs('courses.create') ? 'active' : '' }}">
                    <span>Provision Course</span>
                </a>
            </li>
        </ul>
    </div>
</li>

<!-- 5. Attendance Duties (Collapsible) -->
@php
    $isTeacherAttendanceActive = request()->routeIs('attendance.*');
@endphp
<li class="nav-item">
    <a class="nav-dropdown-toggle {{ $isTeacherAttendanceActive ? 'active-parent' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuTeacherAttendance" role="button" aria-expanded="{{ $isTeacherAttendanceActive ? 'true' : 'false' }}" aria-controls="menuTeacherAttendance">
        <span class="menu-lead">
            <i class="fa-solid fa-clipboard-user"></i>
            <span>Attendance Duties</span>
        </span>
        <i class="fa-solid fa-chevron-down arrow-icon"></i>
    </a>
    <div class="collapse {{ $isTeacherAttendanceActive ? 'show' : '' }}" id="menuTeacherAttendance">
        <ul class="nav-submenu">
            <li>
                <a href="{{ route('attendance.index') }}" class="{{ request()->routeIs('attendance.index', 'attendance.mark') ? 'active' : '' }}">
                    <span>Daily Class Registers</span>
                </a>
            </li>
            <li>
                <a href="{{ route('attendance.qr') }}" class="{{ request()->routeIs('attendance.qr') ? 'active' : '' }}">
                    <span>QR Terminal Scanner</span>
                </a>
            </li>
        </ul>
    </div>
</li>

<!-- 6. Communications Hub (Collapsible) -->
@php
    $isTeacherCommsActive = request()->routeIs('broadcasts.*');
@endphp
<li class="nav-item">
    <a class="nav-dropdown-toggle {{ $isTeacherCommsActive ? 'active-parent' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuTeacherComms" role="button" aria-expanded="{{ $isTeacherCommsActive ? 'true' : 'false' }}" aria-controls="menuTeacherComms">
        <span class="menu-lead">
            <i class="fa-solid fa-bullhorn"></i>
            <span>Announcements & Comms</span>
        </span>
        <i class="fa-solid fa-chevron-down arrow-icon"></i>
    </a>
    <div class="collapse {{ $isTeacherCommsActive ? 'show' : '' }}" id="menuTeacherComms">
        <ul class="nav-submenu">
            <li>
                <a href="{{ route('broadcasts.index') }}" class="{{ request()->routeIs('broadcasts.index', 'broadcasts.show') ? 'active' : '' }}">
                    <span>Broadcast Noticeboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('broadcasts.create') }}" class="{{ request()->routeIs('broadcasts.create') ? 'active' : '' }}">
                    <span>Compose Announcement</span>
                </a>
            </li>
            <li>
                <a href="{{ route('broadcasts.templates.index') }}" class="{{ request()->routeIs('broadcasts.templates.*') ? 'active' : '' }}">
                    <span>Message Templates</span>
                </a>
            </li>
        </ul>
    </div>
</li>
