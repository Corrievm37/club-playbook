<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OutboundEmail;
use Illuminate\Support\Facades\Mail;

class SendQueuedEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-queued-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send queued outbound emails (invoices and reminders)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $emails = OutboundEmail::where('status','queued')
            ->where(function($q){
                $q->whereNull('scheduled_at')->orWhere('scheduled_at','<=', now());
            })
            ->limit(100)
            ->get();

        foreach ($emails as $email) {
            try {
                $body = $email->body_text ?: strip_tags((string)$email->body_html);
                Mail::raw($body, function ($message) use ($email) {
                    $message->to($email->to_email, $email->to_name);
                    $message->subject($email->subject);
                });
                $email->status = 'sent';
                $email->sent_at = now();
                $email->save();
            } catch (\Throwable $e) {
                $email->status = 'failed';
                $email->error_message = $e->getMessage();
                $email->save();
            }
        }
    }
}
