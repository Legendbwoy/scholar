<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BroadcastMessage;
use App\Models\BroadcastTemplate;
use App\Models\SchoolCalendarHoliday;
use App\Models\User;
use App\Models\AuditTrail;
use App\Http\Controllers\CommunicationController;
use Carbon\Carbon;

class ProcessScheduledBroadcasts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'broadcasts:process-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatches due scheduled broadcasts and holiday automated greeting notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("----------------------------------------------------------------");
        $this->info("   Scholar SMS: Processing Scheduled & Auto-Holiday Broadcasts   ");
        $this->info("----------------------------------------------------------------");

        $commsController = new CommunicationController();
        $dispatchedCount = 0;

        // 1. Process queued scheduled broadcasts whose scheduled_at <= now()
        $dueBroadcasts = BroadcastMessage::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();

        $this->info("Found " . $dueBroadcasts->count() . " due scheduled broadcast(s) ready for transmission.");

        foreach ($dueBroadcasts as $broadcast) {
            $this->line("-> Transmitting broadcast #{$broadcast->id}: '{$broadcast->title}' [{$broadcast->send_mode}]...");

            $recipientsCount = $commsController->dispatchBroadcastDeliveries($broadcast);

            AuditTrail::log(
                'SCHEDULED_BROADCAST_EXECUTED',
                "Scheduled broadcast '{$broadcast->title}' transmitted to {$recipientsCount} recipients.",
                'BroadcastMessage',
                $broadcast->id,
                ['scheduled_at' => $broadcast->scheduled_at, 'mode' => $broadcast->send_mode]
            );

            $this->info("   [SUCCESS] Delivered to {$recipientsCount} recipients across " . implode(', ', $broadcast->channels ?? []) . ".");
            $dispatchedCount++;
        }

        // 2. Process automated holiday greetings configured in BroadcastTemplate
        $todayStr = now()->format('Y-m-d');
        $activeHolidayTemplates = BroadcastTemplate::with('linkedHoliday')
            ->where('is_auto_send', true)
            ->where('auto_trigger_type', 'holiday_linked')
            ->whereNotNull('school_calendar_holiday_id')
            ->get();

        $this->info("Checking " . $activeHolidayTemplates->count() . " auto-send holiday template(s) for today ({$todayStr})...");

        $adminUser = User::where('role', 'admin')->first() ?? User::first();

        foreach ($activeHolidayTemplates as $template) {
            $holiday = $template->linkedHoliday;
            if (!$holiday) {
                continue;
            }

            // Check if holiday start date is today
            $isToday = $holiday->start_date && $holiday->start_date->format('Y-m-d') === $todayStr;

            if ($isToday) {
                // Ensure this template hasn't already been triggered today
                $alreadySent = BroadcastMessage::where('broadcast_template_id', $template->id)
                    ->whereDate('created_at', $todayStr)
                    ->exists();

                if (!$alreadySent) {
                    $this->line("-> Holiday Match Detected! Triggering '{$template->title}' for holiday: '{$holiday->title}'...");

                    $broadcast = BroadcastMessage::create([
                        'sender_id' => $adminUser ? $adminUser->id : 1,
                        'broadcast_template_id' => $template->id,
                        'title' => $template->title,
                        'message' => $template->message,
                        'target_audience' => $template->target_audience,
                        'channels' => $template->channels ?? ['sms', 'email', 'whatsapp', 'in_app'],
                        'priority' => $template->priority ?? 'urgent',
                        'send_mode' => 'auto_holiday',
                        'scheduled_at' => now(),
                        'auto_triggered_by' => "Holiday Auto-Trigger: {$holiday->title}",
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);

                    $recipientsCount = $commsController->dispatchBroadcastDeliveries($broadcast);

                    AuditTrail::log(
                        'HOLIDAY_AUTO_GREETING_TRIGGERED',
                        "Automated holiday greeting '{$template->title}' automatically triggered and transmitted for '{$holiday->title}' to {$recipientsCount} recipients.",
                        'BroadcastMessage',
                        $broadcast->id,
                        ['holiday_id' => $holiday->id, 'template_id' => $template->id]
                    );

                    $this->info("   [SUCCESS] Holiday greeting dispatched to {$recipientsCount} recipients!");
                    $dispatchedCount++;
                } else {
                    $this->line("   [SKIP] Template '{$template->title}' was already triggered for today.");
                }
            }
        }

        $this->info("Done! Processed {$dispatchedCount} broadcast dispatches successfully.");
        return Command::SUCCESS;
    }
}
