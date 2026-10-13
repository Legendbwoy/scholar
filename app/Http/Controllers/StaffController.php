<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Staff;
use App\Models\Department;
use App\Models\AuditTrail;

class StaffController extends Controller
{
    public function index()
    {
        $staffMembers = Staff::with(['user', 'department', 'subjects', 'classesAsTeacher'])->paginate(15);
        $departments = Department::withCount('staff')->get();

        return view('portals.staff.index', compact('staffMembers', 'departments'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('portals.staff.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required|string|max:150',
            'qualification' => 'nullable|string|max:150',
            'specialization' => 'nullable|string|max:150',
            'salary' => 'nullable|numeric',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => 'teacher',
            'password' => Hash::make('password'),
            'avatar' => 'scholar/assets/images/member-02.jpg',
            'status' => 'active',
        ]);

        $empId = 'EMP-' . (1000 + Staff::count() + 1);

        $staff = Staff::create([
            'user_id' => $user->id,
            'department_id' => $validated['department_id'],
            'employee_id' => $empId,
            'designation' => $validated['designation'],
            'qualification' => $validated['qualification'] ?? 'M.Sc. / B.Ed',
            'joining_date' => now()->toDateString(),
            'specialization' => $validated['specialization'] ?? null,
            'salary' => $validated['salary'] ?? 72000,
        ]);

        AuditTrail::log(
            'STAFF_APPOINTED',
            "Faculty appointed: {$user->name} ({$empId}), Designation: {$staff->designation}.",
            'Staff',
            $staff->id,
            $staff->toArray()
        );

        return redirect()->route('staff.index')->with('success', "Faculty member {$user->name} onboarded as {$empId}");
    }

    public function show($id)
    {
        $staff = Staff::with(['user', 'department', 'subjects', 'courses', 'classesAsTeacher'])->findOrFail($id);
        return view('portals.staff.show', compact('staff'));
    }

    public function edit($id)
    {
        $staff = Staff::with(['user', 'department'])->findOrFail($id);
        $departments = Department::all();
        return view('portals.staff.edit', compact('staff', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::with('user')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email,' . $staff->user_id,
            'phone' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required|string|max:150',
            'qualification' => 'nullable|string|max:150',
            'specialization' => 'nullable|string|max:150',
            'salary' => 'nullable|numeric',
        ]);

        $oldValues = [
            'name' => $staff->user->name,
            'department_id' => $staff->department_id,
            'designation' => $staff->designation,
        ];

        // Update user
        $staff->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        // Update staff
        $staff->update([
            'department_id' => $validated['department_id'],
            'designation' => $validated['designation'],
            'qualification' => $validated['qualification'] ?? $staff->qualification,
            'specialization' => $validated['specialization'] ?? $staff->specialization,
            'salary' => $validated['salary'] ?? $staff->salary,
        ]);

        AuditTrail::log(
            'STAFF_UPDATED',
            "Faculty record updated for {$staff->user->name} ({$staff->employee_id}).",
            'Staff',
            $staff->id,
            $validated,
            $oldValues
        );

        return redirect()->route('staff.show', $staff->id)->with('success', "Faculty profile for {$staff->user->name} updated successfully.");
    }

    public function destroy($id)
    {
        $staff = Staff::with('user')->findOrFail($id);
        $name = $staff->user->name;
        $staff->user->update(['status' => 'inactive']);

        AuditTrail::log(
            'STAFF_DEACTIVATED',
            "Faculty appointment {$name} ({$staff->employee_id}) deactivated.",
            'Staff',
            $staff->id
        );

        return redirect()->route('staff.index')->with('info', "Faculty member {$name} status set to inactive.");
    }
}