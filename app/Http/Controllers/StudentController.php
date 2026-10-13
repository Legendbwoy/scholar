<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Guardian;
use App\Models\AuditTrail;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'schoolClass', 'guardian.user']);

        if ($request->filled('class_id')) {
            $query->where('school_class_id', $request->class_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('admission_number', 'like', "%{$search}%")
                  ->orWhere('roll_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $students = $query->paginate(15)->withQueryString();
        $classes = SchoolClass::all();

        return view('portals.students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $classes = SchoolClass::all();
        $guardians = Guardian::with('user')->get();
        return view('portals.students.create', compact('classes', 'guardians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'school_class_id' => 'required|exists:school_classes,id',
            'guardian_id' => 'nullable|exists:guardians,id',
            'gender' => 'required|in:Male,Female,Other',
            'dob' => 'nullable|date',
            'blood_group' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => 'student',
            'password' => Hash::make($validated['password'] ?? 'password'),
            'avatar' => 'scholar/assets/images/event-01.jpg',
            'status' => 'active',
        ]);

        $nextNum = Student::count() + 1;
        $admNumber = 'SCH-2026-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        $student = Student::create([
            'user_id' => $user->id,
            'guardian_id' => $validated['guardian_id'] ?? null,
            'school_class_id' => $validated['school_class_id'],
            'admission_number' => $admNumber,
            'dob' => $validated['dob'] ?? null,
            'gender' => $validated['gender'],
            'blood_group' => $validated['blood_group'] ?? 'O+',
            'admission_date' => now()->toDateString(),
            'roll_number' => 'R-' . $nextNum,
            'status' => 'active',
        ]);

        AuditTrail::log(
            'STUDENT_ENROLLED',
            "New student enrolled: {$user->name} ({$admNumber}) in class ID {$student->school_class_id}.",
            'Student',
            $student->id,
            $student->toArray()
        );

        return redirect()->route('students.index')->with('success', "Student {$user->name} registered with Admission Number: {$admNumber}");
    }

    public function show($id)
    {
        $student = Student::with(['user', 'schoolClass.classTeacher.user', 'guardian.user', 'attendances'])->findOrFail($id);

        $currentUser = auth()->user();
        if ($currentUser->isStudent() && $currentUser->student?->id != $student->id) {
            abort(403, 'Access Denied: Students may only access their own personal academic record.');
        }

        if ($currentUser->isParent()) {
            $wardIds = $currentUser->guardian ? $currentUser->guardian->students->pluck('id')->toArray() : [];
            if (!in_array($student->id, $wardIds)) {
                abort(403, 'Access Denied: Guardians may only access records for their registered wards.');
            }
        }

        $attendances = $student->attendances()->orderBy('date', 'desc')->take(20)->get();

        return view('portals.students.show', compact('student', 'attendances'));
    }

    public function edit($id)
    {
        $student = Student::with(['user', 'guardian', 'schoolClass'])->findOrFail($id);
        $classes = SchoolClass::all();
        $guardians = Guardian::with('user')->get();

        return view('portals.students.edit', compact('student', 'classes', 'guardians'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::with('user')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'phone' => 'nullable|string',
            'school_class_id' => 'required|exists:school_classes,id',
            'guardian_id' => 'nullable|exists:guardians,id',
            'roll_number' => 'nullable|string|max:50',
            'gender' => 'required|in:Male,Female,Other',
            'dob' => 'nullable|date',
            'blood_group' => 'nullable|string',
            'status' => 'required|in:active,inactive,graduated,suspended',
        ]);

        $oldValues = [
            'name' => $student->user->name,
            'email' => $student->user->email,
            'class_id' => $student->school_class_id,
            'status' => $student->status,
        ];

        // Update User
        $student->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'] === 'active' ? 'active' : 'inactive',
        ]);

        // Update Student
        $student->update([
            'school_class_id' => $validated['school_class_id'],
            'guardian_id' => $validated['guardian_id'] ?? null,
            'roll_number' => $validated['roll_number'] ?? $student->roll_number,
            'gender' => $validated['gender'],
            'dob' => $validated['dob'] ?? null,
            'blood_group' => $validated['blood_group'] ?? 'O+',
            'status' => $validated['status'],
        ]);

        AuditTrail::log(
            'STUDENT_UPDATED',
            "Student profile updated for {$student->user->name} ({$student->admission_number}).",
            'Student',
            $student->id,
            $validated,
            $oldValues
        );

        return redirect()->route('students.show', $student->id)->with('success', "Student profile for {$student->user->name} successfully updated.");
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $name = $student->user->name;
        $student->update(['status' => 'suspended']);
        $student->user->update(['status' => 'inactive']);

        AuditTrail::log(
            'STUDENT_SUSPENDED',
            "Student account {$name} (ID: {$student->id}) suspended / archived.",
            'Student',
            $student->id
        );

        return redirect()->route('students.index')->with('info', "Student {$name} has been archived & marked as suspended.");
    }
}
