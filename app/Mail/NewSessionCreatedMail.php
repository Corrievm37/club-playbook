<?php

namespace App\Mail;

use App\Models\AttendanceSession;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewSessionCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public AttendanceSession $session;

    public function __construct(AttendanceSession $session)
    {
        $this->session = $session;
    }

    public function build()
    {
        return $this->subject('New '.ucfirst($this->session->type).' Session: '.$this->session->age_group.' '.$this->session->scheduled_at->format('Y-m-d H:i'))
            ->view('emails.new_session_created')
            ->with([
                'session' => $this->session,
            ]);
    }
}
