<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use App\Models\AttendanceRecord;

class RsvpUpdatedPush extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public AttendanceRecord $record) {}

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        $player = $this->record->player;
        $session = $this->record->session;
        $title = 'RSVP Updated — '.$session->age_group;
        $body = $player->first_name.' '.$player->last_name.': '.ucfirst($this->record->rsvp_status).' for '.ucfirst($session->type).' '.$session->scheduled_at->format('Y-m-d H:i');
        return (new WebPushMessage)
            ->title($title)
            ->icon('/favicon.ico')
            ->body($body)
            ->data(['url' => url(route('admin.attendance.show', $session->id))]);
    }
}
