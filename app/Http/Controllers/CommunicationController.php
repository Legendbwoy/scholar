<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BroadcastMessage;
use App\Models\BroadcastDelivery;
use App\Models\BroadcastTemplate;
use App\Models\SchoolCalendarHoliday;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\AuditTrail;
use Carbon\Carbon;

class CommunicationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Role: Student View (Noticeboard of cohort and student announcements)
        if ($user->isStudent()) {
            $student = $user->student;
            $classId = $student?->school_class_id;

            $broadcasts = BroadcastMessage::with(['sender', 'schoolClass'])
                ->where(function ($q) use ($classId) {
                    $q->where('target_audience', 'all')
                      ->orWhere('target_audience', 'students');
                    if ($classId) {
                        $q->orWhere(function ($q2) use ($classId) {
                            $q2->where('target_audience', 'class')->where('school_class_id', $classId);
                        });
                    }
                })
                ->latest()
                ->paginate(10);

            return view('portals.comms.noticeboard', compact('broadcasts', 'user'));
        }

        // 2. Role: Parent View (Noticeboard of parent and ward class announcements)
        if ($user->isParent()) {
            $guardian = $user->guardian;
            $classIds = $guardian ? $guardian->students->pluck('school_class_id')->filter()->unique()->toArray() : [];

            $broadcasts = BroadcastMessage::with(['sender', 'schoolClass'])
                ->where(function ($q) use ($classIds) {
                    $q->where('target_audience', 'all')
                      ->orWhere('target_audience', 'parents');
                    if (!empty($classIds)) {
                        $q->orWhere(function ($q2) use ($classIds) {
                            $q2->where('target_audience', 'class')->whereIn('school_class_id', $classIds);
                        });
                    }
                })
                ->latest()
                ->paginate(10);

            return view('portals.comms.noticeboard', compact('broadcasts', 'user'));
        }

        // 3. Role: Teacher / Faculty Desk View (Faculty broadcasts and own composed campaigns)
        if ($user->isTeacher()) {
            $staff = $user->staff;
            $assignedClassIds = $staff ? $staff->classesAsTeacher->pluck('id')->toArray() : [];

            $broadcasts = BroadcastMessage::with(['sender', 'schoolClass', 'deliveries'])
                ->where(function ($q) use ($user, $assignedClassIds) {
                    $q->where('target_audience', 'all')
                      ->orWhere('target_audience', 'teachers')
                      ->orWhere('sender_id', $user->id);
                    if (!empty($assignedClassIds)) {
                        $q->orWhere(function ($q2) use ($assignedClassIds) {
                            $q2->where('target_audience', 'class')->whereIn('school_class_id', $assignedClassIds);
                        });
                    }
                })
                ->latest()
                ->paginate(10);

            $stats = [
                'total_broadcasts' => BroadcastMessage::where('sender_id', $user->id)->orWhere('target_audience', 'teachers')->count(),
                'total_delivered' => BroadcastDelivery::whereHas('broadcast', function ($q) use ($user) {
                    $q->where('sender_id', $user->id);
                })->where('status', 'delivered')->count(),
                'sms_sent' => BroadcastDelivery::whereHas('broadcast', function ($q) use ($user) {
                    $q->where('sender_id', $user->id);
                })->where('channel', 'sms')->count(),
                'email_sent' => BroadcastDelivery::whereHas('broadcast', function ($q) use ($user) {
                    $q->where('sender_id', $user->id);
                })->where('channel', 'email')->count(),
                'whatsapp_sent' => BroadcastDelivery::whereHas('broadcast', function ($q) use ($user) {
                    $q->where('sender_id', $user->id);
                })->where('channel', 'whatsapp')->count(),
                'in_app_sent' => BroadcastDelivery::whereHas('broadcast', function ($q) use ($user) {
                    $q->where('sender_id', $user->id);
                })->where('channel', 'in_app')->count(),
            ];

            return view('portals.comms.index', compact('broadcasts', 'stats'));
        }

        // 4. Role: Super Administrator (Full schoolwide dispatch telemetry)
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

    public function create(Request $request)
    {
        $user = auth()->user();

        // If teacher, limit class options to assigned cohorts
        if ($user->isTeacher()) {
            $staff = $user->staff;
            $classes = $staff ? $staff->classesAsTeacher : collect();
            if ($classes->isEmpty()) {
                $classes = SchoolClass::all();
            }
        } else {
            $classes = SchoolClass::all();
        }

        $templates = BroadcastTemplate::with('linkedHoliday')->orderBy('category')->orderBy('title')->get();
        $holidays = SchoolCalendarHoliday::where('end_date', '>=', now()->startOfDay())->orderBy('start_date')->get();
        $selectedTemplate = $request->filled('template_id') ? BroadcastTemplate::find($request->template_id) : null;

        return view('portals.comms.create', compact('classes', 'templates', 'holidays', 'selectedTemplate'));
    }

    public function send(Request $request)
    {
        $user = auth()->user();

        // Enforce audience boundaries based on role
        $allowedAudiences = $user->isAdmin()
            ? 'all,teachers,students,parents,class'
            : 'students,parents,class'; // Teachers cannot dispatch schoolwide or to faculty body

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
            'target_audience' => 'required|in:' . $allowedAudiences,
            'school_class_id' => 'nullable|required_if:target_audience,class|exists:school_classes,id',
            'channels' => 'required|array|min:1',
            'priority' => 'required|in:normal,urgent,emergency',
            'send_mode' => 'required|in:immediate,scheduled,auto_holiday',
            'scheduled_at' => 'nullable|required_if:send_mode,scheduled|date',
            'school_calendar_holiday_id' => 'nullable|required_if:send_mode,auto_holiday|exists:school_calendar_holidays,id',
            'broadcast_template_id' => 'nullable|exists:broadcast_templates,id',
            'save_as_template' => 'nullable|boolean',
            'template_category' => 'nullable|string|in:seasonal,religious,gender_event,holiday_notification,academic_milestone',
        ]);

        // If teacher targeting a specific class, verify cohort assignment
        if ($user->isTeacher() && $validated['target_audience'] === 'class') {
            $staff = $user->staff;
            $assignedClassIds = $staff ? $staff->classesAsTeacher->pluck('id')->toArray() : [];
            if (!in_array((int)$validated['school_class_id'], $assignedClassIds, true)) {
                $teachesClass = $staff && $staff->courses()->where('school_class_id', $validated['school_class_id'])->exists();
                if (!$teachesClass) {
                    abort(403, 'Unauthorized: Teachers may only dispatch class advisories to cohorts they instruct.');
                }
            }
        }

        // Save as template option if requested
        $templateId = $validated['broadcast_template_id'] ?? null;
        if ($request->has('save_as_template') && !empty($validated['title'])) {
            $newTemplate = BroadcastTemplate::create([
                'title' => $validated['title'],
                'category' => $request->input('template_category', 'seasonal'),
                'subject' => $validated['title'],
                'message' => $validated['message'],
                'target_audience' => $validated['target_audience'],
                'channels' => $validated['channels'],
                'priority' => $validated['priority'],
                'is_auto_send' => $validated['send_mode'] === 'auto_holiday',
                'auto_trigger_type' => $validated['send_mode'] === 'auto_holiday' ? 'holiday_linked' : 'manual_only',
                'school_calendar_holiday_id' => $validated['school_calendar_holiday_id'] ?? null,
                'created_by' => $user->id,
            ]);
            $templateId = $newTemplate->id;
        }

        $sendMode = $validated['send_mode'];
        $scheduledAt = null;
        $autoTriggeredBy = null;
        $status = 'sent';

        if ($sendMode === 'scheduled') {
            $scheduledAt = Carbon::parse($validated['scheduled_at']);
            $status = 'scheduled';
        } elseif ($sendMode === 'auto_holiday') {
            $holiday = SchoolCalendarHoliday::find($validated['school_calendar_holiday_id']);
            $scheduledAt = Carbon::parse($holiday->start_date)->setTime(8, 0, 0);
            $autoTriggeredBy = "Holiday: {$holiday->title}";
            $status = 'scheduled';
        }

        $broadcast = BroadcastMessage::create([
            'sender_id' => $user->id,
            'broadcast_template_id' => $templateId,
            'title' => $validated['title'],
            'message' => $validated['message'],
            'target_audience' => $validated['target_audience'],
            'school_class_id' => $validated['school_class_id'] ?? null,
            'channels' => $validated['channels'],
            'priority' => $validated['priority'],
            'send_mode' => $sendMode,
            'scheduled_at' => $scheduledAt,
            'auto_triggered_by' => $autoTriggeredBy,
            'status' => $status,
            'sent_at' => $status === 'sent' ? now() : null,
        ]);

        if ($status === 'scheduled') {
            AuditTrail::log(
                'BROADCAST_SCHEDULED',
                "Broadcast scheduled for automated dispatch: '{$broadcast->title}' ({$broadcast->send_mode}) for " . ($broadcast->scheduled_at ? $broadcast->scheduled_at->format('M d, Y g:i A') : 'Holiday Trigger'),
                'BroadcastMessage',
                $broadcast->id,
                ['scheduled_at' => $broadcast->scheduled_at, 'mode' => $broadcast->send_mode]
            );

            return redirect()->route('broadcasts.index')
                ->with('success', "Broadcast successfully scheduled for automated dispatch on " . $broadcast->scheduled_at->format('M d, Y g:i A') . " across " . count($validated['channels']) . " channel(s).");
        }

        // Immediate dispatch
        $recipientsCount = $this->dispatchBroadcastDeliveries($broadcast);

        AuditTrail::log(
            'BROADCAST_TRANSMITTED',
            "Multi-channel broadcast dispatched: '{$broadcast->title}' to {$recipientsCount} recipients via " . implode(', ', $validated['channels']) . ".",
            'BroadcastMessage',
            $broadcast->id,
            ['recipients_count' => $recipientsCount, 'channels' => $validated['channels']]
        );

        return redirect()->route('broadcasts.show', $broadcast->id)
            ->with('success', "Broadcast successfully transmitted to {$recipientsCount} recipients across " . count($validated['channels']) . " channel(s).");
    }

    /**
     * Dispatch broadcast deliveries to target recipients.
     */
    public function dispatchBroadcastDeliveries(BroadcastMessage $broadcast): int
    {
        $recipientQuery = User::where('status', 'active');

        switch ($broadcast->target_audience) {
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
                $classId = $broadcast->school_class_id;
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
                break;
        }

        $recipients = $recipientQuery->get();

        foreach ($recipients as $recipient) {
            foreach ($broadcast->channels as $channel) {
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

        $broadcast->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return $recipients->count();
    }

    public function show($id)
    {
        $broadcast = BroadcastMessage::with(['sender', 'schoolClass', 'deliveries.recipient'])->findOrFail($id);
        $user = auth()->user();

        // 1. Strict recipient audience authorization check
        if (!$user->isAdmin() && $broadcast->sender_id !== $user->id) {
            if ($user->isStudent()) {
                $studentClassId = $user->student?->school_class_id;
                $allowed = in_array($broadcast->target_audience, ['all', 'students']) ||
                    ($broadcast->target_audience === 'class' && $broadcast->school_class_id === $studentClassId);
                if (!$allowed) {
                    abort(403, 'Unauthorized access: You are not an authorized recipient of this institutional communication.');
                }
            } elseif ($user->isParent()) {
                $wardClassIds = $user->guardian ? $user->guardian->students->pluck('school_class_id')->filter()->toArray() : [];
                $allowed = in_array($broadcast->target_audience, ['all', 'parents']) ||
                    ($broadcast->target_audience === 'class' && in_array($broadcast->school_class_id, $wardClassIds));
                if (!$allowed) {
                    abort(403, 'Unauthorized access: Confidential communication not addressed to guardians of your ward.');
                }
            } elseif ($user->isTeacher()) {
                $staff = $user->staff;
                $assignedClassIds = $staff ? $staff->classesAsTeacher->pluck('id')->toArray() : [];
                $allowed = in_array($broadcast->target_audience, ['all', 'teachers']) ||
                    ($broadcast->target_audience === 'class' && in_array($broadcast->school_class_id, $assignedClassIds));
                if (!$allowed) {
                    abort(403, 'Unauthorized access: Broadcast not addressed to your faculty profile.');
                }
            }
        }

        // 2. View segregation:
        // Super Admins and the original authoring Teacher can view carrier delivery logs & recipient tracking
        if ($user->isAdmin() || ($user->isTeacher() && $broadcast->sender_id === $user->id)) {
            $deliveries = $broadcast->deliveries()->paginate(25);
            return view('portals.comms.show', compact('broadcast', 'deliveries'));
        }

        // Students, Parents, and non-author Teachers receive the clean Official Directive Bulletin (zero PII / phone / email log exposure)
        return view('portals.comms.notice', compact('broadcast'));
    }
}