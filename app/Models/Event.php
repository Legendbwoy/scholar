<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'description',
        'event_date',
        'end_date',
        'venue',
        'banner_image',
        'organizer',
        'registration_link',
        'is_published',
        'is_featured',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'end_date' => 'datetime',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now()->startOfDay())
            ->orderBy('event_date', 'asc');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getBannerUrlAttribute(): string
    {
        if (empty($this->banner_image)) {
            return asset('scholar/assets/images/event-01.jpg');
        }
        if (str_starts_with($this->banner_image, 'http://') || str_starts_with($this->banner_image, 'https://')) {
            return $this->banner_image;
        }
        return asset($this->banner_image);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $base = Str::slug($event->title);
                $slug = $base;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$base}-" . (++$counter);
                }
                $event->slug = $slug;
            }
        });
    }
}
