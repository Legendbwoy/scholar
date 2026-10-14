<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BroadcastTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'subject',
        'message',
        'target_audience',
        'channels',
        'priority',
        'is_auto_send',
        'auto_trigger_type',
        'auto_trigger_time',
        'school_calendar_holiday_id',
        'created_by',
    ];

    protected $casts = [
        'channels' => 'array',
        'is_auto_send' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function linkedHoliday()
    {
        return $this->belongsTo(SchoolCalendarHoliday::class, 'school_calendar_holiday_id');
    }

    public function broadcasts()
    {
        return $this->hasMany(BroadcastMessage::class);
    }

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeAutoSend($query)
    {
        return $query->where('is_auto_send', true);
    }

    public function getCategoryMetaAttribute(): array
    {
        return match ($this->category) {
            'seasonal' => [
                'label' => 'Seasonal & New Year',
                'badge' => 'bg-info-subtle text-info border border-info-subtle',
                'icon' => 'fa-solid fa-snowflake',
            ],
            'religious' => [
                'label' => 'Religious Observances',
                'badge' => 'bg-warning-subtle text-warning border border-warning-subtle',
                'icon' => 'fa-solid fa-hands-praying',
            ],
            'gender_event' => [
                'label' => 'Gender & Inclusion',
                'badge' => 'bg-purple-subtle text-purple border border-purple-subtle',
                'icon' => 'fa-solid fa-venus-mars',
            ],
            'holiday_notification' => [
                'label' => 'Holiday & School Closure',
                'badge' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'icon' => 'fa-solid fa-calendar-xmark',
            ],
            'academic_milestone' => [
                'label' => 'Academic Milestones',
                'badge' => 'bg-success-subtle text-success border border-success-subtle',
                'icon' => 'fa-solid fa-graduation-cap',
            ],
            default => [
                'label' => ucfirst(str_replace('_', ' ', $this->category)),
                'badge' => 'bg-secondary-subtle text-secondary',
                'icon' => 'fa-solid fa-bullhorn',
            ],
        };
    }

    public function renderSubstitutedMessage(array $replacements = []): string
    {
        $defaults = [
            '{student_name}' => 'Alexander Cole',
            '{parent_name}' => 'Mr. & Mrs. Arthur Cole',
            '{school_name}' => 'Scholar SMS Academy',
            '{class}' => 'Grade 10 - Alpha',
            '{date}' => now()->format('M d, Y'),
            '{holiday_name}' => $this->linkedHoliday ? $this->linkedHoliday->title : 'Public Observance Day',
            '{resumption_date}' => now()->addDays(3)->format('l, M d, Y'),
        ];

        $merged = array_merge($defaults, $replacements);
        return str_replace(array_keys($merged), array_values($merged), $this->message);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($template) {
            if (empty($template->slug)) {
                $base = Str::slug($template->title);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$base}-" . (++$i);
                }
                $template->slug = $slug;
            }
        });
    }
}
