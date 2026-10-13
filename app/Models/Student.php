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

            $totalWeight = $assessments->sum('weightage');
            $earnedWeightedScore = 0;
            $assessmentBreakdown = [];

            foreach ($assessments as $ass) {
                $grade = Grade::where('assessment_id', $ass->id)
                    ->where('student_id', $this->id)
                    ->first();

                $rawScore = $grade ? (float) $grade->score : 0.0;
                $pct = $ass->max_marks > 0 ? ($rawScore / $ass->max_marks) * 100 : 0;
                $weightedPortion = ($pct * $ass->weightage) / 100;
                $earnedWeightedScore += $weightedPortion;

                $assessmentBreakdown[] = [
                    'title' => $ass->title,
                    'type' => $ass->type,
                    'max_marks' => $ass->max_marks,
                    'weightage' => $ass->weightage,
                    'score' => $rawScore,
                    'percentage' => round($pct, 1),
                    'remarks' => $grade?->remarks ?? 'Completed',
                ];
            }

            // Normalize to 100% if assessments don't total 100 weightage yet
            $finalSubjectPct = $totalWeight > 0 ? round(($earnedWeightedScore / $totalWeight) * 100, 1) : 0;
            $letter = Grade::computeLetter($finalSubjectPct);
            $gpaPoint = Grade::computeGpa($finalSubjectPct);

            $credits = $sub->credit_hours ?: 3;
            $totalWeightedGpa += ($gpaPoint * $credits);
            $totalCredits += $credits;
            $totalPercentageSum += $finalSubjectPct;
            $scoredSubjectsCount++;

            $subjectResults[] = [
                'subject' => $sub,
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
            
            // Compare overall average with peers
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
            'attendance_rate' => $this->attendance_rate,
            'conduct' => $remarkRecord?->conduct ?? 'Exemplary',
            'teacher_remarks' => $remarkRecord?->teacher_remarks ?? 'Demonstrates high diligence, analytical consistency, and active scholastic engagement in class sessions.',
            'principal_remarks' => $remarkRecord?->principal_remarks ?? 'Promoted with commendable academic standing. Approved by Institutional Examination Board.',
        ];
    }
}
