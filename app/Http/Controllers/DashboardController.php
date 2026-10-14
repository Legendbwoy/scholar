<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Student;
use App\Models\Staff;
use App\Models\SchoolClass;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Attendance;
use App\Models\BroadcastMessage;
use App\Models\AuditTrail;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isTeacher()) {
            return $this->teacherDashboard($user);
        } elseif ($user->isStudent()) {
            return $this->studentDashboard($user);
        } elseif ($user->isParent()) {
            return $this->parentDashboard($user);
        }

        return view('portals.generic', compact('user'));
    }

    private function adminDashboard()
    {
        $stats = [
            'total_students' => Student::count(),
            'total_staff' => Staff::count(),
            'total_classes' => SchoolClass::count(),
            'total_courses' => Course::count(),
            'total_materials' => CourseMaterial::count(),
            'total_broadcasts' => BroadcastMessage::count(),
            'total_audits' => AuditTrail::count(),
        ];

        // Today's attendance calculation
        $today = now()->toDateString();
        $todayTotal = Attendance::where('date', $today)->count();
        $todayPresent = Attendance::where('date', $today)->whereIn('status', ['present', 'late'])->count();
        $stats['attendance_rate_today'] = $todayTotal > 0 ? round(($todayPresent / $todayTotal) * 100, 1) : 100.0;
        $stats['absent_today'] = Attendance::where('date', $today)->where('status', 'absent')->count();

        $recentBroadcasts = BroadcastMessage::with('sender')->latest()->take(4)->get();
        $recentAudits = AuditTrail::latest('id')->take(6)->get();
        $classes = SchoolClass::withCount('students')->with('classTeacher.user')->get();
        $recentStudents = Student::with(['user', 'schoolClass'])->latest()->take(5)->get();

        return view('portals.admin.dashboard', compact('stats', 'recentBroadcasts', 'recentAudits', 'classes', 'recentStudents'));
    }

    private function teacherDashboard(User $user)
    {
        $staff = $user->staff()->with(['classesAsTeacher.students.user', 'subjects', 'courses.materials'])->first();

        $myCourses = Course::where('teacher_id', $staff?->id)->with(['subject', 'schoolClass', 'materials'])->get();
        $myClasses = SchoolClass::where('class_teacher_id', $staff?->id)->with('students.user')->get();
        $recentMaterials = CourseMaterial::where('teacher_id', $staff?->id)->latest()->take(5)->get();

        // Today's attendance for teacher's class
        $today = now()->toDateString();
        $classIds = $myClasses->pluck('id');
        $attendanceToday = Attendance::whereIn('school_class_id', $classIds)->where('date', $today)->get();

        $broadcasts = BroadcastMessage::latest()->take(4)->get();
        $activeYear = \App\Models\AcademicYear::current();
        $activeTerm = \App\Models\AcademicTerm::current();

        return view('portals.teacher.dashboard', compact('user', 'staff', 'myCourses', 'myClasses', 'recentMaterials', 'attendanceToday', 'broadcasts', 'activeYear', 'activeTerm'));
    }

    private function studentDashboard(User $user)
    {
        $student = $user->student()->with(['schoolClass.classTeacher.user', 'guardian.user'])->first();
        $class = $student?->schoolClass;

        $courses = Course::where('school_class_id', $class?->id)
            ->with(['teacher.user', 'materials', 'subject'])
            ->get();

        $attendances = Attendance::where('student_id', $student?->id)
            ->orderBy('date', 'desc')
            ->take(15)
            ->get();

        $totalAtt = Attendance::where('student_id', $student?->id)->count();
        $presentAtt = Attendance::where('student_id', $student?->id)->whereIn('status', ['present', 'late'])->count();
        $attendanceRate = $totalAtt > 0 ? round(($presentAtt / $totalAtt) * 100, 1) : 100.0;

        $broadcasts = BroadcastMessage::where(function ($q) use ($class) {
            $q->where('target_audience', 'all')
              ->orWhere('target_audience', 'students')
              ->orWhere(function ($q2) use ($class) {
                  $q2->where('target_audience', 'class')->where('school_class_id', $class?->id);
              });
        })->latest()->take(5)->get();

        return view('portals.student.dashboard', compact('user', 'student', 'class', 'courses', 'attendances', 'attendanceRate', 'broadcasts'));
    }

    private function parentDashboard(User $user)
    {
        $guardian = $user->guardian()->with('students.user', 'students.schoolClass.classTeacher.user', 'students.attendances')->first();
        $children = $guardian?->students ?? collect();

        $broadcasts = BroadcastMessage::where(function ($q) {
            $q->where('target_audience', 'all')
              ->orWhere('target_audience', 'parents');
        })->latest()->take(5)->get();

        return view('portals.parent.dashboard', compact('user', 'guardian', 'children', 'broadcasts'));
    }

    public function quickSearch(Request $request)
    {
        $query = trim($request->get('q', ''));
        if (strlen($query) < 1) {
            return response()->json(['results' => []]);
        }

        $results = [];
        $user = Auth::user();

        // 1. Students search (Admin & Teachers)
        if ($user->isAdmin() || $user->isTeacher()) {
            $students = Student::with(['user', 'schoolClass'])
                ->where('admission_number', 'LIKE', "%{$query}%")
                ->orWhereHas('user', function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%");
                })
                ->take(5)
                ->get();

            foreach ($students as $s) {
                $results[] = [
                    'category' => 'Students Registry',
                    'title' => $s->user->name,
                    'subtitle' => 'Adm #' . $s->admission_number . ' • ' . ($s->schoolClass->name ?? 'Unassigned'),
                    'url' => route('students.show', $s->id),
                    'icon' => 'fa-solid fa-user-graduate',
                    'badge' => ucfirst($s->status),
                    'badge_class' => $s->status === 'active' ? 'bg-success' : 'bg-danger',
                ];
            }
        }

        // 2. LMS Courses (All verified roles)
        $courses = Course::with(['teacher.user', 'schoolClass'])
            ->where('title', 'LIKE', "%{$query}%")
            ->orWhere('code', 'LIKE', "%{$query}%")
            ->take(5)
            ->get();

        foreach ($courses as $c) {
            $results[] = [
                'category' => 'Courses & LMS',
                'title' => $c->title . ' (' . $c->code . ')',
                'subtitle' => 'Faculty: ' . ($c->teacher->user->name ?? 'Staff') . ' • Cohort: ' . ($c->schoolClass->name ?? 'General'),
                'url' => route('courses.show', $c->id),
                'icon' => 'fa-solid fa-book-open',
                'badge' => $c->credits . ' Credits',
                'badge_class' => 'bg-primary',
            ];
        }

        // 3. Faculty & Staff (Admin only)
        if ($user->isAdmin()) {
            $staff = Staff::with(['user', 'department'])
                ->where('employee_id', 'LIKE', "%{$query}%")
                ->orWhereHas('user', function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%");
                })
                ->take(4)
                ->get();

            foreach ($staff as $st) {
                $results[] = [
                    'category' => 'Faculty & Staff',
                    'title' => $st->user->name,
                    'subtitle' => ($st->designation ?? 'Faculty') . ' • ' . ($st->department->name ?? 'Academics'),
                    'url' => route('staff.show', $st->id),
                    'icon' => 'fa-solid fa-chalkboard-user',
                    'badge' => $st->employee_id,
                    'badge_class' => 'bg-info text-dark',
                ];
            }
        }

        return response()->json(['results' => $results]);
    }
}
