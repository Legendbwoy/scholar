<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'student_id',
        'score',
        'remarks',
    ];

    protected $casts = [
        'score' => 'float',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function percentage()
    {
        if (!$this->assessment || $this->assessment->max_marks <= 0) {
            return 0;
        }
        return round(($this->score / $this->assessment->max_marks) * 100, 1);
    }

    public static function computeLetter($pct)
    {
        if ($pct >= 90) return 'A+';
        if ($pct >= 80) return 'A';
        if ($pct >= 70) return 'B';
        if ($pct >= 60) return 'C';
        if ($pct >= 50) return 'D';
        return 'F';
    }

    public static function computeGpa($pct)
    {
        if ($pct >= 90) return 4.0;
        if ($pct >= 80) return 3.7;
        if ($pct >= 70) return 3.0;
        if ($pct >= 60) return 2.0;
        if ($pct >= 50) return 1.0;
        return 0.0;
    }

    public function letterGrade()
    {
        return self::computeLetter($this->percentage());
    }

    public function gpaPoint()
    {
        return self::computeGpa($this->percentage());
    }
}
