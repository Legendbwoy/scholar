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
        $user = auth()->user();
        $classes = SchoolClass::all();
        $subjects = Subject::all();
        $teachers = Staff::with('user')->get();

        $query = Course::with(['teacher.user', 'schoolClass', 'subject', 'materials']);

        // Role-based scoping: Students only see courses of their enrolled cohort
        if ($user && $user->isStudent()) {
            $student = $user->student;
            if ($student && $student->school_class_id) {
                $query->where('school_class_id', $student->school_class_id);
            }
        } elseif ($user && $user->isParent()) {
            // Guardians see courses relevant to their registered wards' cohorts
            $guardian = $user->guardian;
            if ($guardian) {
                $wardClassIds = $guardian->students->pluck('school_class_id')->filter()->unique()->toArray();
                if (!empty($wardClassIds)) {
                    $query->whereIn('school_class_id', $wardClassIds);
                }
            }
        }

        $courses = $query->paginate(12);

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
        $course = Course::with(['teacher.user', 'schoolClass', 'subject', 'materials.teacher.user', 'quizzes.questions', 'quizzes.attempts'])->findOrFail($id);
        return view('portals.courses.show', compact('course'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

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

        // If a teacher provisions a course, enforce assignment to their own staff profile
        if (!$user->isAdmin() && $user->isTeacher()) {
            $validated['teacher_id'] = $user->staff?->id ?? $validated['teacher_id'];
        }

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
        $user = auth()->user();

        // Enforce ownership: Only Admin or the assigned course faculty can edit syllabus
        if (!$user->isAdmin() && (!$user->isTeacher() || $user->staff?->id !== $course->teacher_id)) {
            abort(403, 'Unauthorized: Only the assigned instructor or an administrator can modify this course syllabus.');
        }

        $classes = SchoolClass::all();
        $subjects = Subject::all();
        $teachers = Staff::with('user')->get();

        return view('portals.courses.edit', compact('course', 'classes', 'subjects', 'teachers'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $user = auth()->user();

        // Enforce ownership
        if (!$user->isAdmin() && (!$user->isTeacher() || $user->staff?->id !== $course->teacher_id)) {
            abort(403, 'Unauthorized: Only the assigned instructor or an administrator can modify this course syllabus.');
        }

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

        if (!$user->isAdmin() && $user->isTeacher()) {
            $validated['teacher_id'] = $course->teacher_id; // prevent teacher from changing instructor ownership
        }

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
        $user = auth()->user();

        // Purging courses from catalog is strictly an administrator privilege
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized: Course deletion is restricted to System Administrators.');
        }

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
        $user = auth()->user();

        // Only Admin or the assigned course teacher can upload materials
        if (!$user->isAdmin() && (!$user->isTeacher() || $user->staff?->id !== $course->teacher_id)) {
            abort(403, 'Unauthorized: Only the assigned course instructor or an administrator can publish course materials.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'file_type' => 'nullable|string|in:pdf,video,document,slides,link',
            'file_size' => 'nullable|string',
            'version' => 'nullable|string',
            'material_file' => 'nullable|file|max:51200', // up to 50MB
        ]);

        $fileType = $validated['file_type'] ?? 'document';
        $fileSize = $validated['file_size'] ?? '1.0 MB';
        $filePath = null;

        if ($request->hasFile('material_file') && $request->file('material_file')->isValid()) {
            $uploaded = $request->file('material_file');
            $ext = strtolower($uploaded->getClientOriginalExtension());
            $bytes = $uploaded->getSize();
            $fileSize = $bytes >= 1048576 
                ? number_format($bytes / 1048576, 1) . ' MB' 
                : number_format($bytes / 1024, 1) . ' KB';

            // Auto-detect file type from extension
            if (in_array($ext, ['pdf'])) {
                $fileType = 'pdf';
            } elseif (in_array($ext, ['ppt', 'pptx', 'key'])) {
                $fileType = 'slides';
            } elseif (in_array($ext, ['mp4', 'mov', 'avi', 'mkv', 'webm'])) {
                $fileType = 'video';
            } else {
                $fileType = 'document';
            }

            // Store cleanly in storage/app/public/materials
            $safeName = time() . '_' . strtolower(preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $uploaded->getClientOriginalName()));
            $filePath = $uploaded->storeAs('materials', $safeName, 'public');
        } else {
            $ext = $fileType === 'slides' ? 'pptx' : ($fileType === 'video' ? 'mp4' : 'pdf');
            $filePath = 'materials/' . strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $validated['title'])) . '.' . $ext;
        }

        $material = CourseMaterial::create([
            'course_id' => $course->id,
            'teacher_id' => auth()->user()->staff?->id ?? $course->teacher_id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_type' => $fileType,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'version' => $validated['version'] ?? 'v1.0',
            'download_count' => 0,
        ]);

        AuditTrail::log(
            'MATERIAL_UPLOADED',
            "LMS resource uploaded: {$material->title} [{$material->file_type}] for course {$course->title}.",
            'CourseMaterial',
            $material->id
        );

        return redirect()->route('courses.show', $course->id)->with('success', "Learning material '{$material->title}' published to student LMS repository successfully.");
    }

    public function destroyMaterial($id)
    {
        $material = CourseMaterial::with('course')->findOrFail($id);
        $user = auth()->user();

        // Enforce ownership: Only Admin or the assigned course faculty can delete materials
        if (!$user->isAdmin() && (!$user->isTeacher() || $user->staff?->id !== $material->course?->teacher_id)) {
            abort(403, 'Unauthorized: Only the assigned instructor or an administrator can delete course materials.');
        }

        $courseId = $material->course_id;
        $title = $material->title;

        // Clean up physical file if it exists on disk
        if ($material->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($material->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        AuditTrail::log(
            'MATERIAL_DELETED',
            "LMS resource deleted: {$title} (ID: {$id}).",
            'CourseMaterial',
            $id
        );

        return redirect()->route('courses.show', $courseId)->with('info', "Resource '{$title}' removed from course materials.");
    }

    public function previewMaterial(Request $request, $id)
    {
        $material = CourseMaterial::with(['course.teacher.user', 'teacher.user'])->findOrFail($id);

        AuditTrail::log(
            'MATERIAL_PREVIEWED',
            "User " . (auth()->user()?->name ?? 'Guest') . " reviewed LMS asset: {$material->title}.",
            'CourseMaterial',
            $material->id
        );

        $isRaw = $request->boolean('raw');

        // Raw inline streaming for iframe or media tag
        if ($isRaw) {
            if ($material->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($material->file_path)) {
                $filePath = \Illuminate\Support\Facades\Storage::disk('public')->path($material->file_path);
                $mime = mime_content_type($filePath) ?: 'application/octet-stream';
                return response()->file($filePath, [
                    'Content-Type' => $mime,
                    'Content-Disposition' => 'inline; filename="' . basename($material->file_path) . '"',
                ]);
            }

            // Fallback for demonstration simulated text
            $simulatedContent = "=================================================================\n" .
                "SCHOLAR DIGITAL LMS — DOCUMENT READER\n" .
                "=================================================================\n\n" .
                "Resource Title: " . $material->title . "\n" .
                "Course: " . ($material->course->title ?? 'General Academic') . "\n" .
                "Instructor: " . ($material->teacher->user->name ?? 'Academic Faculty') . "\n" .
                "Category: " . strtoupper($material->file_type ?? 'DOCUMENT') . "\n" .
                "Version: " . ($material->version ?? 'v1.0') . "\n\n" .
                "SYLLABUS & INSTRUCTIONAL NOTES:\n" .
                ($material->description ?? 'Official student learning asset and syllabus notes.') . "\n\n" .
                "=================================================================\n" .
                "Certified Academic Resource • Scholar SMS Digital LMS\n" .
                "=================================================================\n";

            return response($simulatedContent, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        $relatedMaterials = CourseMaterial::where('course_id', $material->course_id)
            ->where('id', '!=', $material->id)
            ->take(5)
            ->get();

        return view('portals.courses.material-viewer', compact('material', 'relatedMaterials'));
    }

    public function downloadMaterial($id)
    {
        $material = CourseMaterial::findOrFail($id);
        $material->increment('download_count');

        AuditTrail::log(
            'MATERIAL_DOWNLOADED',
            "User " . (auth()->user()?->name ?? 'Student') . " accessed LMS file: {$material->title}.",
            'CourseMaterial',
            $material->id
        );

        // Serve real physical file if present on public storage disk
        if ($material->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($material->file_path)) {
            $path = \Illuminate\Support\Facades\Storage::disk('public')->path($material->file_path);
            return response()->download($path, basename($material->file_path));
        }

        // Demonstration streaming fallback
        return response()->streamDownload(function () use ($material) {
            echo "=================================================================\n";
            echo "SCHOLAR LMS ACADEMIC REPOSITORY ASSET\n";
            echo "=================================================================\n\n";
            echo "Resource Title: " . $material->title . "\n";
            echo "Course Curriculum: " . ($material->course->title ?? 'General Academic') . "\n";
            echo "Instructor: " . ($material->teacher->user->name ?? 'Faculty Office') . "\n";
            echo "Version: " . ($material->version ?? 'v1.0') . "\n";
            echo "Category: " . strtoupper($material->file_type ?? 'DOCUMENT') . "\n";
            echo "Timestamp: " . now()->toIso8601String() . "\n";
            echo "Description: " . ($material->description ?? 'Official course syllabus & instructional material.') . "\n\n";
            echo "-----------------------------------------------------------------\n";
            echo "Certified Academic Resource - Scholar SMS Digital LMS\n";
            echo "=================================================================\n";
        }, basename($material->file_path ?? 'document.pdf'));
    }
}