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
}