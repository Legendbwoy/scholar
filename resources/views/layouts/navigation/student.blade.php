{{-- ================= STUDENT NAVIGATION ================= --}}
<li class="nav-category">Student Workspace</li>
<li>
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-house-user"></i> <span>My Dashboard</span>
    </a>
</li>
@if(auth()->user()->student)
<li>
    <a href="{{ route('students.show', auth()->user()->student->id) }}" class="{{ request()->is('students/' . auth()->user()->student->id) ? 'active' : '' }}">
        <i class="fa-solid fa-id-card"></i> <span>My Student 360</span>
    </a>
</li>
<li>
    <a href="{{ route('gradebook.report-card', auth()->user()->student->id) }}" class="{{ request()->routeIs('gradebook.report-card') ? 'active' : '' }}" target="_blank">
        <i class="fa-solid fa-award text-warning"></i> <span>My Report Card <i class="fa-solid fa-arrow-up-right-from-square small ms-1" style="font-size: 10px;"></i></span>
    </a>
</li>
@endif

<li class="nav-category">Learning & LMS</li>
<li>
    <a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.*') ? 'active' : '' }}">
        <i class="fa-solid fa-graduation-cap"></i> <span>My Enrolled Courses</span>
    </a>
</li>

<li class="nav-category">Attendance Standing</li>
<li>
    <a href="{{ route('attendance.index') }}" class="{{ request()->routeIs('attendance.*') ? 'active' : '' }}">
        <i class="fa-solid fa-calendar-check"></i> <span>Attendance Record</span>
    </a>
</li>

<li class="nav-category">Communications</li>
<li>
    <a href="{{ route('broadcasts.index') }}" class="{{ request()->routeIs('broadcasts.*') ? 'active' : '' }}">
        <i class="fa-solid fa-bullhorn"></i> <span>Campus Noticeboard</span>
    </a>
</li>
