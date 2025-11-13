<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\OutboundEmail;
use Carbon\Carbon;

class SendInvoiceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-invoice-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $today = Carbon::today();

        // 1) Upcoming reminders: 7 days before due
        $upcoming = Invoice::whereIn('status', ['sent'])
            ->whereDate('due_date', '=', $today->copy()->addDays(7)->toDateString())
            ->get();

        // 2) Due today
        $dueToday = Invoice::whereIn('status', ['sent'])
            ->whereDate('due_date', '=', $today->toDateString())
            ->get();

        // 3) Overdue reminders: 7 and 14 days after due
        $overdue7 = Invoice::whereIn('status', ['sent','overdue'])
            ->whereDate('due_date', '=', $today->copy()->subDays(7)->toDateString())
            ->get();

        $overdue14 = Invoice::whereIn('status', ['sent','overdue'])
            ->whereDate('due_date', '=', $today->copy()->subDays(14)->toDateString())
            ->get();

        $this->queueReminders($upcoming, 'Reminder: Invoice due in 7 days', 'reminder');
        $this->queueReminders($dueToday, 'Reminder: Invoice due today', 'reminder');
        $this->queueReminders($overdue7, 'Reminder: Invoice overdue by 7 days', 'reminder', setOverdue: true);
        $this->queueReminders($overdue14, 'Reminder: Invoice overdue by 14 days', 'reminder', setOverdue: true);
    }

    protected function queueReminders($invoices, string $subject, string $type, bool $setOverdue = false): void
    {
        foreach ($invoices as $invoice) {
            $player = $invoice->player;
            $club = $invoice->club;
            $toEmail = null;
            $toName = null;
            // Use primary guardian email if available; fallback to player-less email is skipped
            if ($player) {
                $guardian = \App\Models\Guardian::where('player_id', $player->id)
                    ->where('primary_contact', true)->first();
                if ($guardian && $guardian->email) {
                    $toEmail = $guardian->email;
                    $toName = $guardian->first_name . ' ' . $guardian->last_name;
                }
            }
            if (!$toEmail) { continue; }

            OutboundEmail::create([
                'club_id' => $club->id,
                'to_email' => $toEmail,
                'to_name' => $toName,
                'subject' => $subject . ' - ' . $invoice->number,
                'body_text' => 'Dear ' . ($toName ?: 'guardian') . ", please settle invoice " . $invoice->number .
                    " with balance ZAR " . number_format($invoice->balance_cents/100, 2) . ".",
                'type' => $type,
                'status' => 'queued',
                'scheduled_at' => now(),
            ]);

            if ($setOverdue && $invoice->status !== 'paid') {
                $invoice->status = 'overdue';
                $invoice->saveQuietly();
            }
        }
    }
}
