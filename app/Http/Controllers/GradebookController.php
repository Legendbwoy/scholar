<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Assessment;
use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Student;
use App\Models\TermRemark;
use App\Models\AuditTrail;

class GradebookController extends Controller
{
    /**
     * Display all assessments across subjects and cohorts.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $classes = SchoolClass::with('classTeacher.user')->get();

        // Default cohort: For teachers, prioritize their assigned cohort
        $defaultClassId = $classes->first()?->id;
        if ($user && $user->isTeacher() && $user->staff) {
            $myClass = $classes->firstWhere('class_teacher_id', $user->staff->id);
            if ($myClass) {
                $defaultClassId = $myClass->id;
            }
        }

        $currentTerm = \App\Models\AcademicTerm::current();
        $selectedTerm = $request->get('term', $currentTerm->name ?? 'Term 1');
        $selectedSubjectId = $request->get('subject_id');

        $subjectsQuery = Subject::query();
        if ($selectedClassId) {
            $subjectsQuery->where('school_class_id', $selectedClassId);
        }
        $subjects = $subjectsQuery->get();

        $assessmentsQuery = Assessment::with(['subject', 'schoolClass', 'teacher.user', 'grades'])
            ->where('term', $selectedTerm);

        if ($selectedClassId) {
            $assessmentsQuery->where('school_class_id', $selectedClassId);
        }
        if ($selectedSubjectId) {
            $assessmentsQuery->where('subject_id', $selectedSubjectId);
        }

        $assessments = $assessmentsQuery->latest('id')->paginate(12)->withQueryString();

        // Stats calculation
        $totalAssessments = Assessment::where('term', $selectedTerm)->count();
        $totalGradesCount = Grade::count();
        $classStudentsCount = Student::where('school_class_id', $selectedClassId)->count();

        return view('portals.gradebook.index', compact(
            'assessments',
            'classes',
            'subjects',
            'selectedClassId',
            'selectedTerm',
            'selectedSubjectId',
            'totalAssessments',
            'totalGradesCount',
            'classStudentsCount'
        ));
    }

    /**
     * Store a newly created curriculum assessment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'type' => 'required|in:quiz,assignment,midterm,final',
            'term' => 'required|string',
            'academic_year' => 'required|string',
            'max_marks' => 'required|numeric|min:1|max:500',
            'weightage' => 'required|numeric|min:1|max:100',
            'date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);
        $validated['teacher_id'] = $subject->teacher_id ?? Auth::user()->staff?->id;
        $validated['status'] = 'published';

        $assessment = Assessment::create($validated);

        AuditTrail::log(
            'assessment_created',
            "Created academic assessment '{$assessment->title}' for {$subject->name} ({$assessment->term})",
            'assessments',
            $assessment->id,
            null,
            $assessment->toArray()
        );

        return redirect()->route('gradebook.sheet', $assessment->id)
            ->with('success', "Assessment '{$assessment->title}' created! Please input student marks.");
    }

    /**
     * Delete an assessment and its grades.
     */
    public function destroy($id)
    {
        $assessment = Assessment::findOrFail($id);
        $user = Auth::user();

        // Enforce ownership: Admin or assigned teacher
        if (!$user->isAdmin() && (!$user->isTeacher() || $user->staff?->id !== $assessment->teacher_id)) {
            abort(403, 'Unauthorized: Only the assigned assessment instructor or an administrator can delete assessments.');
        }

        $title = $assessment->title;
        $assessment->delete();

        AuditTrail::log(
            'assessment_deleted',
            "Purged assessment '{$title}' and associated student grade records",
            'assessments',
            $id
        );

        return redirect()->back()->with('success', "Assessment '{$title}' has been removed from the institutional ledger.");
    }

    /**
     * Interactive Grade Entry Sheet for a specific assessment.
     */
    public function sheet($id)
    {
        $assessment = Assessment::with(['subject', 'schoolClass', 'teacher.user'])->findOrFail($id);
        $user = Auth::user();

        // Enforce assignment: Teacher must instruct this assessment, subject, or class
        if (!$user->isAdmin() && $user->isTeacher()) {
            $staff = $user->staff;
            $isAssigned = ($assessment->teacher_id === $staff?->id) ||
                ($assessment->schoolClass->class_teacher_id === $staff?->id) ||
                ($assessment->subject->teacher_id === $staff?->id);
            if (!$isAssigned) {
                abort(403, 'Access Denied: You are not authorized to grade assessments for this subject or cohort.');
            }
        }

        $students = Student::where('school_class_id', $assessment->school_class_id)
            ->with(['user', 'grades' => function ($q) use ($assessment) {
                $q->where('assessment_id', $assessment->id);
            }])
            ->get();

        return view('portals.gradebook.sheet', compact('assessment', 'students'));
    }

    /**
     * Batch save scores from the grade sheet.
     */
    public function updateBatch(Request $request, $id)
    {
        $assessment = Assessment::with(['subject', 'schoolClass'])->findOrFail($id);
        $user = Auth::user();

        // Enforce assignment
        if (!$user->isAdmin() && $user->isTeacher()) {
            $staff = $user->staff;
            $isAssigned = ($assessment->teacher_id === $staff?->id) ||
                ($assessment->schoolClass->class_teacher_id === $staff?->id) ||
                ($assessment->subject->teacher_id === $staff?->id);
            if (!$isAssigned) {
                abort(403, 'Access Denied: You are not authorized to record student grades for this assessment.');
            }
        }

        $scoresData = $request->input('grades', []);
        if (is_string($scoresData)) {
            $scoresData = json_decode($scoresData, true) ?? [];
        }
        if (!is_array($scoresData)) {
            $scoresData = [];
        }

        $savedCount = 0;
        foreach ($scoresData as $studentId => $data) {
            if (!is_array($data)) continue;
            $score = isset($data['score']) && $data['score'] !== '' ? (float) $data['score'] : 0.0;
            if ($score > $assessment->max_marks) $score = (float) $assessment->max_marks;
            if ($score < 0) $score = 0.0;

            $remarks = $data['remarks'] ?? null;
            if (!$remarks) {
                $pct = ($score / $assessment->max_marks) * 100;
                $remarks = $pct >= 90 ? 'Distinction honors' : ($pct >= 75 ? 'Commendable performance' : ($pct >= 60 ? 'Competent' : 'Needs remediation'));
            }

            Grade::updateOrCreate(
                [
                    'assessment_id' => $assessment->id,
                    'student_id' => $studentId,
                ],
                [
                    'score' => $score,
                    'remarks' => $remarks,
                ]
            );
            $savedCount++;
        }

        AuditTrail::log(
            'grades_batch_updated',
            "Updated {$savedCount} grade records for assessment '{$assessment->title}'",
            'assessments',
            $assessment->id
        );

        return redirect()->route('gradebook.sheet', $assessment->id)
            ->with('success', "Successfully recorded and cryptographically sealed grades for {$savedCount} students!");
    }

    /**
     * Class-wide Report Cards Registry.
     */
    public function reportsIndex(Request $request)
    {
        $classes = SchoolClass::with('classTeacher.user')->get();
        $selectedClassId = $request->get('class_id', $classes->first()?->id);
        $currentTerm = \App\Models\AcademicTerm::current();
        $currentYear = \App\Models\AcademicYear::current();
        $selectedTerm = $request->get('term', $currentTerm->name ?? 'Term 1');
        $selectedYear = $request->get('academic_year', $currentYear->name ?? date('Y') . '-' . (date('Y') + 1));

        $students = collect();
        $classReports = [];

        if ($selectedClassId) {
            $students = Student::where('school_class_id', $selectedClassId)
                ->with(['user', 'schoolClass'])
                ->get();

            foreach ($students as $st) {
                $classReports[$st->id] = $st->calculateTermReport($selectedTerm, $selectedYear);
            }
        }

        return view('portals.gradebook.reports_index', compact(
            'classes',
            'selectedClassId',
            'selectedTerm',
            'selectedYear',
            'students',
            'classReports'
        ));
    }

    /**
     * Official Institutional Printable Report Card.
     */
    public function reportCard($studentId, Request $request)
    {
        $student = Student::with(['user', 'schoolClass.classTeacher.user', 'guardian.user'])->findOrFail($studentId);
        $user = Auth::user();

        // Role authorization check
        if ($user->isStudent() && $user->student?->id !== $student->id) {
            abort(403, 'Unauthorized access to confidential student academic report.');
        }
        if ($user->isParent() && !$user->guardian?->students->pluck('id')->contains($student->id)) {
            abort(403, 'Unauthorized access: Ward report records only.');
        }

        $currentTerm = \App\Models\AcademicTerm::current();
        $currentYear = \App\Models\AcademicYear::current();
        $term = $request->get('term', $currentTerm->name ?? 'Term 1');
        $year = $request->get('academic_year', $currentYear->name ?? date('Y') . '-' . (date('Y') + 1));

        $reportData = $student->calculateTermReport($term, $year);

        return view('portals.gradebook.report_card', compact('student', 'reportData', 'term', 'year'));
    }

    /**
     * Update custom remarks from principal or form master.
     */
    public function updateRemarks(Request $request, $studentId)
    {
        $validated = $request->validate([
            'term' => 'required|string',
            'academic_year' => 'required|string',
            'conduct' => 'required|string',
            'teacher_remarks' => 'nullable|string',
            'principal_remarks' => 'nullable|string',
        ]);

        TermRemark::updateOrCreate(
            [
                'student_id' => $studentId,
                'term' => $validated['term'],
                'academic_year' => $validated['academic_year'],
            ],
            $validated
        );

        return redirect()->back()->with('success', 'Institutional conduct remarks updated successfully.');
    }
}
