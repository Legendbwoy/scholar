<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BroadcastTemplate;
use App\Models\SchoolCalendarHoliday;
use App\Models\AuditTrail;
use Illuminate\Support\Str;

class BroadcastTemplateController extends Controller
{
    /**
     * Display broadcast templates directory.
     */
    public function index(Request $request)
    {
        $query = BroadcastTemplate::with(['creator', 'linkedHoliday']);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('auto_send')) {
            $query->where('is_auto_send', true);
        }

        $templates = $query->orderBy('category')->orderBy('title')->paginate(12)->withQueryString();

        $categories = [
            'seasonal' => 'Seasonal & New Year Greetings',
            'religious' => 'Religious Observances & Holy Days',
            'gender_event' => 'Gender & Inclusion Events',
            'holiday_notification' => 'Holiday Notifications & Closures',
            'academic_milestone' => 'Academic Milestones & Directives',
        ];

        $holidays = SchoolCalendarHoliday::orderBy('start_date')->get();

        $stats = [
            'total' => BroadcastTemplate::count(),
            'auto_send_active' => BroadcastTemplate::where('is_auto_send', true)->count(),
            'seasonal' => BroadcastTemplate::where('category', 'seasonal')->count(),
            'religious' => BroadcastTemplate::where('category', 'religious')->count(),
            'gender_event' => BroadcastTemplate::where('category', 'gender_event')->count(),
            'holiday_notification' => BroadcastTemplate::where('category', 'holiday_notification')->count(),
        ];

        return view('portals.comms.templates.index', compact('templates', 'categories', 'holidays', 'stats'));
    }

    /**
     * Store a newly created template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'category' => 'required|string|in:seasonal,religious,gender_event,holiday_notification,academic_milestone',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
            'target_audience' => 'required|in:all,students,parents,teachers,class',
            'channels' => 'required|array|min:1',
            'priority' => 'required|in:normal,urgent,emergency',
            'is_auto_send' => 'nullable|boolean',
            'auto_trigger_type' => 'nullable|string|in:manual_only,holiday_linked,scheduled_date',
            'auto_trigger_time' => 'nullable|string|max:10',
            'school_calendar_holiday_id' => 'nullable|exists:school_calendar_holidays,id',
        ]);

        $template = BroadcastTemplate::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'target_audience' => $validated['target_audience'],
            'channels' => $validated['channels'],
            'priority' => $validated['priority'],
            'is_auto_send' => $request->has('is_auto_send'),
            'auto_trigger_type' => $validated['auto_trigger_type'] ?? 'manual_only',
            'auto_trigger_time' => $validated['auto_trigger_time'] ?? '08:00',
            'school_calendar_holiday_id' => $validated['school_calendar_holiday_id'] ?? null,
            'created_by' => auth()->id(),
        ]);

        AuditTrail::log(
            'BROADCAST_TEMPLATE_CREATED',
            "Created broadcast template '{$template->title}' in category {$template->category} (Auto-Send: " . ($template->is_auto_send ? 'YES' : 'NO') . ")",
            'broadcast_templates',
            $template->id
        );

        return redirect()->route('broadcasts.templates.index')->with('success', "Template '{$template->title}' created successfully.");
    }

    /**
     * Update an existing template.
     */
    public function update(Request $request, $id)
    {
        $template = BroadcastTemplate::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'category' => 'required|string|in:seasonal,religious,gender_event,holiday_notification,academic_milestone',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
            'target_audience' => 'required|in:all,students,parents,teachers,class',
            'channels' => 'required|array|min:1',
            'priority' => 'required|in:normal,urgent,emergency',
            'is_auto_send' => 'nullable|boolean',
            'auto_trigger_type' => 'nullable|string|in:manual_only,holiday_linked,scheduled_date',
            'auto_trigger_time' => 'nullable|string|max:10',
            'school_calendar_holiday_id' => 'nullable|exists:school_calendar_holidays,id',
        ]);

        $template->update([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'target_audience' => $validated['target_audience'],
            'channels' => $validated['channels'],
            'priority' => $validated['priority'],
            'is_auto_send' => $request->has('is_auto_send'),
            'auto_trigger_type' => $validated['auto_trigger_type'] ?? 'manual_only',
            'auto_trigger_time' => $validated['auto_trigger_time'] ?? '08:00',
            'school_calendar_holiday_id' => $validated['school_calendar_holiday_id'] ?? null,
        ]);

        AuditTrail::log(
            'BROADCAST_TEMPLATE_UPDATED',
            "Updated parameters for template '{$template->title}'",
            'broadcast_templates',
            $template->id
        );

        return redirect()->back()->with('success', "Template '{$template->title}' updated successfully.");
    }

    /**
     * Delete a template.
     */
    public function destroy($id)
    {
        $template = BroadcastTemplate::findOrFail($id);
        $title = $template->title;
        $template->delete();

        AuditTrail::log(
            'BROADCAST_TEMPLATE_DELETED',
            "Removed broadcast template '{$title}'",
            'broadcast_templates',
            $id
        );

        return redirect()->back()->with('success', "Template '{$title}' removed successfully.");
    }

    /**
     * Quick toggle auto-send on holiday/schedule.
     */
    public function toggleAutoSend($id)
    {
        $template = BroadcastTemplate::findOrFail($id);
        $template->is_auto_send = !$template->is_auto_send;
        $template->save();

        $statusStr = $template->is_auto_send ? 'enabled for automated dispatch' : 'set to manual dispatch only';

        AuditTrail::log(
            'TEMPLATE_AUTO_SEND_TOGGLED',
            "Template '{$template->title}' auto-send status set to: {$statusStr}",
            'broadcast_templates',
            $template->id
        );

        return redirect()->back()->with('success', "Template '{$template->title}' is now {$statusStr}.");
    }

    /**
     * API: Get template JSON details for compose autofill.
     */
    public function getJson($id)
    {
        $template = BroadcastTemplate::with('linkedHoliday')->findOrFail($id);
        return response()->json([
            'id' => $template->id,
            'title' => $template->title,
            'category' => $template->category,
            'subject' => $template->subject,
            'message' => $template->message,
            'target_audience' => $template->target_audience,
            'channels' => $template->channels ?? ['sms', 'email', 'whatsapp', 'in_app'],
            'priority' => $template->priority,
            'is_auto_send' => $template->is_auto_send,
            'linked_holiday' => $template->linkedHoliday ? [
                'id' => $template->linkedHoliday->id,
                'title' => $template->linkedHoliday->title,
                'start_date' => $template->linkedHoliday->start_date->format('M d, Y'),
            ] : null,
        ]);
    }
}
