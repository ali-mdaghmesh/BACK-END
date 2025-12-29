<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class AcceptReservationNotification extends Notification// implements ShouldQueue
{
   // use Queueable;

    public function via($notifiable)
    {
        return [FcmChannel::class, 'database'];
    }

    public function toFcm($notifiable)
    {
    return (new FcmMessage(
        notification: FcmNotification::create()
            ->title('Reservation')
            ->body('your reservation has been accepted')
    ))
    ;
}

    public function toArray($notifiable)
    {
        return [
            'title'  => 'Reservation',
            'body'   => 'your reservation has been accepted'
        ];
    }
}