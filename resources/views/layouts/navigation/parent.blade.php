{{-- ================= PARENT / GUARDIAN NAVIGATION ================= --}}
<li class="nav-category">Guardian Desk</li>
<li>
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-people-roof"></i> <span>Guardian Dashboard</span>
    </a>
</li>

<li class="nav-category">Ward Academics</li>
@if(auth()->user()->guardian && auth()->user()->guardian->students->count() > 0)
    @foreach(auth()->user()->guardian->students as $child)
    <li>
        <a href="{{ route('students.show', $child->id) }}" class="{{ request()->is('students/' . $child->id) ? 'active' : '' }}">
            <i class="fa-solid fa-user-graduate"></i> <span>{{ $child->user->name }} (360)</span>
        </a>
    </li>
    <li>
        <a href="{{ route('gradebook.report-card', $child->id) }}" class="" target="_blank">
            <i class="fa-solid fa-award text-warning"></i> <span>{{ $child->user->name }}'s Report Card <i class="fa-solid fa-arrow-up-right-from-square small ms-1" style="font-size: 10px;"></i></span>
        </a>
    </li>
    @endforeach
@else
    <li>
        <a href="javascript:void(0)" class="text-muted">
            <i class="fa-solid fa-circle-info"></i> <span>No Wards Linked</span>
        </a>
    </li>
@endif

<li class="nav-category">LMS Curriculum & Syllabi</li>
<li>
    <a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.*') ? 'active' : '' }}">
        <i class="fa-solid fa-book-open"></i> <span>Course Catalog & LMS</span>
    </a>
</li>

<li class="nav-category">Attendance & Safety</li>
<li>
    <a href="{{ route('attendance.index') }}" class="{{ request()->routeIs('attendance.*') ? 'active' : '' }}">
        <i class="fa-solid fa-calendar-check"></i> <span>Wards' Attendance</span>
    </a>
</li>

<li class="nav-category">Parent Advisories</li>
<li>
    <a href="{{ route('broadcasts.index') }}" class="{{ request()->routeIs('broadcasts.*') ? 'active' : '' }}">
        <i class="fa-solid fa-bullhorn"></i> <span>School Broadcasts</span>
    </a>
</li>
