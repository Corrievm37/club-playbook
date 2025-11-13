<?php

namespace App\Mail;

use App\Models\CoachInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CoachInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CoachInvitation $invite) {}

    public function build()
    {
        $url = route('coach.invite.accept', $this->invite->token);
        return $this->subject('Coach Invitation — Complete Your Profile')
            ->view('emails.coach_invite')
            ->with(['url' => $url, 'club' => $this->invite->club]);
    }
}
