{{-- ================= SUPER ADMIN COLLAPSIBLE NAVIGATION ================= --}}
<li class="nav-category">Executive Management</li>

<!-- 1. Executive Dashboard (Direct Link) -->
<li>
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge-high"></i> <span>Executive Dashboard</span>
    </a>
</li>

<!-- 2. Academic Registry & Faculty HR (Collapsible) -->
@php
    $isRegistryActive = request()->routeIs('students.*', 'staff.*');
@endphp
<li class="nav-item">
    <a class="nav-dropdown-toggle {{ $isRegistryActive ? 'active-parent' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuRegistry" role="button" aria-expanded="{{ $isRegistryActive ? 'true' : 'false' }}" aria-controls="menuRegistry">
        <span class="menu-lead">
            <i class="fa-solid fa-users-gear"></i>
            <span>Registry & Faculty HR</span>
        </span>
        <i class="fa-solid fa-chevron-down arrow-icon"></i>
    </a>
    <div class="collapse {{ $isRegistryActive ? 'show' : '' }}" id="menuRegistry">
        <ul class="nav-submenu">
            <li>
                <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.index', 'students.show', 'students.edit') ? 'active' : '' }}">
                    <span>Students Roster</span>
                </a>
            </li>
            <li>
                <a href="{{ route('students.create') }}" class="{{ request()->routeIs('students.create') ? 'active' : '' }}">
                    <span>Enroll Student</span>
                </a>
            </li>
            <li>
                <a href="{{ route('students.bulk') }}" class="{{ request()->routeIs('students.bulk*') ? 'active' : '' }}">
                    <span>Bulk Excel Import</span>
                </a>
            </li>
            <li>
                <a href="{{ route('staff.index') }}" class="{{ request()->routeIs('staff.index', 'staff.show', 'staff.edit') ? 'active' : '' }}">
                    <span>Faculty & Staff Directory</span>
                </a>
            </li>
            <li>
                <a href="{{ route('staff.create') }}" class="{{ request()->routeIs('staff.create') ? 'active' : '' }}">
                    <span>Onboard Faculty</span>
                </a>
            </li>
        </ul>
    </div>
</li>

<!-- 3. Cohorts & Curriculum (Collapsible) -->
@php
    $isCohortsActive = request()->routeIs('academic.*');
@endphp
<li class="nav-item">
    <a class="nav-dropdown-toggle {{ $isCohortsActive ? 'active-parent' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuCohorts" role="button" aria-expanded="{{ $isCohortsActive ? 'true' : 'false' }}" aria-controls="menuCohorts">
        <span class="menu-lead">
            <i class="fa-solid fa-sitemap"></i>
            <span>Cohorts & Curriculum</span>
        </span>
        <i class="fa-solid fa-chevron-down arrow-icon"></i>
    </a>
    <div class="collapse {{ $isCohortsActive ? 'show' : '' }}" id="menuCohorts">
        <ul class="nav-submenu">
            <li>
                <a href="{{ route('academic.calendar') }}" class="{{ request()->routeIs('academic.calendar*') ? 'active' : '' }}">
                    <span>Academic Calendar</span>
                </a>
            </li>
            <li>
                <a href="{{ route('academic.grading-policies') }}" class="{{ request()->routeIs('academic.grading-policies*') ? 'active' : '' }}">
                    <span>Grading Policies</span>
                </a>
            </li>
            <li>
                <a href="{{ route('academic.classes') }}" class="{{ request()->routeIs('academic.classes') ? 'active' : '' }}">
                    <span>Cohorts & Classes</span>
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

<!-- 4. Assessments & Grading (Collapsible) -->
@php
    $isGradebookActive = request()->routeIs('gradebook.*');
@endphp
<li class="nav-item">
    <a class="nav-dropdown-toggle {{ $isGradebookActive ? 'active-parent' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuAssessments" role="button" aria-expanded="{{ $isGradebookActive ? 'true' : 'false' }}" aria-controls="menuAssessments">
        <span class="menu-lead">
            <i class="fa-solid fa-file-signature"></i>
            <span>Assessments & Grading</span>
        </span>
        <i class="fa-solid fa-chevron-down arrow-icon"></i>
    </a>
    <div class="collapse {{ $isGradebookActive ? 'show' : '' }}" id="menuAssessments">
        <ul class="nav-submenu">
            <li>
                <a href="{{ route('gradebook.index') }}" class="{{ request()->routeIs('gradebook.index', 'gradebook.sheet') ? 'active' : '' }}">
                    <span>Gradebook & Matrix</span>
                </a>
            </li>
            <li>
                <a href="{{ route('gradebook.reports') }}" class="{{ request()->routeIs('gradebook.reports*') ? 'active' : '' }}">
                    <span>Class Report Cards</span>
                </a>
            </li>
        </ul>
    </div>
</li>

<!-- 5. Digital LMS Instruction (Collapsible) -->
@php
    $isLmsActive = request()->routeIs('courses.*', 'lms.quizzes.*');
@endphp
<li class="nav-item">
    <a class="nav-dropdown-toggle {{ $isLmsActive ? 'active-parent' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuLms" role="button" aria-expanded="{{ $isLmsActive ? 'true' : 'false' }}" aria-controls="menuLms">
        <span class="menu-lead">
            <i class="fa-solid fa-laptop-file"></i>
            <span>Digital LMS & Courses</span>
        </span>
        <i class="fa-solid fa-chevron-down arrow-icon"></i>
    </a>
    <div class="collapse {{ $isLmsActive ? 'show' : '' }}" id="menuLms">
        <ul class="nav-submenu">
            <li>
                <a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.index', 'courses.show', 'courses.edit') ? 'active' : '' }}">
                    <span>All LMS Courses</span>
                </a>
            </li>
            <li>
                <a href="{{ route('courses.create') }}" class="{{ request()->routeIs('courses.create') ? 'active' : '' }}">
                    <span>Provision Course</span>
                </a>
            </li>
            <li>
                <a href="{{ route('lms.quizzes.index') }}" class="{{ request()->routeIs('lms.quizzes.index', 'lms.quizzes.builder', 'lms.quizzes.submissions', 'lms.quizzes.grading*') ? 'active' : '' }}">
                    <span>Tests, Quizzes & Exams</span>
                </a>
            </li>
            <li>
                <a href="{{ route('lms.quizzes.create') }}" class="{{ request()->routeIs('lms.quizzes.create') ? 'active' : '' }}">
                    <span>Author Assessment</span>
                </a>
            </li>
        </ul>
    </div>
</li>

<!-- 6. Attendance Control (Collapsible) -->
@php
    $isAttendanceActive = request()->routeIs('attendance.*');
@endphp
<li class="nav-item">
    <a class="nav-dropdown-toggle {{ $isAttendanceActive ? 'active-parent' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuAttendance" role="button" aria-expanded="{{ $isAttendanceActive ? 'true' : 'false' }}" aria-controls="menuAttendance">
        <span class="menu-lead">
            <i class="fa-solid fa-clipboard-user"></i>
            <span>Attendance Control</span>
        </span>
        <i class="fa-solid fa-chevron-down arrow-icon"></i>
    </a>
    <div class="collapse {{ $isAttendanceActive ? 'show' : '' }}" id="menuAttendance">
        <ul class="nav-submenu">
            <li>
                <a href="{{ route('attendance.index') }}" class="{{ request()->routeIs('attendance.index', 'attendance.mark') ? 'active' : '' }}">
                    <span>Daily Registers</span>
                </a>
            </li>
            <li>
                <a href="{{ route('attendance.qr') }}" class="{{ request()->routeIs('attendance.qr') ? 'active' : '' }}">
                    <span>Smart QR Terminal</span>
                </a>
            </li>
        </ul>
    </div>
</li>

<!-- 7. Communications Hub (Collapsible) -->
@php
    $isCommsActive = request()->routeIs('broadcasts.*', 'gateways.*');
@endphp
<li class="nav-item">
    <a class="nav-dropdown-toggle {{ $isCommsActive ? 'active-parent' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuComms" role="button" aria-expanded="{{ $isCommsActive ? 'true' : 'false' }}" aria-controls="menuComms">
        <span class="menu-lead">
            <i class="fa-solid fa-tower-broadcast"></i>
            <span>Communications Hub</span>
        </span>
        <i class="fa-solid fa-chevron-down arrow-icon"></i>
    </a>
    <div class="collapse {{ $isCommsActive ? 'show' : '' }}" id="menuComms">
        <ul class="nav-submenu">
            <li>
                <a href="{{ route('broadcasts.index') }}" class="{{ request()->routeIs('broadcasts.index', 'broadcasts.show') ? 'active' : '' }}">
                    <span>Broadcast History</span>
                </a>
            </li>
            <li>
                <a href="{{ route('broadcasts.create') }}" class="{{ request()->routeIs('broadcasts.create') ? 'active' : '' }}">
                    <span>Compose Broadcast</span>
                </a>
            </li>
            <li>
                <a href="{{ route('broadcasts.templates.index') }}" class="{{ request()->routeIs('broadcasts.templates.*') ? 'active' : '' }}">
                    <span>Message Templates</span>
                </a>
            </li>
            <li>
                <a href="{{ route('gateways.index') }}" class="{{ request()->routeIs('gateways.*') ? 'active' : '' }}">
                    <span>Gateway & API Configs</span>
                </a>
            </li>
        </ul>
    </div>
</li>

<!-- 8. Portal Media & Events (Collapsible) -->
@php
    $isMediaActive = request()->routeIs('content.*');
@endphp
<li class="nav-item">
    <a class="nav-dropdown-toggle {{ $isMediaActive ? 'active-parent' : 'collapsed' }}" data-bs-toggle="collapse" href="#menuMedia" role="button" aria-expanded="{{ $isMediaActive ? 'true' : 'false' }}" aria-controls="menuMedia">
        <span class="menu-lead">
            <i class="fa-solid fa-photo-film"></i>
            <span>Portal Media & Events</span>
        </span>
        <i class="fa-solid fa-chevron-down arrow-icon"></i>
    </a>
    <div class="collapse {{ $isMediaActive ? 'show' : '' }}" id="menuMedia">
        <ul class="nav-submenu">
            <li>
                <a href="{{ route('content.events.index') }}" class="{{ request()->routeIs('content.events.*') ? 'active' : '' }}">
                    <span>Events & Symposiums</span>
                </a>
            </li>
            <li>
                <a href="{{ route('content.gallery.index') }}" class="{{ request()->routeIs('content.gallery.*') ? 'active' : '' }}">
                    <span>Campus Life Gallery</span>
                </a>
            </li>
        </ul>
    </div>
</li>

<!-- 9. Security & Compliance (Single Link) -->
<li class="nav-category">Security & Compliance</li>
<li>
    <a href="{{ route('audit.index') }}" class="{{ request()->routeIs('audit.*') ? 'active' : '' }}">
        <i class="fa-solid fa-shield-halved"></i> <span>Immutable Audit Trail</span>
    </a>
</li>
