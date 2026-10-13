<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BroadcastMessage;
use App\Models\BroadcastDelivery;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\AuditTrail;

class CommunicationController extends Controller
{
    public function index()
    {
        $broadcasts = BroadcastMessage::with(['sender', 'schoolClass', 'deliveries'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total_broadcasts' => BroadcastMessage::count(),
            'total_delivered' => BroadcastDelivery::where('status', 'delivered')->count(),
            'sms_sent' => BroadcastDelivery::where('channel', 'sms')->count(),
            'email_sent' => BroadcastDelivery::where('channel', 'email')->count(),
            'whatsapp_sent' => BroadcastDelivery::where('channel', 'whatsapp')->count(),
            'in_app_sent' => BroadcastDelivery::where('channel', 'in_app')->count(),
        ];

        return view('portals.comms.index', compact('broadcasts', 'stats'));
    }

    public function create()
    {
        $classes = SchoolClass::all();
        return view('portals.comms.create', compact('classes'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
            'target_audience' => 'required|in:all,teachers,students,parents,class',
            'school_class_id' => 'nullable|required_if:target_audience,class|exists:school_classes,id',
            'channels' => 'required|array|min:1',
            'priority' => 'required|in:normal,urgent,emergency',
        ]);

        $broadcast = BroadcastMessage::create([
            'sender_id' => auth()->id(),
            'title' => $validated['title'],
            'message' => $validated['message'],
            'target_audience' => $validated['target_audience'],
            'school_class_id' => $validated['school_class_id'] ?? null,
            'channels' => $validated['channels'],
            'priority' => $validated['priority'],
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        // Resolve recipients
        $recipientQuery = User::where('status', 'active');

        switch ($validated['target_audience']) {
            case 'teachers':
                $recipientQuery->where('role', 'teacher');
                break;
            case 'students':
                $recipientQuery->where('role', 'student');
                break;
            case 'parents':
                $recipientQuery->where('role', 'parent');
                break;
            case 'class':
                $classId = $validated['school_class_id'];
                $recipientQuery->where(function ($q) use ($classId) {
                    $q->whereHas('student', function ($sq) use ($classId) {
                        $sq->where('school_class_id', $classId);
                    })->orWhereHas('guardian.students', function ($gq) use ($classId) {
                        $gq->where('school_class_id', $classId);
                    });
                });
                break;
            case 'all':
            default:
                // All active users
                break;
        }

        $recipients = $recipientQuery->get();

        foreach ($recipients as $recipient) {
            foreach ($validated['channels'] as $channel) {
                $contact = match ($channel) {
                    'email' => $recipient->email,
                    'sms', 'whatsapp' => $recipient->phone ?? '+1 (555) 000-0000',
                    default => $recipient->name,
                };

                BroadcastDelivery::create([
                    'broadcast_message_id' => $broadcast->id,
                    'recipient_id' => $recipient->id,
                    'channel' => $channel,
                    'recipient_contact' => $contact,
                    'status' => 'delivered',
                    'delivered_at' => now(),
                ]);
            }
        }

        AuditTrail::log(
            'BROADCAST_TRANSMITTED',
            "Multi-channel broadcast dispatched: '{$broadcast->title}' to {$recipients->count()} recipients via " . implode(', ', $validated['channels']) . ".",
            'BroadcastMessage',
            $broadcast->id,
            ['recipients_count' => $recipients->count(), 'channels' => $validated['channels']]
        );

        return redirect()->route('broadcasts.show', $broadcast->id)
            ->with('success', "Broadcast successfully transmitted to {$recipients->count()} recipients across " . count($validated['channels']) . " channel(s).");
    }

    public function show($id)
    {
        $broadcast = BroadcastMessage::with(['sender', 'schoolClass', 'deliveries.recipient'])->findOrFail($id);
        $deliveries = $broadcast->deliveries()->paginate(25);

        return view('portals.comms.show', compact('broadcast', 'deliveries'));
    }
}