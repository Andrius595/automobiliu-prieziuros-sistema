<?php

namespace App\Jobs;

use App\Mail\CarReminderMail;
use App\Models\Reminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCarRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $reminders = Reminder::all();

        foreach ($reminders as $reminder) {
            if ($this->shouldSendReminder($reminder)) {
                $owner = $reminder->car->owner;
                Mail::to($owner->email)->send(new CarReminderMail($reminder));
                $reminder->last_reminded_at = now();
                $reminder->save();
            }
        }
    }

    private function shouldSendReminder(Reminder $reminder): bool
    {
        $shouldSendReminder = false;

        if ($reminder->type === Reminder::TYPE_DAYS) {
            if ($reminder->last_reminded_at->diffInDays(now()) === $reminder->interval) {
                $shouldSendReminder = true;
            }
        } elseif ($reminder->type === Reminder::TYPE_WEEKS) {
            if ($reminder->last_reminded_at->diffInWeeks(now()) === $reminder->interval) {
                $shouldSendReminder = true;
            }
        } elseif ($reminder->type === Reminder::TYPE_MONTHS) {
            if ($reminder->last_reminded_at->diffInMonths(now()) === $reminder->interval) {
                $shouldSendReminder = true;
            }
        } elseif ($reminder->type === Reminder::TYPE_YEARS) {
            if ($reminder->last_reminded_at->diffInYears(now()) === $reminder->interval) {
                $shouldSendReminder = true;
            }
        } elseif ($reminder->type === Reminder::TYPE_MILEAGE) {
            $first_appointment = $reminder->car->appointments()->oldest()->first();
            $latest_appointment = $reminder->car->appointments()->latest()->first();
            $mileage_difference = $latest_appointment->current_mileage - $first_appointment->current_mileage;
            $days_difference = $latest_appointment->created_at->diffInDays($first_appointment->created_at);
            $average_mileage_per_day = $mileage_difference / $days_difference;
            $days_since_last_reminder = $reminder->last_reminded_at->diffInDays(now());

            if ($average_mileage_per_day * $days_since_last_reminder >= $reminder->interval) {
                $shouldSendReminder = true;
            }
        }

        return $shouldSendReminder;
    }
}
