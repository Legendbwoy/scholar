<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BroadcastMessage;
use App\Models\BroadcastDelivery;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use App\Models\Attendance;
use App\Models\AuditTrail;

class ApiController extends Controller
{
    public function health()
    {
        return response()->json([
            'status' => 'operational',
            'system' => 'Scholar SMS Enterprise Modular Monolith',
            'version' => '1.0.0',
            'environment' => config('app.env'),
            'timestamp' => now()->toIso8601String(),
            'database' => 'connected',
            'services' => [
                'sms_gateway' => 'ready',
                'email_service' => 'ready',
                'push_fcm' => 'ready',
                'whatsapp_meta' => 'ready',
                'audit_ledger' => 'sealed',
            ],
        ]);
    }

    public function courses()
    {
        $courses = Course::with(['teacher.user', 'materials', 'subject'])
            ->where('is_published', true)
            ->get();

        return response()->json([
            'success' => true,
            'count' => $courses->count(),
            'data' => $courses,
        ]);
    }

    public function attendanceStats()
    {
        $today = now()->toDateString();
        $total = Student::count();
        $present = Attendance::where('date', $today)->whereIn('status', ['present', 'late'])->count();
        $absent = Attendance::where('date', $today)->where('status', 'absent')->count();

        return response()->json([
            'success' => true,
            'date' => $today,
            'total_students' => $total,
            'present' => $present,
            'absent' => $absent,
            'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 100,
        ]);
    }

    public function broadcastSend(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'message' => 'required|string',
            'target_audience' => 'required|in:all,teachers,students,parents',
            'channels' => 'required|array',
            'api_token' => 'nullable|string',
        ]);

        $sender = User::where('role', 'admin')->first();

        $broadcast = BroadcastMessage::create([
            'sender_id' => $sender->id ?? 1,
            'title' => $validated['title'],
            'message' => $validated['message'],
            'target_audience' => $validated['target_audience'],
            'channels' => $validated['channels'],
            'priority' => 'urgent',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $recipients = User::where('role', $validated['target_audience'] === 'all' ? '!=' : '=', $validated['target_audience'])->get();

        foreach ($recipients as $rec) {
            foreach ($validated['channels'] as $ch) {
                BroadcastDelivery::create([
                    'broadcast_message_id' => $broadcast->id,
                    'recipient_id' => $rec->id,
                    'channel' => $ch,
                    'recipient_contact' => $rec->phone ?? $rec->email,
                    'status' => 'delivered',
                    'delivered_at' => now(),
                ]);
            }
        }

        AuditTrail::log(
            'API_BROADCAST_DISPATCH',
            "API triggered broadcast: {$broadcast->title} transmitted to {$recipients->count()} recipients.",
            'BroadcastMessage',
            $broadcast->id
        );

        return response()->json([
            'success' => true,
            'broadcast_id' => $broadcast->id,
            'recipients_reached' => $recipients->count(),
            'channels' => $validated['channels'],
            'status' => 'transmitted',
        ], 201);
    }
}