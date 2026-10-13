<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\Staff;
use App\Models\AuditTrail;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['teacher.user', 'schoolClass', 'subject', 'materials'])->paginate(12);
        $classes = SchoolClass::all();
        $subjects = Subject::all();
        $teachers = Staff::with('user')->get();

        return view('portals.courses.index', compact('courses', 'classes', 'subjects', 'teachers'));
    }

    public function create()
    {
        $classes = SchoolClass::all();
        $subjects = Subject::all();
        $teachers = Staff::with('user')->get();

        return view('portals.courses.create', compact('classes', 'subjects', 'teachers'));
    }

    public function show($id)
    {
        $course = Course::with(['teacher.user', 'schoolClass', 'subject', 'materials.teacher.user'])->findOrFail($id);
        return view('portals.courses.show', compact('course'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'code' => 'required|string|unique:courses,code|max:50',
            'subject_id' => 'required|exists:subjects,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'teacher_id' => 'required|exists:staff,id',
            'semester' => 'required|string|max:50',
            'category' => 'required|string|max:50',
            'total_lessons' => 'nullable|integer|min:1|max:100',
            'description' => 'nullable|string',
        ]);

        $validated['thumbnail'] = 'scholar/assets/images/course-01.jpg';
        $validated['is_published'] = true;
        $validated['total_lessons'] = $validated['total_lessons'] ?? 12;

        $course = Course::create($validated);

        AuditTrail::log(
            'COURSE_PROVISIONED',
            "LMS course module created: {$course->title} ({$course->code}).",
            'Course',
            $course->id
        );

        return redirect()->route('courses.show', $course->id)->with('success', "Course {$course->title} initialized successfully.");
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $classes = SchoolClass::all();
        $subjects = Subject::all();
        $teachers = Staff::with('user')->get();

        return view('portals.courses.edit', compact('course', 'classes', 'subjects', 'teachers'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'code' => 'required|string|max:50|unique:courses,code,' . $course->id,
            'subject_id' => 'required|exists:subjects,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'teacher_id' => 'required|exists:staff,id',
            'semester' => 'required|string|max:50',
            'category' => 'required|string|max:50',
            'total_lessons' => 'required|integer|min:1|max:100',
            'is_published' => 'required|boolean',
            'description' => 'nullable|string',
        ]);

        $oldValues = [
            'title' => $course->title,
            'code' => $course->code,
            'teacher_id' => $course->teacher_id,
        ];

        $course->update($validated);

        AuditTrail::log(
            'COURSE_UPDATED',
            "Course curriculum record updated for {$course->title} ({$course->code}).",
            'Course',
            $course->id,
            $validated,
            $oldValues
        );

        return redirect()->route('courses.show', $course->id)->with('success', "Course {$course->title} updated successfully.");
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $title = $course->title;
        $code = $course->code;

        $course->delete();

        AuditTrail::log(
            'COURSE_DELETED',
            "Course curriculum record deleted: {$title} ({$code}).",
            'Course',
            $id
        );

        return redirect()->route('courses.index')->with('info', "Course {$title} has been removed from active catalog.");
    }

    public function storeMaterial(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'file_type' => 'required|in:pdf,video,document,slides,link',
            'file_size' => 'nullable|string',
            'version' => 'nullable|string',
        ]);

        $material = CourseMaterial::create([
            'course_id' => $course->id,
            'teacher_id' => auth()->user()->staff?->id ?? $course->teacher_id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_type' => $validated['file_type'],
            'file_path' => 'materials/' . strtolower(str_replace(' ', '_', $validated['title'])) . '.' . ($validated['file_type'] === 'slides' ? 'pptx' : 'pdf'),
            'file_size' => $validated['file_size'] ?? '3.5 MB',
            'version' => $validated['version'] ?? '1.0',
            'download_count' => 0,
        ]);

        AuditTrail::log(
            'MATERIAL_UPLOADED',
            "LMS resource uploaded: {$material->title} [{$material->file_type}] for course {$course->title}.",
            'CourseMaterial',
            $material->id
        );

        return redirect()->route('courses.show', $course->id)->with('success', "Learning resource '{$material->title}' posted to student LMS portal.");
    }

    public function destroyMaterial($id)
    {
        $material = CourseMaterial::findOrFail($id);
        $courseId = $material->course_id;
        $title = $material->title;

        $material->delete();

        AuditTrail::log(
            'MATERIAL_DELETED',
            "LMS resource deleted: {$title} (ID: {$id}).",
            'CourseMaterial',
            $id
        );

        return redirect()->route('courses.show', $courseId)->with('info', "Resource '{$title}' removed from course materials.");
    }

    public function downloadMaterial($id)
    {
        $material = CourseMaterial::findOrFail($id);
        $material->increment('download_count');

        AuditTrail::log(
            'MATERIAL_DOWNLOADED',
            "User " . auth()->user()->name . " accessed LMS file: {$material->title}.",
            'CourseMaterial',
            $material->id
        );

        return response()->streamDownload(function () use ($material) {
            echo "SCHOLAR LMS ACADEMIC RESOURCE: " . $material->title . "\n";
            echo "Course: " . ($material->course->title ?? 'General') . "\n";
            echo "Version: " . $material->version . "\n";
            echo "Timestamp: " . now()->toIso8601String() . "\n";
            echo "Description: " . $material->description . "\n";
            echo "\n[Simulated Document Content - Official Scholar SMS Repository Asset]";
        }, basename($material->file_path ?? 'document.pdf'));
    }
}