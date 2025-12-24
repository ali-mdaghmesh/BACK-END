<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class EditReservationNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
        
    }

 
    public function via(object $notifiable): array
    {
        return [FcmChannel::class,'database'];
    }


    public function toFcm($notifiable)
    {
    return (new FcmMessage(
        notification: FcmNotification::create()
            ->title('Reservation')
            ->body('a tenant request to edit his reservation')
    ));
}

  
    public function toArray(object $notifiable): array
    {
        return [
            'title'=>'Reservation',
            'body'=>'a tenant request to edit his reservation'
        ];
    }
}
