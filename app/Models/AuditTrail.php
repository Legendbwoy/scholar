<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'record_hash',
        'prev_hash',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an audit trail entry with tamper-evident cryptographic chaining.
     */
    public static function log(
        string $action,
        string $description,
        ?string $auditableType = null,
        ?int $auditableId = null,
        ?array $newValues = null,
        ?array $oldValues = null
    ): self {
        $lastRecord = static::latest('id')->first();
        $prevHash = $lastRecord ? $lastRecord->record_hash : '0000000000000000000000000000000000000000000000000000000000000000';

        $userId = auth()->id() ?? null;
        $ip = request()->ip() ?? '127.0.0.1';
        $agent = substr(request()->userAgent() ?? 'System CLI', 0, 250);
        $time = now()->toDateTimeString();

        $payload = json_encode([
            'user_id' => $userId,
            'action' => $action,
            'type' => $auditableType,
            'id' => $auditableId,
            'desc' => $description,
            'new' => $newValues,
            'old' => $oldValues,
            'ip' => $ip,
            'time' => $time,
            'prev' => $prevHash,
        ]);

        $recordHash = hash('sha256', $payload);

        return static::create([
            'user_id' => $userId,
            'action' => $action,
            'auditable_type' => $auditableType,
            'auditable_id' => $auditableId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $ip,
            'user_agent' => $agent,
            'record_hash' => $recordHash,
            'prev_hash' => $prevHash,
            'created_at' => now(),
        ]);
    }
}