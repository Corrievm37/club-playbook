<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use App\Models\AttendanceSession;

class NewSessionCreatedPush extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public AttendanceSession $session) {}

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        $title = 'New '.ucfirst($this->session->type).' — '.$this->session->age_group;
        $body = trim(($this->session->title ? $this->session->title.' — ' : '').
            $this->session->scheduled_at->format('Y-m-d H:i').
            ($this->session->location ? ' @ '.$this->session->location : ''));
        return (new WebPushMessage)
            ->title($title)
            ->icon('/favicon.ico')
            ->body($body)
            ->data(['url' => url('/guardian/sessions')]);
    }
}
