<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Staff;
use App\Models\AuditTrail;

class AcademicController extends Controller
{
    public function classes()
    {
        $classes = SchoolClass::with(['classTeacher.user', 'subjects'])->withCount('students')->get();
        $staffMembers = Staff::with('user')->get();
        return view('portals.academic.classes', compact('classes', 'staffMembers'));
    }

    public function storeClass(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'numeric_grade' => 'required|integer|min:1|max:12',
            'section' => 'required|string|max:10',
            'room_number' => 'nullable|string|max:50',
            'capacity' => 'required|integer|min:10|max:100',
            'class_teacher_id' => 'nullable|exists:staff,id',
        ]);

        $class = SchoolClass::create($validated);

        AuditTrail::log(
            'CLASS_CREATED',
            "Academic cohort registered: {$class->name} (Section {$class->section}) in Room {$class->room_number}.",
            'SchoolClass',
            $class->id
        );

        return redirect()->route('academic.classes')->with('success', "Class {$class->name} created successfully.");
    }

    public function subjects()
    {
        $subjects = Subject::with(['schoolClass', 'teacher.user'])->get();
        $classes = SchoolClass::all();
        $teachers = Staff::with('user')->get();
        return view('portals.academic.subjects', compact('subjects', 'classes', 'teachers'));
    }

    public function storeSubject(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'required|string|unique:subjects,code|max:30',
            'school_class_id' => 'nullable|exists:school_classes,id',
            'teacher_id' => 'nullable|exists:staff,id',
            'credit_hours' => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
        ]);

        $subject = Subject::create($validated);

        AuditTrail::log(
            'SUBJECT_CURRICULUM_ADDED',
            "New curriculum subject enrolled: {$subject->name} [{$subject->code}].",
            'Subject',
            $subject->id
        );

        return redirect()->route('academic.subjects')->with('success', "Subject {$subject->name} added to curriculum.");
    }
}