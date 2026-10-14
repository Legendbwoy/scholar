<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BroadcastMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'broadcast_template_id',
        'title',
        'message',
        'target_audience',
        'school_class_id',
        'channels',
        'priority',
        'send_mode',
        'scheduled_at',
        'auto_triggered_by',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'channels' => 'array',
        'sent_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function template()
    {
        return $this->belongsTo(BroadcastTemplate::class, 'broadcast_template_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function deliveries()
    {
        return $this->hasMany(BroadcastDelivery::class);
    }

    public function isScheduled(): bool
    {
        return $this->send_mode === 'scheduled' && $this->status === 'scheduled';
    }

    public function isAutoHoliday(): bool
    {
        return $this->send_mode === 'auto_holiday';
    }

    /**
     * Resolve dynamic placeholder merge tags for a specific target recipient user.
     */
    public function getReplacementsForUser(?User $user = null): array
    {
        $user = $user ?? (auth()->check() ? auth()->user() : null);

        $studentName = 'Student';
        $parentName = 'Parent / Guardian';
        $className = $this->schoolClass ? $this->schoolClass->name : 'General Cohort';
        $schoolName = config('app.name', 'Scholar International Academy');
        if ($schoolName === 'Laravel') {
            $schoolName = 'Scholar International Academy';
        }

        if ($user) {
            if ($user->isStudent() && $user->student) {
                $studentName = $user->name;
                if ($user->student->guardian && $user->student->guardian->user) {
                    $parentName = $user->student->guardian->user->name;
                }
                if ($user->student->schoolClass) {
                    $className = $user->student->schoolClass->name;
                }
            } elseif ($user->isParent() && $user->guardian) {
                $parentName = $user->name;
                $wards = $user->guardian->students()->with(['user', 'schoolClass'])->get();
                if ($wards->isNotEmpty()) {
                    $studentName = $wards->pluck('user.name')->filter()->join(', ');
                    $className = $wards->pluck('schoolClass.name')->filter()->unique()->join(', ');
                }
            } elseif ($user->isTeacher()) {
                $parentName = 'Parents & Guardians';
                $studentName = 'Students';
            } else {
                $parentName = 'Parents & Guardians';
                $studentName = 'Scholars';
            }
        }

        // Holiday resolution
        $holidayTitle = 'Upcoming Observance';
        $resumptionDate = now()->addDays(3)->format('l, M d, Y');

        if ($this->template && $this->template->linkedHoliday) {
            $h = $this->template->linkedHoliday;
            $holidayTitle = $h->title;
            if ($h->end_date) {
                $resumptionDate = \Carbon\Carbon::parse($h->end_date)->addDay()->format('l, M d, Y');
            }
        } elseif ($this->auto_triggered_by && str_contains($this->auto_triggered_by, 'Holiday:')) {
            $parts = explode('Holiday:', $this->auto_triggered_by);
            $holidayTitle = trim(end($parts));
        }

        $dateStr = $this->created_at ? $this->created_at->format('M d, Y') : now()->format('M d, Y');

        return [
            '{student_name}' => $studentName,
            '{parent_name}' => $parentName,
            '{school_name}' => $schoolName,
            '{class}' => $className ?: 'General Cohort',
            '{holiday_name}' => $holidayTitle,
            '{date}' => $dateStr,
            '{resumption_date}' => $resumptionDate,
        ];
    }

    /**
     * Render message with dynamic merge tags substituted for a recipient.
     */
    public function renderFor(?User $user = null): string
    {
        $replacements = $this->getReplacementsForUser($user);
        return str_ireplace(array_keys($replacements), array_values($replacements), $this->message);
    }

    /**
     * Render title with dynamic merge tags substituted for a recipient.
     */
    public function renderTitleFor(?User $user = null): string
    {
        $replacements = $this->getReplacementsForUser($user);
        return str_ireplace(array_keys($replacements), array_values($replacements), $this->title);
    }

    public function getPersonalizedMessageAttribute(): string
    {
        return $this->renderFor();
    }

    public function getPersonalizedTitleAttribute(): string
    {
        return $this->renderTitleFor();
    }
}