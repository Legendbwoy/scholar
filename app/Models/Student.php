<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guardian_id',
        'school_class_id',
        'admission_number',
        'dob',
        'gender',
        'blood_group',
        'admission_date',
        'roll_number',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function termRemarks()
    {
        return $this->hasMany(TermRemark::class);
    }

    public function getAttendanceRateAttribute()
    {
        $currentTerm = AcademicTerm::current();
        if ($currentTerm) {
            $instructionalDays = $currentTerm->getInstructionalDaysCount(now(), $this->school_class_id);
            if ($instructionalDays === 0) return 100.0;
            $present = $this->attendances()
                ->whereBetween('date', [$currentTerm->start_date->toDateString(), min(now()->toDateString(), $currentTerm->end_date->toDateString())])
                ->whereIn('status', ['present', 'late', 'excused'])
                ->count();
            return round(min(100.0, ($present / $instructionalDays) * 100), 1);
        }

        $total = $this->attendances()->count();
        if ($total === 0) return 100.0;
        $present = $this->attendances()->whereIn('status', ['present', 'late'])->count();
        return round(($present / $total) * 100, 1);
    }

    public function calculateTermReport($term = 'Term 1', $academicYear = '2026-2027')
    {
        $classId = $this->school_class_id;
        $subjects = Subject::where('school_class_id', $classId)
            ->with(['teacher.user'])
            ->get();

        $subjectResults = [];
        $totalWeightedGpa = 0;
        $totalCredits = 0;
        $totalPercentageSum = 0;
        $scoredSubjectsCount = 0;

        foreach ($subjects as $sub) {
            $assessments = Assessment::where('subject_id', $sub->id)
                ->where('term', $term)
                ->where('academic_year', $academicYear)
                ->where('status', 'published')
                ->get();

            // Find associated course for dual grading policy
            $course = Course::where('subject_id', $sub->id)
                ->where(function ($q) use ($classId) {
                    $q->where('school_class_id', $classId)
                      ->orWhereNull('school_class_id');
                })->first();

            $gradingMode = $course->grading_mode ?? 'dual';
            $lmsWeight = $course->lms_weight ?? 30;
            $offlineWeight = $course->offline_weight ?? 70;

            $lmsEarned = 0;
            $lmsTotalWeight = 0;
            $offlineEarned = 0;
            $offlineTotalWeight = 0;
            $assessmentBreakdown = [];

            foreach ($assessments as $ass) {
                $grade = Grade::where('assessment_id', $ass->id)
                    ->where('student_id', $this->id)
                    ->first();

                $rawScore = $grade ? (float) $grade->score : 0.0;
                $pct = $ass->max_marks > 0 ? ($rawScore / $ass->max_marks) * 100 : 0;
                $weight = $ass->weightage > 0 ? $ass->weightage : 10;
                $weightedPortion = ($pct * $weight) / 100;

                $deliveryMode = $ass->delivery_mode ?? ($ass->type === 'quiz' || $ass->type === 'assignment' ? 'lms_online' : 'offline_coursework');

                if ($deliveryMode === 'lms_online') {
                    $lmsEarned += $weightedPortion;
                    $lmsTotalWeight += $weight;
                } else {
                    $offlineEarned += $weightedPortion;
                    $offlineTotalWeight += $weight;
                }

                $assessmentBreakdown[] = [
                    'title' => $ass->title,
                    'type' => $ass->type,
                    'delivery_mode' => $deliveryMode,
                    'category_group' => $ass->category_group ?? $ass->type,
                    'max_marks' => $ass->max_marks,
                    'weightage' => $ass->weightage,
                    'score' => $rawScore,
                    'percentage' => round($pct, 1),
                    'remarks' => $grade?->remarks ?? 'Completed',
                ];
            }

            // Normalize LMS and Offline components to 100%
            $lmsPct = $lmsTotalWeight > 0 ? round(($lmsEarned / $lmsTotalWeight) * 100, 1) : 0;
            $offlinePct = $offlineTotalWeight > 0 ? round(($offlineEarned / $offlineTotalWeight) * 100, 1) : 0;

            // Compute composite final score according to course grading mode
            if ($gradingMode === 'dual') {
                if ($lmsTotalWeight > 0 && $offlineTotalWeight > 0) {
                    $finalSubjectPct = round((($lmsPct * $lmsWeight) + ($offlinePct * $offlineWeight)) / 100, 1);
                } elseif ($offlineTotalWeight > 0) {
                    $finalSubjectPct = $offlinePct;
                } elseif ($lmsTotalWeight > 0) {
                    $finalSubjectPct = $lmsPct;
                } else {
                    $finalSubjectPct = 0.0;
                }
            } elseif ($gradingMode === 'lms_only') {
                $finalSubjectPct = $lmsPct;
            } else {
                $finalSubjectPct = $offlinePct;
            }

            $letter = Grade::computeLetter($finalSubjectPct);
            $gpaPoint = Grade::computeGpa($finalSubjectPct);

            $credits = $sub->credit_hours ?: 3;
            $totalWeightedGpa += ($gpaPoint * $credits);
            $totalCredits += $credits;
            $totalPercentageSum += $finalSubjectPct;
            $scoredSubjectsCount++;

            $subjectResults[] = [
                'subject' => $sub,
                'course' => $course,
                'grading_mode' => $gradingMode,
                'lms_weight' => $lmsWeight,
                'offline_weight' => $offlineWeight,
                'lms_percentage' => $lmsPct,
                'lms_letter' => Grade::computeLetter($lmsPct),
                'offline_percentage' => $offlinePct,
                'offline_letter' => Grade::computeLetter($offlinePct),
                'final_percentage' => $finalSubjectPct,
                'letter_grade' => $letter,
                'gpa_point' => $gpaPoint,
                'credits' => $credits,
                'teacher' => $sub->teacher?->user?->name ?? 'Faculty Member',
                'assessments' => $assessmentBreakdown,
            ];
        }

        $cumulativeGpa = $totalCredits > 0 ? round($totalWeightedGpa / $totalCredits, 2) : 0.0;
        $overallPercentage = $scoredSubjectsCount > 0 ? round($totalPercentageSum / $scoredSubjectsCount, 1) : 0.0;
        $overallLetter = Grade::computeLetter($overallPercentage);

        // Calculate Class Rank
        $classRank = 1;
        $totalInClass = 1;
        if ($classId) {
            $classStudents = Student::where('school_class_id', $classId)->get();
            $totalInClass = $classStudents->count();
            
            $peerScores = [];
            foreach ($classStudents as $peer) {
                $peerGrades = Grade::where('student_id', $peer->id)
                    ->whereHas('assessment', function ($q) use ($term, $academicYear, $classId) {
                        $q->where('school_class_id', $classId)
                          ->where('term', $term)
                          ->where('academic_year', $academicYear);
                    })->avg('score') ?? 0;
                $peerScores[$peer->id] = $peerGrades;
            }
            arsort($peerScores);
            $rank = 1;
            foreach ($peerScores as $pId => $pScore) {
                if ($pId == $this->id) {
                    $classRank = $rank;
                    break;
                }
                $rank++;
            }
        }

        // Calendar-accurate attendance statistics
        $termModel = AcademicTerm::where('name', 'like', "%{$term}%")
            ->orWhere('term_code', 'like', "%{$term}%")
            ->first() ?? AcademicTerm::current();

        $eligibleDays = $termModel ? $termModel->getInstructionalDaysCount(now(), $classId) : 90;
        $termStartDate = $termModel ? $termModel->start_date->toDateString() : '2026-08-24';
        $termEndDate = $termModel ? min(now()->toDateString(), $termModel->end_date->toDateString()) : now()->toDateString();

        $termAttendances = $this->attendances()->whereBetween('date', [$termStartDate, $termEndDate])->get();
        $presentDays = $termAttendances->whereIn('status', ['present', 'late'])->count();
        $excusedDays = $termAttendances->where('status', 'excused')->count();
        $absentDays = $termAttendances->where('status', 'absent')->count();
        $calRate = $eligibleDays > 0 ? round(min(100.0, (($presentDays + $excusedDays) / $eligibleDays) * 100), 1) : 100.0;

        // Retrieve or generate term remark
        $remarkRecord = TermRemark::where('student_id', $this->id)
            ->where('term', $term)
            ->where('academic_year', $academicYear)
            ->first();

        return [
            'term' => $term,
            'academic_year' => $academicYear,
            'subjects' => $subjectResults,
            'cumulative_gpa' => $cumulativeGpa,
            'overall_percentage' => $overallPercentage,
            'overall_letter' => $overallLetter,
            'class_rank' => $classRank,
            'total_students_in_class' => $totalInClass,
            'eligible_instructional_days' => $eligibleDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'excused_days' => $excusedDays,
            'attendance_rate' => $calRate,
            'conduct' => $remarkRecord?->conduct ?? 'Exemplary',
            'teacher_remarks' => $remarkRecord?->teacher_remarks ?? 'Demonstrates high diligence, analytical consistency, and active scholastic engagement in class sessions.',
            'principal_remarks' => $remarkRecord?->principal_remarks ?? 'Promoted with commendable academic standing. Approved by Institutional Examination Board.',
        ];
    }
}
